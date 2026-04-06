<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPB API Docs</title>
    <style>
        :root {
            color-scheme: light;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 24px 16px 48px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.04);
            padding: 24px;
        }

        .header {
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .header p {
            margin: 6px 0 0;
            color: #475569;
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 14px;
        }

        .btn {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            border-radius: 10px;
            padding: 0 14px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            border-color: #cbd5e1;
            background: #ffffff;
            color: #334155;
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        .content h1,
        .content h2,
        .content h3,
        .content h4 {
            color: #0f172a;
            line-height: 1.3;
        }

        .content h1 {
            font-size: 28px;
        }

        .content h2 {
            margin-top: 28px;
            font-size: 21px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .content h3 {
            margin-top: 20px;
            font-size: 17px;
        }

        .content p,
        .content li {
            color: #334155;
            line-height: 1.6;
            font-size: 15px;
        }

        .content code {
            background: #eff6ff;
            color: #1e3a8a;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 13px;
        }

        .content pre {
            overflow-x: auto;
            background: #0f172a;
            color: #f8fafc;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #1e293b;
        }

        .content pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }

        .content table {
            border-collapse: collapse;
            width: 100%;
        }

        .content table th,
        .content table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>TPB Mobile API Documentation</h1>
                <p>Dokumentasi endpoint API untuk integrasi Flutter (khusus akun ormawa).</p>
                <div class="actions">
                    <a class="btn btn-primary" href="{{ route('api.docs.postman') }}">Download Postman Collection</a>
                    <a class="btn btn-secondary" href="{{ url('/') }}">Kembali ke Aplikasi</a>
                </div>
            </div>
            <div class="content">{!! $content !!}</div>
        </div>
    </div>
</body>

</html>
