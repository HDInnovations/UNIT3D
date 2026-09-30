@component('mail::message')
# {{ __('email.disabled-header') }}!
{{ __('application-messages.mail.disabled-body', [
    'softDelete' => config('pruning.soft_delete'),
    'site'       => config('other.title'),
    'lastLogin'  => config('pruning.last_login'),
]) }}
@endcomponent
