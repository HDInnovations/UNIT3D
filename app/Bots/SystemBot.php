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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Bots;

use App\Events\Chatter;
use App\Http\Resources\ChatConversationResource;
use App\Models\Bot;
use App\Models\ChatConversation;
use App\Models\Gift;
use App\Models\User;
use App\Notifications\NewBon;
use App\Repositories\ChatRepository;

class SystemBot
{
    private Bot $bot;

    private User $target;

    private string $type;

    private string $message;

    private string $log;

    public function __construct(private readonly ChatRepository $chatRepository)
    {
        $this->bot = Bot::query()->where('is_systembot', '=', true)->sole();
    }

    public function replaceVars(string $output): string
    {
        $output = str_replace(['{me}', '{command}'], [$this->bot->name, $this->bot->command], $output);

        if (str_contains($output, '{bots}')) {
            $botHelp = '';
            $bots = Bot::query()->where('active', '=', 1)->where('id', '!=', $this->bot->id)->oldest('position')->get();

            foreach ($bots as $bot) {
                $botHelp .= '( ! | / | @)'.$bot->command.' help triggers help file for '.$bot->name."\n";
            }

            $output = str_replace('{bots}', $botHelp, $output);
        }

        return $output;
    }

    /**
     * Get Help.
     */
    public function getHelp(): string
    {
        return $this->replaceVars($this->bot->help ?? '');
    }

    /**
     * Send Gift.
     *
     * @param numeric-string $amount
     */
    public function putGift(string $receiver, string $amount, string $note): string
    {
        $v = validator(['receiver' => $receiver, 'amount' => $amount, 'note' => $note], [
            'receiver' => 'required|string|exists:users,username',
            'amount'   => \sprintf('required|decimal:0,2|min:1|max:%s', $this->target->seedbonus),
            'note'     => 'required|string',
        ]);

        if ($v->passes()) {
            $recipient = User::query()->where('username', 'LIKE', $receiver)->first();

            if (!$recipient || $recipient->id === $this->target->id) {
                return 'Your BON gift could not be sent.';
            }

            $amount = (float) $amount;

            $recipient->increment('seedbonus', $amount);
            $this->target->decrement('seedbonus', $amount);

            $gift = Gift::query()->create([
                'sender_id'    => $this->target->id,
                'recipient_id' => $recipient->id,
                'bon'          => $amount,
                'message'      => $note,
            ]);

            if ($this->target->id !== $recipient->id && $recipient->acceptsNotification($this->target, $recipient, 'bon', 'show_bon_gift')) {
                $recipient->notify(new NewBon($gift));
            }

            $profileUrl = href_profile($this->target);
            $recipientUrl = href_profile($recipient);

            $this->chatRepository->systemMessage(
                \sprintf('[url=%s]%s[/url] has gifted %s BON to [url=%s]%s[/url]', $profileUrl, $this->target->username, $amount, $recipientUrl, $recipient->username)
            );

            return 'Your gift to '.$recipient->username.' for '.$amount.' BON has been sent!';
        }

        return 'Your BON gift could not be sent.';
    }

    /**
     * Process Message.
     */
    public function process(string $type, User $user, string $message): \Illuminate\Http\Response|bool
    {
        $this->target = $user;

        if ($type !== 'message') {
            $message = trim(strstr($message, ' ') ?: '');
        }

        $firstWord = strstr($message, ' ', true);

        switch ($firstWord) {
            case 'gift':
                [, $username, $amount, $message] = mb_split(' +', $message, 4) + [null, null, null, null];

                $this->log = $this->putGift($username, $amount, $message);

                break;
            case 'help':
                $this->log = $this->getHelp();

                break;
            default:
                $this->log = 'All '.$this->bot->name.' commands must be a private message or begin with /'.$this->bot->command.' or !'.$this->bot->command.'. Need help? Type /'.$this->bot->command.' help and you shall be helped.';
        }

        $this->type = $type;
        $this->message = $message;

        return $this->pm();
    }

    /**
     * Output Message.
     */
    public function pm(): \Illuminate\Http\Response|true
    {
        $type = $this->type;
        $target = $this->target;
        $txt = $this->log;
        $message = $this->message;

        if ($type === 'message' || $type === 'private') {
            // Create conversation for user if missing
            $affected = ChatConversation::query()->upsert([[
                'user_id'    => $target->id,
                'bot_id'     => $this->bot->id,
                'audible'    => false,
                'deleted_at' => null,
            ]], ['user_id', 'bot_id'], ['deleted_at']);

            if ($affected === 1) {
                Chatter::dispatch('conversations', $target->id, ChatConversationResource::collection(
                    ChatConversation::query()
                        ->with(['user', 'room', 'target', 'bot'])
                        ->where('user_id', '=', $target->id)
                        ->get()
                ));
            }

            // Create message
            $this->chatRepository->privateMessage($target->id, $message, User::SYSTEM_USER_ID, $this->bot->id);
            $this->chatRepository->privateMessage(User::SYSTEM_USER_ID, $txt, $target->id, $this->bot->id);

            return response('success');
        }

        if ($type === 'echo') {
            $this->chatRepository->botMessage($this->bot->id, $txt, $target->id);

            return response('success');
        }

        if ($type === 'public') {
            $this->chatRepository->message($target->id, $target->chatroom->id, $message, null);
            $this->chatRepository->message(User::SYSTEM_USER_ID, $target->chatroom->id, $txt, $this->bot->id);

            return response('success');
        }

        return true;
    }
}
