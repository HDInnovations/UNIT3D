<div id="editChatStatus-{{ $chatstatus->id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog{{ \modal_style() }}">
        <div class="modal-content">

            <div class="modal-header" style="text-align: center;">
                <h3>@lang('common.edit') Chat Status ({{ $chatstatus->name }})</h3>
            </div>

            <form class="form-horizontal" role="form" method="POST"
                action="{{ route('staff.statuses.update', ['id' => $chatstatus->id]) }}">
                @csrf
                <div class="modal-body" style="text-align: center;">
                    <h4>Please enter the new settings you want to use for {{ $chatstatus->name }}</h4>
                    <label for="chatstatus_name"> @lang('common.name'):</label> <label for="name"></label><input
                        style="margin:0 auto; width:300px;" type="text" class="form-control" name="name" id="name"
                        placeholder="Enter @lang('common.name') Here..." value="{{ $chatstatus->name }}" required>
                    <label for="chatstatus_color"> Color:</label> <label for="color"></label><input
                        style="margin:0 auto; width:300px;" type="text" class="form-control" name="color" id="color"
                        placeholder="Enter Hex Color Code Here..." value="{{ $chatstatus->color }}" required>
                    <label for="chatstatus_icon"> Icon:</label> <label for="icon"></label><input
                        style="margin:0 auto; width:300px;" type="text" class="form-control" name="icon" id="icon"
                        placeholder="Enter Font Awesome Code Here..." value="{{ $chatstatus->icon }}" required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-md btn-primary" data-dismiss="modal">@lang('common.cancel')</button>
                    <input class="btn btn-md btn-success" type="submit">
                </div>
            </form>
        </div>
    </div>
</div>

<div id="deleteChatStatus-{{ $chatstatus->id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog{{ \modal_style() }}">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">@lang('common.delete') Chat Status ({{ $chatstatus->name }}) Permanently</h4>
            </div>

            <form class="form-horizontal" role="form" method="POST"
                action="{{ route('staff.statuses.destroy', ['id' => $chatstatus->id]) }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure about this?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-md btn-primary"
                        data-dismiss="modal">@lang('common.cancel')</button>
                    <input class="btn btn-md btn-danger" type="submit">
                </div>
            </form>
        </div>
    </div>
</div>
