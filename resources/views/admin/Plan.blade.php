@extends('layouts.admin')

@section('title', 'Kelola Plan')

@section('content')
  <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
    <h1 class="text-2xl font-bold text-slate-800">💳 Kelola Plan</h1>
    <a href="{{ route('admin.plans.create') }}"
      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
      + Tambah Plan
    </a>
  </div>

  @if (session('success'))
    <div class="p-3 mb-4 text-green-800 bg-green-100 border border-green-300 rounded-lg">
      {{ session('success') }}
    </div>
  @endif

  <div class="overflow-x-auto bg-white border border-slate-200 shadow-sm rounded-xl">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-100 text-slate-700">
        <tr>
          <th class="p-3">Nama</th>
          <th class="p-3">Harga</th>
          <th class="p-3">Durasi</th>
          <th class="p-3">Kuota</th>
          <th class="p-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($plans as $p)
          <tr class="border-t hover:bg-slate-50">
            <td class="p-3 font-medium">{{ $p->name }}</td>
            <td class="p-3">Rp{{ number_format($p->price, 0, ',', '.') }}</td>
            <td class="p-3">{{ $p->duration_days }} hari</td>
            <td class="p-3">{{ $p->quota_region ?? '-' }}</td>
            <td class="p-3">
              <div class="flex gap-2 flex-wrap">
                <a href="{{ route('admin.plans.edit', $p) }}"
                  class="bg-slate-700 hover:bg-slate-800 text-white px-3 py-1.5 rounded-md text-sm transition">
                  Edit
                </a>
                <form method="POST" action="{{ route('admin.plans.destroy', $p) }}"
                  onsubmit="return confirm('Hapus plan ini?')">
                  @csrf @method('DELETE')
                  <button
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-sm transition">
                    Hapus
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="p-4 text-center text-slate-500">Belum ada plan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $plans->links() }}
  </div>
@endsection
