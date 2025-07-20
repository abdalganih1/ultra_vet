@extends('layouts.app')

@section('title', 'معاينة وطباعة رموز QR')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-print me-2"></i>معاينة رموز QR المولدة</h4>
            <form action="{{ route('qrcodes.stray.print.pdf') }}" method="POST" class="d-inline">
                @csrf
                @foreach($generatedQRs as $qr)
                    <input type="hidden" name="uuids[]" value="{{ $qr['strayPet']->uuid }}">
                @endforeach
                <button type="submit" class="btn btn-info btn-sm text-white"><i class="fas fa-file-pdf me-1"></i> طباعة الكل إلى PDF</button>
            </form>
        </div>
        <div class="card-body">
            @if (empty($generatedQRs))
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>لم يتم توليد أي رموز QR في هذه الجلسة.
                    <a href="{{ route('qrcodes.stray.generate.form') }}">انتقل إلى صفحة التوليد.</a>
                </div>
            @else
                <p class="mb-4">تم توليد الرموز التالية. يمكنك معاينتها أو طباعتها.</p>
                <div class="row g-3">
                    @foreach ($generatedQRs as $qr)
                    <div class="col-md-4 col-lg-3 text-center">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title text-truncate">{{ $qr['strayPet']->serial_number ?? ($qr['strayPet']->animal_type ?? 'حيوان غير مسمى') . ' (' . Str::limit($qr['strayPet']->uuid, 8, '') . ')' }}</h6>
                                <img src="{{ $qr['qr_image_data_uri'] }}" alt="QR Code for {{ $qr['strayPet']->serial_number ?? $qr['strayPet']->uuid }}" class="img-fluid mb-2" style="max-width: 150px;">
                                <p class="small text-muted">{{ $qr['strayPet']->uuid }}</p>
                                <a href="{{ $qr['qr_file_path'] }}" download="{{ ($qr['strayPet']->serial_number ?? $qr['strayPet']->uuid) }}_qrcode.png" class="btn btn-sm btn-success"><i class="fas fa-download me-1"></i>تحميل</a>
                                <a href="{{ route('stray-pets.data-entry-form', ['uuid' => $qr['strayPet']->uuid]) }}" target="_blank" class="btn btn-sm btn-info text-white mt-2 mt-md-0 ms-md-1"><i class="fas fa-edit me-1"></i>إدخال بيانات</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection