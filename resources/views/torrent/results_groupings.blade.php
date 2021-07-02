<div style="width: 100% !important; display: table !important;">
    <div class="align-center" style="width: 100% !important; display: table-cell !important;">
        @if($links)
            {{ $links->links() }}
        @endif
    </div>
</div>
<div style="width: 100% !important; display: table !important;">
    <div class="mb-5" style="width: 100% !important; display: table-cell !important;">
        @if($torrents && is_array($torrents))
            @foreach ($torrents as $k => $c)
                @php
                    $parent = reset($c);
					$t = $parent['chunk'];
                @endphp
                <div class="box">
                    <div class="card is-torrent is-group">
                        <div class="card_head">
                            @if($attributes && is_array($attributes))
                                <span class="badge-user text-bold" style="float:right;">
                                    <i class="{{ config('other.font-awesome') }} fa-fw fa-arrow-up text-green"></i> {{ $attributes[$k]['seeders'] }} /
                                    <i class="{{ config('other.font-awesome') }} fa-fw fa-arrow-down text-red"></i> {{ $attributes[$k]['leechers'] }} /
                                    <i class="{{ config('other.font-awesome') }} fa-fw fa-check text-orange"></i>{{ $attributes[$k]['times_completed'] }}
                                </span>&nbsp;
                            @endif
                            <span class="badge-user text-bold" style="float: right;">
                                                <i class="{{ config('other.font-awesome') }} fa-thumbs-up text-gold"></i>
                                                    {{ $t->meta->vote_average ?? 0 }}/10 ({{ $t->meta->vote_count ?? 0 }} @lang('torrent.votes'))
                                            </span>
                        </div>
                        <div class="card_alt">
                            <div class="body_poster">
                                <img src="{{ isset($t->meta->poster) ? \tmdb_image('poster_mid', $t->meta->poster) : 'https://via.placeholder.com/200x300' }}"
                                     class="show-poster" alt="@lang('torrent.poster')">
                            </div>
                            <div class="body_grouping" style="width: 100%;">
                                <h3 class="description_title">
                                    @if ($t->category->movie_meta)
                                        {{ $t->meta->title ?? 'Unknown' }}
                                    @endif
                                    @if ($t->category->tv_meta)
                                        {{ $t->meta->name ?? 'Unknown' }}
                                    @endif
                                        @if ($t->category->movie_meta)
                                            <span class="text-bold text-pink"> {{ substr($t->meta->release_date ?? '', 0, 4) }}</span>
                                        @endif
                                        @if ($t->category->tv_meta)
                                            <span class="text-bold text-pink"> {{ substr($t->meta->first_air_date ?? '', 0, 4) }}</span>
                                        @endif
                                </h3>
                                @if (isset($t->meta->genres))
                                @foreach ($t->meta->genres as $genre)
                                    <span class="genre-label">{{ $genre->name }}</span>
                                @endforeach
                                @endif
                                <p class="description_plot" style="width: 100%;">
                                    {{ $t->meta->plot ?? '' }}
                                </p>
                                <div class="card_holder" style="width: 100%;" ;>
                                    <hr style="padding: 0 !important; margin: 5px 0 !important; width: 100%;">
                                    <div style="padding: 10px 0; margin: auto;">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-bordered table-striped table-hover" style="margin-bottom: 5px !important;">
                                                <thead>
                                                <tr>
                                                    <th>@lang('torrent.category')</th>
                                                    <th>@lang('torrent.type')/@lang('torrent.resolution')</th>
                                                    <th>@lang('torrent.name')</th>
                                                    <th><i class="{{ config('other.font-awesome') }} fa-clock"></i></th>
                                                    <th><i class="{{ config('other.font-awesome') }} fa-file"></i></th>
                                                    <th><i class="{{ config('other.font-awesome') }} fa-arrow-circle-up"></i></th>
                                                    <th><i class="{{ config('other.font-awesome') }} fa-arrow-circle-down"></i></th>
                                                    <th><i class="{{ config('other.font-awesome') }} fa-check-square"></i></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php
                                                    $class = "";
													$hidden = "";
													$attr = "";
                                                @endphp
                                                @for($x = 0,$xMax = count($c); $x<$xMax; $x++)
                                                    @php
                                                        if(array_key_exists('torrent'.$x,$c)) {
															$current = $c['torrent'.$x]['chunk'];
														} else {
															continue;
														}
														if($x > 5) {
															$class=" facetedLoading";
															$hidden="display: none;";
															$attr = $k;
														}
                                                    @endphp
                                                    @if ($current->sticky == 1)
                                                        <tr class="success{{ $class }}" style="{{ $hidden }}" torrent="{{ $attr }}">
                                                    @else
                                                        <tr class="{{ $class }}" style="{{ $hidden }}" torrent="{{ $attr }}">
                                                            @endif
                                                            <td style="width: 1%;">
                                                                @if ($current->category->image != null)
                                                                    <a href="{{ route('categories.show', ['id' => $current->category->id]) }}">
                                                                        <div class="text-center">
                                                                            <img src="{{ url('files/img/' . $current->category->image) }}" alt="{{ $current->category->name }}"
                                                                                 data-toggle="tooltip"
                                                                                 data-original-title="{{ $current->category->name }} {{ strtolower(trans('torrent.torrent')) }}">
                                                                        </div>
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('categories.show', ['id' => $current->category->id]) }}">
                                                                        <div class="text-center">
                                                                            <i class="{{ $current->category->icon }} torrent-icon" data-toggle="tooltip"
                                                                               data-original-title="{{ $current->category->name }} {{ strtolower(trans('torrent.torrent')) }}"></i>
                                                                        </div>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td style="width: 1%;">
                                                                <div class="text-center" style="padding-top: 4px;">
                                                                                    <span class="label label-success" data-toggle="tooltip"
                                                                                          data-original-title="@lang('torrent.type')">
                                                                                        {{ $current->type->name }}
                                                                                    </span>
                                                                </div>
                                                                <div class="text-center" style="padding-top: 8px;">
                                                                                    <span class="label label-success" data-toggle="tooltip"
                                                                                          data-original-title="@lang('torrent.resolution')">
                                                                                        {{ $current->resolution->name ?? 'No Res' }}
                                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a class="view-torrent" href="{{ route('torrent', ['id' => $current->id]) }}">
                                                                    {{ $current->name }}
                                                                </a>
                                                                @if (config('torrent.download_check_page') == 1)
                                                                    <a href="{{ route('download_check', ['id' => $current->id]) }}">
                                                                        <button class="btn btn-primary btn-circle" type="button" data-toggle="tooltip"
                                                                                data-original-title="@lang('common.download')">
                                                                            <i class="{{ config('other.font-awesome') }} fa-download"></i>
                                                                        </button>
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('download', ['id' => $current->id]) }}">
                                                                        <button class="btn btn-primary btn-circle" type="button" data-toggle="tooltip"
                                                                                data-original-title="@lang('common.download')">
                                                                            <i class="{{ config('other.font-awesome') }} fa-download"></i>
                                                                        </button>
                                                                    </a>
                                                                @endif

                                                                <span id="torrentBookmark{{ $current->id }}" torrent="{{ $current->id }}" state="{{ $bookmarks->where('torrent_id', $current->id)->first() ? 1 : 0}}" class="torrentBookmark"></span>

                                                                <br>
                                                                @if ($current->anon == 1)
                                                                    <span class="badge-extra text-bold">
                                                                <i class="{{ config('other.font-awesome') }} fa-upload" data-toggle="tooltip" data-original-title="@lang('torrent.uploader')"></i> @lang('common.anonymous')
                                                                        @if ($user->id == $current->user->id || $user->hasPrivilegeTo('users_view_private'))
                                                                            <a href="{{ route('users.show', ['username' => $current->user->username]) }}">
                                                                        ({{ $current->user->username }})
                                                                    </a>
                                                                        @endif
                                                            </span>
                                                                @else
                                                                    <span class="badge-extra text-bold">
                                                                <i class="{{ config('other.font-awesome') }} fa-upload" data-toggle="tooltip" data-original-title="@lang('torrent.uploader')"></i>
                                                                    <a href="{{ route('users.show', ['username' => $current->user->username]) }}">
                                                                        {{ $current->user->username }}
                                                                    </a>
                                                            </span>
                                                                @endif
                                                                <span class="badge-extra text-bold text-pink">
                            <i class="{{ config('other.font-awesome') }} fa-heart" data-toggle="tooltip" data-original-title="@lang('torrent.thanks-given')"></i>
                                                        {{ $current->thanks_count }}
                        </span>

                                                                <span class="badge-extra text-bold text-green">
                            <i class="{{ config('other.font-awesome') }} fa-comment" data-toggle="tooltip" data-original-title="@lang('common.comments')"></i>
                                                        {{ $current->comments_count }}
                        </span>

                                                                @if ($current->internal == 1)
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-magic' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.internal-release')' style="color: #baaf92;"></i> @lang('torrent.internal')
                            </span>
                                                                @endif
                                                                @if ($current->stream == 1)
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-play text-red' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.stream-optimized')'></i> @lang('torrent.stream-optimized')
                            </span>
                                                                @endif

                                                                @if ($current->featured == 0)
                                                                    @if ($current->doubleup == 1)
                                                                        <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-gem text-green' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.double-upload')'></i> @lang('torrent.double-upload')
                            </span>
                                                                    @endif
                                                                    @if ($current->free == 1)
                                                                        <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-star text-gold' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.freeleech')'></i> @lang('torrent.freeleech')
                            </span>
                                                                    @endif
                                                                @endif

                                                                @if ($personal_freeleech)
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-id-badge text-orange' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.personal-freeleech')'></i> @lang('torrent.personal-freeleech')
                            </span>
                                                                @endif

                                                                @if ($user->freeleechTokens->where('torrent_id', $current->id)->first())
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-star text-bold' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.freeleech-token')'></i> @lang('torrent.freeleech-token')
                            </span>
                                                                @endif

                                                                @if ($current->featured == 1)
                                                                    <span class='badge-extra text-bold' style='background-image:url(/img/sparkels.gif);'>
                                <i class='{{ config("other.font-awesome") }} fa-certificate text-pink' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.featured')'></i> @lang('torrent.featured')
                            </span>
                                                                @endif

                                                                @if ($user->hasPrivilegeTo('user_special_freeleech'))
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-trophy text-purple' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.special-freeleech')'></i> @lang('torrent.special-freeleech')
                            </span>
                                                                @endif

                                                                @if (config('other.freeleech') == 1)
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-globe text-blue' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.global-freeleech')'></i> @lang('torrent.global-freeleech')
                            </span>
                                                                @endif

                                                                @if (config('other.doubleup') == 1)
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-globe text-green' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.double-upload')'></i> @lang('torrent.double-upload')
                            </span>
                                                                @endif

                                                                @if ($user->hasPrivilegeTo('user_special_double_upload'))
                                                                    <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-trophy text-purple'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('
                                                    torrent.special-double_upload')'></i> @lang('torrent.special-double_upload')
                                            </span>
                                                                @endif

                                                                @if ($current->leechers >= 5)
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-fire text-orange' data-toggle='tooltip' title=''
                                   data-original-title='@lang('common.hot')!'></i> @lang('common.hot')!
                            </span>
                                                                @endif

                                                                @if ($current->sticky == 1)
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-thumbtack text-black' data-toggle='tooltip' title=''
                                   data-original-title='@lang('torrent.sticky')!'></i> @lang('torrent.sticky')
                            </span>
                                                                @endif

                                                                @if ($user->updated_at->getTimestamp() < $current->created_at->getTimestamp())
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-magic text-black' data-toggle='tooltip' title=''
                                   data-original-title='@lang('common.new')!'></i> @lang('common.new')
                            </span>
                                                                @endif

                                                                @if ($current->highspeed == 1)
                                                                    <span class='badge-extra text-bold'>
                                <i class='{{ config("other.font-awesome") }} fa-tachometer text-red' data-toggle='tooltip' title=''
                                   data-original-title='@lang('common.high-speeds')'></i> @lang('common.high-speeds')
                            </span>
                                                                @endif

                                                                @if ($current->sd == 1)
                                                                    <span class='badge-extra text-bold'>
                                                                            <i class='{{ config("other.font-awesome") }} fa-ticket text-orange' data-toggle='tooltip' title=''
                                                                               data-original-title='@lang('torrent.sd-content')!'></i> @lang('torrent.sd-content')
                                                                            </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <time>{{ $current->created_at->diffForHumans() }}</time>
                                                            </td>
                                                            <td>
                                                                <span class='badge-extra text-blue text-bold'>{{ $current->getSize() }}</span>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('peers', ['id' => $current->id]) }}">
                            <span class='badge-extra text-green text-bold'>
                                {{ $current->seeders }}
                            </span>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('peers', ['id' => $current->id]) }}">
                            <span class='badge-extra text-red text-bold'>
                                {{ $current->leechers }}
                            </span>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('history', ['id' => $current->id]) }}">
                            <span class='badge-extra text-orange text-bold'>
                                {{ $current->times_completed }} @lang('common.times')
                            </span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endfor
                                                        @if($x >= 5)
                                                            <tr id="facetedCell{{ $k }}">
                                                                <td colspan="7" class="text-center">
                                                                    <a href="#/" class="facetedLoader" torrent="{{ $k }}">
                            <span class='btn btn-sm btn-warning'>
                                Load All {{ $x }} Torrents
                            </span>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card_footer">
                            <div style="float: right;">
                                @if($attributes && is_array($attributes))
                                    @php $cats = $repository->categories()->toArray(); @endphp
                                    @php $types = $repository->types()->toArray(); @endphp
                                    @foreach($attributes[$k]['types'] as $a => $attr)
                                        @if(array_key_exists($attr,$types))
                                            <span class="badge-user text-bold text-blue" style="float:right;">{{ $types[$attr] }}</span>
                                        @endif
                                    @endforeach
                                    @foreach($attributes[$k]['categories'] as $a => $attr)
                                        @if(array_key_exists($attr,$cats))
                                            <span class="badge-user text-bold text-blue" style="float:right;">{{ $cats[$attr] }}</span>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
<div style="width: 100% !important; display: table !important;">
    <div class="align-center" style="width: 100% !important; display: table-cell !important;">
        @if($links)
            {{ $links->links() }}
        @endif
    </div>
</div>
