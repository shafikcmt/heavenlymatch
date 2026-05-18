<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | HeavenlyMatch</title>
    <link rel="stylesheet" href="{{ asset('css/hm-admin.css') }}">
</head>
<body class="hm-admin-body">
    <main class="hm-admin-login-page">
        <section class="hm-admin-login-card">
            <div class="hm-admin-brand" style="justify-content:center;margin-bottom:18px">
                <div class="hm-admin-logo">♥</div>
                <div>
                    <div class="hm-admin-brand-title">HeavenlyMatch</div>
                    <div class="hm-admin-brand-sub">Secure admin login</div>
                </div>
            </div>

            <div style="text-align:center;margin-bottom:20px">
                <h1 style="font-size:28px;margin:0;font-weight:950">Admin Dashboard</h1>
                <p style="margin:8px 0 0;color:#64748b;font-weight:700">Manage users, biodata approvals and platform settings.</p>
            </div>

            @if(session('success'))
                <div class="hm-admin-alert success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="hm-admin-alert error">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="hm-admin-alert error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" style="display:grid;gap:14px">
                @csrf
                <div class="hm-admin-field">
                    <label>Email address</label>
                    <input class="hm-admin-input" type="email" name="email" value="{{ old('email') }}" placeholder="admin@heavenlymatch.test" required autofocus>
                </div>
                <div class="hm-admin-field">
                    <label>Password</label>
                    <input class="hm-admin-input" type="password" name="password" placeholder="Enter password" required>
                </div>
                <label style="display:flex;gap:8px;align-items:center;font-weight:800;color:#475569">
                    <input type="checkbox" name="remember" value="1"> Remember me
                </label>
                <button type="submit" class="hm-admin-btn primary" style="width:100%;padding:14px">Login to dashboard</button>
            </form>

            <div class="hm-admin-note-box" style="margin-top:18px">
                Demo admin after seed: admin@heavenlymatch.test / admin123
            </div>
        </section>
    </main>
</body>
</html>
