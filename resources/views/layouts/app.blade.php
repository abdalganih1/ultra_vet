<!DOCTYPE html>
{{-- تحديد اللغة والاتجاه لـ RTL افتراضياً، سيتم تحديثه بـ JavaScript --}}
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- رمز CSRF لحماية الطلبات --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- عنوان الصفحة الافتراضي، يمكن تغييره في كل View --}}
    <title>Dr.Em - @yield('title', 'إدارة الحيوانات الأليفة')</title>

    <!-- خطوط مخصصة من Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS (من CDN لتجنب مشاكل التجميع) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Font Awesome (للأيقونات) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- تنسيقات CSS مخصصة لتطبيق Dr.Em -->
    <style>
        /* الخطوط الأساسية وخلفية الجسم */
        body {
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif; /* الخط العربي الأساسي */
        }
        /* لضمان عرض الأرقام والحروف اللاتينية بشكل صحيح في حقول التاريخ */
        input[type="date"], input[type="tel"], input[type="text"].serial-number-field {
            font-family: 'Poppins', sans-serif, system-ui;
        }

        /* تعريف ألوان Dr.Em كمتغيرات CSS */
        :root {
            --dr-em-primary: #2c3e50;   /* كحلي غامق (لون الشريط العلوي) */
            --dr-em-accent: #1abc9c;    /* تركواز (لون الأزرار الرئيسية، النشط) */
            --dr-em-text: #333;         /* لون النص الأساسي */
            --dr-em-light-grey: #f4f7f6; /* خلفية رمادية فاتحة جداً */
            --dr-em-gold: #d4a373;      /* ذهبي (لبعض اللمسات) */
            --dr-em-light-blue: #a7c5c9; /* أزرق فاتح (لبعض الخلفيات) */
        }

        /* تنسيقات شريط التنقل (Navbar) */
        .navbar-custom {
            background-color: var(--dr-em-primary) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        .navbar-custom .navbar-brand img {
            max-height: 40px;
            margin-left: 10px; /* مسافة للشعار في RTL */
            margin-right: 0; /* إلغاء أي margin-right افتراضي */
        }
        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.75) !important;
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.25rem;
        }
        .navbar-custom .dropdown-menu {
            background-color: var(--dr-em-primary);
            border: none;
        }
        .navbar-custom .dropdown-item {
            color: rgba(255, 255, 255, 0.75) !important;
        }
        .navbar-custom .dropdown-item:hover,
        .navbar-custom .dropdown-item:focus {
            background-color: var(--dr-em-accent);
            color: white !important;
        }
        .navbar-custom .dropdown-divider {
            border-top-color: rgba(255, 255, 255, 0.2);
        }

        /* تنسيقات الأزرار العامة */
        .btn-primary { background-color: var(--dr-em-accent); border-color: var(--dr-em-accent); }
        .btn-primary:hover { background-color: #16a085; border-color: #16a085; }
        .btn-info { background-color: #3498db; border-color: #3498db; }
        .btn-info:hover { background-color: #2980b9; border-color: #2980b9; }
        .btn-danger { background-color: #e74c3c; border-color: #e74c3c; }
        .btn-danger:hover { background-color: #c0392b; border-color: #c0392b; }
        .btn-success { background-color: #2ecc71; border-color: #2ecc71; }
        .btn-success:hover { background-color: #27ae60; border-color: #27ae60; }
        .btn-secondary, .btn-outline-secondary { background-color: #95a5a6; border-color: #95a5a6; color: white; }
        .btn-secondary:hover, .btn-outline-secondary:hover { background-color: #7f8c8d; border-color: #7f8c8d; color: white; }
        .btn-outline-primary { color: var(--dr-em-accent); border-color: var(--dr-em-accent); }
        .btn-outline-primary:hover { background-color: var(--dr-em-accent); color: white; }
        .btn-outline-info { color: #3498db; border-color: #3498db; }
        .btn-outline-info:hover { background-color: #3498db; color: white; }
        
        /* تنسيقات الشارات (badges) */
        .badge.bg-success { background-color: #2ecc71 !important; }
        .badge.bg-warning { background-color: #f1c40f !important; }
        .badge.bg-danger { background-color: #e74c3c !important; }
        .badge.bg-info { background-color: #3498db !important; }

        /* تنسيقات خاصة بقوائم الحيوانات ونماذج الإدخال */
        .dashboard-card, .pet-card, .form-section-card {
            background-color: #fff;
            border-radius: .5rem;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
            margin-bottom: 2rem;
        }
        .form-section-card .card-header {
            background-color: var(--dr-em-primary);
            color: white;
            font-weight: 500;
            border-bottom: 3px solid var(--dr-em-gold);
        }
        .form-label {
            font-weight: 500;
            color: var(--dr-em-text);
        }
        .btn-submit { /* زر الحفظ في نماذج الإدخال */
            background-color: var(--dr-em-gold);
            border-color: var(--dr-em-gold);
            color: white;
            font-weight: bold;
        }
        .btn-submit:hover {
            background-color: #bf8a54; /* درجة أغمق من الذهبي */
            border-color: #bf8a54;
            color: white;
        }
        .card-footer {
            background-color: rgba(0, 0, 0, 0.03);
            border-top: 1px solid rgba(0, 0, 0, 0.125);
        }
        .table img {
            object-fit: cover;
        }

        /* --- دعم اللغات --- */
        /* إخفاء النصوص الإنجليزية افتراضياً في الوضع العربي */
        html[lang="ar"] .lang-en { display: none; }
        /* إخفاء النصوص العربية افتراضياً في الوضع الإنجليزي */
        html[lang="en"] .lang-ar { display: none; }
        /* ضبط محاذاة الأيقونات في LTR (الإنجليزية) */
        html[lang="en"] .info-list dt i {
            margin-right: 8px; /* مسافة للأيقونة في LTR */
            margin-left: 0;
        }
        /* ضبط محاذاة الأيقونات في RTL (العربية) - افتراضي */
        html[lang="ar"] .info-list dt i {
            margin-left: 8px; /* مسافة للأيقونة في RTL */
            margin-right: 0;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-custom">
            <div class="container-fluid">
                {{-- شعار التطبيق ورابط الصفحة الرئيسية --}}
                <a class="navbar-brand" href="{{ url('/home') }}">
                    <img src="{{ asset('images/img.jpg') }}" alt="شعار Dr.Em">
                    <span class="lang-ar">Dr.Em</span>
                    <span class="lang-en">Dr.Em</span>
                </a>
                {{-- زر القائمة المتنقلة --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- الروابط اليسرى (للتنقل الرئيسي) -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>
                                <span class="lang-ar">لوحة التحكم</span>
                                <span class="lang-en">Dashboard</span>
                            </a>
                        </li>
                        
                        @auth {{-- الروابط تظهر فقط للمستخدمين المسجلين --}}
                            @if(Auth::user()->isAdmin() || Auth::user()->isDataEntry())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ Request::is('stray-pets*') || Request::is('qrcodes/stray*') ? 'active' : '' }}" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-paw me-1"></i>
                                    <span class="lang-ar">إدارة الحيوانات الشاردة</span>
                                    <span class="lang-en">Stray Animals Management</span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownAdmin">
                                    <li><a class="dropdown-item" href="{{ route('stray-pets.index') }}">
                                        <i class="fas fa-list-alt me-1"></i>
                                        <span class="lang-ar">سجل الحيوانات / الباركودات</span>
                                        <span class="lang-en">Animals Log / QR Codes</span>
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('qrcodes.stray.generate.form') }}">
                                        <i class="fas fa-qrcode me-1"></i>
                                        <span class="lang-ar">توليد رموز QR جديدة</span>
                                        <span class="lang-en">Generate New QR Codes</span>
                                    </a></li>
                                </ul>
                            </li>
                            @endif
                        @endauth
                        
                        {{-- يمكن إضافة روابط أخرى هنا (المهام، التنبيهات، المتجر، المقالات) لاحقاً --}}
                    </ul>

                    <!-- الروابط اليمنى (أزرار اللغة، المصادقة، وملف المستخدم) -->
                    <ul class="navbar-nav ms-auto">
                        {{-- أزرار تبديل اللغة --}}
                        <li class="nav-item lang-switcher ms-lg-3 mt-2 mt-lg-0">
                            <button class="btn btn-outline-light btn-sm" onclick="setLanguage('ar')">العربية</button>
                            <button class="btn btn-outline-light btn-sm" onclick="setLanguage('en')">English</button>
                        </li>

                        @guest {{-- روابط تسجيل الدخول والتسجيل للضيوف --}}
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <span class="lang-ar">تسجيل الدخول</span>
                                        <span class="lang-en">Login</span>
                                    </a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <span class="lang-ar">تسجيل</span>
                                        <span class="lang-en">Register</span>
                                    </a>
                                </li>
                            @endif
                        @else {{-- روابط المستخدم المسجل دخولاً --}}
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle me-1"></i>
                                    {{-- عرض اسم المستخدم ودوره مترجمين --}}
                                    <span class="lang-ar">{{ Auth::user()->name }} ({{ Auth::user()->role_ar ?? Auth::user()->role }})</span>
                                    <span class="lang-en">{{ Auth::user()->name }} ({{ Auth::user()->role_en ?? Auth::user()->role }})</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user-edit me-2"></i>
                                        <span class="lang-ar">تعديل الملف الشخصي</span>
                                        <span class="lang-en">Edit Profile</span>
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-cog me-2"></i>
                                        <span class="lang-ar">الإعدادات</span>
                                        <span class="lang-en">Settings</span>
                                    </a>
                                    <hr class="dropdown-divider">
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        <span class="lang-ar">تسجيل الخروج</span>
                                        <span class="lang-en">Logout</span>
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
            {{-- عرض رسائل النجاح أو الخطأ (Flash Messages) --}}
            @if (session('success'))
                <div class="container">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="container">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            @yield('content') {{-- محتوى الصفحة الرئيسي --}}
        </main>

        {{-- تذييل الصفحة --}}
        <footer class="bg-light text-center text-lg-start mt-5">
            <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
                © {{ date('Y') }} Dr.Em - 
                <span class="lang-ar">جميع الحقوق محفوظة</span>
                <span class="lang-en">All rights reserved</span>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle (يجب أن يكون في نهاية body) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    {{-- سكربت مخصص لتبديل اللغة وحفظها --}}
    <script>
        // دالة تبديل اللغة (تطبيق اتجاه الصفحة وتخزين اللغة في localStorage)
        function setLanguage(lang) {
            document.documentElement.lang = lang; // تحديث سمة lang في وسم html
            document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr'; // تحديث اتجاه الصفحة
            localStorage.setItem('preferredLanguage', lang); // حفظ اللغة المفضلة في التخزين المحلي
        }

        // عند تحميل الصفحة، تحقق من اللغة المفضلة في localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const preferredLanguage = localStorage.getItem('preferredLanguage');
            if (preferredLanguage) {
                setLanguage(preferredLanguage); // تطبيق اللغة المحفوظة
            } else {
                setLanguage('ar'); // اللغة الافتراضية إذا لم تكن مخزنة
            }
        });
    </script>
    @stack('scripts') {{-- لاستقبال أي سكربتات إضافية من الـ Views --}}
</body>
</html>