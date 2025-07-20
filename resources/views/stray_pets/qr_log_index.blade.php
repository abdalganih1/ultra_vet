@extends('layouts.app')

@section('title', 'سجل رموز QR المولدة')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6 fw-light">سجل رموز QR المولدة</h1>
        <div>
            <a href="{{ route('qrcodes.stray.generate.form') }}" class="btn btn-primary me-2"><i class="fas fa-plus me-1"></i> توليد رموز جديدة</a>
        </div>
    </div>
    <hr>

    {{-- قسم البحث --}}
    <div class="mb-3">
        <form action="{{ route('stray-pets.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="بحث بالرقم التسلسلي أو UUID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search me-1"></i> بحث</button>
            </div>
            @if(request('search'))
            <div class="col-md-auto">
<a href="{{ route('stray-pets.index') }}" class="btn btn-outline-danger">            </div>
            @endif
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if (empty($qrsToDisplay))
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>لا توجد رموز QR مولدة في السجل أو تطابق البحث.
                </div>
            @else
                {{-- النموذج الرئيسي للطباعة، يحيط بكل شيء --}}
                <form action="{{ route('qrcodes.stray.print.pdf') }}" method="POST" id="printForm">
                    @csrf

                    {{-- **الحل الرئيسي: إضافة الـ UUIDs كمدخلات مخفية هنا مباشرة باستخدام Blade** --}}
                    @foreach ($qrsToDisplay as $qr)
                        <input type="hidden" name="uuids[]" value="{{ $qr['strayPet']->uuid }}">
                    @endforeach

                    {{-- زر الطباعة في الأعلى --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">عرض النتائج ({{ count($qrsToDisplay) }} عنصر)</h5>
                        <button type="submit" id="printAllBtn" class="btn btn-info text-white">
                            <i class="fas fa-file-pdf me-1"></i> طباعة هذه الصفحة (PDF)
                        </button>
                    </div>
                    
                    <div class="row g-3">
                        @foreach ($qrsToDisplay as $qr)
                            <div class="col-md-4 col-lg-3 text-center">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-truncate" title="{{ $qr['strayPet']->serial_number ?? ($qr['strayPet']->animal_type ?? 'حيوان غير مسمى') }}">
                                            {{ $qr['strayPet']->serial_number ?? ($qr['strayPet']->animal_type ?? 'حيوان غير مسمى') }}
                                        </h6>
                                        <img src="{{ $qr['qr_image_data_uri'] }}" alt="QR Code" class="img-fluid mb-2" style="max-width: 150px;">
                                        <p class="small text-muted" style="word-wrap: break-word;">{{ $qr['strayPet']->uuid }}</p>
                                        
                                        <div class="btn-group mt-auto" role="group">
                                            <a href="{{ $qr['qr_file_path'] }}" download="{{ ($qr['strayPet']->serial_number ?? $qr['strayPet']->uuid) }}_qrcode.png" class="btn btn-sm btn-outline-success" title="تحميل PNG"><i class="fas fa-download"></i></a>
                                            <a href="{{ route('stray-pets.data-entry-form', ['uuid' => $qr['strayPet']->uuid]) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="إدخال/تعديل بيانات"><i class="fas fa-edit"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            @endif
        </div>

        {{-- التصفح --}}
        @if (!empty($qrsToDisplay) && $pagination->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $pagination->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

{{-- لا حاجة لأي JavaScript خاص بهذه الصفحة الآن --}}
@endsection