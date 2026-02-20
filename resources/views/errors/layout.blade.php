<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title')</title>
    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ env('API_URL') }}/assets/imgs/logo.png" type="image/x-icon">
    <style>
        /* Import font seram */
        @import url('https://fonts.googleapis.com/css2?family=Creepster&display=swap');

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background: #000;
            overflow: hidden;
            background-color: #000;
            cursor: none;
            font-family: 'Creepster', cursive;
        }

        .full {
            position: fixed;
            width: 100%;
            height: 100%;
            background: url(https://i0.wp.com/volunteerswhererainbowsmeet.wordpress.com/wp-content/uploads/2017/08/gambar-setan-paling-seram.png?w=NaN&h=&ssl=1) no-repeat center center fixed;
            background-size: cover;
            mix-blend-mode: overlay;
            z-index: 2;
            padding-top: 20px;
        }

        .error-container {
            position: fixed;
            display: flex;
            flex-direction: column;
            justify-content: end;
            bottom: 0;
            right: 0;
            color: #fff;
            margin: 20px;
        }

        .error-container .title {
            font-size: 3rem;
        }

        .error-container .code {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 10px;
        }

        .blood-btn {
            position: relative;
            padding: 15px 40px;
            font-size: 22px;
            font-family: 'Creepster', cursive;
            color: #fff;
            background: linear-gradient(to bottom, #8b0000, #400000);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.6);
            overflow: hidden;
            text-decoration: none;
        }

        /* Efek darah menetes */
        .blood-btn::before,
        .blood-btn::after {
            content: '';
            position: absolute;
            top: 100%;
            width: 12px;
            height: 12px;
            background: #8b0000;
            border-radius: 50%;
            animation: drip 3s infinite;
        }

        .blood-btn::before {
            left: 30%;
            animation-delay: 0s;
        }

        .blood-btn::after {
            left: 70%;
            animation-delay: 1.5s;
        }

        @keyframes drip {
            0% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            50% {
                transform: translateY(20px) scale(0.8);
                opacity: 0.7;
            }

            100% {
                transform: translateY(40px) scale(0.5);
                opacity: 0;
            }
        }

        #custom-cursor {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle,
                    rgba(200, 200, 200, 0.7) 0%,
                    rgba(25, 25, 25, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            mix-blend-mode: exclusion;
        }

        .ghost {
            position: absolute;
            width: 180px;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            z-index: 1;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="full">
        <div class="error-container">
            <div class="title">@yield('title')</div>
            <div class="code">@yield('code')</div>
            <a href="{{ url()->previous() }}" class="blood-btn">Back 🔙</a>
        </div>
    </div>
    <div id="custom-cursor"></div>

    <script>
        const cursor = document.getElementById("custom-cursor"),
            ghostContainer = document.getElementsByClassName("full")[0];

        window.addEventListener("mousemove", (e) => {
            cursor.style.left = e.clientX - 250 + "px";
            cursor.style.top = e.clientY - 250 + "px";
        });

        // Fungsi spawn ghost
        function spawnGhost() {
            const ghost = document.createElement("img");
            ghost.src = "{{ asset('assets/admin/images/pngegg.png') }}";
            ghost.className = "ghost";

            // Posisi acak
            ghost.style.left = Math.random() * (window.innerWidth - 200) + "px";
            ghost.style.top = Math.random() * (window.innerHeight - 200) + "px";

            // document.body.appendChild(ghost);
            ghostContainer.appendChild(ghost);

            // Fade in
            setTimeout(() => {
                ghost.style.opacity = 1;
            }, 100);

            // Fade out dan hapus
            setTimeout(() => {
                ghost.style.opacity = 0;
                setTimeout(() => ghost.remove(), 1000);
            }, 4000);
        }

        // Munculkan hantu setiap 5–10 detik
        setInterval(spawnGhost, 5000 + Math.random() * 5000);
    </script>
</body>

</html>
