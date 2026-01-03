@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if (isset($level) && $level === 'error')
# {{ __('Whoops!') }}
@else
# {{ __('Hello!') }}
@endif
@endif

{{-- Intro lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action button --}}
@isset($actionText)
<?php
    $color = 'primary';
    if (isset($level)) {
        switch ($level) {
            case 'success':
            case 'error':
                $color = $level;
                break;
        }
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
{{ __('Regards') }},<br>
{{ config('app.name') }}
@endif

{{-- cspell:words subcopy --}}
{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
{{ __(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) }} <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
@endslot
@endisset
@endcomponent
