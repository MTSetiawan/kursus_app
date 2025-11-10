@extends('layouts.public')

@section('content')
    <section class="container">
        <h1 class="h1">Daftar Guru Les</h1>

        <form method="GET" action="{{ route('catalog') }}" class="grid grid-3" style="margin:12px 0 18px">
            <div>
                <label class="muted" style="font-size:12px">Kecamatan</label>
                <select name="region" class="select">
                    <option value="">Semua</option>
                    @foreach ($regions as $r)
                        <option value="{{ $r->id }}" @selected(($filters['region'] ?? null) == $r->id)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="muted" style="font-size:12px">Cari Kursus</label>
                <input class="input" name="title" value="{{ $filters['title'] ?? '' }}" placeholder="Cari...">
            </div>
            <div style="display:flex;align-items:flex-end">
                <button class="btn primary" style="width:100%">Cari</button>
            </div>
        </form>

        <div class="grid grid-3">
            @forelse($items as $listing)
                <x-listing-card :listing="$listing" />
            @empty
                <x-empty-state message="Tidak ditemukan hasil." />
            @endforelse
        </div>

        <div style="margin-top:16px">
            {{ $items->links() }}
        </div>
    </section>
@endsection
