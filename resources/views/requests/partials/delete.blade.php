@if ($canEdit)
    <li class="form__group form__group--short-horizontal">
        <form
            action="{{ route('requests.destroy', ['torrentRequest' => $torrentRequest]) }}"
            method="POST"
            x-data="confirmation"
            style="display: contents"
        >
            @csrf
            @method('DELETE')
            <button
                x-on:click.prevent="confirmAction"
                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this torrent request and lose the BON?') }}"
                class="form__button form__button--outlined form__button--centered"
            >
                {{ __('common.delete') }}
            </button>
        </form>
    </li>
@endif
