@extends('layouts.app')

@section('title', isset($strayPet) && $strayPet->data_entered_status ? 'تعديل بيانات حيوان' : 'إدخال بيانات حيوان جديد')

@section('content')
<header class="bg-light py-3 mb-4 border-bottom">
    <div class="container text-center">
        <img src="{{ asset('images/img.jpg') }}" alt="UltraVet Logo" style="max-height: 60px;" class="mb-2">
        <h1 class="h3" style="color: var(--brand-teal);">نموذج إدخال بيانات الحيوانات الشاردة</h1>
        <p class="text-muted">مبادرة UltraVet لرعاية الحيوانات</p>
        <p class="lead fw-bold">
            الرقم التعريفي (UUID): <span class="text-primary">{{ $strayPet->uuid }}</span>
        </p>
    </div>
</header>

<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="animalDataForm" action="{{ route('stray-pets.store-or-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="uuid" value="{{ $strayPet->uuid }}">

        <!-- Section: Supervising Association and Team -->
        <div class="card form-section-card mb-4">
            <div class="card-header fw-bold">
                <i class="fas fa-sitemap me-2"></i>الجهة المشرفة والفريق المنفذ
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="supervising_association" class="form-label">الجمعية المشرفة:</label>
                        <select class="form-select @error('supervising_association') is-invalid @enderror" id="supervising_association" name="supervising_association" required @if(Auth::user()->role !== 'admin') disabled @endif>
                            <option value="">اختر الجمعية...</option>
                            @foreach($governorates as $governorate)
                                <option value="فريق ultravet لمحافظة {{ $governorate->name }}" {{ old('supervising_association', $strayPet->supervising_association) == "فريق ultravet لمحافظة {$governorate->name}" ? 'selected' : '' }}>
                                    فريق ultravet لمحافظة {{ $governorate->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(Auth::user()->role !== 'admin')
                            <input type="hidden" name="supervising_association" value="{{ $strayPet->supervising_association }}">
                        @endif
                        @error('supervising_association')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="independent_team_id" class="form-label">الفريق المستقل المنفذ:</label>
                        <select class="form-select @error('independent_team_id') is-invalid @enderror" id="independent_team_id" name="independent_team_id" required @if(Auth::user()->role !== 'admin') disabled @endif>
                            <option value="">اختر الفريق...</option>
                            @foreach($independentTeams as $team)
                                <option value="{{ $team->id }}" data-governorate="{{ $team->governorate_id }}" {{ old('independent_team_id', $strayPet->independent_team_id) == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }} ({{ $team->governorate->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @if(Auth::user()->role !== 'admin')
                            <input type="hidden" name="independent_team_id" value="{{ $strayPet->independent_team_id }}">
                        @endif
                        @error('independent_team_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Basic Animal Data -->
        <div class="card form-section-card mb-4">
            <div class="card-header fw-bold">
                <i class="fas fa-paw me-2"></i>البيانات الأساسية للحيوان
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="serial_number" class="form-label">الرقم التسلسلي للحيوان:</label>
                        <input type="text" class="form-control bg-light" id="serial_number" name="serial_number" 
                               placeholder="يتم توليده تلقائياً عند الحفظ" 
                               value="{{ $strayPet->serial_number ?? '' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="city_province" class="form-label">مكان العثور (عربي):</label>
                        <input type="text" class="form-control @error('city_province') is-invalid @enderror" id="city_province" name="city_province" placeholder="مثال: حماه \ طريق حلب" value="{{ old('city_province', $strayPet->city_province) }}">
                        @error('city_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="city_province_en" class="form-label">Found Location (English):</label>
                        <input type="text" class="form-control @error('city_province_en') is-invalid @enderror" id="city_province_en" name="city_province_en" placeholder="e.g., Hama / Aleppo Road" value="{{ old('city_province_en', $strayPet->city_province_en) }}">
                        @error('city_province_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="relocation_place" class="form-label">مكان إطلاق السراح (عربي):</label>
                        <input type="text" class="form-control @error('relocation_place') is-invalid @enderror" id="relocation_place" name="relocation_place" placeholder="مثال: نفس مكان الإمساك" value="{{ old('relocation_place', $strayPet->relocation_place) }}">
                        @error('relocation_place')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                     <div class="col-md-6">
                        <label for="relocation_place_en" class="form-label">Release Location (English):</label>
                        <input type="text" class="form-control @error('relocation_place_en') is-invalid @enderror" id="relocation_place_en" name="relocation_place_en" placeholder="e.g., Same as found" value="{{ old('relocation_place_en', $strayPet->relocation_place_en) }}">
                        @error('relocation_place_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Animal Details & Appearance -->
        <div class="card form-section-card mb-4">
            <div class="card-header fw-bold">
                <i class="fas fa-dog me-2"></i>تفاصيل ومظهر الحيوان
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="animal_type" class="form-label">نوع الحيوان:</label>
                        <select class="form-select @error('animal_type') is-invalid @enderror" id="animal_type" name="animal_type">
                            <option value="">اختر النوع...</option>
                            <option value="كلب" {{ old('animal_type', $strayPet->animal_type ?? 'كلب') == 'كلب' ? 'selected' : '' }}>كلب</option>
                            <option value="قطة" {{ old('animal_type', $strayPet->animal_type) == 'قطة' ? 'selected' : '' }}>قطة</option>
                            <option value="آخر" {{ old('animal_type', $strayPet->animal_type) == 'آخر' ? 'selected' : '' }}>آخر (حدد)</option>
                        </select>
                        @error('animal_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6" id="custom_animal_type_wrapper" style="{{ old('animal_type', $strayPet->animal_type) == 'آخر' ? '' : 'display: none;' }}">
                        <label for="custom_animal_type" class="form-label">تحديد نوع الحيوان:</label>
                        <input type="text" class="form-control @error('custom_animal_type') is-invalid @enderror" id="custom_animal_type" name="custom_animal_type" value="{{ old('custom_animal_type', $strayPet->custom_animal_type) }}">
                        @error('custom_animal_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="breed_name" class="form-label">السلالة (عربي):</label>
                        <input type="text" class="form-control @error('breed_name') is-invalid @enderror" id="breed_name" name="breed_name" placeholder="مثال: بلدي كنعاني" value="{{ old('breed_name', $strayPet->breed_name) }}">
                        @error('breed_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="breed_name_en" class="form-label">Breed (English):</label>
                        <input type="text" class="form-control @error('breed_name_en') is-invalid @enderror" id="breed_name_en" name="breed_name_en" placeholder="e.g., Canaan Dog" value="{{ old('breed_name_en', $strayPet->breed_name_en ?? 'Baladi Canaan') }}">
                        @error('breed_name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="gender" class="form-label">الجنس:</label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="">اختر الجنس...</option>
                            <option value="male" {{ old('gender', $strayPet->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender', $strayPet->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="estimated_age" class="form-label">العمر التقريبي:</label>
                        <input type="text" class="form-control @error('estimated_age') is-invalid @enderror" id="estimated_age" name="estimated_age" placeholder="مثال: سنتان" value="{{ old('estimated_age', $strayPet->estimated_age) }}">
                        @error('estimated_age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="color" class="form-label">اللون (عربي):</label>
                        <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" name="color" placeholder="مثال: أسود وبني" value="{{ old('color', $strayPet->color) }}">
                        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="color_en" class="form-label">Color (English):</label>
                        <input type="text" class="form-control @error('color_en') is-invalid @enderror" id="color_en" name="color_en" placeholder="e.g., Black and Brown" value="{{ old('color_en', $strayPet->color_en) }}">
                        @error('color_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="distinguishing_marks" class="form-label">علامات مميزة (عربي):</label>
                        <textarea class="form-control @error('distinguishing_marks') is-invalid @enderror" id="distinguishing_marks" name="distinguishing_marks" rows="3">{{ old('distinguishing_marks', $strayPet->distinguishing_marks) }}</textarea>
                        @error('distinguishing_marks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="distinguishing_marks_en" class="form-label">Distinguishing Marks (English):</label>
                        <textarea class="form-control @error('distinguishing_marks_en') is-invalid @enderror" id="distinguishing_marks_en" name="distinguishing_marks_en" rows="3">{{ old('distinguishing_marks_en', $strayPet->distinguishing_marks_en) }}</textarea>
                        @error('distinguishing_marks_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="image" class="form-label">صورة الحيوان:</label>
                        <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($strayPet->image_path)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $strayPet->image_path) }}" alt="صورة الحيوان الحالية" class="img-thumbnail" style="max-width: 200px;">
                                <p class="text-muted small">الصورة الحالية</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="card form-section-card mb-4">
            <div class="card-header fw-bold">
                <i class="fas fa-briefcase-medical me-2"></i>المعلومات الطبية
            </div>
            <div class="card-body p-4">
                <!-- Medical Supervisor -->
                <h5 class="card-title mb-3">المشرف الطبي</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="vetName" class="form-label">اسم الطبيب البيطري المسؤول:</label>
                        <input type="text" class="form-control @error('vetName') is-invalid @enderror" id="vetName" name="vetName" value="{{ old('vetName', $prefill['vet_name']) }}">
                        @error('vetName')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="supervisingSociety" class="form-label">الجمعية الطبية المشرفة (إن وجدت):</label>
                        <input type="text" class="form-control @error('supervisingSociety') is-invalid @enderror" id="supervisingSociety" name="supervisingSociety" value="{{ old('supervisingSociety', $strayPet->medical_supervisor_info['supervising_society'] ?? '') }}">
                        @error('supervisingSociety')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Surgeries and Treatments -->
                <h5 class="card-title mb-3">العمليات الجراحية والعلاجات</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="surgeryDetails" class="form-label">تفاصيل عملية التعقيم (SPAY/NEUTER):</label>
                        <textarea class="form-control" id="surgeryDetails" name="surgeryDetails" rows="3">{{ old('surgeryDetails', $strayPet->medical_procedures['surgery_details'] ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="otherTreatments" class="form-label">علاجات أخرى:</label>
                        <textarea class="form-control" id="otherTreatments" name="otherTreatments" rows="3">{{ old('otherTreatments', $strayPet->medical_procedures['other_treatments'] ?? '') }}</textarea>
                    </div>
                </div>

                <!-- Parasite Treatments -->
                <h5 class="card-title mb-3">علاجات الطفيليات</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <h6>الطفيليات الداخلية</h6>
                        <label for="internalParasiteTreatment" class="form-label">العلاج:</label>
                        <input type="text" class="form-control mb-2" id="internalParasiteTreatment" name="internalParasiteTreatment" value="{{ old('internalParasiteTreatment', $strayPet->parasite_treatments['internal']['treatment'] ?? '') }}">
                        <label for="internalParasiteDate" class="form-label">التاريخ:</label>
                        <input type="date" class="form-control mb-2" id="internalParasiteDate" name="internalParasiteDate" value="{{ old('internalParasiteDate', $strayPet->parasite_treatments['internal']['date'] ?? '') }}">
                        <label for="internalParasiteDose" class="form-label">الجرعة:</label>
                        <input type="text" class="form-control" id="internalParasiteDose" name="internalParasiteDose" value="{{ old('internalParasiteDose', $strayPet->parasite_treatments['internal']['dose'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <h6>الطفيليات الخارجية</h6>
                        <label for="externalParasiteTreatment" class="form-label">العلاج:</label>
                        <input type="text" class="form-control mb-2" id="externalParasiteTreatment" name="externalParasiteTreatment" value="{{ old('externalParasiteTreatment', $strayPet->parasite_treatments['external']['treatment'] ?? '') }}">
                        <label for="externalParasiteDate" class="form-label">التاريخ:</label>
                        <input type="date" class="form-control mb-2" id="externalParasiteDate" name="externalParasiteDate" value="{{ old('externalParasiteDate', $strayPet->parasite_treatments['external']['date'] ?? '') }}">
                        <label for="externalParasiteDose" class="form-label">الجرعة:</label>
                        <input type="text" class="form-control" id="externalParasiteDose" name="externalParasiteDose" value="{{ old('externalParasiteDose', $strayPet->parasite_treatments['external']['dose'] ?? '') }}">
                    </div>
                </div>

                <!-- Vaccinations -->
                <h5 class="card-title mb-3">اللقاحات</h5>
                <div id="vaccinations-container">
                    @if(is_array(old('vaccine_type', $strayPet->vaccinations_details)))
                        @foreach(old('vaccine_type', $strayPet->vaccinations_details) as $key => $vaccine)
                            <div class="vaccine-entry row g-3 mb-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">نوع اللقاح:</label>
                                    <select class="form-select vaccine-type" name="vaccine_type[]">
                                        <option value="">اختر...</option>
                                        <option value="سعفة" {{ (is_array($vaccine) ? $vaccine['type'] : $vaccine) == 'سعفة' ? 'selected' : '' }}>سعفة</option>
                                        <option value="رباعي" {{ (is_array($vaccine) ? $vaccine['type'] : $vaccine) == 'رباعي' ? 'selected' : '' }}>رباعي</option>
                                        <option value="آخر" {{ (is_array($vaccine) ? $vaccine['type'] : $vaccine) == 'آخر' ? 'selected' : '' }}>آخر</option>
                                    </select>
                                </div>
                                <div class="col-md-3 other-vaccine-name-wrapper" style="{{ (is_array($vaccine) ? $vaccine['type'] : $vaccine) == 'آخر' ? '' : 'display: none;' }}">
                                    <label class="form-label">اسم اللقاح الآخر:</label>
                                    <input type="text" class="form-control" name="other_vaccine_name[]" value="{{ old('other_vaccine_name.'.$key, is_array($vaccine) ? ($vaccine['custom_name'] ?? '') : '') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">الشركة المصنعة:</label>
                                    <input type="text" class="form-control" name="vaccine_manufacturer[]" value="{{ old('vaccine_manufacturer.'.$key, is_array($vaccine) ? ($vaccine['manufacturer'] ?? '') : '') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">تاريخ الإعطاء:</label>
                                    <input type="date" class="form-control" name="vaccine_date_given[]" value="{{ old('vaccine_date_given.'.$key, is_array($vaccine) ? ($vaccine['date_given'] ?? '') : '') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">التاريخ التالي:</label>
                                    <input type="date" class="form-control" name="vaccine_date_next[]" value="{{ old('vaccine_date_next.'.$key, is_array($vaccine) ? ($vaccine['date_next'] ?? '') : '') }}">
                                </div>
                                <div class="col-md-12 text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-vaccine-entry">حذف</button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" id="add-vaccine-entry" class="btn btn-outline-primary mt-2"><i class="fas fa-plus me-2"></i>إضافة لقاح</button>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card form-section-card mb-4">
            <div class="card-header fw-bold">
                <i class="fas fa-address-book me-2"></i>معلومات الاتصال للطوارئ
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="emergency_contact" class="form-label">رقم هاتف الطوارئ:</label>
                        <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $prefill['emergency_contact_phone']) }}">
                        @error('emergency_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 mb-5">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-save me-2"></i> حفظ البيانات
            </button>
        </div>
    </form>
</div>

@endsection
