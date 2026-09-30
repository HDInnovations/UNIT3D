@component('mail::message')
# {{ __('application-messages.mail.test-email-heading') }}
{{ __('application-messages.mail.test-email-body') }}
{{ __('application-messages.mail.signature', ['site' => config('other.title')]) }}
@endcomponent
