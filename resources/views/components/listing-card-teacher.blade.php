@props(['listing'])
<div class="card pad" style="display:flex;justify-content:space-between;align-items:center">
    <div>
        <div class="h2" style="margin:0">{{ $listing->title }}</div>
        <div class="muted">{{ $listing->region->name }} • Rp{{ number_format($listing->price, 0, ',', '.') }}/jam</div>
        <div class="muted" style="font-size:12px;margin-top:4px">
            Status: {{ $listing->status }}
            @isset($listing->views)
                • Views {{ $listing->views }}
            @endisset
            @isset($listing->wa_clicks)
                • WA {{ $listing->wa_clicks }}
            @endisset
        </div>
    </div>
    <div style="display:flex;gap:8px">
        <a class="btn primary" href="{{ route('listings.edit', ['listing' => $listing->slug]) }}">Edit</a>
        <form method="POST" action="{{ route('listings.destroy', ['listing' => $listing->slug]) }}">
            @csrf @method('DELETE')
            <button class="btn">Delete</button>
        </form>
    </div>
</div>
