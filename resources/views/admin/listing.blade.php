@extends('layouts.admin')

@section('title', 'Manajemen Listing')

@section('content')
  <h1 class="text-2xl font-bold text-slate-800 mb-6">📚 Manajemen Listing</h1>

  @if (session('success'))
    <div class="p-3 mb-4 text-green-800 bg-green-100 border border-green-300 rounded-lg">
      {{ session('success') }}
    </div>
  @endif

  <div class="overflow-x-auto bg-white shadow-sm border border-slate-200 rounded-xl">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-100 text-slate-700">
        <tr>
          <th class="p-3">Judul</th>
          <th class="p-3">Guru</th>
          <th class="p-3">Region</th>
          <th class="p-3">Status</th>
          <th class="p-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($listing as $it)
          <tr class="border-t hover:bg-slate-50">
            <td class="p-3 font-medium">{{ $it->title }}</td>
            <td class="p-3">{{ $it->user->name }}</td>
            <td class="p-3">{{ $it->region->name ?? '-' }}</td>
            <td class="p-3">
              <form method="POST" action="{{ route('admin.listings.updateStatus', $it) }}">
                @csrf @method('PATCH')
                <select name="status" class="border rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-400"
                  onchange="this.form.submit()">
                  <option value="active" @selected($it->status === 'active')>Active</option>
                  <option value="inactive" @selected($it->status === 'inactive')>Inactive</option>
                </select>
              </form>
            </td>
            <td class="p-3">
              <form method="POST" action="{{ route('admin.listings.destroy', $it) }}"
                onsubmit="return confirm('Hapus listing ini?')">
                @csrf @method('DELETE')
                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-sm transition">
                  Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="p-4 text-center text-slate-500">Belum ada listing.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $listing->links() }}
  </div>
@endsection
