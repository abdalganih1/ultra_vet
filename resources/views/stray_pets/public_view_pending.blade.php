@extends('layouts.app')

@section('title', 'بيانات قيد الإدخال')

@section('content')
<div class="container text-center py-5">
    <img src="{{ asset('images/img.jpg') }}" alt="UltraVet Logo" style="max-height: 80px;" class="mb-3">
    <h1 class="display-5 text-secondary">
        <span class="lang-ar">بيانات هذا الحيوان قيد الإدخال</span>
        <span class="lang-en">This Animal's Data Is Pending</span>
    </h1>
    <p class="lead text-muted">
        <span class="lang-ar">شكراً لاهتمامك. فريقنا يعمل حالياً على إدخال وتحديث البيانات الصحية لهذا الحيوان.</span>
        <span class="lang-en">Thank you for your interest. Our team is currently working on entering and updating this animal's health data.</span>
    </p>
    <p class="text-muted">
        <span class="lang-ar">الرقم التعريفي:</span>
        <span class="lang-en">UUID:</span>
        <span class="text-primary">{{ $strayPet->uuid }}</span>
    </p>
    
    {{-- زر إدخال البيانات يظهر فقط للمدير ومدخل البيانات --}}
    @auth
        @if(Auth::user()->isAdmin() || Auth::user()->isDataEntry())
        <a href="{{ route('stray-pets.data-entry-form', ['uuid' => $strayPet->uuid]) }}" class="btn btn-primary mt-3 me-2">
            <i class="fas fa-pencil-alt me-2"></i>
            <span class="lang-ar">الذهاب لصفحة إدخال البيانات</span>
            <span class="lang-en">Go to Data Entry Page</span>
        </a>
        @endif
    @endauth

    {{-- زر العودة للصفحة الرئيسية يظهر للجميع --}}
    <a href="{{ url('/') }}" class="btn btn-secondary mt-3">
        <i class="fas fa-home me-2"></i>
        <span class="lang-ar">العودة إلى الصفحة الرئيسية</span>
        <span class="lang-en">Back to Home Page</span>
    </a>
</div>

@push('scripts')
<script>
    // دالة تحديد اللغة (من قالب layouts.app)
    function setLanguage(lang) {
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
    }
    // افتراضيًا، ابدأ باللغة العربية
    setLanguage('ar');
</script>
@endpush
@endsection