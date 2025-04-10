<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Login') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f9ff;
            color: #1e3a8a;
        }
        
        .container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        
        p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: #334155;
            line-height: 1.6;
        }
        
        .buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: #1d4ed8;
            color: white;
            border: 2px solid #1d4ed8;
        }
        
        .btn-primary:hover {
            background-color: #1e40af;
            border-color: #1e40af;
        }
        
        .btn-outline {
            background-color: transparent;
            color: #1d4ed8;
            border: 2px solid #1d4ed8;
        }
        
        .btn-outline:hover {
            background-color: #dbeafe;
        }
        
        .logo {
            margin-bottom: 2rem;
            font-size: 3rem;
            font-weight: 700;
            color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            {{ config('app.name', 'Login') }}
        </div>
        
        <h1>Benvingut a la nostra aplicació</h1>
        
        <p>
            Accedeix al teu compte o registra't per començar a utilitzar totes les funcionalitats que oferim.
        </p>
        
        <div class="buttons">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Iniciar sessió</a>
                    
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline">Registrar-se</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</body>
</html>