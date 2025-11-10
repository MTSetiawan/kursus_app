@props(['listing'])
<article class="card pad">
  <div class="badge">📍 {{ $listing->region->name }}</div>
  <h3 class="title" style="margin-top:8px">{{ $listing->title }}</h3>
  <div class="muted" style="font-size:13px">{{ $listing->category }}</div>
  <div style="margin:8px 0;font-weight:700">
    Rp{{ number_format($listing->price,0,',','.') }}/jam
  </div>
  @if(isset($listing->wa_clicks) || isset($listing->views))
    <div class="muted" style="font-size:12px">
      @isset($listing->wa_clicks) {{ $listing->wa_clicks }} klik WA @endisset
      @if(isset($listing->wa_clicks) && isset($listing->views)) • @endif
      @isset($listing->views) {{ $listing->views }} tayangan @endisset
    </div>
  @endif
  <p style="margin-top:12px">
    <a class="btn" href="{{ route('listing.show', [$listing->slug, $listing->region->slug]) }}">Lihat detail</a>
  </p>
</article>
