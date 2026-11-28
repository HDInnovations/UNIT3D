<input type="hidden" name="_captcha" value="{{ $token }}" />
<div style="position: fixed; transform: translateX(-10000px)">
    <label for="{{ $mustBeEmptyField }}">Name</label>
    <input
        id="{{ $mustBeEmptyField }}"
        type="text"
        name="{{ $mustBeEmptyField }}"
        value=""
        tabindex="-1"
    />
</div>
<input type="hidden" name="{{ $random }}" value="{{ $ts }}" />
