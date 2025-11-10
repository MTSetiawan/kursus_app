@extends('layouts.admin')

@php($isEdit = $plan->exists)

@section('title', $isEdit ? 'Edit Plan' : 'Tambah Plan')

@section('content')
  <h1 class="text-2xl font-bold text-slate-800 mb-6">
    {{ $isEdit ? '✏️ Edit Plan' : '➕ Tambah Plan' }}
  </h1>

  <form method="POST"
    action="{{ $isEdit ? route('admin.plans.update', $plan) : route('admin.plans.store') }}"
    class="bg-white shadow-sm border border-slate-200 rounded-xl p-6 max-w-xl space-y-4">
    @csrf
    @if ($isEdit)
      @method('PUT')
    @endif

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Nama Plan</label>
      <input class="border border-slate-300 rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
        name="name" value="{{ old('name', $plan->name) }}">
      @error('name')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp)</label>
        <input class="border border-slate-300 rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
          type="number" name="price" min="0" value="{{ old('price', $plan->price) }}">
        @error('price')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Durasi (hari)</label>
        <input class="border border-slate-300 rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
          type="number" name="duration_days" min="1" value="{{ old('duration_days', $plan->duration_days) }}">
        @error('duration_days')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Kuota Region/Listing</label>
      <input class="border border-slate-300 rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
        type="number" name="quota_region" min="0" value="{{ old('quota_region', $plan->quota_region) }}">
      @error('quota_region')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="flex gap-2 pt-4">
      <a class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-100 transition"
        href="{{ route('admin.plans.index') }}">Batal</a>
      <button
        class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
        {{ $isEdit ? 'Update' : 'Simpan' }}
      </button>
    </div>
  </form>
@endsection
