<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
    </url>
    <url>
        <loc>{{ route('categories.index') }}</loc>
    </url>
    @foreach ($categories as $category)
        <url>
            <loc>{{ route('categories.show', $category) }}</loc>
        </url>
    @endforeach
    @foreach ($topics as $topic)
        <url>
            <loc>{{ route('topics.show', $topic) }}</loc>
        </url>
    @endforeach
</urlset>
