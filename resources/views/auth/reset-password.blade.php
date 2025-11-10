@extends('layouts.auth')

@section('content')
<h1 class="h1" style="text-align:center">Reset Password</h1>

<form method="POST" action="{{ route('password.store') }}" style="display:grid;gap:12px">
  @csrf
  <input type="hidden" name="token" value="{{ request()->route('token') }}">

  <div>
    <label class="muted" style="font-size:12px">Email</label>
    <input class="input" type="email" name="email" value="{{ old('email', request('email')) }}" required>
    @error('email') <div class="muted" style="color:#b91c1c">{{ $message }}</div> @enderror
  </div>

  <div>
    <label class="muted" style="font-size:12px">Password Baru</label>
    <input class="input" type="password" name="password" required>
    @error('password') <div class="muted" style="color:#b91c1c">{{ $message }}</div> @enderror
  </div>

  <div>
    <label class="muted" style="font-size:12px">Konfirmasi Password</label>
    <input class="input" type="password" name="password_confirmation" required>
  </div>

  <button class="btn primary block">Simpan Password</button>
</form>
@endsection
