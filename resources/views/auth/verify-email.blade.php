@extends('layouts.auth')

@section('content')
<h1 class="h1" style="text-align:center">Verifikasi Email</h1>
<p class="muted" style="text-align:center;margin-bottom:8px">
  Link verifikasi telah dikirim ke emailmu.
</p>

@if (session('status') == 'verification-link-sent')
  <div class="card pad" style="margin:8px 0;background:var(--sky-50)">
    Link verifikasi baru telah dikirim.
  </div>
@endif

<form method="POST" action="{{ route('verification.send') }}" style="display:grid;gap:12px">
  @csrf
  <button class="btn primary block">Kirim Ulang Link</button>
</form>

<form method="POST" action="{{ route('logout') }}" style="margin-top:8px">
  @csrf
  <button class="btn block">Keluar</button>
</form>
@endsection
