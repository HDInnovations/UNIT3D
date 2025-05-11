<div class="comparison" x-data="{ show: false }">
    <div class="comparison__text">
        @foreach ($comparates as $comparate)
            @if ($loop->last)
                {{ $comparate }}:
            @else
                {{ $comparate }}
                <span class="comparison__divider">vs</span>
            @endif
        @endforeach

        <button
            class="comparison__button"
            x-on:click.prevent="
                show = true;
                $nextTick(() => $refs.screenshots.focus())
            "
            x-on:keydown.escape.window="show = false"
        >
            Show
        </button>
    </div>
    <ul
        class="comparison__screenshots"
        tabindex="-1"
        x-ref="screenshots"
        x-show="show"
        x-cloak
        x-on:click="show = false"
        x-on:keydown.down.window="
            if (show) {
                $event.preventDefault();
                $event.stopPropagation();
                $el.scrollBy(0, $el.getElementsByTagName('li')[0].offsetHeight);
            }
        "
        x-on:keydown.up.window="
            if (show) {
                $event.preventDefault();
                $event.stopPropagation();
                $el.scrollBy(0, -1 * $el.getElementsByTagName('li')[0].offsetHeight);
            }
        "
    >
        @foreach ($urls as $row)
            <li>
                <ul
                    class="comparison__row"
                    x-data="{ screen: 1 }"
                    x-on:keydown.window="
                        if (isFinite($event.key) && 1 <= $event.key && $event.key <= {{ \count($comparates) }}) {
                            screen = $event.key;
                        }
                    "
                    x-on:keydown.left.window="
                        if (show) {
                            $event.preventDefault();
                            $event.stopPropagation();
                            screen = screen == 1 ? {{ \count($comparates) }} : screen - 1;
                        }
                    "
                    x-on:keydown.right.window="
                        if (show) {
                            $event.preventDefault();
                            $event.stopPropagation();
                            screen = screen == {{ \count($comparates) }} ? 1 : screen + 1;
                        }
                    "
                    x-on:mousemove.window="screen = Math.ceil(($event.clientX * {{ \count($comparates) }}) / window.innerWidth)"
                >
                    @foreach ($row as $url)
                        <li
                            class="comparison__image-container"
                            x-bind:class="screen != {{ $loop->iteration }} && 'comparison__image-container--hidden'"
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
                                    x-bind:class="screen != {{ $loop->iteration }} && 'comparison__image--hidden'"
                                />
                            </figure>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>
