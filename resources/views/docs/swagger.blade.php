<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPB API Swagger</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css" />
    <style>
        body {
            margin: 0;
            background: #f8fafc;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 36px;
            padding: 0 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #fff;
            color: #334155;
            border-color: #cbd5e1;
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        #swagger-ui {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="topbar">
        <div class="title">TPB Mobile API - Swagger UI</div>
        <div class="actions">
            <a class="btn btn-primary" href="{{ route('api.docs.postman') }}">Download Postman</a>
            <a class="btn btn-secondary" href="{{ route('api.docs.markdown') }}">Lihat Markdown</a>
        </div>
    </div>

    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
        window.onload = function() {
            SwaggerUIBundle({
                url: "{{ $openapiUrl }}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                displayRequestDuration: true,
                persistAuthorization: true,
                docExpansion: 'list',
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1,
            });
        };
    </script>
</body>

</html>
