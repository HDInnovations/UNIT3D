<footer class="footer">
    <div class="footer__wrapper">
        <section class="footer__section">
            <h2 class="footer__section-title">
                <img src="{{ url('/favicon.ico') }}" style="height: 30px; vertical-align: sub" />
                <span class="top-nav__site-logo">{{ \config('other.title') }}</span>
            </h2>
            <p>{{ config('other.meta_description') }}</p>
            <p class="footer__icons">
                @if (! empty(config('unit3d.chat-link-url')))
                    <a href="{{ config('unit3d.chat-link-url') }}">
                        <i class="{{ config('unit3d.chat-link-icon') }}"></i>
                        {{ config('unit3d.chat-link-name') ?: __('common.chat') }}
                    </a>
                @endif
            </p>
            @if (config('announce.healthcheck_badge_url'))
                <p>
                    <img src="{{ config('announce.healthcheck_badge_url') }}" />
                </p>
            @endif
        </section>
        <section class="footer__section">
            <h2 class="footer__section-title">{{ __('common.account') }}</h2>
            <ul class="footer__section-list">
                <li>
                    <a href="{{ route('users.show', ['user' => auth()->user()]) }}">
                        {{ __('user.my-profile') }}
                    </a>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" style="display: contents">
                        @csrf
                        <button style="display: contents">
                            {{ __('common.logout') }}
                        </button>
                    </form>
                </li>
            </ul>
        </section>
        <section class="footer__section">
            <h2 class="footer__section-title">{{ __('common.community') }}</h2>
            <ul class="footer__section-list">
                <li>
                    <a href="{{ route('forums.index') }}">{{ __('forum.forums') }}</a>
                </li>
                <li>
                    <a href="{{ route('articles.index') }}">{{ __('common.news') }}</a>
                </li>
                <li><a href="{{ route('wikis.index') }}">Wikis</a></li>
            </ul>
        </section>
        @if ($pages->isNotEmpty())
            <section class="footer__section">
                <h2 class="footer__section-title">{{ __('common.pages') }}</h2>
                <ul class="footer__section-list">
                    @foreach ($pages as $page)
                        <li>
                            <a href="{{ route('pages.show', ['page' => $page]) }}">
                                {{ $page->name }}
                            </a>
                        </li>
                    @endforeach

                    <li>
                        <a href="{{ route('pages.index') }}">[View All]</a>
                    </li>
                </ul>
            </section>
        @endif

        <section class="footer__section">
            <h2 class="footer__section-title">{{ __('common.info') }}</h2>
            <ul class="footer__section-list">
                <li>
                    <a href="{{ route('staff') }}">{{ __('common.staff') }}</a>
                </li>
                <li>
                    <a href="{{ route('internal') }}">{{ __('common.internal') }}</a>
                </li>
                <li>
                    <a href="{{ route('client_blacklist') }}">{{ __('common.blacklist') }}</a>
                </li>
                <li>
                    <a href="{{ route('about') }}">{{ __('common.about') }}</a>
                </li>
                <li>
                    <a
                        href="https://github.com/HDInnovations/UNIT3D-Community-Edition/wiki/Torrent-API-(UNIT3D-v8.x.x)"
                    >
                        API Documentation
                    </a>
                </li>
            </ul>
        </section>
    </div>
    <div class="footer__sub-footer">
        <p class="footer__icons">
            Built using:
            <a href="https://laravel.com" target="_blank">
                <svg height="22" viewBox="0 0 50 52" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 0 1-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 0 1 0 39.25V6.334c0-.072.01-.142.028-.21.006-.023.02-.044.028-.067.015-.042.029-.085.051-.124.015-.026.037-.047.055-.071.023-.032.044-.065.071-.093.023-.023.053-.04.079-.06.029-.024.055-.05.088-.069h.001l9.61-5.533a.802.802 0 0 1 .8 0l9.61 5.533h.002c.032.02.059.045.088.068.026.02.055.038.078.06.028.029.048.062.072.094.017.024.04.045.054.071.023.04.036.082.052.124.008.023.022.044.028.068a.809.809 0 0 1 .028.209v20.559l8.008-4.611v-10.51c0-.07.01-.141.028-.208.007-.024.02-.045.028-.068.016-.042.03-.085.052-.124.015-.026.037-.047.054-.071.024-.032.044-.065.072-.093.023-.023.052-.04.078-.06.03-.024.056-.05.088-.069h.001l9.611-5.533a.801.801 0 0 1 .8 0l9.61 5.533c.034.02.06.045.09.068.025.02.054.038.077.06.028.029.048.062.072.094.018.024.04.045.054.071.023.039.036.082.052.124.009.023.022.044.028.068zm-1.574 10.718v-9.124l-3.363 1.936-4.646 2.675v9.124l8.01-4.611zm-9.61 16.505v-9.13l-4.57 2.61-13.05 7.448v9.216l17.62-10.144zM1.602 7.719v31.068L19.22 48.93v-9.214l-9.204-5.209-.003-.002-.004-.002c-.031-.018-.057-.044-.086-.066-.025-.02-.054-.036-.076-.058l-.002-.003c-.026-.025-.044-.056-.066-.084-.02-.027-.044-.05-.06-.078l-.001-.003c-.018-.03-.029-.066-.042-.1-.013-.03-.03-.058-.038-.09v-.001c-.01-.038-.012-.078-.016-.117-.004-.03-.012-.06-.012-.09v-.002-21.481L4.965 9.654 1.602 7.72zm8.81-5.994L2.405 6.334l8.005 4.609 8.006-4.61-8.006-4.608zm4.164 28.764l4.645-2.674V7.719l-3.363 1.936-4.646 2.675v20.096l3.364-1.937zM39.243 7.164l-8.006 4.609 8.006 4.609 8.005-4.61-8.005-4.608zm-.801 10.605l-4.646-2.675-3.363-1.936v9.124l4.645 2.674 3.364 1.937v-9.124zM20.02 38.33l11.743-6.704 5.87-3.35-8-4.606-9.211 5.303-8.395 4.833 7.993 4.524z"
                        fill="#FF2D20"
                        fill-rule="evenodd"
                    />
                </svg>
            </a>
            <a href="https://livewire.laravel.com" target="_blank">
                <svg height="22" viewBox="0 0 40 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M34.8 27.706C34.12 28.734 33.605 30 32.223 30c-2.326 0-2.452-3.587-4.78-3.587-2.327 0-2.201 3.587-4.527 3.587s-2.452-3.587-4.78-3.587c-2.327 0-2.201 3.587-4.528 3.587-2.326 0-2.452-3.587-4.78-3.587C6.5 26.413 6.628 30 4.3 30c-.731 0-1.245-.354-1.678-.84A19.866 19.866 0 0 1 0 19.24C0 8.613 8.208 0 18.333 0 28.46 0 36.667 8.614 36.667 19.24c0 3.037-.671 5.91-1.866 8.466Z"
                        fill="#FB70A9"
                    ></path>
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M34.8 27.706C34.12 28.734 33.605 30 32.223 30c-2.326 0-2.452-3.587-4.78-3.587-2.327 0-2.201 3.587-4.527 3.587s-2.452-3.587-4.78-3.587c-2.327 0-2.201 3.587-4.528 3.587-2.326 0-2.452-3.587-4.78-3.587C6.5 26.413 6.628 30 4.3 30c-.731 0-1.245-.354-1.678-.84A19.866 19.866 0 0 1 0 19.24C0 8.613 8.208 0 18.333 0 28.46 0 36.667 8.614 36.667 19.24c0 3.037-.671 5.91-1.866 8.466Z"
                        fill="#FB70A9"
                    ></path>
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M30.834 29.617c4.804-7.147 4.929-15.075.372-23.784a19.19 19.19 0 0 1 5.461 13.447c0 3.026-.695 5.89-1.934 8.434C34.028 28.738 33.493 30 32.06 30c-.49 0-.886-.148-1.226-.383Z"
                        fill="#E24CA6"
                    ></path>
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M17.35 24.038c6.376 0 9.06-3.698 9.06-8.95C26.41 9.834 22.355 5 17.35 5c-5.003 0-9.059 4.835-9.059 10.087 0 5.253 2.684 8.951 9.06 8.951Z"
                        fill="#fff"
                    ></path>
                    <path
                        d="M14.915 15.385c1.876 0 3.397-1.68 3.397-3.75 0-2.071-1.52-3.75-3.397-3.75-1.876 0-3.397 1.679-3.397 3.75 0 2.07 1.52 3.75 3.397 3.75Z"
                        fill="#030776"
                    ></path>
                    <path
                        d="M14.35 12.5c.937 0 1.698-.775 1.698-1.73 0-.957-.76-1.731-1.699-1.731-.938 0-1.699.774-1.699 1.73s.76 1.731 1.7 1.731Z"
                        fill="#fff"
                    ></path>
                </svg>
            </a>
            <a href="https://alpinejs.dev" target="_blank">
                <svg height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 32 128 96">
                    <path
                        fill="#77c1d2"
                        fill-rule="evenodd"
                        d="M98.444 35.562 126 62.997 98.444 90.432 70.889 62.997z"
                        clip-rule="evenodd"
                    />
                    <path
                        fill="#2d3441"
                        fill-rule="evenodd"
                        d="m29.556 35.562 57.126 56.876H31.571L2 62.997z"
                        clip-rule="evenodd"
                    />
                </svg>
            </a>
            <a href="https://rust-lang.org" target="_blank">
                <svg
                    height="22"
                    viewBox="0 0 106 106"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                >
                    <g id="logo" transform="translate(53, 53)">
                        <path
                            id="r"
                            transform="translate(0.5, 0.5)"
                            stroke="black"
                            stroke-width="1"
                            stroke-linejoin="round"
                            d="     M -9,-15 H 4 C 12,-15 12,-7 4,-7 H -9 Z     M -40,22 H 0 V 11 H -9 V 3 H 1 C 12,3 6,22 15,22 H 40     V 3 H 34 V 5 C 34,13 25,12 24,7 C 23,2 19,-2 18,-2 C 33,-10 24,-26 12,-26 H -35     V -15 H -25 V 11 H -40 Z"
                        />
                        <g id="gear" mask="url(#holes)">
                            <circle r="43" fill="none" stroke="black" stroke-width="9" />
                            <g id="cogs">
                                <polygon
                                    id="cog"
                                    stroke="black"
                                    stroke-width="3"
                                    stroke-linejoin="round"
                                    points="46,3 51,0 46,-3"
                                />
                                <use xlink:href="#cog" transform="rotate(11.25)" />
                                <use xlink:href="#cog" transform="rotate(22.50)" />
                                <use xlink:href="#cog" transform="rotate(33.75)" />
                                <use xlink:href="#cog" transform="rotate(45.00)" />
                                <use xlink:href="#cog" transform="rotate(56.25)" />
                                <use xlink:href="#cog" transform="rotate(67.50)" />
                                <use xlink:href="#cog" transform="rotate(78.75)" />
                                <use xlink:href="#cog" transform="rotate(90.00)" />
                                <use xlink:href="#cog" transform="rotate(101.25)" />
                                <use xlink:href="#cog" transform="rotate(112.50)" />
                                <use xlink:href="#cog" transform="rotate(123.75)" />
                                <use xlink:href="#cog" transform="rotate(135.00)" />
                                <use xlink:href="#cog" transform="rotate(146.25)" />
                                <use xlink:href="#cog" transform="rotate(157.50)" />
                                <use xlink:href="#cog" transform="rotate(168.75)" />
                                <use xlink:href="#cog" transform="rotate(180.00)" />
                                <use xlink:href="#cog" transform="rotate(191.25)" />
                                <use xlink:href="#cog" transform="rotate(202.50)" />
                                <use xlink:href="#cog" transform="rotate(213.75)" />
                                <use xlink:href="#cog" transform="rotate(225.00)" />
                                <use xlink:href="#cog" transform="rotate(236.25)" />
                                <use xlink:href="#cog" transform="rotate(247.50)" />
                                <use xlink:href="#cog" transform="rotate(258.75)" />
                                <use xlink:href="#cog" transform="rotate(270.00)" />
                                <use xlink:href="#cog" transform="rotate(281.25)" />
                                <use xlink:href="#cog" transform="rotate(292.50)" />
                                <use xlink:href="#cog" transform="rotate(303.75)" />
                                <use xlink:href="#cog" transform="rotate(315.00)" />
                                <use xlink:href="#cog" transform="rotate(326.25)" />
                                <use xlink:href="#cog" transform="rotate(337.50)" />
                                <use xlink:href="#cog" transform="rotate(348.75)" />
                            </g>
                            <g id="mounts">
                                <polygon
                                    id="mount"
                                    stroke="black"
                                    stroke-width="6"
                                    stroke-linejoin="round"
                                    points="-7,-42 0,-35 7,-42"
                                />
                                <use xlink:href="#mount" transform="rotate(72)" />
                                <use xlink:href="#mount" transform="rotate(144)" />
                                <use xlink:href="#mount" transform="rotate(216)" />
                                <use xlink:href="#mount" transform="rotate(288)" />
                            </g>
                        </g>
                        <mask id="holes">
                            <rect x="-60" y="-60" width="120" height="120" fill="white" />
                            <circle id="hole" cy="-40" r="3" />
                            <use xlink:href="#hole" transform="rotate(72)" />
                            <use xlink:href="#hole" transform="rotate(144)" />
                            <use xlink:href="#hole" transform="rotate(216)" />
                            <use xlink:href="#hole" transform="rotate(288)" />
                        </mask>
                    </g>
                </svg>
            </a>
        </p>
        <p class="footer__stats">
            <strong>Time:</strong>
            <span>
                {{ number_format((microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT'))) * 1000, 5) }}
                ms
            </span>
            <strong>Used:</strong>
            <span>{{ number_format(memory_get_peak_usage(true) / 1024 / 1024, 2) }} MiB</span>
            <strong>Load:</strong>
            <span>
                {{ implode(' ', array_map(fn ($n) => number_format($n, 2), sys_getloadavg())) }}
            </span>
            <strong>Date:</strong>
            <span>{{ now() }}</span>
        </p>
        <p class="footer__copyright">
            Site and design &copy;
            {{ date('Y', strtotime(config('other.birthdate'))) }}-{{ date('Y') }}
            {{ config('other.title') }} |
            <a href="https://github.com/HDInnovations/UNIT3D-Community-Edition">
                UNIT3D {{ config('unit3d.version') }}
            </a>
            @if (config('announce.external_tracker.is_enabled'))
                +
                <a href="https://github.com/HDInnovations/UNIT3D-Announce">UNIT3D-Announce</a>
            @endif
        </p>
    </div>
</footer>
