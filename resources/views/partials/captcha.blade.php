<input type="hidden" name="_captcha" value="{{ $token }}" />
<div style="position: fixed; transform: translateX(-10000px)">
    <label for="{{ $mustBeEmptyField }}">{{ __('interface.honeypot-name') }}</label>
    <input id="{{ $mustBeEmptyField }}" type="text" name="{{ $mustBeEmptyField }}" value="" />
</div>
<input type="hidden" name="{{ $random }}" value="{{ $ts }}" />
