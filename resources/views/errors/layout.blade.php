
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ $title ?? 'Terjadi Kesalahan' }} - VJ Gallery
    </title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #0b1224;
            color: #ffffff;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            width: 100%;
            padding: 20px 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #263653;
            background: #101a31;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            background: #ffffff;
            color: #101a31;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
        }

        .nav-link {
            color: #c4d0e5;
            font-size: 14px;
            transition: 0.3s;
        }

        .nav-link:hover {
            color: #ffffff;
        }

        /* =========================
           ERROR PAGE
        ========================= */

        .error-wrapper {
            min-height: calc(100vh - 83px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 20px;
        }

        .error-container {
            width: 100%;
            max-width: 650px;
            text-align: center;
        }

        .error-illustration {
            width: 150px;
            height: 150px;
            margin: 0 auto 28px;
            border-radius: 50%;
            background: #172641;
            border: 1px solid #2b4166;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9bb9ef;
            font-size: 70px;
        }

        .error-code {
            font-size: 86px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: 3px;
            color: #ffffff;
            margin-bottom: 18px;
        }

        .error-title {
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #ffffff;
        }

        .error-message {
            font-size: 16px;
            line-height: 1.8;
            color: #aab9d2;
            max-width: 500px;
            margin: 0 auto 30px;
        }

        .error-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 13px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
            cursor: pointer;
        }

        .btn-primary {
            background: #ffffff;
            color: #101a31;
        }

        .btn-primary:hover {
            background: #dce6f7;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            color: #ffffff;
            border: 1px solid #52698e;
        }

        .btn-secondary:hover {
            background: #172641;
            border-color: #8ca8d7;
        }

        .error-status {
            display: inline-block;
            margin-top: 35px;
            padding: 8px 14px;
            border-radius: 20px;
            background: #172641;
            border: 1px solid #2b4166;
            color: #9bb9ef;
            font-size: 12px;
        }

        .footer {
            text-align: center;
            padding: 25px 20px;
            color: #637895;
            font-size: 12px;
            border-top: 1px solid #1c2b44;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 600px) {
            .navbar {
                padding: 16px 5%;
            }

            .logo {
                font-size: 18px;
            }

            .logo-icon {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }

            .error-wrapper {
                min-height: calc(100vh - 70px);
                padding: 40px 20px;
            }

            .error-illustration {
                width: 110px;
                height: 110px;
                font-size: 50px;
            }

            .error-code {
                font-size: 64px;
            }

            .error-title {
                font-size: 23px;
            }

            .error-message {
                font-size: 14px;
            }

            .btn {
                width: 100%;
                max-width: 280px;
            }
        }
    </style>
</head>

<body>

    <!-- =========================
         NAVBAR
    ========================= -->

    <nav class="navbar">

        <a href="{{ url('/') }}" class="logo">
            <span class="logo-icon">VJ</span>
            <span>VJ Gallery</span>
        </a>

        <a href="{{ url('/') }}" class="nav-link">
            Halaman Utama
        </a>

    </nav>

    <!-- =========================
         ERROR CONTENT
    ========================= -->

    <main class="error-wrapper">

        <div class="error-container">

            <div class="error-illustration">
                {{ $icon ?? '⚠' }}
            </div>

            <div class="error-code">
                {{ $code ?? '500' }}
            </div>

            <h1 class="error-title">
                {{ $title ?? 'Oops! Terjadi Kesalahan' }}
            </h1>

            <p class="error-message">
                {{ $message ?? 'Maaf, terjadi kesalahan pada sistem. Silakan coba kembali beberapa saat lagi.' }}
            </p>

            <div class="error-actions">

                <a href="{{ url('/') }}" class="btn btn-primary">
                    ← Kembali ke Beranda
                </a>

                <a href="{{ url()->current() }}" class="btn btn-secondary">
                    ↻ Coba Lagi
                </a>

            </div>

            <div class="error-status">
                VJ Gallery • Multimedia Experience
            </div>

        </div>

    </main>

    <!-- =========================
         FOOTER
    ========================= -->

    <footer class="footer">
        &copy; {{ date('Y') }} VJ Gallery. All rights reserved.
    </footer>

</body>
</html>