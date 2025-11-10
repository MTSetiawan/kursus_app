@extends('layouts.public')

@section('content')
    <div class="container grid" style="grid-template-columns:2fr 1fr">
        <section class="space-y-3">
            <h1 class="h1">{{ $listing->title }}</h1>
            <div class="muted">{{ $listing->category }} • {{ $listing->region->name }}</div>

            @if (!empty($listing->description))
                <div class="card pad">{!! nl2br(e($listing->description)) !!}</div>
            @endif

            <div class="card pad">
                <div class="h2">Harga</div>
                Rp{{ number_format($listing->price, 0, ',', '.') }}/jam
            </div>
        </section>

        <aside class="grid" style="gap:16px">
            <div class="card pad">
                <div class="h2" style="margin-bottom:6px">Kontak Guru</div>
                <div class="muted">{{ $listing->user->name }}</div>
                @if (optional($listing->user->teacherProfile)->location)
                    <div class="muted" style="font-size:12px">{{ $listing->user->teacherProfile->location }}</div>
                @endif
                <div style="margin-top:12px">
                    <x-wa-button :href="route('go.wa', $listing->slug)" />
                </div>
                @if (isset($listing->views) || isset($listing->wa_clicks))
                    <div class="muted" style="font-size:12px;margin-top:8px">
                        @isset($listing->views)
                            {{ (int) $listing->views }} tayangan
                        @endisset
                        @if (isset($listing->views) && isset($listing->wa_clicks))
                            •
                        @endif
                        @isset($listing->wa_clicks)
                            {{ (int) $listing->wa_clicks }} klik WA
                        @endisset
                    </div>
                @endif
            </div>
        </aside>
    </div>

    @push('head')
        <title>Les {{ $listing->category }} di {{ $listing->region->name }} | {{ $listing->user->name }}</title>
        <meta name="description"
            content="Guru les {{ strtolower($listing->category) }} di {{ $listing->region->name }}. Tarif Rp{{ number_format($listing->price, 0, ',', '.') }}/jam. Hubungi {{ $listing->user->name }} via WhatsApp.">
        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="robots" content="index, follow">
        <script type="application/ld+json">
{!! json_encode([
  '@context'=>'https://schema.org',
  '@type'=>'Service',
  'name'=>"Les {$listing->category} di {$listing->region->name}",
  'provider'=>['@type'=>'Person','name'=>$listing->user->name],
  'areaServed'=>$listing->region->name,
  'offers'=>['@type'=>'Offer','priceCurrency'=>'IDR','price'=>(string)$listing->price],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
    @endpush
@endsection
