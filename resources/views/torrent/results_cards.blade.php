<div style="width: 100% !important; display: table !important;">
    <div class="align-center" style="display: table-cell !important; width: 100% !important;">{{ $torrents->links() }}</div>
</div>
<div style="width: 100% !important; display: table !important;">
    <div class="mb-5" style="display: table-cell !important; width: 100% !important;">
        @foreach ($torrents as $k => $t)
            <div class="col-md-4">
                <div class="card is-torrent">
                    <div class="card_head">
                    <span class="badge-user text-bold" style="float:right;">
                        <i class="{{ config('other.font-awesome') }} fa-fw fa-arrow-up text-green"></i> {{ $t->seeders }} /
                        <i class="{{ config('other.font-awesome') }} fa-fw fa-arrow-down text-red"></i> {{ $t->leechers }} /
                        <i class="{{ config('other.font-awesome') }} fa-fw fa-check text-orange"></i>{{ $t->times_completed }}
                    </span>&nbsp;
                        <span class="badge-user text-bold text-blue" style="float:right;">{{ $t->getSize() }}</span>&nbsp;
                        <span class="badge-user text-bold text-blue" style="float:right;">{{ $t->resolution->name ?? 'No Res' }}</span>
                        <span class="badge-user text-bold text-blue" style="float:right;">{{ $t->type->name }}</span>&nbsp;
                        <span class="badge-user text-bold text-blue" style="float:right;">{{ $t->category->name }}</span>&nbsp;
                    </div>
                    <div class="card_body">
                        <div class="body_poster">
                            @if ($t->category->movie_meta || $t->category->tv_meta)
                                <img src="{{ isset($t->meta->poster) ? \tmdb_image('poster_mid', $t->meta->poster) : 'https://via.placeholder.com/200x300' }}"
                                     class="show-poster" alt="@lang('torrent.poster')">
                            @endif

                            @if ($t->category->game_meta && isset($t->meta) && $t->meta->cover->image_id && $t->meta->name)
                                <img src="https://images.igdb.com/igdb/image/upload/t_cover_big/{{ $t->meta->cover->image_id }}.jpg" class="show-poster"
                                     data-name='<i style="color: #a5a5a5;">{{ $t->meta->name ?? 'N/A' }}</i>' data-image='<img src="https://images.igdb.com/igdb/image/upload/t_original/{{ $t->meta->cover->image_id }}.jpg" alt="@lang('torrent.poster')" style="height: 1000px;">'
                                     class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
                            @endif

                            @if ($t->category->no_meta || $t->category->music_meta)
                                <img src="https://via.placeholder.com/200x300" class="show-poster"
                                     data-name='<i style="color: #a5a5a5;">N/A</i>' data-image='<img src="https://via.placeholder.com/200x300" alt="@lang('torrent.poster')" style="height: 1000px;">'
                                     class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
                            @endif
                        </div>
                        <div class="body_description">
                            <h3 class="description_title">
                                <a href="{{ route('torrent', ['id' => $t->id]) }}">{{ $t->name }}
                                    @if(($t->category->movie_meta || $t->category->tv_meta) && isset($t->meta) && $t->meta->releaseYear)
                                        <span class="text-bold text-pink"> {{ $t->meta->releaseYear ?? '' }}</span>
                                    @endif
                                    @if($t->category->game_meta && isset($t->meta) && $t->meta->first_release_date)
                                        <span class="text-bold text-pink"> {{ date('Y', strtotime( $t->meta->first_release_date)) }}</span>
                                    @endif
                                </a>
                            </h3>
                            @if ($t->category->movie_meta && isset($t->meta) && $t->meta->genres)
                                @foreach ($t->meta->genres as $genre)
                                    <span class="genre-label">{{ $genre->name }}</span>
                                @endforeach
                            @endif
                            @if ($t->category->tv_meta && isset($t->meta) && $t->meta->genres)
                                @foreach ($t->meta->genres as $genre)
                                    <span class="genre-label">{{ $genre->name }}</span>
                                @endforeach
                            @endif
                            @if ($t->category->game_meta && isset($t->meta) && $t->meta->genres)
                                @foreach ($t->meta->genres as $genre)
                                    <span class="genre-label">{{ $genre->name }}</span>
                                @endforeach
                            @endif
                            <p class="description_plot">
                                {{ $t->meta->overview ?? '' }}
                            </p>
                        </div>
                    </div>
                    <div class="card_footer">
                        <div style="float: left;">
                            @if ($t->anon == 1)
                                <span class="badge-user text-orange text-bold">{{ strtoupper(trans('common.anonymous')) }} @if (auth()->user()->id == $t->user->id || auth()->user()->group->is_modo)
                                        <a href="{{ route('users.show', ['username' => $t->user->username]) }}">({{ $t->user->username }}
                                                    )</a>@endif</span>
                            @else
                                <a href="{{ route('users.show', ['username' => $t->user->username]) }}">
                                <span class="badge-user text-bold" style="color:{{ $t->user->primaryRole->color }}; background-image:{{ $t->user->primaryRole->effect }};">
                                    <i class="{{ $t->user->primaryRole->icon }}" data-toggle="tooltip" title=""
                                       data-original-title="{{ $t->user->primaryRole->name }}"></i> {{ $t->user->username }}
                                </span>
                                </a>
                            @endif
                        </div>
                        <span class="badge-user text-bold" style="float: right;">
                        <i class="{{ config('other.font-awesome') }} fa-thumbs-up text-gold"></i>
                        {{ $t->meta->vote_average ?? 0 }}/10 ({{ $t->meta->vote_count ?? 0 }} @lang('torrent.votes'))
                    </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<div style="width: 100% !important; display: table !important;">
    <div class="align-center" style="display: table-cell !important; width: 100% !important;">{{ $torrents->links() }}</div>
</div>
