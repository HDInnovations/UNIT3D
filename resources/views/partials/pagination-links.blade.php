@php
    if (! isset($scrollTo)) {
        $scrollTo = '.panelV2';
    }

    $scrollIntoViewJsSnippet =
        $scrollTo !== false
            ? <<<JS
       window.scroll({ top: ((\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).offsetTop - 80)})
    JS
            : '';
@endphp

@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination navigation">
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
    </nav>
@endif
