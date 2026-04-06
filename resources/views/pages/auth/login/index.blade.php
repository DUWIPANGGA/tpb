<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/f74deb4653.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Login</title>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
            });
        </script>
    @endif
    <div class="flex flex-col items-center justify-center h-screen p-4 space-y-4">
        <div class="flex justify-center">
            <img src="{{ asset('image/logo/polindra.png') }}" alt="" class="object-cover w-full h-24">
        </div>
        <div class="w-full max-w-md p-8 bg-white rounded-xl shadow-lg border border-gray-100">
            <h1 class="mb-8 text-3xl font-bold text-center text-gray-800 tracking-tight">Masuk</h1>
            <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-700">Nama Pengguna</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="name" id="name" placeholder="Masukkan nama..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-600 focus:border-blue-600 outline-none transition-colors" />
                    </div>
                </div>
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" placeholder="Password"
                            class="w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-600 focus:border-blue-600 outline-none transition-colors" />
                    </div>
                </div>
                <div class="pt-2">
                    <button type="submit"
                        class="w-full px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 transition-all">Sign
                        in</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>
