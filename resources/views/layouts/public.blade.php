<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'LesPrivat.id' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
  <x-navbar />
  <main class="min-h-screen">
    @yield('content')
  </main>
  <x-footer />
  @stack('head')
</body>
</html>
