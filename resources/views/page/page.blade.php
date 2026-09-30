@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('pages.index') }}" class="breadcrumb__link">
            {{ __('common.pages') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $page->name }}
    </li>
@endsection

@section('page', 'page__page--show')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ $page->name }}</h2>
        <div class="panel__body bbcode-rendered">
            @joypixels($page->getContentHtml())
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('common.created_at') }}</dt>
                <dd>
                    <time datetime="{{ $page->created_at }}" title="{{ $page->created_at }}">
                        {{ $page->created_at }}
                    </time>
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('torrent.updated_at') }}</dt>
                <dd>
                    <time datetime="{{ $page->updated_at }}" title="{{ $page->updated_at }}">
                        {{ $page->updated_at }}
                    </time>
                </dd>
            </div>
        </dl>
    </section>
@endsection

@section('javascripts')
    @if (parse_url(request()->url(), PHP_URL_PATH) === parse_url(config('other.rules_url'), PHP_URL_PATH) && auth()->user()->read_rules == 0)
        <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
            confirmRules = function () {
                let scrollHeight, totalHeight;
                scrollHeight = document.body.scrollHeight;
                totalHeight = Math.ceil(window.scrollY + window.innerHeight);

                if (totalHeight >= scrollHeight) {
                    Swal.fire({
                        title: '<strong><u>{{ __('member-interface.misc.page.read-the-rules') }}</u></strong>',
                        text: @js(__('member-interface.misc.page.understand-rules-question')),
                        icon: 'question',
                        confirmButtonText:
                            '<i class="fa fa-thumbs-up"></i> {{ __('member-interface.misc.page.i-do') }}',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post(
                                `/users/${atob(
                                    '{{ base64_encode(auth()->user()->username) }}',
                                )}/accept-rules`,
                            );
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                            });

                            Toast.fire({
                                icon: 'success',
                                title: @js(__('member-interface.misc.page.rules-accepted-success')),
                            });
                        } else {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                            });

                            Toast.fire({
                                icon: 'error',
                                title: @js(__('common.something-went-wrong')),
                            });
                        }
                    });
                }
            };
            window.onscroll = confirmRules;
            window.onload = confirmRules;
        </script>
    @endif
@endsection
