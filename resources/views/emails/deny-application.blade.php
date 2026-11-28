@component('mail::message')
# Your {{ config('other.title') }} application
Your application has been denied for the following reason:<br>
{{ $deniedMessage }}<br><br>
{{ __('Regards') }},<br>
{{ config('other.title') }}
@endcomponent
