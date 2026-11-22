<li class="form__group form__group--short-horizontal">
    <form
        method="POST"
        action="{{ route('requests.approved_fills.destroy', ['torrentRequest' => $torrentRequest]) }}"
        x-data="confirmation"
        style="display: contents"
    >
        @csrf
        @method('DELETE')
        <button
            x-on:click.prevent="confirmAction"
            data-b64-deletion-message="{{ base64_encode('Are you sure you want revoke the torrent request fill\'s approval and revert the filler\'s bon reward?') }}"
            class="form__button form__button--outlined form__button--centered"
        >
            Revoke approval
        </button>
    </form>
</li>
