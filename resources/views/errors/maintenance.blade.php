<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f3f4f8;color:#243552;font-family:Arial,sans-serif}
        .card{width:min(560px,calc(100% - 32px));background:#fff;border-radius:24px;padding:36px;box-shadow:0 24px 70px rgba(36,53,82,.14);text-align:center}
        .icon{width:74px;height:74px;border-radius:24px;background:linear-gradient(135deg,#4738ff,#ec2f7b);display:grid;place-items:center;color:#fff;font-size:34px;margin:0 auto 18px}
        h1{margin:0 0 10px;font-size:30px}.message{color:#64748b;line-height:1.7;font-weight:600}
    </style>
</head>
<body>
    <main class="card">
        <div class="icon">⚙</div>
        <h1>{{ $title }}</h1>
        <div class="message">{{ $message }}</div>
    </main>
</body>
</html>
