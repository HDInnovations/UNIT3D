@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# {{ __('application-messages.mail.whoops') }}
@else
# {{ __('application-messages.mail.hello') }}
@endif
@endif

{{-- Intro lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
{{ __('application-messages.mail.regards') }},<br>
{{ config('app.name') }}
@endif

{{-- cspell:words subcopy --}}
{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
{{ __(
    'email.footer-link',
    [
        'actionText' => $actionText,
    ]
) }} <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
@endslot
@endisset
@endcomponent
