<div id="bbcode-input" class="bbcode-input" x-data="{{ $name }}BbcodeInput">
    <p class="bbcode-input__tabs">
        <input
            class="bbcode-input__tab-input"
            type="radio"
            id="{{ $name }}-bbcode-preview-disabled"
            value="0"
            wire:model.live="isPreviewEnabled"
        />
        <label class="bbcode-input__tab-label" for="{{ $name }}-bbcode-preview-disabled">
            {{ __('livewire-interface.write') }}
        </label>
        <input
            class="bbcode-input__tab-input"
            type="radio"
            id="{{ $name }}-bbcode-preview-enabled"
            value="1"
            wire:model.live="isPreviewEnabled"
        />
        <label class="bbcode-input__tab-label" for="{{ $name }}-bbcode-preview-enabled">
            {{ __('common.preview') }}
        </label>
    </p>
    <p class="bbcode-input__icon-bar-toggle">
        <button
            type="button"
            class="form__button form__button--text"
            x-on:click="toggleButtonVisibility"
        >
            {{ __('livewire-interface.bbcode-toggle') }}
        </button>
    </p>
    <menu class="bbcode-input__icon-bar" x-cloak x-show="showButtons">
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertBold">
                <abbr title="{{ __('livewire-interface.bold') }}">
                    <i class="{{ config('other.font-awesome') }} fa-bold"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertItalic">
                <abbr title="{{ __('livewire-interface.italics') }}">
                    <i class="{{ config('other.font-awesome') }} fa-italic"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button
                type="button"
                class="form__standard-icon-button"
                x-on:click="insertUnderline"
            >
                <abbr title="{{ __('livewire-interface.underline') }}">
                    <i class="{{ config('other.font-awesome') }} fa-underline"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button
                type="button"
                class="form__standard-icon-button"
                x-on:click="insertStrikethrough"
            >
                <abbr title="{{ __('livewire-interface.strikethrough') }}">
                    <i class="{{ config('other.font-awesome') }} fa-strikethrough"></i>
                </abbr>
            </button>
        </li>
        <hr class="bbcode-input__icon-separator" />
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertImage">
                <abbr title="{{ __('livewire-interface.insert-image') }}">
                    <i class="{{ config('other.font-awesome') }} fa-image"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertYoutube">
                <abbr title="{{ __('livewire-interface.insert-youtube') }}">
                    <i class="fab fa-youtube"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertUrl">
                <abbr title="{{ __('livewire-interface.link') }}">
                    <i class="{{ config('other.font-awesome') }} fa-link"></i>
                </abbr>
            </button>
        </li>
        <hr class="bbcode-input__icon-separator" />
        <li>
            <button
                type="button"
                class="form__standard-icon-button"
                x-on:click="insertUnorderedList"
            >
                <abbr title="{{ __('livewire-interface.unordered-list') }}">
                    <i class="{{ config('other.font-awesome') }} fa-list"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button
                type="button"
                class="form__standard-icon-button"
                x-on:click="insertOrderedList"
            >
                <abbr title="{{ __('livewire-interface.ordered-list') }}">
                    <i class="{{ config('other.font-awesome') }} fa-list-ol"></i>
                </abbr>
            </button>
        </li>
        <hr class="bbcode-input__icon-separator" />
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertColor">
                <abbr title="{{ __('livewire-interface.font-color') }}">
                    <i class="{{ config('other.font-awesome') }} fa-palette"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertSize">
                <abbr title="{{ __('livewire-interface.font-size') }}">
                    <i class="{{ config('other.font-awesome') }} fa-text-size"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button
                type="button"
                class="form__button form__button--text"
                x-on:click="insertFont"
            >
                <abbr title="{{ __('livewire-interface.font-family') }}">
                    {{ __('livewire-interface.font') }}
                </abbr>
            </button>
        </li>
        <hr class="bbcode-input__icon-separator" />
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertLeft">
                <abbr title="{{ __('livewire-interface.align-left') }}">
                    <i class="{{ config('other.font-awesome') }} fa-align-left"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertCenter">
                <abbr title="{{ __('livewire-interface.align-center') }}">
                    <i class="{{ config('other.font-awesome') }} fa-align-center"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertRight">
                <abbr title="{{ __('livewire-interface.align-right') }}">
                    <i class="{{ config('other.font-awesome') }} fa-align-right"></i>
                </abbr>
            </button>
        </li>
        <hr class="bbcode-input__icon-separator" />
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertQuote">
                <abbr title="{{ __('livewire-interface.quote') }}">
                    <i class="{{ config('other.font-awesome') }} fa-quote-right"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertCode">
                <abbr title="{{ __('common.code') }}">
                    <i class="{{ config('other.font-awesome') }} fa-code"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertSpoiler">
                <abbr title="{{ __('livewire-interface.spoiler') }}">
                    <i class="{{ config('other.font-awesome') }} fa-eye-slash"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertNote">
                <abbr title="{{ __('livewire-interface.note') }}">
                    <i class="{{ config('other.font-awesome') }} fa-sticky-note"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertAlert">
                <abbr title="{{ __('livewire-interface.alert') }}">
                    <i class="{{ config('other.font-awesome') }} fa-file-exclamation"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertTable">
                <abbr title="{{ __('livewire-interface.table') }}">
                    <i class="{{ config('other.font-awesome') }} fa-table"></i>
                </abbr>
            </button>
        </li>
        <li>
            <button type="button" class="form__standard-icon-button" x-on:click="insertEmoji">
                <abbr
                    title="{{ __('livewire-interface.emoji-picker-shortcut-hint') }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-face-smile"></i>
                </abbr>
            </button>
        </li>
    </menu>
    <div class="bbcode-input__tab-pane">
        <div class="bbcode-input__preview bbcode-rendered" x-show="isPreviewEnabled">
            @bbcode($contentBbcode)
        </div>
        <p class="form__group" x-show="isPreviewDisabled">
            <textarea
                id="bbcode-{{ $name }}"
                name="{{ $name }}"
                class="form__textarea bbcode-input__input"
                placeholder=" "
                x-bind="textarea"
                wire:model="contentBbcode"
                @required($isRequired)
            ></textarea>
            <label class="form__label form__label--floating" for="bbcode-{{ $name }}">
                {{ $label }}
            </label>
        </p>
    </div>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('{{ $name }}BbcodeInput', () => ({
                showButtons: false,
                bbcodePreviewHeight: null,
                isPreviewEnabled: @entangle('isPreviewEnabled').live,
                isOverInput: false,
                previousActiveElement: document.activeElement,
                toggleButtonVisibility() {
                    this.showButtons = !this.showButtons;
                },
                isPreviewDisabled() {
                    return !this.isPreviewEnabled;
                },
                textarea: {
                    ['x-ref']: 'bbcode',
                    ['x-on:mouseup']() {
                        if (this.isOverInput) {
                            this.bbcodePreviewHeight = this.$el.style.height;
                        }
                    },
                    ['x-on:mousedown']() {
                        this.previousActiveElement = document.activeElement;
                    },
                    ['x-on:mouseover']() {
                        this.isOverInput = true;
                    },
                    ['x-on:mouseleave']() {
                        this.isOverInput = false;
                    },
                    ['x-bind:style']() {
                        return {
                            height: this.bbcodePreviewHeight !== null && this.bbcodePreviewHeight,
                            transition:
                                this.previousActiveElement === this.$el
                                    ? 'none'
                                    : 'border-color 600ms cubic-bezier(0.25, 0.8, 0.25, 1), height 600ms cubic-bezier(0.25, 0.8, 0.25, 1)',
                        };
                    },
                    ['x-on:keydown.self.ctrl.b.prevent']() {
                        this.insertBold();
                    },
                    ['x-on:keydown.self.ctrl.i.prevent']() {
                        this.insertItalic();
                    },
                    ['x-on:keydown.self.ctrl.u.prevent']() {
                        this.insertUnderline();
                    },
                },
                insertBold() {
                    this.insert('[b]', '[/b]');
                },
                insertItalic() {
                    this.insert('[i]', '[/i]');
                },
                insertUnderline() {
                    this.insert('[u]', '[/u]');
                },
                insertStrikethrough() {
                    this.insert('[s]', '[/s]');
                },
                insertImage() {
                    this.insert('[img=350]', '[/img]');
                },
                insertYoutube() {
                    this.insert('[video=&quot;youtube&quot;]', '[/video]');
                },
                insertUrl() {
                    this.insert('[url]', '[/url]');
                },
                insertUnorderedList() {
                    this.insert('\n[list]\n[*]', '\n[/list]\n');
                },
                insertOrderedList() {
                    this.insert('\n[list=1]\n[*]', '\n[/list]\n');
                },
                insertColor() {
                    this.insert('[color=]', '[/color]');
                },
                insertSize() {
                    this.insert('[size=]', '[/size]');
                },
                insertFont() {
                    this.insert('[font=]', '[/font]');
                },
                insertLeft() {
                    this.insert('\n[left]\n', '\n[/left]\n');
                },
                insertCenter() {
                    this.insert('\n[center]\n', '\n[/center]\n');
                },
                insertRight() {
                    this.insert('\n[right]\n', '\n[/right]\n');
                },
                insertQuote() {
                    this.insert('\n[quote]\n', '\n[/quote]\n');
                },
                insertCode() {
                    this.insert('[code]', '[/code]');
                },
                insertSpoiler() {
                    this.insert('[spoiler]', '[/spoiler]');
                },
                insertNote() {
                    this.insert('[note]', '[/note]');
                },
                insertAlert() {
                    this.insert('[alert]', '[/alert]');
                },
                insertTable() {
                    this.insert('[table]\n[tr]\n[td]', '[/td]\n[/tr]\n[/table]');
                },
                insertEmoji() {
                    Swal.fire({
                        title: @js(__('livewire-interface.emoji-picker')),
                        html: @js(__('livewire-interface.emoji-picker-shortcut-hint-html')),
                        icon: 'info',
                        showConfirmButton: true,
                    });
                },
                insert(openTag, closeTag) {
                    input = this.$refs.bbcode;
                    start = input.selectionStart;
                    end = input.selectionEnd;
                    alreadyNested =
                        input.value.substring(start, start + openTag.length) === openTag &&
                        input.value.substring(end - closeTag.length, end) === closeTag;
                    if (alreadyNested) {
                        input.value =
                            input.value.substring(0, start) +
                            input.value.substring(start + openTag.length, end - closeTag.length) +
                            input.value.substring(end);
                    } else {
                        input.value =
                            input.value.substring(0, start) +
                            openTag +
                            input.value.substring(start, end) +
                            closeTag +
                            input.value.substring(end);
                    }
                    input.dispatchEvent(new Event('input'));
                    input.focus();
                    if (openTag.charAt(openTag.length - 2) === '=') {
                        input.setSelectionRange(
                            start + openTag.length - 1,
                            start + openTag.length - 1,
                        );
                    } else if (start == end) {
                        input.setSelectionRange(start + openTag.length, end + openTag.length);
                    } else {
                        if (alreadyNested) {
                            input.setSelectionRange(start, end - openTag.length - closeTag.length);
                        } else {
                            input.setSelectionRange(start, end + openTag.length + closeTag.length);
                        }
                    }
                },
            }));
        });
    </script>
</div>
