<dialog class="dialog" x-data x-ref="dialog" x-init="$el.showModal()">
    <h1 class="dialog__heading">
        {{ __('common.unlocked-achievement', ['achievement' => Session::get('achievement')]) }}
    </h1>
    <div
        class="dialog__form"
        x-on:click.outside="$refs.dialog.close()"
        style="text-align: center"
    >
        <span class="modal-icon display-1-lg">
            <i class="fas fa-trophy-alt fa-4x text-gold"></i>
        </span>
        <span>Well done!</span>
        <p class="form__group">
            <a
                href="{{ route('users.achievements.index', ['user' => auth()->user()]) }}"
                class="form__button form__button--outlined"
            >
                All achievements
            </a>
        </p>
    </div>
</dialog>
