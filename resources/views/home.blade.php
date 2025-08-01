@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-6 fw-light">نظرة عامة</h1>
            <hr>
        </div>
    </div>

    <div class="row g-4">
        <!-- Card: إجمالي الحيوانات الشاردة -->
        <div class="col-md-6 col-lg-3">
            <div class="card text-center dashboard-card shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-paw fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">إجمالي الحيوانات الشاردة</h5>
                    <p class="display-4 fw-bold">{{ $totalStrayPets ?? '0' }}</p>
                    <a href="{{ route('stray-pets.index') }}" class="btn btn-sm btn-outline-primary">إدارة الحيوانات الشاردة</a>
                </div>
            </div>
        </div>

        <!-- ... باقي الكروت (المهام، التنبيهات) كما هي، ويمكن تحديثها لتشير لبيانات حقيقية إذا أردت ... -->

        <!-- Card: توليد QR جديد لحيوان شارد (للمدير ومدخل البيانات) -->
        @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isDataEntry()))
        <div class="col-md-6 col-lg-3">
             <div class="card text-center dashboard-card shadow-sm h-100 bg-light">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-qrcode fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">توليد QR لحيوان شارد</h5>
                    <p class="text-muted">ابدأ بتوليد QR Code لحيوان شارد جديد</p>
                    <a href="{{ route('qrcodes.stray.generate.form') }}" class="btn btn-secondary mt-auto">
                       <i class="fas fa-plus me-1"></i> ابدأ التوليد
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- قسم إحصائيات الفرق -->
    @if(isset($teamStats) && $teamStats->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="display-6 fw-light">إحصائيات الفرق حسب المحافظة</h2>
            <hr>
        </div>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="accordion" id="statsAccordion">
                        @foreach($teamStats as $province => $teams)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ Str::slug($province) }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($province) }}" aria-expanded="false" aria-controls="collapse-{{ Str::slug($province) }}">
                                    <strong>{{ $province }}</strong>
                                    <span class="badge bg-primary rounded-pill ms-auto me-2">{{ $teams->sum('stray_pets_count') }} حالة</span>
                                </button>
                            </h2>
                            <div id="collapse-{{ Str::slug($province) }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ Str::slug($province) }}" data-bs-parent="#statsAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group">
                                        @foreach($teams as $team)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $team->name }}
                                            <span class="badge bg-info rounded-pill">{{ $team->stray_pets_count }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

     <!-- ... قسم آخر التحديثات كما هو ... -->

</div>
@endsection