@extends('layouts.auth')

@section('content')
    <h1 class="h1" style="text-align:center">Masuk</h1>
    <form method="POST" action="{{ route('login') }}" style="display:grid;gap:12px">
        @csrf
        <div>
            <label class="muted" style="font-size:12px">Email</label>
            <input class="input" type="email" name="email">
        </div>
        <div>
            <label class="muted" style="font-size:12px">Password</label>
            <input class="input" type="password" name="password">
        </div>
        <button class="btn primary block">Masuk</button>
    </form>
    <p class="muted" style="text-align:center;margin-top:10px">
        Belum punya akun? <a href="{{ route('register') }}" style="color:var(--primary)">Daftar</a>
    </p>
@endsection
