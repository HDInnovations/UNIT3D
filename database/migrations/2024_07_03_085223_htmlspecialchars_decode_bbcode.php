<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('articles')
            ->lazyById()
            ->each(function (object $article): void {
                DB::table('articles')
                    ->where('id', '=', $article->id)
                    ->update([
                        'content' => htmlspecialchars_decode($article->content),
                    ]);
            });

        DB::table('comments')
            ->lazyById()
            ->each(function (object $comment): void {
                DB::table('comments')
                    ->where('id', '=', $comment->id)
                    ->update([
                        'content' => htmlspecialchars_decode($comment->content),
                    ]);
            });

        DB::table('messages')
            ->lazyById()
            ->each(function (object $message): void {
                DB::table('messages')
                    ->where('id', '=', $message->id)
                    ->update([
                        'message' => htmlspecialchars_decode($message->message),
                    ]);
            });

        DB::table('user_notes')
            ->lazyById()
            ->each(function (object $userNote): void {
                DB::table('user_notes')
                    ->where('id', '=', $userNote->id)
                    ->update([
                        'message' => htmlspecialchars_decode($userNote->message),
                    ]);
            });

        DB::table('playlists')
            ->lazyById()
            ->each(function (object $playlist): void {
                DB::table('playlists')
                    ->where('id', '=', $playlist->id)
                    ->update([
                        'description' => htmlspecialchars_decode($playlist->description),
                    ]);
            });

        DB::table('posts')
            ->lazyById()
            ->each(function (object $post): void {
                DB::table('posts')
                    ->where('id', '=', $post->id)
                    ->update([
                        'content' => htmlspecialchars_decode($post->content),
                    ]);
            });

        DB::table('private_messages')
            ->lazyById()
            ->each(function (object $privateMessage): void {
                DB::table('private_messages')
                    ->where('id', '=', $privateMessage->id)
                    ->update([
                        'message' => htmlspecialchars_decode($privateMessage->message),
                    ]);
            });

        DB::table('ticket_notes')
            ->lazyById()
            ->each(function (object $ticketNote): void {
                DB::table('ticket_notes')
                    ->where('id', '=', $ticketNote->id)
                    ->update([
                        'message' => htmlspecialchars_decode($ticketNote->message),
                    ]);
            });

        DB::table('torrents')
            ->lazyById()
            ->each(function (object $torrent): void {
                DB::table('torrents')
                    ->where('id', '=', $torrent->id)
                    ->update([
                        'description' => htmlspecialchars_decode($torrent->description),
                    ]);
            });

        DB::table('requests')
            ->lazyById()
            ->each(function (object $request): void {
                DB::table('requests')
                    ->where('id', '=', $request->id)
                    ->update([
                        'description' => htmlspecialchars_decode($request->description),
                    ]);
            });

        DB::table('users')
            ->lazyById()
            ->each(function (object $user): void {
                DB::table('users')
                    ->where('id', '=', $user->id)
                    ->update([
                        'about'     => htmlspecialchars_decode($user->about ?? ''),
                        'signature' => htmlspecialchars_decode($user->signature ?? ''),
                    ]);
            });
    }
};
