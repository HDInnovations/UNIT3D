<div class="modal fade" id="modal-comment-edit-{{ $comment->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog{{ \modal_style() }}">
        <div class="modal-content">
            <meta charset="utf-8">
            <title>@lang('common.edit-your-comment')</title>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang('common.edit-your-comment')</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" method="POST"
                    action="{{ route('comment_edit', ['comment_id' => $comment->id]) }}">
                    @csrf
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="comment-edit"></label>
                            <textarea class="form-control" rows="5" name="comment-edit" cols="50" id="comment-edit">{{ $comment->content }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input style="float:right;" class="btn btn-primary" type="submit"
                                value="@lang('common.submit')">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
