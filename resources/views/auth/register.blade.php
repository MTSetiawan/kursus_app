@extends('layouts.auth')

@section('content')
<h1 class="h1" style="text-align:center">Daftar</h1>

<form method="POST" action="{{ route('register') }}" style="display:grid;gap:12px">
  @csrf
  <div>
    <label class="muted" style="font-size:12px">Nama</label>
    <input class="input" name="name" value="{{ old('name') }}" required>
    @error('name') <div class="muted" style="color:#b91c1c">{{ $message }}</div> @enderror
  </div>
  <div>
    <label class="muted" style="font-size:12px">Email</label>
    <input class="input" type="email" name="email" value="{{ old('email') }}" required>
    @error('email') <div class="muted" style="color:#b91c1c">{{ $message }}</div> @enderror
  </div>
  <div>
    <label class="muted" style="font-size:12px">Password</label>
    <input class="input" type="password" name="password" required>
    @error('password') <div class="muted" style="color:#b91c1c">{{ $message }}</div> @enderror
  </div>
  <div>
    <label class="muted" style="font-size:12px">Konfirmasi Password</label>
    <input class="input" type="password" name="password_confirmation" required>
  </div>
  <button class="btn primary block">Daftar</button>
</form>

<p class="muted" style="text-align:center;margin-top:10px">
  Sudah punya akun?
  <a href="{{ route('login') }}" style="color:var(--primary)">Masuk</a>
</p>
@endsection
