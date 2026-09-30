@php
    echo '<?xml version="1.0" encoding="UTF-8" ?>';
@endphp
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <title>{{ config('other.title') }}: {{ $rss->name }}</title>
        <link>{{ config('app.url') }}</link>
        <description>
            {{ __('media-interface.rss.feed-disclaimer') }}
        </description>
        <atom:link href="{{ route('rss.show.rsskey', ['id' => $rss->id, 'rsskey' => $user->rsskey]) }}"
                   type="application/rss+xml" rel="self"></atom:link>
        <copyright>{{ config('other.title') }} {{ now()->year }}</copyright>
        <language>{{ app()->getLocale() }}</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <ttl>5</ttl>
        @if($torrents)
            @foreach($torrents as $torrent)
                <item>
                    <title>{{ $torrent['name'] }}</title>
                    <category>{{ $torrent['category']['name'] }}</category>
                    <contentlength>{{ $torrent['size'] }}</contentlength>
                    <link>{{ route('torrent.download.rsskey', ['id' => $torrent['id'], 'rsskey' => $user->rsskey ]) }}</link>
                    <guid>{{ $torrent['id'] }}</guid>
                    <description>
                        <![CDATA[<p>
                            <strong>{{ __('common.name') }}</strong>: {{ $torrent['name'] }}<br>
                            <strong>{{ __('common.category') }}</strong>: {{ $torrent['category']['name'] }}<br>
                            <strong>{{ __('common.type') }}</strong>: {{ $torrent['type']['name'] }}<br>
                            <strong>{{ __('common.resolution') }}</strong>: {{ $torrent['resolution']['name'] ?? __('media-interface.rss.no-resolution') }}<br>
                            <strong>{{ __('torrent.size') }}</strong>: {{ App\Helpers\StringHelper::formatBytes($torrent['size'], 2) }}<br>
                            <strong>{{ __('common.uploaded') }}</strong>: {{ \Illuminate\Support\Carbon::createFromTimestampUTC($torrent['created_at'])->diffForHumans() }}<br>
                            <strong>{{ __('torrent.seeders') }}</strong>: {{ $torrent['seeders'] }} |
                            <strong>{{ __('torrent.leechers') }}</strong>: {{ $torrent['leechers'] }} |
                            <strong>{{ __('torrent.completed-times') }}</strong>: {{ $torrent['times_completed'] }}<br>
                            <strong>{{ __('torrent.uploader') }}</strong>:
                            @if(!$torrent['anon'] && $torrent['user'])
                                {{ __('torrent.uploaded-by') }} {{ $torrent['user']['username'] }}
                            @else
                                {{ __('common.anonymous') }} {{ __('torrent.uploader') }}
                            @endif<br>
                            @if (($torrent['category']['movie_meta'] || $torrent['category']['tv_meta']) && $torrent['imdb'] != 0)
                                {{ __('media-interface.rss.imdb-link') }}<a href="https://anon.to?http://www.imdb.com/title/tt{{ \str_pad((string) $torrent['imdb'], 7, '0', STR_PAD_LEFT) }}"
                                             target="_blank">tt{{ $torrent['imdb'] }}</a><br>
                            @endif
                            @if ($torrent['category']['movie_meta'] && $torrent['tmdb_movie_id'] > 0)
                                {{ __('media-interface.rss.tmdb-link') }} <a href="https://anon.to?https://www.themoviedb.org/movie/{{ $torrent['tmdb_movie_id'] }}"
                                              target="_blank">{{ $torrent['tmdb_movie_id'] }}</a><br>
                            @elseif ($torrent['category']['tv_meta'] && $torrent['tmdb_tv_id'] > 0)
                                {{ __('media-interface.rss.tmdb-link') }} <a href="https://anon.to?https://www.themoviedb.org/tv/{{ $torrent['tmdb_tv_id'] }}"
                                              target="_blank">{{ $torrent['tmdb_tv_id'] }}</a><br>
                            @endif
                            @if (($torrent['category']['tv_meta']) && $torrent['tvdb'] != 0)
                                {{ __('media-interface.rss.tvdb-link') }}<a href="https://anon.to?https://www.thetvdb.com/?tab=series&id={{ $torrent['tvdb'] }}"
                                             target="_blank">{{ $torrent['tvdb'] }}</a><br>
                            @endif
                            @if (($torrent['category']['movie_meta'] || $torrent['category']['tv_meta']) && $torrent['mal'] != 0)
                                {{ __('media-interface.rss.mal-link') }}<a href="https://anon.to?https://myanimelist.net/anime/{{ $torrent['mal'] }}"
                                             target="_blank">{{ $torrent['mal'] }}</a><br>
                            @endif
                            @if ($torrent['internal'] == 1)
                                <comments>{{ __('torrent.internal-release') }}</comments>
                            @endif
                        </p>]]>
                    </description>
                    <dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/">
                        @if(!$torrent['anon'] && $torrent['user'])
                            {{ __('torrent.uploaded-by') }} {{ $torrent['user']['username'] }}
                        @else
                            {{ __('common.anonymous') }} {{ __('torrent.uploader') }}
                        @endif
                    </dc:creator>
                    <pubDate>{{ \Illuminate\Support\Carbon::createFromTimestampUTC($torrent['created_at'])->toRssString() }}</pubDate>
                </item>
            @endforeach
        @endif
    </channel>
</rss>
