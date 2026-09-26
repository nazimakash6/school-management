<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'School Management')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body>

    <div class="app-wrapper">
        @include('partials.sidebar')

        <div class="main-content">
            @include('partials.notificationPanel')

            <main class="page-content">
                @yield('content')
            </main>

            @include('partials.footer')
        </div>
    </div>

    @include('partials.email-quick-modal')
    @include('partials.confirm-modal')

    @stack('scripts')
</body>                                                                                                                                                                                                                                                                                                                                                                                                                                                                 

</html>