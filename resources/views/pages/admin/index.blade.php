<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/f74deb4653.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .ui-btn {
            display: inline-flex;
            height: 40px;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            padding: 0 12px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .ui-btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }

        .ui-btn-primary:hover {
            background-color: #1d4ed8;
        }

        .ui-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            border-width: 1px;
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
        }

        .ui-badge-success {
            border-color: #a7f3d0;
            background-color: #ecfdf5;
            color: #047857;
        }

        .ui-badge-warning {
            border-color: #fde68a;
            background-color: #fffbeb;
            color: #a16207;
        }

        .ui-badge-danger {
            border-color: #fecaca;
            background-color: #fef2f2;
            color: #b91c1c;
        }
    </style>
    <title>Admin</title>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    @include('components.navbar.navbar')
    <main class="pt-24">
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>
