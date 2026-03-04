<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | HARMONI BPS Tuban</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bps-blue: #0058a8;
            --bps-sky: #007bff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #eef6ff 0%, #f4f7fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 50px 20px; 
            overflow-x: hidden;
        }

        .bg-decoration {
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(0, 88, 168, 0.03);
            border-radius: 50%;
            z-index: -1;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 35px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(0, 88, 168, 0.15);
            padding: 45px 40px;
            position: relative;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-logo-wrapper {
            width: 150px; 
            height: auto;
            margin: 0 auto 5px; 
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-logo {
            width: 100%; 
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 5px 15px rgba(0, 88, 168, 0.15)); 
        }

        .brand-title {
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -1px;
            font-size: 1.6rem;
        }

        .form-label {
            margin-left: 5px;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .input-group-text {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-right: none;
            color: #94a3b8;
            padding-left: 18px;
            border-radius: 15px 0 0 15px !important;
        }

        .form-control {
            border-radius: 0 15px 15px 0 !important;
            padding: 12px 18px 12px 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-left: none;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #e2e8f0;
            background-color: #fff;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--bps-blue);
            color: var(--bps-blue);
        }
        .input-group:focus-within .form-control {
            border-color: var(--bps-blue);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--bps-blue) 0%, var(--bps-sky) 100%);
            border: none;
            border-radius: 15px;
            padding: 15px;
            font-weight: 700;
            color: white;
            width: 100%;
            transition: all 0.3s;
            margin-top: 15px;
            box-shadow: 0 10px 20px -5px rgba(0, 88, 168, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(0, 88, 168, 0.4);
            color: white;
        }

        .footer-text {
            margin-top: 35px;
            font-size: 0.7rem;
            color: #94a3b8;
            font-weight: 700;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<div class="bg-decoration" style="top: -10%; right: -5%;"></div>
<div class="bg-decoration" style="bottom: -10%; left: -5%;"></div>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="login-logo-wrapper">
            <img src="{{ asset('img/logo_harmoni.png') }}" alt="Logo Harmoni" class="login-logo">
        </div>
        
        <h3 class="brand-title mb-1">HARMONI <span style="font-weight: 400; color: var(--bps-blue);">BPS</span></h3>
        <p class="text-muted small fw-medium">Harian Monitoring Instansi BPS Kabupaten Tuban</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 small mb-4 shadow-sm py-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-circle-exclamation me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <form action="{{ route('login.action') }}" method="POST">
        @csrf <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-user-circle"></i>
                </span>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus autocomplete="username">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-shield-halved"></i>
                </span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
            </div>
        </div>

        <button type="submit" class="btn btn-login">
            Masuk ke Dashboard <i class="fas fa-arrow-right ms-2 small"></i>
        </button>
    </form>
    
    <div class="text-center footer-text">
        <p class="mb-0 text-uppercase">© 2026 BPS KABUPATEN TUBAN</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>