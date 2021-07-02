<div class="modal fade" id="modal_user_gift" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog{{ \modal_style() }} modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mx-auto">
                <div class="text-center">
                    <p style="font-size: 27px;">@lang('bon.gift-to'): {{ $user->username }}</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="py-3">
                    <form role="form" method="POST" action="{{ route('bonus_send_gift') }}">
                        @csrf
                        <input type="hidden" name="dest" value="profile" />
                        <input type="hidden" name="to_username" value="{{ $user->username }}" />
                        <div class="form-group">
                            <label for="bonus_points">@lang('bon.amount')</label>
                        </div>
                        <div class="form-group">
                            <input class="form-control"
                                placeholder="@lang('common.enter') {{ strtolower(trans('common.amount')) }}"
                                name="bonus_points" type="number" id="bonus_points" required>
                        </div>
                        <div class="form-group">
                            <label for="bonus_message">@lang('pm.message')</label>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="bonus_message" cols="50" rows="10" id="bonus_message"></textarea>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-small btn-primary" type="submit" value="@lang('bon.gift')">
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_user_note" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog{{ \modal_style() }} modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mx-auto">
                <div class="text-center">
                    <p style="font-size: 27px;">Note User: {{ $user->username }}</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="py-3">
                    <form role="form" method="POST"
                        action="{{ route('staff.notes.store', ['username' => $user->username]) }}">
                        @csrf
                        <div class="form-group">
                            <label for="report_reason">Note</label>
                        </div>
                        <div class="form-group">
                            <label>
                                <textarea name="message" class="form-control"></textarea>
                            </label>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-warning" type="submit" value="Save">
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_user_ban" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog{{ \modal_style() }} modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mx-auto">
                <div class="text-center">
                    <p style="font-size: 27px;">Ban User: {{ $user->username }}</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="py-3">
                    <form role="form" method="POST"
                        action="{{ route('staff.bans.store', ['username' => $user->username]) }}">
                        @csrf
                        <div class="form-group">
                            <label for="report_reason">Reason</label>
                        </div>
                        <div class="form-group">
                            <label for="ban_reason"></label>
                            <textarea class="form-control" rows="5" name="ban_reason" cols="50" id="ban_reason"></textarea>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-danger" type="submit" value="Ban">
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_user_unban" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog{{ \modal_style() }} modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mx-auto">
                <div class="text-center">
                    <p style="font-size: 27px;">Unban User: {{ $user->username }}</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="py-3">
                    <form role="form" method="POST"
                        action="{{ route('staff.bans.update', ['username' => $user->username]) }}">
                        @csrf
                        <div class="form-group">
                            <label for="report_reason">UnBan Reason</label>
                        </div>
                        <div class="form-group">
                            <label for="unban_reason"></label>
                            <textarea class="form-control" rows="5" name="unban_reason" cols="50" id="unban_reason"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="report_reason">New Group</label>
                        </div>
                        <div class="form-group">
                            <label>
                                <select name="group_id" class="form-control">
                                    <option value="{{ $user->primaryRole->id }}">{{ $user->primaryRole->name }} (Default)</option>
                                    @foreach (\App\Models\Role::all() as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Unban">
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_user_report" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog{{ \modal_style() }} modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mx-auto">
                <div class="text-center">
                    <p style="font-size: 27px;">Report User: {{ $user->username }}</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="py-3">
                    <form role="form" method="POST"
                        action="{{ route('report_user', ['username' => $user->username]) }}">
                        @csrf
                        <div class="form-group">
                            <label for="report_reason">Reason</label>
                        </div>
                        <div class="form-group">
                            <label for="message"></label>
                            <textarea class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-danger" type="submit" value="Report">
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_user_delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog{{ \modal_style() }} modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mx-auto">
                <div class="text-center">
                    <p style="font-size: 27px;">Delete User: {{ $user->username }}</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="py-3">
                    <div class="text-center">
                        <a href="{{ route('user_delete', ['username' => $user->username]) }}"><input
                                class="btn btn-danger" type="submit" value="Yes, Delete"></a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_user_watch" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog{{ \modal_style() }} modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mx-auto">
                <div class="text-center">
                    <p style="font-size: 27px;">Watch User: {{ $user->username }}</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="py-3">
                    <form role="form" method="POST"
                          action="{{ route('staff.watchlist.store', ['id' => $user->id]) }}">
                        @csrf
                        <div class="form-group">
                            <label for="report_reason">Reason</label>
                        </div>
                        <div class="form-group">
                            <label for="message"></label>
                            <textarea class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-danger" type="submit" value="Submit">
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    </div>
</div>
