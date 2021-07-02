@php
    echo '<?xml version="1.0" encoding="UTF-8" ?>'
@endphp
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <title>{{ config('other.title') }}: {{ $rss->name }}</title>
        <link>{{ config('app.url') }}</link>
        <description>
            <![CDATA[This feed contains your secure RSS PID, please do not share with anyone.]]>
        </description>
        <atom:link href="{{ route('rss.show.rsskey', ['id' => $rss->id, 'rsskey' => $user->rsskey]) }}" type="application/rss+xml" rel="self"></atom:link>
        <copyright>{{ config('other.title') }} {{ now()->year }}</copyright>
        <language>en</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <ttl>600</ttl>
        @if($torrents)
            @foreach($torrents as $data)
                <item>
                    <title>{{ $data->name }}</title>
                    <category>{{ $data->category->name }}</category>
                    <link>{{ route('torrent', ['id' => $data->id ]) }}</link>
                    <guid isPermaLink="true">{{ route('torrent', ['id' => $data->id ]) }}</guid>
                    <description><![CDATA[<p>
                            <strong>Name</strong>: {{ $data->name }}<br>
                            <strong>Category</strong>: {{ $data->category->name }}<br>
                            <strong>Type</strong>: {{ $data->type->name }}<br>
                            <strong>Resolution</strong>: {{ $data->resolution->name ?? 'No Res' }}<br>
                            <strong>Size</strong>: {{ $data->getSize() }}<br>
                            <strong>Uploaded</strong>: {{ $data->created_at->diffForHumans() }}<br>
                            <strong>Seeders</strong>: {{ $data->seeders }} | <strong>Leechers</strong>: {{ $data->leechers }} | <strong>Completed</strong>: {{ $data->times_completed }}<br>
                            <strong>Uploader</strong>:
                            @if(!$data->anon && $data->user)
                                @lang('torrent.uploaded-by') {{ $data->user->username }}
                            @else
                                @lang('common.anonymous') @lang('torrent.uploader')
                            @endif<br>
                            @if (($data->category->movie_meta || $data->category->tv_meta) && $data->imdb != 0)
                                IMDB Link:<a href="https://anon.to?http://www.imdb.com/title/tt{{ $data->imdb }}" target="_blank">tt{{ $data->imdb }}</a><br>
                            @endif
                            @if ($data->category->movie_meta && $data->tmdb != 0)
                                TMDB Link: <a href="https://anon.to?https://www.themoviedb.org/movie/{{ $data->tmdb }}" target="_blank">{{ $data->tmdb }}</a><br>
                            @elseif ($data->category->tv_meta && $data->tmdb != 0)
                                TMDB Link: <a href="https://anon.to?https://www.themoviedb.org/tv/{{ $data->tmdb }}" target="_blank">{{ $data->tmdb }}</a><br>
                            @endif
                        </p>]]>
                    </description>
                    <dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/">
                        @if(!$data->anon && $data->user)
                            @lang('torrent.uploaded-by') {{ $data->user->username }}
                        @else
                            @lang('common.anonymous') @lang('torrent.uploader')
                        @endif
                    </dc:creator>
                    <pubDate>{{ $data->created_at->toRssString() }}</pubDate>
                    <enclosure
                            url="{{ route('torrent.download.rsskey', ['id' => $data->id, 'rsskey' => $user->rsskey ]) }}"
                            type="application/x-bittorrent"
                            length="39399"
                    />
                </item>
            @endforeach
        @endif
    </channel>
</rss>