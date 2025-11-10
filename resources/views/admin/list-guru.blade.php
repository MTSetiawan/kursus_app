@extends('layouts.admin')

@section('title', 'Daftar Guru')

@section('content')
  <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-slate-800">👤 Daftar Guru</h1>

    <form method="GET" class="flex items-center gap-2 w-full sm:w-auto">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / email..."
        class="border border-slate-300 rounded-lg px-3 py-2 w-full sm:w-64 focus:ring-2 focus:ring-blue-500 outline-none">
      <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Cari</button>
    </form>
  </div>

  @if (session('success'))
    <div class="p-3 mb-4 text-green-800 bg-green-100 border border-green-300 rounded-lg">
      {{ session('success') }}
    </div>
  @endif

  <div class="overflow-x-auto bg-white shadow-sm border border-slate-200 rounded-xl">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-100 text-slate-700">
        <tr>
          <th class="p-3">Nama</th>
          <th class="p-3">Email</th>
          <th class="p-3">Plan Aktif</th>
          <th class="p-3">Masa Aktif</th>
          <th class="p-3">Status</th>
          <th class="p-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
          @php
            $activePlan = $u->activePlan;
            $planName = $activePlan?->plan?->name ?? '-';
            $endDate = $activePlan?->end_date;
            $remaining = $endDate ? now()->diffInDays(\Carbon\Carbon::parse($endDate), false) : null;
          @endphp
          <tr class="border-t hover:bg-slate-50">
            <td class="p-3 font-medium">{{ $u->name }}</td>
            <td class="p-3">{{ $u->email }}</td>
            <td class="p-3">{{ $planName }}</td>
            <td class="p-3">
              @if ($endDate)
                <div>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
                <div class="text-xs text-slate-500">
                  {{ $remaining > 0 ? $remaining . ' hari tersisa' : 'expired' }}
                </div>
              @else
                <span class="text-slate-400">—</span>
              @endif
            </td>
            <td class="p-3">
              @if ($u->is_banned ?? false)
                <span class="px-2 py-1 rounded-md text-sm bg-red-100 text-red-700">Banned</span>
              @else
                <span class="px-2 py-1 rounded-md text-sm bg-green-100 text-green-700">Active</span>
              @endif
            </td>
            <td class="p-3">
              <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.users.toggleBan', $u) }}">
                  @csrf
                  <button
                    class="px-3 py-1.5 rounded-md text-sm {{ $u->is_banned ?? false ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white transition">
                    {{ $u->is_banned ?? false ? 'Unban' : 'Ban' }}
                  </button>
                </form>

                <a class="px-3 py-1.5 rounded-md text-sm bg-slate-700 hover:bg-slate-800 text-white transition"
                  href="{{ route('admin.listings.index', ['user' => $u->id]) }}">
                  Lihat Listing
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="p-4 text-center text-slate-500">Belum ada guru.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $users->withQueryString()->links() }}
  </div>
@endsection
