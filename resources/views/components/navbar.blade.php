<header class="navbar">
  <div class="nav-wrap container">
    <a class="brand" href="{{ route('landing') }}">LesPrivat<span style="color:var(--primary)">.id</span></a>
    <nav style="display:flex;gap:12px;align-items:center;color:var(--muted)">
      <a href="{{ route('catalog') }}">Kursus</a>
      @auth
        <a href="{{ route('teacher.dashboard') }}" class="btn ghost">Dashboard</a>
      @else
        <a href="{{ route('login') }}" class="btn ghost">Masuk</a>
        <a href="{{ route('register') }}" class="btn primary">Daftar</a>
      @endauth
    </nav>
  </div>
</header>
