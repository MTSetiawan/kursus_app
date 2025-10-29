@extends('layouts.app')

@section('content')
    {{-- Plan Banner --}}
    <x-plan-banner :plan="$planName ?? 'bronze'" :used="$used ?? 0" :limit="$limit ?? 3" :endDate="$endDate" />

    {{-- Listings --}}
    <div class="flex items-center justify-between mb-4 mt-8">
        <h2 class="text-xl font-semibold">My Listings</h2>
        <button id="openCreateListing"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            + New Listing
        </button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse(($listings ?? collect()) as $item)
            <x-listing-card-teacher :listing="$item" />
        @empty
            <x-empty-state message="Belum ada listing." />
        @endforelse
    </div>

    {{-- Upgrade Modal --}}
    <div id="upgrade-modal" class="modal-backdrop" hidden aria-hidden="true">
        <div class="modal-scrim" data-close="upgrade-modal"></div>
        <div class="modal-card card pad bg-white p-6 rounded-xl shadow-xl w-full max-w-3xl"
            role="dialog" aria-modal="true" aria-labelledby="upgrade-title">
            <div class="flex justify-between items-center mb-4">
                <h2 id="upgrade-title" class="text-lg font-semibold">Paket Langganan</h2>
                <button class="btn" type="button" data-close="upgrade-modal">Tutup</button>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                @foreach ($plans as $plan)
                    <article class="card pad bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
                        <div class="text-lg font-semibold text-gray-800">{{ $plan->name }}</div>
                        @if ($plan->benefits)
                            <div class="text-sm text-gray-500 mt-1">
                                {{ is_array($plan->benefits) ? implode(' · ', $plan->benefits) : $plan->benefits }}
                            </div>
                        @endif
                        <div class="text-2xl font-bold mt-2 text-indigo-600">
                            {{ $plan->quota_listings ?? ($plan->quota_region ?? '-') }}
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            @if ((int) $plan->price === 0)
                                Gratis ({{ $plan->duration_days }} hari)
                            @else
                                Rp{{ number_format($plan->price, 0, ',', '.') }}/{{ $plan->duration_days }} hari
                            @endif
                        </div>

                        <form method="POST" action="{{ route('plan-requests.store') }}" class="mt-3">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-md text-sm font-medium transition">
                                Kirim Request {{ $plan->name }}
                            </button>
                        </form>

                        <a class="block text-center mt-2 px-3 py-2 text-sm rounded-md bg-gray-100 hover:bg-gray-200 transition"
                            href="{{ route('plans.wa', $plan) }}">
                            Chat Admin via WA
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
    
    
@endsection
