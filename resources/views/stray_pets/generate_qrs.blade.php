@extends('layouts.app')

@section('title', 'توليد رموز QR للحيوانات الشاردة')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-qrcode me-2"></i>توليد رموز QR للحيوانات الشاردة</h4>
        </div>
        <div class="card-body">
            <p class="text-muted">هنا يمكنك توليد عدد من رموز QR لحيوانات شاردة جديدة. يمكنك إدخال بيانات موحدة لتعبئتها تلقائياً في نموذج البيانات لاحقاً.</p>
            <hr>
            <form action="{{ route('qrcodes.stray.generate.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="num_qrcodes" class="form-label">عدد رموز QR المراد توليدها:</label>
                    <input type="number" class="form-control @error('num_qrcodes') is-invalid @enderror" id="num_qrcodes" name="num_qrcodes" min="1" max="500" value="1" required>
                    @error('num_qrcodes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <hr class="my-4">
                <h5 class="mb-3" style="color: #2c3e50;"><i class="fas fa-pencil-alt me-2"></i>بيانات موحدة للتعبئة المسبقة (اختياري)</h5>
                <p class="text-muted small">هذه البيانات ستُعبأ تلقائياً في نموذج إدخال البيانات عند مسح QR Code لاحقاً.</p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="animal_type_prefill" class="form-label">النوع:</label>
                        <select class="form-select" id="animal_type_prefill" name="animal_type_prefill">
                            <option value="">لا شيء (يدوي)</option>
                            <option value="dog_baladi">كلب / بلدي</option>
                            <option value="dog_breed">كلب / سلالة أخرى</option>
                            <option value="cat_baladi">قط / بلدي</option>
                            <option value="cat_breed">قط / سلالة أخرى</option>
                            <option value="other">أخرى (يُحدد لاحقاً)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="gender_prefill" class="form-label">الجنس:</label>
                        <select class="form-select" id="gender_prefill" name="gender_prefill">
                            <option value="">لا شيء (يدوي)</option>
                            <option value="male">ذكر</option>
                            <option value="female">أنثى</option>
                            <option value="unknown">غير معروف</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="estimated_age_prefill" class="form-label">العمر التقديري:</label>
                        <input type="text" class="form-control" id="estimated_age_prefill" name="estimated_age_prefill" placeholder="مثال: سنتان، صغير">
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-cogs me-2"></i>ابدأ التوليد</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection