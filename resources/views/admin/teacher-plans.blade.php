@extends('layouts.admin')

@section('title', 'Teacher Plans')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">Teacher Plans</h1>

    <table class="min-w-full border border-gray-200 bg-white rounded-lg shadow-sm">
        <thead class="bg-gray-100 text-sm text-gray-700">
            <tr>
                <th class="py-3 px-4 text-left">Name</th>
                <th class="py-3 px-4 text-left">Email</th>
                <th class="py-3 px-4 text-left">Plan</th>
                <th class="py-3 px-4 text-left">Status</th>
                <th class="py-3 px-4 text-left">End Date</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-200">
            @forelse ($teachers as $t)
                <tr>
                    <td class="py-3 px-4">{{ $t['name'] }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $t['email'] }}</td>
                    <td class="py-3 px-4 font-medium text-indigo-600">{{ $t['plan'] }}</td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded-md text-xs 
                            {{ $t['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($t['status']) }}
                        </span>
                    </td>
                    <td class="py-3 px-4">{{ $t['end_date'] ?? '-' }}</td>

                    {{-- ACTIONS --}}
                    <td class="py-3 px-4 text-right space-x-2">
                        {{-- Change Plan Dropdown --}}
                        <form action="{{ route('admin.teacher-plans.update', $t['id']) }}" method="POST" class="inline-flex items-center gap-2">
                            @csrf
                            <select name="plan_id"
                                class="border border-gray-300 rounded-md text-sm px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach ($plans as $p)
                                    <option value="{{ $p->id }}" {{ $t['plan_id'] == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="px-3 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs rounded-md">
                                Change
                            </button>
                        </form>

                        {{-- Extend Plan --}}
                        <form action="{{ route('admin.teacher-plans.extend', $t['id']) }}" method="POST" class="inline">
                            @csrf
                            <button class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-md">
                                +7d
                            </button>
                        </form>

                        {{-- Remove Plan --}}
                        <form action="{{ route('admin.teacher-plans.remove', $t['id']) }}" method="POST" class="inline">
                            @csrf
                            <button class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-md"
                                onclick="return confirm('Yakin hapus plan guru ini?')">
                                Remove
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-gray-500">
                        Belum ada guru dengan plan aktif.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
