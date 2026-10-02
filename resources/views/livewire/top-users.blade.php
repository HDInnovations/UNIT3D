<section class="panelV2 blocks__uploaders" x-data="tabs($wire.entangle('tab').live)">
    <h2 class="panel__heading">Top users</h2>
    <menu class="panel__tabs">
        <li x-bind="tabButton" data-tab="uploaders">Uploaders</li>
        <li x-bind="tabButton" data-tab="downloaders">Downloaders</li>
        <li x-bind="tabButton" data-tab="uploaded">Uploaded</li>
        <li x-bind="tabButton" data-tab="downloaded">Downloaded</li>
        <li x-bind="tabButton" data-tab="seeders">Seeders</li>
        <li x-bind="tabButton" data-tab="seedtime">Seedtime</li>
        <li x-bind="tabButton" data-tab="served">Users served</li>
        <li x-bind="tabButton" data-tab="commenters">Commenters</li>
        <li x-bind="tabButton" data-tab="posters">Posters</li>
        @if (config('other.thanks-system.is-enabled'))
            <li x-bind="tabButton" data-tab="thanked">Thanked</li>
            <li x-bind="tabButton" data-tab="thankers">Thankers</li>
        @endif

        <li x-bind="tabButton" data-tab="personals">Personal releases</li>
    </menu>
    <div class="panel__body" wire:loading.block>Loading...</div>
    <div class="panel__body" wire:loading.remove>
        <div class="user-stat-card-container">
            @switch($this->tab)
                @case('uploaders')
                    @foreach ($this->uploaders as $uploader)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$uploader->user"
                                    :anon="$uploader->user->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">{{ $uploader->value }} uploads</h4>

                            @if ($uploader->user->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                    loading="lazy"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $uploader->user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $uploader->user]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('downloaders')
                    @foreach ($this->downloaders as $downloader)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$downloader->user"
                                    :anon="$downloader->user->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">
                                {{ $downloader->value }} downloads
                            </h4>

                            @if ($downloader->user->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $downloader->user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $downloader->user]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('uploaded')
                    @foreach ($this->uploaded as $upload)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$upload"
                                    :anon="$upload->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">
                                {{ App\Helpers\StringHelper::formatBytes($upload->uploaded, 2) }}
                                Uploaded
                            </h4>

                            @if ($upload->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $upload->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $upload]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('downloaded')
                    @foreach ($this->downloaded as $download)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$download"
                                    :anon="$download->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">
                                {{ App\Helpers\StringHelper::formatBytes($download->downloaded, 2) }}
                                Downloaded
                            </h4>

                            @if ($download->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $download->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $download]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('seeders')
                    @foreach ($this->seeders as $seeder)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$seeder->user"
                                    :anon="$seeder->user->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">{{ $seeder->value }} seeds</h4>

                            @if ($seeder->user->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $seeder->user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $seeder->user]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('seedtime')
                    @foreach ($this->seedtimes as $seedtime)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$seedtime"
                                    :anon="$seedtime->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">
                                {{ App\Helpers\StringHelper::timeElapsed($seedtime->seedtime ?? 0) }}
                                {{ __('user.total-seedtime') }}
                            </h4>

                            @if ($seedtime->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $seedtime->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $seedtime]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('served')
                    @foreach ($this->served as $serve)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$serve"
                                    :anon="$serve->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">
                                {{ $serve->upload_snatches_count }} users served
                            </h4>

                            @if ($serve->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $serve->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $serve]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('commenters')
                    @foreach ($this->commenters as $commenter)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$commenter->user"
                                    :anon="$commenter->user->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">
                                {{ $commenter->value }} comments made
                            </h4>

                            @if ($commenter->user->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $commenter->user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $commenter->user]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('posters')
                    @foreach ($this->posters as $poster)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$poster->user"
                                    :anon="$poster->user->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">{{ $poster->value }} posts made</h4>

                            @if ($poster->user->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $poster->user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $poster->user]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('thanked')
                    @foreach ($this->thanked as $thanked)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$thanked->user"
                                    :anon="$thanked->user->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">
                                {{ $thanked->value }} thanks received
                            </h4>

                            @if ($thanked->user->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $thanked->user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $thanked->user]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('thankers')
                    @foreach ($this->thankers as $thanker)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$thanker->user"
                                    :anon="$thanker->user->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">
                                {{ $thanker->value }} thanks given
                            </h4>

                            @if ($thanker->user->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $thanker->user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $thanker->user]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
                @case('personals')
                    @foreach ($this->personals as $personal)
                        <article class="user-stat-card">
                            <h3 class="user-stat-card__username">
                                <x-user-tag
                                    :user="$personal->user"
                                    :anon="$personal->user->privacy?->private_profile"
                                />
                                <div title="Place" class="top-users__place">
                                    {{ Number::ordinal($loop->iteration) }}
                                </div>
                            </h3>
                            <h4 class="user-stat-card__stat">
                                {{ $personal->value }} personal releases
                            </h4>

                            @if ($personal->user->privacy?->private_profile)
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ url('img/profile.png') }}"
                                />
                            @else
                                <img
                                    class="user-stat-card__avatar"
                                    alt=""
                                    src="{{ $personal->user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $personal->user]) }}"
                                />
                            @endif
                        </article>
                    @endforeach

                    @break
            @endswitch
        </div>
    </div>
</section>
