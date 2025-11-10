@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-slate-800 mb-6">📊 Dashboard Admin</h1>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white shadow-sm rounded-xl p-5 border border-slate-100">
      <div class="text-sm text-slate-500">Total Guru</div>
      <div class="text-3xl font-semibold text-blue-600 mt-1">{{ $totalGuru ?? 0 }}</div>
    </div>

    <div class="bg-white shadow-sm rounded-xl p-5 border border-slate-100">
      <div class="text-sm text-slate-500">Listing Aktif</div>
      <div class="text-3xl font-semibold text-blue-600 mt-1">{{ $listingAktif ?? 0 }}</div>
    </div>

    <div class="bg-white shadow-sm rounded-xl p-5 border border-slate-100">
      <div class="text-sm text-slate-500">Pending Request</div>
      <div class="text-3xl font-semibold text-blue-600 mt-1">{{ $reqPending ?? 0 }}</div>
    </div>

    <div class="bg-white shadow-sm rounded-xl p-5 border border-slate-100">
      <div class="text-sm text-slate-500">Pendapatan Hari Ini</div>
      <div class="text-3xl font-semibold text-green-600 mt-1">
        Rp{{ number_format($todayRevenue ?? 0, 0, ',', '.') }}
      </div>
    </div>
  </div>
@endsection
