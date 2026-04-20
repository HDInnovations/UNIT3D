@if ($sortField ?? request('sortField') === $field)
    <i
        data-sort-by="{{ $field }}"
        class="{{ config('other.font-awesome') }} fa-sort-{{ $sortDirection ?? request('sortDirection', 'desc') === 'asc' ? 'up' : 'down' }}"
    ></i>
@else
    <i
        data-sort-by="{{ $field }}"
        class="{{ config('other.font-awesome') }} fa-sort"
        style="opacity: 0.35"
    ></i>
@endif
