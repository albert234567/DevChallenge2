<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Inici</title>
    @vite(['resources/css/styles.css']) 
</head>
<body>
    <h1>Benvingut, {{ Auth::user()->name }}</h1>
    <p>Email: {{ Auth::user()->email }}</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Tancar sessió</button>
    </form>
</body>
</html>
