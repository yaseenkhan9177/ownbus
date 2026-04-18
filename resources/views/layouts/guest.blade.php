<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>OWN BUSES - Enterprise Fleet Solutions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/images.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#020617] text-slate-200 antialiased">
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('sweet_alert'))
            Swal.fire({
                icon: '{{ session('
                sweet_alert.type ') }}',
                title: '{{ session('
                sweet_alert.title ') }}',
                text: '{{ session('
                sweet_alert.text ') }}',
                confirmButtonColor: '#3b82f6'
            });
            @endif
        });
    </script>
</body>

</html>