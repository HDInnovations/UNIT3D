<!DOCTYPE html>
<html lang="{{ auth()->user()->settings->locale }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <div class="alerts">
            @include('partials.alerts')
        </div>
        <header>
            @include('partials.top-nav')
            <nav class="secondary-nav">
                <ol class="breadcrumbsV2">
                    @if (! Route::is('home.index'))
                        <li class="breadcrumbV2">
                            <a class="breadcrumb__link" href="{{ route('home.index') }}">
                                <i class="{{ config('other.font-awesome') }} fa-home"></i>
                            </a>
                        </li>
                    @endif

                    @yield('breadcrumbs')
                </ol>
                <ul class="nav-tabsV2">
                    @yield('nav-tabs')
                </ul>
            </nav>
            @if (Session::has('achievement'))
                @include('partials.achievement-modal')
            @endif

            @if (Session::has('errors'))
                <div id="ERROR_COPY" style="display: none">
                    @foreach ($errors->getBags() as $bag)
                        @foreach ($bag->getMessages() as $errors)
                            @foreach ($errors as $error)
                                {{ $error }}
                                <br />
                            @endforeach
                        @endforeach
                    @endforeach
                </div>
            @endif
        </header>
        <main class="@yield('page')">
            @yield('content')
        </main>
        @include('partials.footer')

        @vite('resources/js/app.js')

        @if (config('other.freeleech') == true || config('other.invite-only') == false || config('other.doubleup') == true)
            <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
                function timer() {
                    return {
                        seconds: '00',
                        minutes: '00',
                        hours: '00',
                        days: '00',
                        distance: 0,
                        countdown: null,
                        promoTime: new Date('{{ config('other.freeleech_until') }}').getTime(),
                        now: new Date().getTime(),
                        start: function () {
                            this.countdown = setInterval(() => {
                                // Calculate time
                                this.now = new Date().getTime();
                                this.distance = this.promoTime - this.now;
                                // Set times
                                this.days = this.padNum(
                                    Math.floor(this.distance / (1000 * 60 * 60 * 24)),
                                );
                                this.hours = this.padNum(
                                    Math.floor(
                                        (this.distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60),
                                    ),
                                );
                                this.minutes = this.padNum(
                                    Math.floor((this.distance % (1000 * 60 * 60)) / (1000 * 60)),
                                );
                                this.seconds = this.padNum(
                                    Math.floor((this.distance % (1000 * 60)) / 1000),
                                );
                                // Stop
                                if (this.distance < 0) {
                                    clearInterval(this.countdown);
                                    this.days = '00';
                                    this.hours = '00';
                                    this.minutes = '00';
                                    this.seconds = '00';
                                }
                            }, 100);
                        },
                        padNum: function (num) {
                            let zero = '';
                            for (let i = 0; i < 2; i++) {
                                zero += '0';
                            }
                            return (zero + num).slice(-2);
                        },
                    };
                }
            </script>
        @endif

        @foreach (['warning', 'success', 'info'] as $key)
            @if (Session::has($key))
                <script
                    nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}"
                    type="module"
                >
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                    });

                    Toast.fire({
                        icon: '{{ $key }}',
                        title: '{{ Session::get($key) }}',
                    });
                </script>
            @endif
        @endforeach

        @if (Session::has('errors'))
            <script
                nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}"
                type="module"
            >
                Swal.fire({
                    title: '<strong style=" color: rgb(17,17,17);">Error</strong>',
                    icon: 'error',
                    html: document.getElementById('ERROR_COPY').innerHTML,
                    showCloseButton: true,
                    willOpen: function (el) {
                        el.querySelector('textarea').remove();
                    },
                });
            </script>
        @endif

        <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
            window.addEventListener('success', (event) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });

                Toast.fire({
                    icon: 'success',
                    title: event.detail.message,
                });
            });
        </script>

        <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
            window.addEventListener('error', (event) => {
                Swal.fire({
                    title: '<strong style=" color: rgb(17,17,17);">Error</strong>',
                    icon: 'error',
                    html: event.detail.message,
                    showCloseButton: true,
                });
            });
        </script>

        <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
            document.addEventListener('alpine:init', () => {
                Alpine.data('confirmation', () => ({
                    confirmAction() {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: atob(this.$el.dataset.b64DeletionMessage),
                            icon: 'warning',
                            showConfirmButton: true,
                            showCancelButton: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.$root.submit();
                            }
                        });
                    },
                }));
            });
        </script>

        @yield('javascripts')
        @yield('scripts')
        @livewireScriptConfig(['nonce' => HDVinnie\SecureHeaders\SecureHeaders::nonce()])
    </body>
</html>
