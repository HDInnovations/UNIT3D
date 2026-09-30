@component('mail::message')
# {{ __('application-messages.mail.deny-application-heading', ['site' => config('other.title')]) }}
{{ __('application-messages.mail.deny-application-body') }}
{{ $deniedMessage }}
{{ __('application-messages.mail.signature', ['site' => config('other.title')]) }}
@endcomponent
