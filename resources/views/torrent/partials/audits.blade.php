<div class="panel panel-chat shoutbox">
    <div class="panel-heading" data-toggle="collapse" data-parent="#accordion2"
         href="#collapseOne">
        <h4>
            <i class="{{ config("other.font-awesome") }} fa-clipboard-list"></i> Audits
            <i class="{{ config("other.font-awesome") }} fa-plus-circle fa-pull-right"></i>
        </h4>
    </div>

    <div class="table-responsive panel-collapse collapse" id="collapseOne">
        <table class="table table-condensed table-bordered table-striped">
            <tbody>
            @foreach(App\Models\Audit::whereModelEntryId($torrent->id)->latest()->get() as $audit)
                @php $values = json_decode($audit->record, true); @endphp
                <tr>
                    <td>
                        <span>
                            {{ $audit->user->username }} {{ $audit->action }}d this torrent {{ $audit->created_at->diffForHumans() }}
                        </span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
