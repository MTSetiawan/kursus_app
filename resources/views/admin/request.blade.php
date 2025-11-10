@extends('layouts.admin')

@section('title', 'Plan Requests')

@section('content')
  <h1 class="text-2xl font-bold text-slate-800 mb-6">📨 Daftar Request Upgrade Plan</h1>

  @if (session('success'))
    <div class="p-3 mb-4 text-green-800 bg-green-100 border border-green-300 rounded-lg">
      {{ session('success') }}
    </div>
  @endif

  <form method="GET" class="mb-6 flex flex-wrap gap-2 items-center">
    <label class="text-slate-600 text-sm font-medium">Filter:</label>
    <select name="status" onchange="this.form.submit()"
      class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
      <option value="">Semua Status</option>
      @foreach (['requested', 'approved', 'rejected'] as $s)
        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
  </form>

  <div class="overflow-x-auto bg-white border border-slate-200 shadow-sm rounded-xl">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-100 text-slate-700">
        <tr>
          <th class="p-3">Tanggal</th>
          <th class="p-3">User</th>
          <th class="p-3">Plan</th>
          <th class="p-3">Status</th>
          <th class="p-3">Catatan</th>
          <th class="p-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reqs as $r)
          <tr class="border-t hover:bg-slate-50">
            <td class="p-3">{{ $r->created_at->format('d M Y H:i') }}</td>
            <td class="p-3">
              <div class="font-medium">{{ $r->user->name }}</div>
              <div class="text-xs text-slate-500">{{ $r->user->email }}</div>
            </td>
            <td class="p-3">
              {{ $r->plan->name }}<br>
              <span class="text-xs text-slate-500">Rp{{ number_format($r->plan->price, 0, ',', '.') }}</span>
            </td>
            <td class="p-3">
              @if ($r->status === 'requested')
                <span class="px-2 py-1 text-sm bg-yellow-100 text-yellow-800 rounded-md">Requested</span>
              @elseif($r->status === 'approved')
                <span class="px-2 py-1 text-sm bg-green-100 text-green-800 rounded-md">Approved</span>
              @else
                <span class="px-2 py-1 text-sm bg-red-100 text-red-800 rounded-md">Rejected</span>
              @endif
            </td>
            <td class="p-3">{{ $r->note ?? '-' }}</td>
            <td class="p-3">
              @if ($r->status === 'requested')
                <div class="flex gap-2 flex-wrap">
                  <form method="POST" action="{{ route('admin.plan-requests.approve', $r) }}">
                    @csrf
                    <button type="submit"
                      class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-sm transition">
                      ✅ Approve
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.plan-requests.reject', $r) }}">
                    @csrf
                    <button type="submit"
                      class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-sm transition">
                      ❌ Reject
                    </button>
                  </form>
                </div>
              @else
                <em class="text-slate-400 text-sm">Tidak ada aksi</em>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center p-4 text-slate-500">Belum ada request plan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $reqs->links() }}
  </div>
@endsection
