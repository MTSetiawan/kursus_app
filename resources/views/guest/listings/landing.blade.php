@extends('layouts.public')

@section('content')
    <section class="hero">
        <div class="container" style="text-align:center">
            <h1 class="h1">Temukan Guru Les Privat Terbaik</h1>
            <p class="muted">Cari berdasarkan mata pelajaran & kecamatan</p>
            <p style="margin-top:16px">
                <a class="btn primary" href="{{ route('catalog') }}">Cari Sekarang</a>
                <a class="btn accent" href="{{ route('register') }}" style="margin-left:8px">Gabung Jadi Guru</a>
            </p>
        </div>
    </section>

    <section class="container">
        <h2 class="h2">Terbaru</h2>
        <div class="grid grid-3">
            @forelse($listings as $listing)
                <x-listing-card :listing="$listing" />
            @empty
                <x-empty-state message="Belum ada listing aktif." />
            @endforelse
        </div>
    </section>
@endsection
