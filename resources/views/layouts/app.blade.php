<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Help Desk</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-soft-dove font-sans">

    @include('layouts.navigation')

    <div class="container mx-auto mt-6 px-4">
        @if(session('success'))
            <div class="bg-green-200 text-green-800 p-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>

</body>
</html>
