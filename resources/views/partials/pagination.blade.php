@if ($paginator->hasPages())
    <nav
        class="pagination"
        role="navigation"
        aria-label="Pagination navigation"
        x-data="pagination"
    >
        <ul class="pagination__items">
            @if ($paginator->onFirstPage())
                <li class="pagination__previous pagination__previous--disabled">
                    &lsaquo; {{ __('common.previous') }}
                </li>
            @else
                <li class="pagination__previous">
                    <a
                        class="pagination__previous"
                        href="{{ $paginator->previousPageUrl() }}"
                        wire:click.prevent="previousPage"
                        x-bind="newPage"
                        rel="prev"
                    >
                        &lsaquo; {{ __('common.previous') }}
                    </a>
                </li>
            @endif
            <li class="pagination__page-wrapper">
                <ul class="pagination__pages">
                    @if ($paginator->onFirstPage())
                        <li class="pagination__current">1</li>
                    @else
                        <li>
                            <a
                                class="pagination__link"
                                href="{{ $paginator->url(1) }}"
                                wire:click.prevent="gotoPage(1, '{{ $paginator->getPageName() }}')"
                                x-bind="newPage"
                                rel="prev"
                            >
                                1
                            </a>
                        </li>
                    @endif
                    @if ($paginator->currentPage() - 3 > 2)
                        @if ($paginator->currentPage() - 4 === 2)
                            <li>
                                <a
                                    class="pagination__link"
                                    href="{{ $paginator->url(2) }}"
                                    wire:click.prevent="gotoPage(2, '{{ $paginator->getPageName() }}')"
                                    x-bind="newPage"
                                >
                                    2
                                </a>
                            </li>
                        @else
                            <li class="pagination__ellipsis">&middot;&middot;&middot;</li>
                        @endif
                    @endif

                    @if ($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        @for ($page = max(2, $paginator->currentPage() - 3); $page <= min($paginator->currentPage() + 3, $paginator->lastPage() - 1); $page++)
                            @if ($page === $paginator->currentPage())
                                <li class="pagination__current">{{ $page }}</li>
                            @else
                                <li>
                                    <a
                                        class="pagination__link"
                                        href="{{ $paginator->url($page) }}"
                                        wire:click.prevent="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                        x-bind="newPage"
                                    >
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endfor

                        @if ($paginator->currentPage() + 3 < $paginator->lastPage() - 1)
                            @if ($paginator->currentPage() + 4 === $paginator->lastPage() - 1)
                                <li>
                                    <a
                                        class="pagination__link"
                                        href="{{ $paginator->url($paginator->currentPage() + 4) }}"
                                        wire:click.prevent="gotoPage({{ $paginator->currentPage() + 4 }}, '{{ $paginator->getPageName() }}')"
                                        x-bind="newPage"
                                    >
                                        {{ $paginator->currentPage() + 4 }}
                                    </a>
                                </li>
                            @else
                                <li class="pagination__ellipsis">&middot;&middot;&middot;</li>
                            @endif
                        @endif

                        @if ($paginator->hasMorePages())
                            <li>
                                <a
                                    class="pagination__link"
                                    href="{{ $paginator->url($paginator->lastPage()) }}"
                                    wire:click.prevent="gotoPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')"
                                    x-bind="newPage"
                                    rel="next"
                                >
                                    {{ $paginator->lastPage() }}
                                </a>
                            </li>
                        @else
                            <li class="pagination__current">{{ $paginator->lastPage() }}</li>
                        @endif
                    @else
                        @for ($page = max(2, $paginator->currentPage() - 3); $page <= $paginator->currentPage(); $page++)
                            @if ($page === $paginator->currentPage())
                                <li class="pagination__current">{{ $page }}</li>
                            @else
                                <li>
                                    <a
                                        class="pagination__link"
                                        href="{{ $paginator->url($page) }}"
                                        wire:click.prevent="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                        x-bind="newPage"
                                    >
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endfor

                        @if ($paginator->hasMorePages())
                            <li>
                                <a
                                    class="pagination__link"
                                    href="{{ $paginator->url($paginator->currentPage() + 1) }}"
                                    wire:click.prevent="gotoPage({{ $paginator->currentPage() + 1 }}, '{{ $paginator->getPageName() }}')"
                                    x-bind="newPage"
                                    rel="next"
                                >
                                    {{ $paginator->currentPage() + 1 }}
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </li>

            @if ($paginator->hasMorePages())
                <li class="pagination__next">
                    <a
                        class="pagination__next"
                        href="{{ $paginator->nextPageUrl() }}"
                        wire:click.prevent="nextPage"
                        x-bind="newPage"
                        rel="next"
                    >
                        {{ __('common.next') }} &rsaquo;
                    </a>
                </li>
            @else
                <li class="pagination__next pagination__next--disabled">
                    {{ __('common.next') }} &rsaquo;
                </li>
            @endif
        </ul>

        <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
            document.addEventListener('alpine:init', () => {
                Alpine.data('pagination', (wire) => ({
                    newPage: {
                        ['x-on:click']() {
                            window.scroll({
                                top:
                                    (
                                        this.$el.closest('.panelV2') ||
                                        document.querySelector('.panelV2')
                                    ).offsetTop - 80,
                            });
                        },
                    },
                }));
            });
        </script>
    </nav>
@endif
