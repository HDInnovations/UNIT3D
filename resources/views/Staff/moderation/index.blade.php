@extends('layout.default')

@section('title')
    <title>Moderation - @lang('staff.staff-dashboard') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('staff.dashboard.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.staff-dashboard')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('staff.moderation.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.torrent-moderation')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container box">
        <div class="table-responsive">
            <table class="table table-condensed table-striped table-bordered">
                <thead>
                <tr>
                    <th>@lang('torrent.uploaded')</th>
                    <th>@lang('common.name')</th>
                    <th>@lang('common.category')</th>
                    <th>@lang('common.type')</th>
                    <th>@lang('common.resolution')</th>
                    <th>@lang('torrent.size')</th>
                    <th>@lang('torrent.uploader')</th>
                    <th>@lang('common.moderation-approve')</th>
                    <th>@lang('common.moderation-postpone')</th>
                    <th>@lang('common.moderation-reject')</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($pending as $p)
                    <tr>
                        <td><span class="text-red text-bold">{{ $p->created_at->diffForHumans() }}</span></td>
                        <td><a href="{{ route('torrent', ['id' => $p->id]) }}" itemprop="url"
                               class="l-breadcrumb-item-link"><span itemprop="title"
                                                                    class="l-breadcrumb-item-link-title">{{ $p->name }}</span></a>
                        </td>
                        <td><i class="{{ $p->category->icon }} torrent-icon" data-toggle="tooltip"
                               data-original-title="{{ $p->category->name }} Torrent"></i></td>
                        <td>{{ $p->type->name }}</td>
                        <td>{{ $p->resolution->name ?? 'No Res' }}</td>
                        <td>{{ $p->getSize() }}</td>
                        <td><a href="{{ route('users.show', ['username' => $p->user->username]) }}"
                               itemprop="url" class="l-breadcrumb-item-link"><span itemprop="title"
                                                                                   class="l-breadcrumb-item-link-title">{{ $p->user->username }}
                                    ({{ $p->user->primaryRole->name }})</span></a></td>
                        <td><a href="{{ route('staff.moderation.approve', ['id' => $p->id]) }}" role='button'
                               class='btn btn-labeled btn-success'>
                                <span class="btn-label">
                                    <i class="{{ config('other.font-awesome') }} fa-thumbs-up"></i>
                                </span>
                                @lang('common.moderation-approve')
                            </a></td>
                        <td>
                            <button data-target="#pendpostpone-{{ $p->id }}" data-toggle="modal" class="btn btn-labeled btn-danger">
                                <span class="btn-label"><i class="{{ config('other.font-awesome') }} fa-pause"></i></span>
                                @lang('common.moderation-postpone')
                            </button>

                            <div class="modal fade" id="pendpostpone-{{ $p->id }}" tabindex="-1" role="dialog"
                                 aria-hidden="true">
                                <form method="POST" action="{{ route('staff.moderation.postpone') }}">
                                    @csrf
                                    <div class="modal-dialog{{ \modal_style() }}">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="@lang('common.close')"><span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">
                                                    @lang('common.moderation-postpone') @lang('torrent.torrent'): {{ $p->name }}
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <input id="type" name="type" type="hidden"
                                                           value="@lang('torrent.torrent')">
                                                    <input id="id" name="id" type="hidden" value="{{ $p->id }}">
                                                    <input id="slug" name="slug" type="hidden" value="{{ $p->slug }}">
                                                    <label for="postpone_reason"
                                                           class="col-sm-2 control-label">@lang('common.reason')</label>
                                                    <div class="col-sm-10">
                                                        <label for="message"></label>
                                                        <textarea title="Postpone message" class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-10 col-sm-offset-2">
                                                        <button class="btn btn-danger" type="submit">
                                                            @lang('common.moderation-postpone')
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-sm btn-primary"
                                                        data-dismiss="modal">@lang('common.close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </td>
                        <td>
                            <button data-target="#pendreject-{{ $p->id }}" data-toggle="modal" class="btn btn-labeled btn-danger">
                                <span class="btn-label">
                                    <i class="{{ config('other.font-awesome') }} fa-thumbs-down"></i>
                                </span>
                                @lang('common.moderation-reject')
                            </button>
                            <div class="modal fade" id="pendreject-{{ $p->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <form method="POST" action="{{ route("staff.moderation.reject") }}">
                                    @csrf
                                    <div class="modal-dialog{{ \modal_style() }}">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="@lang('common.close')"><span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">
                                                    @lang('common.moderation-reject') @lang('torrent.torrent'): {{ $p->name }}
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <input id="type" type="hidden" name="type"
                                                           value="@lang('torrent.torrent')">
                                                    <input id="id" type="hidden" name="id" value="{{ $p->id }}">
                                                    <input id="slug" type="hidden" name="slug" value="{{ $p->slug }}">
                                                    <label for="file_name" class="col-sm-2 control-label">@lang('torrent.torrent')</label>
                                                    <div class="col-sm-10">
                                                        <p id="title" name="title" class="form-control-static">{{ $p->name }}</p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="report_reason"
                                                           class="col-sm-2 control-label">@lang('common.reason')</label>
                                                    <div class="col-sm-10">
                                                        <label for="message"></label>
                                                        <textarea title="Rejection Message" class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-10 col-sm-offset-2">
                                                        <button class="btn btn-danger" type="submit">
                                                            @lang('common.moderation-reject')
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-sm btn-primary"
                                                        data-dismiss="modal">@lang('common.close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        </div>

    <div class="text-center"><h1>@lang('torrent.postponed-torrents')</h1></div>
    <div class="container box">
        <div class="table-responsive">
            <table class="table table-condensed table-striped table-bordered">
                <thead>
                <tr>
                    <th>@lang('staff.moderation-since')</th>
                    <th>@lang('common.name')</th>
                    <th>@lang('common.category')</th>
                    <th>@lang('common.type')</th>
                    <th>@lang('common.resolution')</th>
                    <th>@lang('torrent.size')</th>
                    <th>@lang('torrent.uploader')</th>
                    <th>@lang('common.staff')</th>
                    <th>@lang('common.moderation-approve')</th>
                    <th>@lang('common.edit')</th>
                    <th>@lang('common.delete')</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($postponed as $post)
                    <tr>
                        <td><span class="text-red text-bold">{{ $post->moderated_at->diffForHumans() }}</span></td>
                        <td><a href="{{ route('torrent', ['id' => $post->id]) }}" itemprop="url"
                               class="l-breadcrumb-item-link"><span itemprop="title"
                                                                    class="l-breadcrumb-item-link-title">{{ $post->name }}</span></a>
                        </td>
                        <td><i class="{{ $post->category->icon }} torrent-icon" data-toggle="tooltip"
                               data-original-title="{{ $post->category->name }} Torrent"></i></td>
                        <td>{{ $post->type->name }}</td>
                        <td>{{ $post->resolution->name ?? 'No Res' }}</td>
                        <td>{{ $post->getSize() }}</td>
                        <td>
                            <a href="{{ route('users.show', ['username' => $post->user->username]) }}"
                               itemprop="url" class="l-breadcrumb-item-link"><span itemprop="title"
                                                                                   class="l-breadcrumb-item-link-title">{{ $post->user->username }}
                                    ({{ $post->user->primaryRole->name }})</span></a></td>
                        <td>
                            <a href="{{ route('users.show', ['username' => $post->moderated->username]) }}"
                               itemprop="url" class="l-breadcrumb-item-link"><span itemprop="title"
                                                                                   class="l-breadcrumb-item-link-title">{{ $post->moderated->username }}
                                    ({{ $post->moderated->primaryRole->name }})</span></a></td>
                        <td><a href="{{ route('staff.moderation.approve', ['id' => $post->id]) }}" role='button'
                               class='btn btn-labeled btn-success'>
                                <span class="btn-label">
                                    <i class="{{ config('other.font-awesome') }} fa-thumbs-up"></i>
                                </span>
                                @lang('common.moderation-approve')
                            </a></td>
                        <td><a href="{{ route('edit', ['id' => $post->id]) }}" role='button'
                               class='btn btn-labeled btn-info'><span class="btn-label"><i
                                            class="{{ config('other.font-awesome') }} fa-pencil"></i></span>@lang('common.edit')</a></td>
                        <td>
                            <button data-target="#postdelete-{{ $post->id }}" data-toggle="modal"
                                    class="btn btn-labeled btn-danger"><span class="btn-label"><i
                                            class="{{ config('other.font-awesome') }} fa-thumbs-down"></i></span>@lang('common.delete')
                            </button>

                            <div class="modal fade" id="postdelete-{{ $post->id }}" tabindex="-1" role="dialog"
                                 aria-hidden="true">
                                <form method="POST" action="{{ route('delete') }}">
                                    @csrf
                                    <div class="modal-dialog{{ \modal_style() }}">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="@lang('common.close')"><span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">
                                                    @lang('common.delete') @lang('torrent.torrent'): {{ $post->name }}
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <input id="type" type="hidden" name="type"
                                                           value="@lang('torrent.torrent')">
                                                    <input id="id" type="hidden" name="id" value="{{ $post->id }}">
                                                    <input id="slug" type="hidden" name="slug" value="{{ $post->slug }}">
                                                    <label for="file_name"
                                                           class="col-sm-2 control-label">@lang('torrent.torrent')</label>
                                                    <div class="col-sm-10">
                                                        <p id="title" name="title" class="form-control-static">{{ $post->name }}</p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="report_reason"
                                                           class="col-sm-2 control-label">@lang('common.reason')</label>
                                                    <div class="col-sm-10">
                                                        <label for="message"></label>
                                                        <textarea title="Deletion message" class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-10 col-sm-offset-2">
                                                        <button class="btn btn-danger" type="submit">@lang('common.delete')</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-sm btn-primary"
                                                        data-dismiss="modal">@lang('common.close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        </div>

    <div class="text-center"><h1>@lang('torrent.rejected')</h1></div>
    <div class="container box">
        <div class="table-responsive">
            <table class="table table-condensed table-striped table-bordered">
                <thead>
                <tr>
                    <th>@lang('staff.moderation-since')</th>
                    <th>@lang('common.name')</th>
                    <th>@lang('common.category')</th>
                    <th>@lang('common.type')</th>
                    <th>@lang('common.resolution')</th>
                    <th>@lang('torrent.size')</th>
                    <th>@lang('torrent.uploader')</th>
                    <th>@lang('common.staff')</th>
                    <th>@lang('common.moderation-approve')</th>
                    <th>@lang('common.moderation-postpone')</th>
                    <th>@lang('common.edit')</th>
                    <th>@lang('common.delete')</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($rejected as $reject)
                    <tr>
                        <td><span class="text-red text-red">{{ $reject->moderated_at->diffForHumans() }}</span></td>
                        <td><a href="{{ route('torrent', ['id' => $reject->id]) }}"
                               itemprop="url" class="l-breadcrumb-item-link"><span itemprop="title"
                                                                                   class="l-breadcrumb-item-link-title">{{ $reject->name }}</span></a>
                        </td>
                        <td><i class="{{ $reject->category->icon }} torrent-icon" data-toggle="tooltip"
                               data-original-title="{{ $reject->category->name }} Torrent"></i></td>
                        <td>{{ $reject->type->name }}</td>
                        <td>{{ $reject->resolution->name ?? 'No Res' }}</td>
                        <td>{{ $reject->getSize() }}</td>
                        <td>@if ($reject->user) <a
                                    href="{{ route('users.show', ['username' => $reject->user->username]) }}"
                                    itemprop="url" class="l-breadcrumb-item-link"><span itemprop="title"
                                                                                        class="l-breadcrumb-item-link-title">{{ $reject->user->username }}
                                    ({{ $reject->user->primaryRole->name }})</span></a> @else System @endif </td>
                        <td>
                            <a href="{{ route('users.show', ['username' => $reject->moderated->username]) }}"
                               itemprop="url" class="l-breadcrumb-item-link"><span itemprop="title"
                                                                                   class="l-breadcrumb-item-link-title">{{ $reject->moderated->username }}
                                    ({{ $reject->moderated->primaryRole->name }})</span></a></td>
                        <td><a href="{{ route('staff.moderation.approve', ['id' => $reject->id]) }}" role='button'
                               class='btn btn-labeled btn-success'>
                                <span class="btn-label">
                                    <i class="{{ config('other.font-awesome') }} fa-thumbs-up"></i>
                                </span>
                                @lang('common.moderation-approve')
                            </a></td>
                        <td>
                            <button data-target="#rejectpost-{{ $reject->id }}" data-toggle="modal" class="btn btn-labeled btn-danger">
                                    <span class="btn-label"><i class="{{ config('other.font-awesome') }} fa-pause"></i></span>
                                @lang('common.moderation-postpone')
                            </button>

                            <div class="modal fade" id="rejectpost-{{ $reject->id }}" tabindex="-1" role="dialog"
                                 aria-hidden="true">
                                <form method="POST" action="{{ route('staff.moderation.postpone') }}">
                                    @csrf
                                    <div class="modal-dialog{{ \modal_style() }}">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="@lang('common.close')"><span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">
                                                    @lang('common.moderation-postpone') @lang('torrent.torrent'): {{ $reject->name }}
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <input id="type" name="type" type="hidden"
                                                           value="@lang('torrent.torrent')">
                                                    <input id="id" name="id" type="hidden" value="{{ $reject->id }}">
                                                    <input id="slug" name="slug" type="hidden" value="{{ $reject->slug }}">
                                                    <label for="postpone_reason"
                                                           class="col-sm-2 control-label">@lang('common.reason')</label>
                                                    <div class="col-sm-10">
                                                        <label for="message"></label>
                                                        <textarea title="Postpone message" class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-10 col-sm-offset-2">
                                                        <button class="btn btn-danger" type="submit">
                                                            @lang('common.moderation-postpone')
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-sm btn-primary" type="button"
                                                        data-dismiss="modal">@lang('common.close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </td>
                        <td><a href="{{ route('edit', ['id' => $reject->id]) }}" role='button'
                               class='btn btn-labeled btn-info'><span class="btn-label"><i
                                            class="{{ config('other.font-awesome') }} fa-pencil"></i></span>@lang('common.edit')</a></td>
                        <td>
                            <button data-target="#rejectdelete-{{ $reject->id }}" data-toggle="modal"
                                    class="btn btn-labeled btn-danger"><span class="btn-label"><i
                                            class="{{ config('other.font-awesome') }} fa-thumbs-down"></i></span>@lang('common.delete')
                            </button>

                            <div class="modal fade" id="rejectdelete-{{ $reject->id }}" tabindex="-1" role="dialog"
                                 aria-hidden="true">
                                <form method="POST" action=" {{ route('delete') }}">
                                    @csrf
                                    <div class="modal-dialog{{ \modal_style() }}">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="@lang('common.close')"><span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">
                                                    @lang('common.delete') @lang('torrent.torrent'): {{ $reject->name }}
                                                </h4>
                                            </div>
                                            <div class="modal-body">

                                                <div class="form-group">
                                                    <input id="type" type="hidden" name="type"
                                                           value="@lang('torrent.torrent')">
                                                    <input id="id" type="hidden" name="id" value="{{ $reject->id }}">
                                                    <input id="slug" type="hidden" name="slug" value="{{ $reject->slug }}">
                                                    <label for="file_name"
                                                           class="col-sm-2 control-label">@lang('torrent.torrent')</label>
                                                    <div class="col-sm-10">
                                                        <p id="title" name="title" class="form-control-static">{{ $reject->name }}</p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="report_reason"
                                                           class="col-sm-2 control-label">@lang('common.reason')</label>
                                                    <div class="col-sm-10">
                                                        <label for="message"></label>
                                                        <textarea title="Deletion message" class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-10 col-sm-offset-2">
                                                        <button class="btn btn-danger" type="submit">@lang('common.delete')</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-sm btn-primary" type="button"
                                                        data-dismiss="modal">@lang('common.close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        </div>
@endsection
