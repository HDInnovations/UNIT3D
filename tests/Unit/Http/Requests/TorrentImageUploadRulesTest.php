<?php

declare(strict_types=1);

use App\Http\Requests\StoreTorrentRequest;
use App\Http\Requests\UpdateTorrentRequest;
use App\Models\Category;
use App\Models\Torrent;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;

function makeTorrentImageUploadRulesRequest(?Torrent $torrent = null): Request
{
    $category = Category::factory()->create([
        'no_meta'    => true,
        'music_meta' => false,
        'game_meta'  => false,
        'tv_meta'    => false,
        'movie_meta' => false,
    ]);
    $type = Type::factory()->create();
    $user = User::factory()->create();

    $request = Request::create('/torrents', $torrent === null ? 'POST' : 'PATCH', [
        'category_id' => $category->id,
        'type_id'     => $type->id,
    ]);
    $request->setUserResolver(fn () => $user);

    if ($torrent !== null) {
        $request->setRouteResolver(
            fn () => new class ($torrent->id) {
                public function __construct(private readonly int $torrentId)
                {
                }

                public function parameter(string $name, mixed $default = null): mixed
                {
                    return $name === 'id' ? (string) $this->torrentId : $default;
                }
            }
        );
    }

    return $request;
}

test('store torrent request allows webp and gif torrent images', function (): void {
    $rules = (new StoreTorrentRequest())->rules(makeTorrentImageUploadRulesRequest());

    expect($rules['torrent-cover'])->toContain('mimes:jpg,jpeg,png,webp,gif')
        ->and($rules['torrent-banner'])->toContain('mimes:jpg,jpeg,png,webp,gif');
});

test('update torrent request allows webp and gif torrent images', function (): void {
    $torrent = Torrent::factory()->create();
    $rules = (new UpdateTorrentRequest())->rules(makeTorrentImageUploadRulesRequest($torrent));

    expect($rules['torrent-cover'])->toContain('mimes:jpg,jpeg,png,webp,gif')
        ->and($rules['torrent-banner'])->toContain('mimes:jpg,jpeg,png,webp,gif');
});
