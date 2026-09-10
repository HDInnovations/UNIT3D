<div class="comparison" x-data="comparison({{ count($comparates) }})">
    <div class="comparison__text">
        @foreach ($comparates as $comparate)
            @if ($loop->last)
                {{ $comparate }}:
            @else
                {{ $comparate }}
                <span class="comparison__divider">vs</span>
            @endif
        @endforeach

        <button class="comparison__button" x-bind="showButton">Show</button>
    </div>
    <ul class="comparison__screenshots" tabindex="-1" x-bind="screenshots" x-cloak>
        @foreach ($urls as $row)
            <li>
                <ul class="comparison__row">
                    @foreach ($row as $url)
                        <li
                            class="comparison__image-container"
                            data-index="{{ $loop->iteration }}"
                            x-bind="container"
                        >
                            <figure class="comparison__figure">
                                @if ($loop->parent->first)
                                    <figcaption class="comparison__figcaption">
                                        {{ $comparates[$loop->index] }}
                                    </figcaption>
                                @endif

                                <img
                                    class="comparison__image"
                                    src="{!! $url !!}"
                                    loading="lazy"
                                    data-index="{{ $loop->iteration }}"
                                    x-bind="image"
                                />
                            </figure>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>
