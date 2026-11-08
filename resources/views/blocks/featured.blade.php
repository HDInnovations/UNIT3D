@if ($featured->isNotEmpty())
    <section class="panelV2 blocks__featured" x-data="featured">
        <header class="panel__header">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-star"></i>
                {{ __('blocks.featured-torrents') }}
            </h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <button class="form__standard-icon-button" x-bind="left">
                        <i class="{{ \config('other.font-awesome') }} fa-angle-left"></i>
                    </button>
                </div>
                <div class="panel__action">
                    <button class="form__standard-icon-button" x-bind="right">
                        <i class="{{ \config('other.font-awesome') }} fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </header>
        <div>
            <ul class="featured-carousel" x-ref="featured" x-bind="list">
                @foreach ($featured as $feature)
                    <li class="featured-carousel__slide">
                        <x-torrent.card
                            :meta="$feature->torrent->meta"
                            :torrent="$feature->torrent"
                        />
                        <footer class="featured-carousel__feature-details">
                            <p class="featured-carousel__featured-until">
                                {{ __('blocks.featured-until') }}:
                                <br />
                                <time
                                    datetime="{{ $feature->created_at->addDay(7) }}"
                                    title="{{ $feature->created_at->addDay(7) }}"
                                >
                                    {{ $feature->created_at->addDay(7)->toFormattedDateString() }}
                                    ({{ $feature->created_at->addDay(7)->diffForHumans() }}!)
                                </time>
                            </p>
                            <p class="featured-carousel__featured-by">
                                {{ __('blocks.featured-by') }}: {{ $feature->user->username }}!
                            </p>
                        </footer>
                    </li>
                @endforeach
            </ul>
        </div>

        <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
            document.addEventListener('alpine:init', () => {
                Alpine.data('featured', () => ({
                    left: {
                        ['x-on:click']() {
                            if (this.$refs.featured.scrollLeft == 16) {
                                this.$refs.featured.scrollLeft = this.$refs.featured.scrollWidth;
                            } else {
                                this.$refs.featured.scrollLeft -=
                                    (this.$refs.featured.children[0].offsetWidth + 16) / 2 + 2;
                            }
                        },
                    },
                    right: {
                        ['x-on:click']() {
                            if (
                                this.$refs.featured.scrollLeft ==
                                this.$refs.featured.scrollWidth -
                                    this.$refs.featured.offsetWidth -
                                    16
                            ) {
                                this.$refs.featured.scrollLeft = 0;
                            } else {
                                this.$refs.featured.scrollLeft +=
                                    (this.$refs.featured.children[0].offsetWidth + 16) / 2 + 2;
                            }
                        },
                    },
                    list: {
                        ['x-init']() {
                            setInterval(() => {
                                if (!this.$root.matches(':hover')) {
                                    if (
                                        this.$el.scrollLeft ==
                                        this.$el.scrollWidth - this.$el.offsetWidth - 16
                                    ) {
                                        this.$el.scrollLeft = 0;
                                    } else {
                                        this.$el.scrollLeft +=
                                            (this.$el.children[0].offsetWidth + 16) / 2 + 2;
                                    }
                                }
                            }, 5000);
                        },
                    },
                }));
            });
        </script>
    </section>
@endif
