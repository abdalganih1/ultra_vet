<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('messages.app_name') }} - @yield('title')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            font-family: '{{ app()->getLocale() == 'ar' ? 'Tajawal' : 'Poppins' }}', sans-serif;
        }
        :root {
            --dr-em-primary: #2c3e50;
            --dr-em-accent: #1abc9c;
        }
        .navbar-custom { background-color: var(--dr-em-primary) !important; }
        .navbar-custom .navbar-brand, .navbar-custom .nav-link { color: white !important; }
        .navbar-custom .nav-link.active { background-color: rgba(255,255,255,0.1); }
        
        /* Styling for dropdowns within the custom navbar */
        .navbar-custom .dropdown-menu {
            background-color: var(--dr-em-primary);
            border-color: rgba(255,255,255,0.2);
        }
        .navbar-custom .dropdown-item {
            color: white !important;
        }
        .navbar-custom .dropdown-item:hover, .navbar-custom .dropdown-item:focus {
            background-color: var(--dr-em-accent);
        }
        .navbar-custom .dropdown-divider {
            border-top-color: rgba(255, 255, 255, 0.2);
        }

        .btn-primary { background-color: var(--dr-em-accent); border-color: var(--dr-em-accent); }
    </style>
    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark navbar-custom shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ __('messages.app_name') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('messages.dashboard') }}</a>
                            </li>
                            @if(Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">{{ __('messages.users') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}" href="{{ route('admin.teams.index') }}">{{ __('messages.teams') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.governorates.*') ? 'active' : '' }}" href="{{ route('admin.governorates.index') }}">{{ __('messages.governorates') }}</a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Language Switcher -->
                        <li class="nav-item dropdown">
                            <a id="navbarDropdownLang" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-globe"></i> {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLang">
                                <a class="dropdown-item" href="{{ route('language.switch', 'en') }}">English</a>
                                <a class="dropdown-item" href="{{ route('language.switch', 'ar') }}">العربية</a>
                            </div>
                        </li>

                        <!-- Authentication Links -->
                        @guest
                            {{-- The login link is intentionally removed for guests as per requirements --}}
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        {{ __('messages.profile') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('messages.logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
