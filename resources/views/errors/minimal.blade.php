<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <!-- Favicon and Touch Icons -->
    <link rel="icon" href="{{ env('API_URL') }}/assets/imgs/logo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root {
            --bg-light: #fefefe;
            --bg-dark: #121212;
            --text-light: #333;
            --text-dark: #f0f0f0;
            --accent: #dc3545;
            --blue: #007bff;
        }

        body {
            background-color: #fffdfd;
            color: #333;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            margin: 0;
            padding: 1rem;
        }

        body.dark {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        .toggle-dark {
            position: absolute;
            top: 1rem;
            right: 1rem;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 1.2rem;
            color: inherit;
        }

        .emoji-box {
            width: 120px;
            height: 120px;
            background-color: #f44336;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            animation: pulse 1.2s infinite;
            color: white;
            margin-bottom: 1.5rem;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        h1 {
            font-size: 2.5rem;
            margin: 0;
            color: #d32f2f;
        }

        p {
            font-size: 1.2rem;
            margin: 1rem 0;
        }

        a {
            text-decoration: none;
            background-color: #007bff;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: #0056b3;
        }

        @media (max-width: 480px) {
            .emoji-box {
                width: 80px;
                height: 80px;
                font-size: 2.5rem;
            }

            h1 {
                font-size: 2rem;
            }

            p {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <button class="toggle-dark" onclick="toggleDark()">🌓</button>

    <div class="emoji-box">@yield('emoji')</div>

    <h1>@yield('code')</h1>
    <p>@yield('message')</p>
    <a href="{{ url('/') }}">Kembali ke Beranda</a>

    <script>
        // Toggle dark mode
        function toggleDark() {
            document.body.classList.toggle('dark');
            localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
        }

        // Restore dark mode on load
        (function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark');
            }
        })();
    </script>

</body>

</html>
