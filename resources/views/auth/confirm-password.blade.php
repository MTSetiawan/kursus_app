@extends('layouts.auth')

@section('content')
<h1 class="h1" style="text-align:center">Konfirmasi Password</h1>
<p class="muted" style="text-align:center;margin-bottom:8px">
  Ini demi keamanan akunmu.
</p>

<form method="POST" action="{{ route('password.confirm') }}" style="display:grid;gap:12px">
  @csrf
  <div>
    <label class="muted" style="font-size:12px">Password</label>
    <input class="input" type="password" name="password" required autofocus>
    @error('password') <div class="muted" style="color:#b91c1c">{{ $message }}</div> @enderror
  </div>
  <button class="btn primary block">Konfirmasi</button>
</form>
@endsection
