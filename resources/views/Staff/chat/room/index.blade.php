@extends('layout.default')

@section('breadcrumb')
    <li>
        <a href="{{ route('staff.dashboard.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.staff-dashboard')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('staff.rooms.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('common.chat-rooms')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container box">
        <h2>@lang('common.chat-rooms')</h2>

        <button class="btn btn-primary" data-toggle="modal" data-target="#addChatroom">
            @lang('common.add') @lang('common.chat-room')
        </button>
        <div id="addChatroom" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog{{ \modal_style() }}">
                <div class="modal-content">

                    <div class="modal-header" style="text-align: center;">
                        <h3>@lang('common.add') @lang('common.chat-room')</h3>
                    </div>

                    <form class="form-horizontal" role="form" method="POST" action="{{ route('staff.rooms.store') }}">
                        @csrf
                        <div class="modal-body" style="text-align: center;">
                            <h4>Please enter the name of the chatroom you would like to create.</h4>
                            <label for="chatroom_name"> @lang('common.name'):</label> <label for="name"></label><input
                                style="margin:0 auto; width:300px;" type="text" class="form-control" name="name" id="name"
                                placeholder="Enter @lang('common.name') Here..." required>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-md btn-primary" data-dismiss="modal">@lang('common.cancel')</button>
                            <input class="btn btn-md btn-success" type="submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-condensed table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>@lang('common.name')</th>
                        <th>@lang('common.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chatrooms as $chatroom)
                        <tr>
                            <td>
                                {{ $chatroom->id }}
                            </td>
                            <td>
                                <a href="#">
                                    {{ $chatroom->name }}
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-xs btn-warning" data-toggle="modal"
                                    data-target="#editChatroom-{{ $chatroom->id }}">
                                    <i class="{{ config('other.font-awesome') }} fa-pen-square"></i>
                                </button>
                                <button class="btn btn-xs btn-danger" data-toggle="modal"
                                    data-target="#deleteChatroom-{{ $chatroom->id }}">
                                    <i class="{{ config('other.font-awesome') }} fa-trash"></i>
                                </button>
                                @include('Staff.chat.room.chatroom_modals', ['chatroom' => $chatroom])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
