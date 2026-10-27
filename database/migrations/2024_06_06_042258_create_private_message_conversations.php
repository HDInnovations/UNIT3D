<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $output = new Symfony\Component\Console\Output\ConsoleOutput();

        $output->writeLn("\n\tThis migration is long and will take approximately 30-60 seconds per 100 thousand stored private messages.");

        $output->writeLn("\tCreating new tables and columns...");

        Schema::create('conversations', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('subject');
            $table->timestamps();

            $table->index('updated_at');
            $table->index('created_at');
        });

        Schema::create('participants', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('conversation_id')->index();
            $table->unsignedInteger('user_id');
            $table->boolean('read')->default('0');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['user_id', 'conversation_id']);
            $table->index(['user_id', 'read', 'deleted_at']);

            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::table('private_messages', function (Blueprint $table): void {
            $table->unsignedInteger('conversation_id')->nullable()->after('id');
        });

        $messageId2ConversationId = [];
        $privateMessageUpdates = [];
        $conversations = [];
        $participants = [];

        $output->writeLn("\tParsing previous private message structure...");

        DB::table('private_messages')
            ->select('id', 'sender_id', 'receiver_id', 'subject', 'read', 'related_to', 'created_at', 'updated_at')
            ->lazyByIdDesc()
            ->each(function (object $message) use (&$messageId2ConversationId, &$participants, &$privateMessageUpdates, &$conversations): void {
                if (\array_key_exists($message->id, $messageId2ConversationId)) {
                    $conversationId = $messageId2ConversationId[$message->id];

                    // There should be no need to fill in the other variables
                    // because the messages are only updated, not inserted.
                    // Regardless, we still need to include all values for
                    // all columns without a default value.
                    $privateMessageUpdates[] = [
                        'id'              => $message->id,
                        'conversation_id' => $conversationId,
                        'sender_id'       => 1,
                        'receiver_id'     => 1,
                        'subject'         => '',
                        'message'         => '',
                    ];

                    if ($message->related_to) {
                        $messageId2ConversationId[$message->related_to] = $conversationId;
                    }
                } else {
                    $conversationId = $message->id;

                    $conversations[] = [
                        'id'         => $message->id,
                        'subject'    => $message->subject,
                        'created_at' => $message->created_at,
                        'updated_at' => $message->created_at,
                    ];

                    $participants[] = [
                        'conversation_id' => $conversationId,
                        'user_id'         => $message->sender_id,
                        'read'            => true,
                        'created_at'      => $message->created_at,
                        'updated_at'      => $message->created_at,
                    ];

                    if ($message->sender_id !== $message->receiver_id) {
                        $participants[] = [
                            'conversation_id' => $conversationId,
                            'user_id'         => $message->receiver_id,
                            'read'            => $message->read,
                            'created_at'      => $message->created_at,
                            'updated_at'      => $message->created_at,
                        ];
                    }

                    // There should be no need to fill in the other variables
                    // because the messages are only updated, not inserted.
                    // Regardless, we still need to include all values for
                    // all columns without a default value.
                    $privateMessageUpdates[] = [
                        'id'              => $message->id,
                        'conversation_id' => $conversationId,
                        'sender_id'       => 1,
                        'receiver_id'     => 1,
                        'subject'         => '',
                        'message'         => '',
                    ];

                    if ($message->related_to) {
                        $messageId2ConversationId[$message->related_to] = $conversationId;
                    }
                }
            });

        $output->writeLn("\tInserting all conversations into new conversations table...");

        foreach (collect($conversations)->chunk(intdiv(65_000, 4)) as $conversations) {
            DB::table('conversations')->insert($conversations->toArray());
        }

        $output->writeLn("\tAdding conversation_id to all private messages...");

        foreach (collect($privateMessageUpdates)->chunk(intdiv(65_000, 6)) as $privateMessageUpdates) {
            DB::table('private_messages')->upsert($privateMessageUpdates->toArray(), ['id'], ['conversation_id']);
        }

        $output->writeLn("\tInserting all conversation participants...");

        foreach (collect($participants)->chunk(intdiv(65_000, 5)) as $participants) {
            DB::table('participants')->insert($participants->toArray());
        }

        $output->writeLn("\tUpdating all conversation timestamps...");

        DB::table('conversations')
            ->joinSub(
                DB::table('private_messages')
                    ->select([
                        'conversation_id',
                        DB::raw('MIN(created_at) AS min_created_at'),
                    ])
                    ->groupBy('conversation_id'),
                'oldest_messages',
                fn ($join) => $join->on('oldest_messages.conversation_id', '=', 'conversations.id')
            )
            ->update([
                'conversations.created_at' => DB::raw('oldest_messages.min_created_at')
            ]);

        $output->writeLn("\tUpdating all participant timestamps...");

        DB::table('participants')
            ->join('conversations', 'conversations.id', '=', 'participants.conversation_id')
            ->update([
                'participants.created_at' => DB::raw('conversations.created_at'),
            ]);

        $output->writeLn("\tDropping unused columns from private messages and adding constraints...");

        Schema::table('private_messages', function (Blueprint $table): void {
            $table->unsignedInteger('conversation_id')->change();
            $table->dropForeign(['receiver_id']);
            $table->dropColumn([
                'receiver_id',
                'subject',
                'related_to',
                'read'
            ]);

            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
