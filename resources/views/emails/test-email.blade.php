@component('mail::message')
# Test email
Your test email has been successfully delivered! Looks like your mail configs are on point!<br><br>
{{ __('Regards') }},<br>
{{ config('other.title') }}
@endcomponent
