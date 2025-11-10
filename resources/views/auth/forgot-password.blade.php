@extends('layouts.auth')

@section('content')
<h1 class="h1" style="text-align:center">Lupa Password</h1>
<p class="muted" style="text-align:center;margin-bottom:8px">
  Masukkan email untuk menerima link reset.
</p>

@if (session('status'))
  <div class="card pad" style="margin:8px 0;background:var(--sky-50)">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}" style="display:grid;gap:12px">
  @csrf
  <div>
    <label class="muted" style="font-size:12px">Email</label>
    <input class="input" type="email" name="email" value="{{ old('email') }}" required autofocus>
    @error('email') <div class="muted" style="color:#b91c1c">{{ $message }}</div> @enderror
  </div>
  <button class="btn primary block">Kirim Link Reset</button>
</form>

<p class="muted" style="text-align:center;margin-top:10px">
  <a href="{{ route('login') }}" style="color:var(--primary)">Kembali ke Login</a>
</p>
@endsection
