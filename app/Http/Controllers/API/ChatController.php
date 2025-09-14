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

namespace App\Http\Controllers\API;

use App\Bots\NerdBot;
use App\Bots\SystemBot;
use App\Events\Chatter;
use App\Events\MessageDeleted;
use App\Http\Controllers\Controller;
use App\Http\Resources\BotResource;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\ChatRoomResource;
use App\Http\Resources\UserAudibleResource;
use App\Http\Resources\UserEchoResource;
use App\Models\Bot;
use App\Models\Chatroom;
use App\Models\ChatStatus;
use App\Models\Message;
use App\Models\User;
use App\Models\UserAudible;
use App\Models\UserEcho;
use App\Repositories\ChatRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * @see \Tests\Feature\Http\Controllers\API\ChatControllerTest
 */
class ChatController extends Controller
{
    /**
     * ChatController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /* STATUSES */
    public function statuses(): \Illuminate\Http\JsonResponse
    {
        return response()->json(ChatStatus::all());
    }

    /* ECHOES */
    public function echoes(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $echoes = UserEcho::query()
            ->whereBelongsTo($request->user())
            ->with(['bot', 'user', 'target', 'room'])
            ->oldest('id')
            ->get();

        if ($echoes->isEmpty()) {
            $echoes->push(UserEcho::create([
                'user_id' => $request->user()->id,
                'room_id' => 1,
            ]));
        }

        return UserEchoResource::collection($echoes);
    }

    /* AUDIBLES */
    public function audibles(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $audibles = UserAudible::query()
            ->whereBelongsTo($request->user())
            ->with(['bot', 'user', 'target', 'room'])
            ->latest()
            ->get();

        if ($audibles->isEmpty()) {
            $audibles->prepend(UserAudible::create([
                'user_id' => $request->user()->id,
                'room_id' => 1,
                'status'  => true,
            ]));
        }

        return UserAudibleResource::collection($audibles);
    }

    /* BOTS */
    public function bots(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return BotResource::collection(Bot::all());
    }

    /* ROOMS */
    public function rooms(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ChatRoomResource::collection(Chatroom::all());
    }

    public function config(): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        return response(config('chat'));
    }

    /* MESSAGES */
    public function messages(int $roomId): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ChatMessageResource::collection($this->chatRepository->messages($roomId));
    }

    /* MESSAGES */
    public function privateMessages(Request $request, int $targetId): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ChatMessageResource::collection($this->chatRepository->privateMessages($request->user()->id, $targetId));
    }

    /* MESSAGES */
    public function botMessages(Request $request, int $botId): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $bot = Bot::findOrFail($botId);
        $user = $request->user();

        // Create echo for user if missing
        $echoes = cache()->remember(
            'user-echoes'.$user->id,
            3600,
            fn () => UserEcho::with(['user', 'room', 'target', 'bot'])->where('user_id', '=', $user->id)->get()
        );

        if ($echoes->doesntContain(fn ($echo) => $echo->bot_id == $bot->id)) {
            $echoes->push(UserEcho::create([
                'user_id' => $user->id,
                'bot_id'  => $bot->id,
            ]));

            cache()->put('user-echoes'.$user->id, $echoes, 3600);

            Chatter::dispatch('echo', $user->id, UserEchoResource::collection($echoes));
        }

        // Create audible for user if missing
        $audibles = cache()->remember(
            'user-audibles'.$user->id,
            3600,
            fn () => UserAudible::with(['user', 'room', 'target', 'bot'])->where('user_id', '=', $user->id)->get()
        );

        if ($audibles->doesntContain(fn ($audible) => $audible->bot_id == $bot->id)) {
            $audibles->push(UserAudible::create([
                'user_id' => $user->id,
                'bot_id'  => $bot->id,
                'status'  => 0,
            ]));

            cache()->put('user-audibles'.$user->id, $audibles, 3600);

            Chatter::dispatch('audible', $user->id, UserAudibleResource::collection($audibles));
        }

        return ChatMessageResource::collection($this->chatRepository->botMessages($request->user()->id, $bot->id));
    }

    public function createMessage(Request $request): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|bool|ChatMessageResource
    {
        $user = $request->user();

        $userId = $user->id;
        $receiverId = $request->integer('receiver_id');
        $roomId = $request->input('chatroom_id');
        $botId = $request->input('bot_id');
        $message = (string) $request->input('message');

        if (!($user->can_chat ?? $user->group->can_chat)) {
            return response('error', 401);
        }

        $bots = cache()->remember('bots', 3600, fn () => Bot::where('active', '=', 1)->orderByDesc('position')->get());

        if (str_starts_with($message, '/msg')) {
            [, $username, $message] = mb_split(' +', trim($message), 3) + [null, null, ''];

            if ($username !== null) {
                $receiverId = User::where('username', '=', $username)->soleValue('id');
            }

            $botId = 1;
        } elseif (str_starts_with($message, '/gift')) {
            $message = '/bot gift'.substr($message, \strlen('/gift'), \strlen($message));

            return new SystemBot($this->chatRepository)->process('echo', $request->user(), $message);
        } else {
            foreach ($bots as $bot) {
                $which = match (true) {
                    str_starts_with($message, '/'.$bot->command)         => 'echo',
                    str_starts_with($message, '!'.$bot->command)         => 'public',
                    str_starts_with($message, '@'.$bot->command)         => 'private',
                    $message && $receiverId === 1 && $bot->id === $botId => 'message',
                    default                                              => null,
                };

                if ($which !== null) {
                    if ($bot->is_systembot) {
                        return new SystemBot($this->chatRepository)->process($which, $request->user(), $message);
                    }

                    if ($bot->is_nerdbot) {
                        return new NerdBot($this->chatRepository)->process($which, $request->user(), $message);
                    }
                }
            }
        }

        if ($receiverId && $receiverId > 0) {
            // Create echo for both users if missing
            foreach ([[$userId, $receiverId], [$receiverId, $userId]] as [$user1Id, $user2Id]) {
                $echoes = cache()->remember(
                    'user-echoes'.$user1Id,
                    3600,
                    fn () => UserEcho::with(['user', 'room', 'target', 'bot'])->where('user_id', '=', $user1Id)->get()
                );

                if ($echoes->doesntContain(fn ($echo) => $echo->target_id == $user2Id)) {
                    $echoes->push(UserEcho::create([
                        'user_id'   => $user1Id,
                        'target_id' => $user2Id,
                    ]));

                    cache()->put('user-echoes'.$user1Id, $echoes, 3600);

                    Chatter::dispatch('echo', $user1Id, UserEchoResource::collection($echoes));
                }
            }

            // Create audible for both users if missing
            foreach ([[$userId, $receiverId], [$receiverId, $userId]] as [$user1Id, $user2Id]) {
                $audibles = cache()->remember(
                    'user-audibles'.$user1Id,
                    3600,
                    fn () => UserAudible::with(['user', 'room', 'target', 'bot'])->where('user_id', '=', $user1Id)->get()
                );

                if ($audibles->doesntContain(fn ($audible) => $audible->target_id == $user2Id)) {
                    $audibles->push(UserAudible::create([
                        'user_id'   => $user1Id,
                        'target_id' => $user2Id,
                        'status'    => 1,
                    ]));

                    cache()->put('user-audibles'.$user1Id, $audibles, 3600);

                    Chatter::dispatch('audible', $user1Id, UserAudibleResource::collection($audibles));
                }
            }

            $roomId = 0;
            $ignore = $botId > 0 && $receiverId == 1 ? true : null;
            $message = $this->chatRepository->privateMessage($userId, $roomId, $message, $receiverId, null, $ignore);

            return new ChatMessageResource($message);
        }

        $receiverId = null;
        $botId = null;
        $message = $this->chatRepository->message($userId, $roomId, $message, $receiverId, $botId);

        return response('success');
    }

    public function deleteMessage(Request $request, int $id): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $message = Message::findOrFail($id);

        abort_unless($request->user()->id === $message->user_id || $request->user()->group->is_modo, 403);

        $changedByStaff = $request->user()->id !== $message->user_id;

        abort_if($changedByStaff && !$request->user()->group->is_owner && $request->user()->group->level <= $message->user->group->level, 403);

        broadcast(new MessageDeleted($message));

        $message->delete();

        return response('success');
    }

    public function deleteRoomEcho(Request $request): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $user = $request->user();
        UserEcho::where('user_id', '=', $user->id)->where('room_id', '=', $request->integer('room_id'))->delete();

        $user->load(['chatStatus', 'chatroom', 'group', 'echoes']);
        $room = Chatroom::findOrFail($request->integer('room_id'));

        $user->chatroom()->dissociate();
        $user->chatroom()->associate($room);

        $user->save();

        $senderEchoes = UserEcho::with(['room', 'target', 'bot'])->where('user_id', $user->id)->get();

        $expiresAt = Carbon::now()->addMinutes(60);
        cache()->put('user-echoes'.$user->id, $senderEchoes, $expiresAt);
        event(new Chatter('echo', $user->id, UserEchoResource::collection($senderEchoes)));

        /**
         * @see https://github.com/laravel/framework/blob/48246da2320c95a17bfae922d36264105a917906/src/Illuminate/Http/Response.php#L56
         * @phpstan-ignore-next-line Laravel automatically converts models to json
         */
        return response($user);
    }

    public function deleteTargetEcho(Request $request): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $user = $request->user();
        UserEcho::where('user_id', '=', $user->id)->where('target_id', '=', $request->input('target_id'))->delete();

        $user->load(['chatStatus', 'chatroom', 'group', 'echoes']);
        $senderEchoes = UserEcho::with(['room', 'target', 'bot'])->where('user_id', $user->id)->get();

        $expiresAt = Carbon::now()->addMinutes(60);
        cache()->put('user-echoes'.$user->id, $senderEchoes, $expiresAt);
        event(new Chatter('echo', $user->id, UserEchoResource::collection($senderEchoes)));

        /**
         * @see https://github.com/laravel/framework/blob/48246da2320c95a17bfae922d36264105a917906/src/Illuminate/Http/Response.php#L56
         * @phpstan-ignore-next-line Laravel automatically converts models to json
         */
        return response($user);
    }

    public function deleteBotEcho(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        UserEcho::where('user_id', '=', $user->id)->where('bot_id', '=', $request->input('bot_id'))->delete();

        $user->load(['chatStatus', 'chatroom', 'group', 'echoes']);
        $senderEchoes = UserEcho::with(['room', 'target', 'bot'])->where('user_id', $user->id)->get();

        $expiresAt = Carbon::now()->addMinutes(60);
        cache()->put('user-echoes'.$user->id, $senderEchoes, $expiresAt);
        event(new Chatter('echo', $user->id, UserEchoResource::collection($senderEchoes)));

        return response()->json($user);
    }

    public function toggleRoomAudible(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $echo = UserAudible::where('user_id', '=', $user->id)->where('room_id', '=', $request->input('room_id'))->sole();
        $echo->status = !$echo->status;
        $echo->save();

        $user->load(['chatStatus', 'chatroom', 'group', 'audibles', 'audibles']);
        $senderAudibles = UserAudible::with(['room', 'target', 'bot'])->where('user_id', $user->id)->get();

        $expiresAt = Carbon::now()->addMinutes(60);
        cache()->put('user-audibles'.$user->id, $senderAudibles, $expiresAt);
        event(new Chatter('audible', $user->id, UserAudibleResource::collection($senderAudibles)));

        return response()->json($user);
    }

    public function toggleTargetAudible(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $echo = UserAudible::where('user_id', '=', $user->id)->where('target_id', '=', $request->input('target_id'))->sole();
        $echo->status = !$echo->status;
        $echo->save();

        $user->load(['chatStatus', 'chatroom', 'group', 'audibles', 'audibles']);
        $senderAudibles = UserAudible::with(['target', 'room', 'bot'])->where('user_id', $user->id)->get();

        $expiresAt = Carbon::now()->addMinutes(60);
        cache()->put('user-audibles'.$user->id, $senderAudibles, $expiresAt);
        event(new Chatter('audible', $user->id, UserAudibleResource::collection($senderAudibles)));

        return response()->json($user);
    }

    public function toggleBotAudible(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $echo = UserAudible::where('user_id', '=', $user->id)->where('bot_id', '=', $request->input('bot_id'))->sole();
        $echo->status = !$echo->status;
        $echo->save();

        $user->load(['chatStatus', 'chatroom', 'group', 'audibles', 'audibles'])->findOrFail($user->id);
        $senderAudibles = UserAudible::with(['bot', 'room', 'bot'])->where('user_id', $user->id)->get();

        $expiresAt = Carbon::now()->addMinutes(60);
        cache()->put('user-audibles'.$user->id, $senderAudibles, $expiresAt);
        event(new Chatter('audible', $user->id, UserAudibleResource::collection($senderAudibles)));

        return response()->json($user);
    }

    /* USERS */
    public function updateUserChatStatus(Request $request): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $user = $request->user();
        $status = ChatStatus::findOrFail($request->integer('status_id'));

        $this->chatRepository->systemMessage('[url=/users/'.$user->username.']'.$user->username.'[/url] has updated their status to [b]'.$status->name.'[/b]');

        $user->chatStatus()->dissociate();
        $user->chatStatus()->associate($status);
        $user->save();

        return response('success');
    }

    public function updateUserRoom(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $room = Chatroom::findOrFail($request->integer('room_id'));

        $user->chatroom()->dissociate();
        $user->chatroom()->associate($room);

        $user->save();

        // Create echo for user if missing
        $echoes = cache()->remember(
            'user-echoes'.$user->id,
            3600,
            fn () => UserEcho::with(['user', 'room', 'target', 'bot'])->where('user_id', '=', $user->id)->get(),
        );

        if ($echoes->doesntContain(fn ($echo) => $echo->room_id == $room->id)) {
            $echoes->push(UserEcho::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
            ]));

            cache()->put('user-echoes'.$user->id, $echoes, 3600);

            Chatter::dispatch('echo', $user->id, UserEchoResource::collection($echoes));
        }

        return response()->json($user);
    }

    public function updateUserTarget(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user()->load(['chatStatus', 'chatroom', 'group', 'echoes']);

        return response()->json($user);
    }
}
