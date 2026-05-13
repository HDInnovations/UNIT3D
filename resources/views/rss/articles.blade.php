@php
    echo '<?xml version="1.0" encoding="UTF-8" ?>';
@endphp
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title>{{ config('other.title') }}: {{ __('common.news') }}</title>
        <link>{{ route('articles.index') }}</link>
        <description>{{ __('articles.meta-articles') }}</description>
        <atom:link href="{{ route('rss.articles.rsskey', ['rsskey' => $user->rsskey]) }}"
                   type="application/rss+xml" rel="self"></atom:link>
        <copyright>{{ config('other.title') }} {{ now()->year }}</copyright>
        <language>en-us</language>
        <lastBuildDate>{{ $articles->first()?->created_at?->toRssString() ?? now()->toRssString() }}</lastBuildDate>
        <ttl>5</ttl>
        @foreach ($articles as $article)
            @php
                $description = preg_replace('#\[[^\]]+\]#', '', Str::limit(strip_tags($article->content), 500, '...'));
                $description = str_replace(']]>', ']]&gt;', $description ?? '');
                $content = (new \hdvinnie\LaravelJoyPixels\LaravelJoyPixels())
                    ->toImage((new \App\Helpers\Linkify())->linky((new \App\Helpers\Bbcode())->parse($article->content)));
                $content = str_replace(']]>', ']]&gt;', $content);
            @endphp
            <item>
                <title>{{ $article->title }}</title>
                <link>{{ route('articles.show', ['article' => $article]) }}</link>
                <guid isPermaLink="false">article-{{ $article->id }}</guid>
                <description><![CDATA[{!! $description !!}]]></description>
                <content:encoded><![CDATA[{!! $content !!}]]></content:encoded>
                <dc:creator>{{ $article->user->username }}</dc:creator>
                <pubDate>{{ $article->created_at->toRssString() }}</pubDate>
            </item>
        @endforeach
    </channel>
</rss>
