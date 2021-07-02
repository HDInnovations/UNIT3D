@extends('layout.default')

@section('title')
    <title>{{ $torrent->name }} - @lang('torrent.torrents') - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="@lang('torrent.meta-desc', ['name' => $torrent->name])!">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('torrents') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('torrent.torrents')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('torrent', ['id' => $torrent->id]) }}" itemprop="url"
           class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $torrent->name }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="meta-wrapper box container" id="meta-info">
        @if ($torrent->category->movie_meta)
            @include('torrent.partials.movie_meta')
        @endif

        @if ($torrent->category->tv_meta)
            @include('torrent.partials.tv_meta')
        @endif

        @if ($torrent->category->game_meta)
            @include('torrent.partials.game_meta')
        @endif
        
        @if ($torrent->category->no_meta)
            @include('torrent.partials.no_meta')
        @endif

        <div id="vue" class="torrent-buttons">
            <div class="button-overlay"></div>
            <div class="vibrant-overlay"></div>
            <div class="button-block">
                @if (file_exists(public_path().'/files/torrents/'.$torrent->file_name))
                @if (config('torrent.download_check_page') == 1)
                    <a href="{{ route('download_check', ['id' => $torrent->id]) }}" role="button" class="down btn btn-sm btn-success">
                        <i class='{{ config("other.font-awesome") }} fa-download'></i> @lang('common.download')
                    </a>
                @else
                    <a href="{{ route('download', ['id' => $torrent->id]) }}" role="button" class="down btn btn-sm btn-success">
                        <i class='{{ config("other.font-awesome") }} fa-download'></i> @lang('common.download')
                    </a>
                @endif
                @else
                    <a href="magnet:?dn={{ $torrent->name }}&xt=urn:btih:{{ $torrent->info_hash }}&as={{ route('torrent.download.rsskey', ['id' => $torrent->id, 'rsskey' => $user->rsskey ]) }}&tr={{ route('announce', ['passkey' => $user->passkey]) }}&xl={{ $torrent->size }}" role="button" class="down btn btn-sm btn-success">
                        <i class='{{ config("other.font-awesome") }} fa-magnet'></i> @lang('common.magnet')
                    </a>
                @endif

                @livewire('thank-button', ['torrent' => $torrent->id])

                @if ($torrent->tmdb != 0 && $torrent->category->no_meta == 0)
                    <a href="{{ route('torrents.similar', ['category_id' => $torrent->category_id, 'tmdb' => $torrent->tmdb]) }}" role="button" class="btn btn-sm btn-primary">
                        <i class='{{ config("other.font-awesome") }} fa-clone'></i> Similar
                    </a>
                @endif

                @if ($torrent->nfo != null)
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-10">
                        <i class='{{ config("other.font-awesome") }} fa-info-circle'></i> NFO
                    </button>
                @endif

                <a href="{{ route('comment_thanks', ['id' => $torrent->id]) }}" role="button" class="btn btn-sm btn-primary">
                    <i class='{{ config("other.font-awesome") }} fa-heart'></i> @lang('torrent.quick-comment')
                </a>

                <a data-toggle="modal" href="#myModal" role="button" class="btn btn-sm btn-primary">
                    <i class='{{ config("other.font-awesome") }} fa-file'></i>  @lang('torrent.show-files')
                </a>

                @livewire('bookmark-button', ['torrent' => $torrent->id])

                @if ($playlists->count() > 0)
                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal_playlist_torrent">
                    <i class="{{ config('other.font-awesome') }} fa-list-ol"></i> @lang('torrent.add-to-playlist')
                </button>
                @endif

                @if ($torrent->seeders <= 2)
                <a href="{{ route('reseed', ['id' => $torrent->id]) }}" role="button" class="btn btn-sm btn-warning">
                    <i class='{{ config("other.font-awesome") }} fa-envelope'></i> @lang('torrent.request-reseed')
                </a>
                @endif

                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal_torrent_report">
                    <i class="{{ config('other.font-awesome') }} fa-fw fa-eye"></i> @lang('common.report')
                </button>

                <a role="button" class="btn btn-sm btn-primary" href="{{ route('upload_form', ['category_id' => $torrent->category_id, 'title' => \rawurlencode($torrent->name) ?? 'Unknown', 'imdb' => $torrent->imdb, 'tmdb' => $torrent->tmdb]) }}">
                    <i class="{{ config('other.font-awesome') }} fa-upload"></i> @lang('common.upload')
                </a>
            </div>
        </div>
    </div>

    <div class="meta-general box container">
        <div class="panel panel-chat shoutbox">
            <div class="panel-heading">
                <h4><i class="{{ config("other.font-awesome") }} fa-info"></i> @lang('torrent.general')</h4>
            </div>
            <div class="table-responsive">
                <table class="table table-condensed table-bordered table-striped">
                    <tbody>

                    @if ($torrent->featured == '0')
                        <tr class="success">
                            <td>
                                <strong>@lang('torrent.discounts')</strong>
                            </td>
                            <td>
                                @if ($torrent->doubleup == '1' || $torrent->free == '1' || config('other.freeleech') == '1' || config('other.doubleup') == '1' || $personal_freeleech || $user->group->is_freeleech == '1' || $freeleech_token)
                                    @if ($freeleech_token)
                                        <span class="badge-extra text-bold">
                                            <i class="{{ config('other.font-awesome') }} fa-coins text-bold"></i> @lang('common.fl_token')
                                        </span>
                                    @endif

                                    @if ($user->group->is_freeleech == '1')
                                        <span class="badge-extra text-bold">
                                            <i class="{{ config('other.font-awesome') }} fa-trophy text-purple"></i> @lang('common.special') @lang('torrent.freeleech')
                                        </span>
                                    @endif

                                    @if ($personal_freeleech)
                                        <span class="badge-extra text-bold">
                                            <i class="{{ config('other.font-awesome') }} fa-id-badge text-orange"></i> @lang('common.personal') @lang('torrent.freeleech')
                                        </span>
                                    @endif

                                    @if ($torrent->doubleup == '1')
                                        <span class="badge-extra text-bold">
                                            <i class="{{ config('other.font-awesome') }} fa-gem text-green"></i> @lang('torrent.double-upload')
                                        </span>
                                    @endif

                                    @if ($user->group->is_double_upload == '1')
                                        <span class="badge-extra text-bold">
                                            <i class="{{ config('other.font-awesome') }} fa-trophy text-purple"></i> @lang('common.special') @lang('torrent.double-upload')
                                        </span>
                                    @endif

                                    @if ($torrent->free == '1')
                                        <span class="badge-extra text-bold">
                                            <i class="{{ config('other.font-awesome') }} fa-star text-gold"></i> 100% @lang('common.free')
                                        </span>
                                    @endif

                                    @if (config('other.freeleech') == '1')
                                        <span class="badge-extra text-bold">
                                            <i class="{{ config('other.font-awesome') }} fa-globe text-blue"></i> @lang('common.global') @lang('torrent.freeleech')
                                        </span>
                                    @endif

                                    @if (config('other.doubleup') == '1')
                                        <span class="badge-extra text-bold">
                                            <i class="{{ config('other.font-awesome') }} fa-globe text-green"></i> @lang('common.global') {{ strtolower(trans('torrent.double-upload')) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-bold text-danger">
                                        <i class="{{ config('other.font-awesome') }} fa-frown"></i> @lang('torrent.no-discounts')
                                    </span>
                                @endif
                            </td>
                        </tr>

                        @if ($torrent->free == "0" && config('other.freeleech') == false && !$personal_freeleech && $user->group->is_freeleech == 0 && !$freeleech_token)
                            <tr>
                                <td><strong>@lang('common.fl_token')</strong></td>
                                <td>
                                    <a href="{{ route('freeleech_token', ['id' => $torrent->id]) }}"
                                       class="btn btn-default btn-xs"
                                       role="button">@lang('torrent.use-fl-token')
                                    </a>
                                    <span class="small">
                                    <em>{!! trans('torrent.fl-tokens-left', ['tokens' => $user->fl_tokens]) !!}
                                        !</strong>
                                    </em>
                                </span>
                                </td>
                            </tr>
                        @endif
                    @endif

                    @if ($torrent->featured == 1)
                        <tr class="info">
                            <td>
                                <strong>@lang('torrent.featured')</strong>
                            </td>
                            <td>
                                <span class="badge-user text-bold text-pink" style="background-image:url(https://i.imgur.com/F0UCb7A.gif);">
                                    @lang('torrent.featured-until') {{ $featured->created_at->addDay(7)->toFormattedDateString() }} ({{ $featured->created_at->addDay(7)->diffForHumans() }}!)
                                </span>
                                <span class="small">
                                    <em>{!! trans('torrent.featured-desc') !!}</em>
                                </span>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td class="col-sm-2">
                            <strong>@lang('torrent.name')</strong>
                        </td>
                        <td>{{ $torrent->name }} &nbsp; &nbsp;
                            @if (auth()->user()->group->is_modo || auth()->user()->id === $uploader->id)
                                <a class="btn btn-warning btn-xs" href="{{ route('edit_form', ['id' => $torrent->id]) }}" role="button">
                                    <i class="{{ config('other.font-awesome') }} fa-pencil-alt"></i> @lang('common.edit')
                                </a>
                            @endif
                            @if (auth()->user()->group->is_modo || ( auth()->user()->id === $uploader->id && Carbon\Carbon::now()->lt($torrent->created_at->addDay())))
                                <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal_torrent_delete">
                                    <i class="{{ config('other.font-awesome') }} fa-times"></i> @lang('common.delete')
                                </button>
                            @endif
                        </td>
                    </tr>

                    @if (auth()->user()->group->is_modo)
                        <tr>
                            <td class="col-sm-2">
                                <strong>@lang('common.moderation')</strong>
                            </td>
                            <td>
                                <a href="{{ route('staff.moderation.approve', ['id' => $torrent->id]) }}" role='button'
                                   class='btn btn-labeled btn-success btn-xs @if ($torrent->isApproved()) disabled @endif'>
                                    <i class="{{ config('other.font-awesome') }} fa-thumbs-up"></i> @lang('common.moderation-approve')
                                </a>

                                <button data-target="#postpone-{{ $torrent->id }}" data-toggle="modal"
                                        class="btn btn-labeled btn-warning btn-xs @if ($torrent->isPostponed()) disabled @endif">
                                    <i class="{{ config('other.font-awesome') }} fa-pause"></i> @lang('common.moderation-postpone')
                                </button>

                                <button data-target="#reject-{{ $torrent->id }}" data-toggle="modal"
                                        class="btn btn-labeled btn-danger btn-xs @if ($torrent->isRejected()) disabled @endif">
                                    <i class="{{ config('other.font-awesome') }} fa-thumbs-down"></i> @lang('common.moderation-reject')
                                </button>

                                <span>
                                    &nbsp;[ @lang('common.moderated-by')
                                    <a href="{{ route('users.show', ['username' => $torrent->moderated->username]) }}"
                                       style="color:{{ $torrent->moderated->group->color }};">
                                        <i class="{{ $torrent->moderated->group->icon }}" data-toggle="tooltip"
                                           data-original-title="{{ $torrent->moderated->group->name }}"></i> {{ $torrent->moderated->username }}
                                    </a>]
                                </span>
                            </td>
                        </tr>
                    @endif


                    @if (auth()->user()->group->is_modo || auth()->user()->group->is_internal)
                        <tr>
                            <td class="col-sm-2"><strong>@lang('common.staff-tools')</strong></td>
                            <td>
                                @if ($torrent->free == 0)
                                    <a href="{{ route('torrent_fl', ['id' => $torrent->id]) }}"
                                       class="btn btn-success btn-xs" role="button">
                                        <i class="{{ config('other.font-awesome') }} fa-star"></i> @lang('torrent.grant') @lang('torrent.freeleech')
                                    </a>
                                @else
                                    <a href="{{ route('torrent_fl', ['id' => $torrent->id]) }}"
                                       class="btn btn-danger btn-xs" role="button">
                                        <i class="{{ config('other.font-awesome') }} fa-star"></i> @lang('torrent.revoke') @lang('torrent.freeleech')
                                    </a>
                                @endif

                                @if ($torrent->doubleup == 0)
                                    <a href="{{ route('torrent_doubleup', ['id' => $torrent->id]) }}"
                                       class="btn btn-success btn-xs" role="button">
                                        <i class="{{ config('other.font-awesome') }} fa-chevron-double-up"></i> @lang('torrent.grant') @lang('torrent.double-upload')
                                    </a>
                                @else
                                    <a href="{{ route('torrent_doubleup', ['id' => $torrent->id]) }}"
                                       class="btn btn-danger btn-xs" role="button">
                                        <i class="{{ config('other.font-awesome') }} fa-chevron-double-up"></i> @lang('torrent.revoke') @lang('torrent.double-upload')
                                    </a>
                                @endif

                                @if ($torrent->sticky == 0)
                                    <a href="{{ route('torrent_sticky', ['id' => $torrent->id]) }}"
                                       class="btn btn-success btn-xs" role="button">
                                        <i class="{{ config('other.font-awesome') }} fa-thumbtack"></i> @lang('torrent.sticky')
                                    </a>
                                @else
                                    <a href="{{ route('torrent_sticky', ['id' => $torrent->id]) }}"
                                       class="btn btn-danger btn-xs" role="button">
                                        <i class="{{ config('other.font-awesome') }} fa-thumbtack"></i> @lang('torrent.unsticky')
                                    </a>
                                @endif

                                <a href="{{ route('bumpTorrent', ['id' => $torrent->id]) }}"
                                   class="btn btn-primary btn-xs" role="button">
                                    <i class="{{ config('other.font-awesome') }} fa-arrow-to-top"></i> @lang('torrent.bump')
                                </a>

                                @if ($torrent->featured == 0)
                                    <a href="{{ route('torrent_feature', ['id' => $torrent->id]) }}"
                                       class="btn btn-default btn-xs" role="button">
                                        <i class="{{ config('other.font-awesome') }} fa-certificate"></i> @lang('torrent.feature')
                                    </a>
                                @else
                                    <a href="{{ route('torrent_revokefeature', ['id' => $torrent->id]) }}"
                                       class="btn btn-danger btn-xs" role="button">
                                        <i class="{{ config('other.font-awesome') }} fa-certificate"></i> @lang('torrent.revokefeatured')
                                    </a>
                                @endif

                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td class="col-sm-2"><strong>@lang('torrent.uploader')</strong></td>
                        <td>
                            @if ($torrent->anon == 1)
                                <span class="badge-user text-orange text-bold">{{ strtoupper(trans('common.anonymous')) }}
                                    @if (auth()->user()->id == $uploader->id || auth()->user()->group->is_modo)
                                        <a href="{{ route('users.show', ['username' => $uploader->username]) }}">
                                            ({{ $uploader->username }}
                                        )</a>
                                    @endif
                                </span>
                            @else
                                <a href="{{ route('users.show', ['username' => $uploader->username]) }}">
                                    <span class="badge-user text-bold" style="color:{{ $uploader->group->color }}; background-image:{{ $uploader->group->effect }};">
                                        <i class="{{ $uploader->group->icon }}" data-toggle="tooltip" data-original-title="{{ $uploader->group->name }}"></i> {{ $uploader->username }}
                                    </span>
                                </a>
                            @endif

                            @if ($torrent->anon !== 1 && $uploader->private_profile !== 1)
                                @if (auth()->user()->isFollowing($uploader->id))
                                    <form class="form-inline" role="form" action="{{ route('follow.destroy', ['username' => $uploader->username]) }}"
                                          style="display: inline-block;" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="form-group">
                                        <button type="submit" id="delete-follow-{{ $uploader->target_id }}" class="btn btn-xs btn-info"
                                                title="@lang('user.unfollow')">
                                            <i class="{{ config('other.font-awesome') }} fa-user"></i> @lang('user.unfollow')
                                        </button>
                                        </div>
                                    </form>
                                @else
                                    <form class="form-inline" role="form" action="{{ route('follow.store', ['username' => $uploader->username]) }}"
                                          style="display: inline-block;" method="POST">
                                        @csrf
                                        <div class="form-group">
                                        <button type="submit" id="follow-user-{{ $uploader->id }}" class="btn btn-xs btn-success"
                                                title="@lang('user.follow')">
                                            <i class="{{ config('other.font-awesome') }} fa-user"></i> @lang('user.follow')
                                        </button>
                                        </div>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td class="col-sm-2"><strong>@lang('torrent.uploaded')</strong></td>
                        <td>{{ $torrent->created_at }} ({{ $torrent->created_at->diffForHumans() }})</td>
                    </tr>

                    <tr>
                        <td class="col-sm-2"><strong>@lang('torrent.size')</strong></td>
                        <td>{{ $torrent->getSize() }}</td>
                    </tr>

                    <tr>
                        <td class="col-sm-2"><strong>@lang('torrent.estimated-ratio')</strong></td>
                        <td>{{ $user->ratioAfterSizeString($torrent->size, $torrent->isFreeleech(auth()->user())) }}</td>
                    </tr>

                    <tr>
                        <td class="col-sm-2"><strong>@lang('torrent.category')</strong></td>
                        <td><i class="{{ $torrent->category->icon }} torrent-icon torrent-icon-small"
                               data-toggle="tooltip" data-original-title="{{ $torrent->category->name }} @lang('torrent.torrent')"></i>
                            {{ $torrent->category->name }}
                        </td>
                    </tr>

                    <tr>
                        <td class="col-sm-2"><strong>@lang('torrent.type')</strong></td>
                        <td>{{ $torrent->type->name }}</td>
                    </tr>

                    <tr>
                        <td class="col-sm-2"><strong>@lang('torrent.resolution')</strong></td>
                        <td>{{ $torrent->resolution->name ?? 'No Res' }}</td>
                    </tr>

                    @if ($torrent->keywords->isNotEmpty())
                        <tr>
                            <td class="col-sm-2"><strong>@lang('torrent.keywords')</strong></td>
                            <td>
                                @foreach($torrent->keywords as $keyword)
                                    <span class="badge-user text-bold">{{ $keyword->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td class="col-sm-2"><strong>@lang('torrent.stream-optimized')?</strong></td>
                        <td>
                            @if ($torrent->stream == "1") @lang('common.yes') @else @lang('common.no') @endif
                        </td>
                    </tr>

                    <tr>
                        <td class="col-sm-2"><strong>@lang('torrent.info-hash')</strong></td>
                        <td>{{ $torrent->info_hash }}</td>
                    </tr>

                    <tr>
                        <td class="col-sm-2"><strong>@lang('torrent.peers')</strong></td>
                        <td>
                            <span class="badge-extra text-green">
                                <i class="{{ config('other.font-awesome') }} fa-fw fa-arrow-up"></i> {{ $torrent->seeders }}
                            </span>
                            <span class="badge-extra text-red">
                                <i class="{{ config('other.font-awesome') }} fa-fw fa-arrow-down"></i> {{ $torrent->leechers }}
                            </span>
                            <span class="badge-extra text-info">
                                <i class="{{ config('other.font-awesome') }} fa-fw fa-check"></i>{{ $torrent->times_completed }} {{ strtolower(trans('common.times')) }}
                            </span>
                            <span class="badge-extra">
                                <a href="{{ route('peers', ['id' => $torrent->id]) }}"
                                   title="@lang('common.view') @lang('torrent.peers')">@lang('common.view') @lang('torrent.peers')
                                </a>
                            </span>
                            <span class="badge-extra">
                                <a href="{{ route('history', ['id' => $torrent->id]) }}"
                                   title="@lang('common.view') @lang('torrent.history')">@lang('common.view') @lang('torrent.history')
                                </a>
                            </span>
                        </td>
                    </tr>

                    @if ($torrent->seeders == 0)
                        <tr>
                            <td class="col-sm-2"><strong>@lang('torrent.last-seed-activity')</strong></td>
                            <td>
                                @if ($last_seed_activity)
                                    <span class="badge-extra text-orange">
                                        <i class="{{ config('other.font-awesome') }} fa-fw fa-clock"></i> {{ $last_seed_activity->updated_at->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="badge-extra text-orange">
                                        <i class="{{ config('other.font-awesome') }} fa-fw fa-clock"></i> @lang('common.unknown')
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

	    @if (auth()->user()->group->is_modo)
		    @include('torrent.partials.audits')
	    @endif

	    <div class="panel panel-chat shoutbox">
		    <div class="panel-heading">
			    <h4><i class="{{ config("other.font-awesome") }} fa-sticky-note"></i> @lang('common.description')</h4>
		    </div>
		    <div class="table-responsive">
			    <table class="table table-condensed table-bordered table-striped">
				    <tbody>
				    <tr>
					    <td>
						    <div class="panel-body">
							    @joypixels($torrent->getDescriptionHtml())

							    @if (! empty($meta->collection['0']) && $torrent->category->movie_meta)
								    <hr>
								    <div id="collection_waypoint" class="collection">
									    <div class="header collection"
									         @php $backdrop = $meta->collection['0']->backdrop; @endphp
									         style=" background-image: url({{ isset($backdrop) ? \tmdb_image('back_big', $backdrop) : 'https://via.placeholder.com/1280x300' }}); background-size: cover; background-position: 50% 50%;">
										    <div class="collection-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: linear-gradient(rgba(0, 0, 0, 0.87), rgba(45, 71, 131, 0.46));"></div>
										    <section class="collection">
											    <h2>Part of the {{ $meta->collection['0']->name }}</h2>
											    <p class="text-blue">Includes:
												    @foreach($meta->collection['0']->movie as $collection_movie)
													    {{ $collection_movie->title }},
												    @endforeach
											    </p>

											    <a href="{{ route('mediahub.collections.show', ['id' => $meta->collection['0']->id]) }}"
											       role="button" class="btn btn-labeled btn-primary"
											       style=" margin: 0; text-transform: uppercase; position: absolute; bottom: 50px;">
												<span class="btn-label">
													<i class="{{ config("other.font-awesome") }} fa-copy"></i> View The Collection
												</span>
											    </a>
										    </section>
									    </div>
								    </div>
							    @endif
						    </div>
					    </td>
				    </tr>
				    </tbody>
			    </table>
		    </div>
	    </div>

	@if ($torrent->mediainfo != null)
		<div class="panel panel-chat shoutbox">
			<div class="panel-heading">
				<h4><i class="{{ config("other.font-awesome") }} fa-info-square"></i> Media Info</h4>
			</div>
			<div class="table-responsive">
				<table class="table table-condensed table-bordered table-striped">
					<tbody>
					<tr>
						<td>
							<div class="panel-body">
								<div class="text-center">
									<span class="text-bold text-blue">
										@joypixels(':blue_heart:') @lang('torrent.media-info') @joypixels(':blue_heart:')
									</span>
								</div>
								<br>
								@if ($general !== null && isset($general['file_name']))
									<span class="text-bold text-blue">
										@joypixels(':file_folder:') {{ strtoupper(trans('torrent.file')) }}:
									</span>
									<span class="text-bold">
										<em>{{ $general['file_name'] }}</em>
									</span>
									<br>
									<br>
								@endif
								@if ($general_crumbs !== null)
									<span class="text-bold text-blue">@joypixels(':information_source:') {{ strtoupper(trans('torrent.general')) }}
									:</span>
									<span class="text-bold"><em>
				  @foreach ($general_crumbs as $crumb)
												{{ $crumb }}
												@if (!$loop->last)
													/
												@endif
											@endforeach
				</em></span>
									<br>
									<br>
								@endif
								@if ($video_crumbs !== null)
									@foreach ($video_crumbs as $key => $v)
										<span class="text-bold text-blue">@joypixels(':projector:') {{ strtoupper(trans('torrent.video')) }}
										:</span>
										<span class="text-bold"><em>
					@foreach ($v as $crumb)
													{{ $crumb }}
													@if (!$loop->last)
														/
													@endif
												@endforeach
				  </em></span>
										<br>
										<br>
									@endforeach
								@endif
								@if ($audio_crumbs !== null)
									@foreach ($audio_crumbs as $key => $a)
										<span class="text-bold text-blue">@joypixels(':loud_sound:') {{ strtoupper(trans('torrent.audio')) }} {{ ++$key }}
										:</span>
										<span class="text-bold"><em>
				  @foreach ($a as $crumb)
													{{ $crumb }}
													@if (!$loop->last)
														/
													@endif
												@endforeach
				</em></span>
										<br>
									@endforeach
								@endif
								<br>
								@if ($text_crumbs !== null)
									@foreach ($text_crumbs as $key => $s)
										<span class="text-bold text-blue">@joypixels(':speech_balloon:') {{ strtoupper(trans('torrent.subtitle')) }} {{ ++$key }}
										:</span>
										<span class="text-bold"><em>
				  @foreach ($s as $crumb)
													{{ $crumb }}
													@if (!$loop->last)
														/
													@endif
												@endforeach
				</em></span>
										<br>
									@endforeach
								@endif
								@if ($settings)
									<br>
									<span class="text-bold text-blue">@joypixels(':gear:') {{ strtoupper(trans('torrent.encode-settings')) }}
									:</span>
									<br>
									<div class="decoda-code text-black">{{ $settings }}</div>
								@endif
								<br>
								<br>
								<div class="text-center">
									<button class="show_hide btn btn-labeled btn-primary" href="#">
										{{ strtoupper(trans('torrent.original-output')) }}
									</button>
								</div>
								<div class="slidingDiv">
									<pre class="decoda-code"><code>{{ $torrent->mediainfo }}</code></pre>
								</div>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	@endif

        @if ($torrent->bdinfo != null)
            <div class="panel panel-chat shoutbox">
                <div class="panel-heading">
                    <h4><i class="{{ config("other.font-awesome") }} fa-compact-disc"></i> BDInfo Info</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed table-bordered table-striped">
                        <tbody>
                        <tr>
                            <td>
                                <div class="panel-body">
                                    <pre class="decoda-code"><code>{{ $torrent->bdinfo }}</code></pre>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

	{{-- Subtitles Block --}}
	@if($torrent->category->movie_meta || $torrent->category->tv_meta)
		@include('torrent.partials.subtitles')
	@endif

	<div class="panel panel-chat shoutbox">
		<div class="panel-heading">
			<h4><i class="{{ config("other.font-awesome") }} fa-coins"></i> @lang('torrent.tip-jar')</h4>
		</div>
		<div class="table-responsive">
			<table class="table table-condensed table-bordered table-striped">
				<tbody>
				<tr>
					<td>
						<div class="col-md-7">
							<form role="form" method="POST"
								  action="{{ route('tip_uploader', ['id' => $torrent->id]) }}"
								  class="form-inline">
								@csrf
								<div class="form-group">
									<span class="text-green text-bold">@lang('torrent.define-tip-amount')</span>
									<label>
										<input type="number" name="tip" value="0" placeholder="0" class="form-control"
											   style="width: 80%;">
									</label>
									<button type="submit"
											class="btn btn-primary">@lang('torrent.leave-tip')</button>
								</div>
								<br>
								<span class="text-green text-bold">@lang('torrent.quick-tip')</span>
								<br>
								<button type="submit" value="1000" name="tip" class="btn">1,000</button>
								<button type="submit" value="2000" name="tip" class="btn">2,000</button>
								<button type="submit" value="5000" name="tip" class="btn">5,000</button>
								<button type="submit" value="10000" name="tip" class="btn">10,000</button>
								<button type="submit" value="20000" name="tip" class="btn">20,000</button>
								<button type="submit" value="50000" name="tip" class="btn">50,000</button>
								<button type="submit" value="100000" name="tip" class="btn">100,000</button>
							</form>
						</div>
						<div class="col-md-5">
							<div class="well" style="box-shadow: none !important;">
								<h4>{!! trans('torrent.torrent-tips', ['total' => $total_tips, 'user' => $user_tips]) !!}
									.</h4>
								<span class="text-red text-bold">(@lang('torrent.torrent-tips-desc'))</span>
							</div>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="torrent box container" id="comments">
	<div class="clearfix"></div>
	<div class="row ">
		<div class="col-md-12 col-sm-12">
			<div class="panel panel-chat shoutbox">
				<div class="panel-heading">
					<h4>
						<i class="{{ config('other.font-awesome') }} fa-comment"></i> @lang('common.comments')
					</h4>
				</div>
				<div class="panel-body no-padding">
					<ul class="media-list comments-list">
						@if (count($comments) == 0)
							<div class="text-center"><h4 class="text-bold text-danger"><i
											class="{{ config('other.font-awesome') }} fa-frown"></i> @lang('common.no-comments')
									!</h4>
							</div>
						@else
							@foreach ($comments as $comment)
								<li class="media" style="border-left: 5px solid rgb(1,188,140);">
									<div class="media-body">
										@if ($comment->anon == 1)
											<a href="#" class="pull-left" style="padding-right: 10px;">
												<img src="{{ url('img/profile.png') }}" class="img-avatar-48">
												<strong>{{ strtoupper(trans('common.anonymous')) }}</strong></a> @if (auth()->user()->id == $comment->user->id || auth()->user()->group->is_modo)
												<a href="{{ route('users.show', ['username' => $comment->user->username]) }}"
												   style="color:{{ $comment->user->group->color }};">(<span><i
																class="{{ $comment->user->group->icon }}"></i> {{ $comment->user->username }}</span>)</a> @endif
										@else
											<a href="{{ route('users.show', ['username' => $comment->user->username]) }}"
											   class="pull-left" style="padding-right: 10px;">
												@if ($comment->user->image != null)
													<img src="{{ url('files/img/' . $comment->user->image) }}"
														 alt="{{ $comment->user->username }}" class="img-avatar-48"></a>
											@else
												<img src="{{ url('img/profile.png') }}"
													 alt="{{ $comment->user->username }}" class="img-avatar-48"></a>
											@endif
											<strong><a
														href="{{ route('users.show', ['username' => $comment->user->username]) }}"
														style="color:{{ $comment->user->group->color }};"><span><i
																class="{{ $comment->user->group->icon }}"></i> {{ $comment->user->username }}</span></a></strong> @endif
										<span class="text-muted"><small><em>{{ $comment->created_at->toDayDateTimeString() }} ({{ $comment->created_at->diffForHumans() }})</em></small></span>
										@if ($comment->user_id == auth()->id() || auth()->user()->group->is_modo)
											<a title="@lang('common.delete-comment')"
											   href="{{route('comment_delete',['comment_id'=>$comment->id])}}"><i
														class="pull-right {{ config('other.font-awesome') }} fa fa-times"
														aria-hidden="true"></i></a>
											<a title="@lang('common.edit-comment')" data-toggle="modal"
											   data-target="#modal-comment-edit-{{ $comment->id }}"><i
														class="pull-right {{ config('other.font-awesome') }} fa-pencil"
														aria-hidden="true"></i></a>
										@endif
										<div class="pt-5">
											@joypixels($comment->getContentHtml())
										</div>
									</div>
								</li>
								@include('partials.modals', ['comment' => $comment])
							@endforeach
						@endif
					</ul>
				</div>
			</div>
		</div>


		<div class="clearfix"></div>
		<div class="col-md-12 home-pagination">
			<div class="text-center">{{ $comments->links() }}</div>
		</div>
		<br>

		<div class="col-md-12">
			<form role="form" method="POST"
				  action="{{ route('comment_torrent', ['id' => $torrent->id]) }}">
				@csrf
				<div class="form-group">
					<label for="content">@lang('common.your-comment'):</label><span class="badge-extra">@lang('common.type-verb')
					<strong>":"</strong> @lang('common.for') emoji</span> <span
							class="badge-extra">BBCode @lang('common.is-allowed')</span>
					<textarea id="content" name="content" cols="30" rows="5" class="form-control"></textarea>
				</div>
				<button type="submit" class="btn btn-danger">@lang('common.submit')</button>
				<label class="radio-inline"><strong>@lang('common.anonymous') @lang('common.comment')
						:</strong></label>
				<label>
					<input type="radio" value="1" name="anonymous">
				</label> @lang('common.yes')
				<label>
					<input type="radio" value="0" checked="checked" name="anonymous">
				</label> @lang('common.no')
			</form>
		</div>
	</div>
</div>
@include('torrent.torrent_modals', ['user' => $user, 'torrent' => $torrent])
@endsection

@section('javascripts')
<script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce() }}">
  $(document).ready(function () {
	$('#content').wysibb({});
  })
</script>

<script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce() }}">
  $(document).ready(function () {

	$('.slidingDiv').hide();
	$('.show_hide').show();

	$('.show_hide').click(function () {
	  $('.slidingDiv').slideToggle()
	})

  })
</script>
@endsection