@extends('layouts.app')

@section('title', 'قائمة الحيوانات الشاردة')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6 fw-light">قائمة الحيوانات الشاردة</h1>
        <div>
             <a href="{{ route('qrcodes.stray.generate.form') }}" class="btn btn-primary me-2"><i class="fas fa-qrcode me-1"></i> توليد QR جديد</a>
        </div>
    </div>
    <hr>

    <div class="row g-4">
        @forelse ($strayPets as $pet)
        <div class="col-md-6 col-lg-4">
            <div class="card pet-card shadow-sm h-100">
                <img src="{{ $pet->image_path ? asset('storage/' . $pet->image_path) : asset('images/dog-placeholder.jpg') }}" class="card-img-top" alt="صورة {{ $pet->serial_number }}">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold">{{ $pet->serial_number ?? 'غير مسمى' }}
                        <i class="fas fa-{{ $pet->animal_type == 'dog_baladi' || $pet->animal_type == 'dog_breed' ? 'dog' : ($pet->animal_type == 'cat_baladi' || $pet->animal_type == 'cat_breed' ? 'cat' : 'paw') }} ms-1 text-muted"></i>
                    </h5>
                    <p class="card-text text-muted">
                        النوع: {{ $pet->animal_type ?? 'غير محدد' }}
                        @if($pet->custom_animal_type) ({{ $pet->custom_animal_type }}) @endif
                        @if($pet->breed_name) / السلالة: {{ $pet->breed_name }} @endif
                    </p>
                    <p class="card-text small">
                        العمر التقديري: {{ $pet->estimated_age ?? 'غير محدد' }}
                        <br>
                        المكان: {{ $pet->relocation_place ?? 'غير محدد' }}
                    </p>
                    <div class="mt-auto text-center">
                        <a href="{{ route('stray-pets.show', $pet->uuid) }}" class="btn btn-primary w-100"><i class="fas fa-eye me-1"></i> عرض/تعديل البيانات</a>
                    </div>
                </div>
                <div class="card-footer text-muted small d-flex justify-content-between align-items-center">
                    <span>UUID: {{ $pet->uuid }}</span>
                    @if($pet->qrCodeLink)
                    <a href="{{ asset('storage/' . $pet->qrCodeLink->qr_image_path) }}" download class="btn btn-link btn-sm p-0 ms-2" title="تحميل QR Code"><i class="fas fa-download"></i></a>
                    <form action="{{ route('stray-pets.destroy', $pet->uuid) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger px-2 py-1 ms-2" onclick="return confirm('هل أنت متأكد من حذف هذا الحيوان؟ سيتم حذف جميع بياناته ورمز QR.')" title="حذف الحيوان"><i class="fas fa-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>لا توجد حيوانات شاردة مسجلة بعد. <a href="{{ route('qrcodes.stray.generate.form') }}">قم بتوليد أول QR Code!</a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection