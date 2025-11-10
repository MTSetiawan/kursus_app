<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Auth' }} | LesPrivat.id</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="container" style="display:flex;min-height:100vh;align-items:center;justify-content:center">
  <main class="card pad" style="width:100%;max-width:420px">
    @yield('content')
  </main>
</body>
</html>
