@extends('layouts.app')

@section('title', isset($strayPet) && $strayPet->serial_number ? 'تعديل بيانات حيوان' : 'إدخال بيانات حيوان جديد')

@section('content')
<header class="bg-light py-3 mb-4 border-bottom">
    <div class="container text-center">
        {{-- تأكد من مسار الشعار الصحيح --}}
        <img src="{{ asset('images/img.jpg') }}" alt="UltraVet Logo" style="max-height: 60px;" class="mb-2">
        <h1 class="h3" style="color: var(--brand-teal);">نموذج إدخال بيانات الحيوانات الشاردة</h1>
        <p class="text-muted">مبادرة UltraVet لرعاية الحيوانات</p>
        <p class="lead fw-bold">
            الرقم التعريفي (UUID): <span class="text-primary">{{ $strayPet->uuid }}</span>
        </p>
    </div>
</header>

<div class="container">
    <form id="animalDataForm" action="{{ route('stray-pets.store-or-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- UUID الحيوان يتم تمريره كمُدخل مخفي --}}
        <input type="hidden" name="uuid" value="{{ $strayPet->uuid }}">

        <!-- القسم الأول: البيانات الأساسية للحيوان -->
        <div class="card form-section-card">
            <div class="card-header">
                البيانات الأساسية للحيوان
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    {{-- الرقم التسلسلي للحيوان (تلقائي ويعرض UUID إذا لم يدخل بعد) --}}
                    <div class="col-md-6">
                        <label for="serialNumber" class="form-label">الرقم التسلسلي للحيوان:</label>
                        <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serialNumber" name="serial_number" 
                               placeholder="مثال: UV-K9-00123" 
                               value="{{ old('serial_number', $strayPet->serial_number ?? $strayPet->uuid) }}" 
                               readonly> {{-- حقل قراءة فقط --}}
                        @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- مكان العثور --}}
                    <div class="col-md-6">
                        <label for="city_of_finding" class="form-label">مكان العثور (المحافظة/المدينة):</label>
                        <input type="text" class="form-control @error('city_province') is-invalid @enderror" id="city_of_finding" name="city_province" placeholder="مثال: حماه \ طريق حلب" value="{{ old('city_province', $strayPet->city_province ?? '') }}">
                        @error('city_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- مكان إعادة التوطين --}}
                    <div class="col-md-12">
                        <label for="relocationPlace" class="form-label">مكان إعادة التوطين:</label>
                        <input type="text" class="form-control @error('relocation_place') is-invalid @enderror" id="relocationPlace" name="relocation_place" placeholder="مثال: ملجأ UltraVet، أو منزل التبني" value="{{ old('relocation_place', $strayPet->relocation_place ?? '') }}">
                        @error('relocation_place')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- نوع الحيوان --}}
                    <div class="col-md-6">
                        <label for="animalType" class="form-label">نوع الحيوان:</label>
                        <select class="form-select @error('animal_type') is-invalid @enderror" id="animalType" name="animal_type">
                            <option value="">اختر...</option>
                            <option value="dog_baladi" {{ old('animal_type', $strayPet->animal_type ?? ($prefill['animal_type'] ?? '')) == 'dog_baladi' ? 'selected' : '' }}>كلب / بلدي</option>
                            <option value="dog_breed" {{ old('animal_type', $strayPet->animal_type ?? ($prefill['animal_type'] ?? '')) == 'dog_breed' ? 'selected' : '' }}>كلب / سلالة أخرى</option>
                            <option value="cat_baladi" {{ old('animal_type', $strayPet->animal_type ?? ($prefill['animal_type'] ?? '')) == 'cat_baladi' ? 'selected' : '' }}>قط / بلدي</option>
                            <option value="cat_breed" {{ old('animal_type', $strayPet->animal_type ?? ($prefill['animal_type'] ?? '')) == 'cat_breed' ? 'selected' : '' }}>قط / سلالة أخرى</option>
                            <option value="other" {{ old('animal_type', $strayPet->animal_type ?? ($prefill['animal_type'] ?? '')) == 'other' ? 'selected' : '' }}>أخرى (يُحدد)</option>
                        </select>
                        @error('animal_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- تحديد نوع الحيوان الآخر --}}
                    <div class="col-md-6" id="otherAnimalTypeContainer" style="display: {{ old('animal_type', $strayPet->animal_type ?? ($prefill['animal_type'] ?? '')) == 'other' ? 'block' : 'none' }};">
                        <label for="otherAnimalTypeName" class="form-label">تحديد نوع الحيوان الآخر:</label>
                        <input type="text" class="form-control" id="otherAnimalTypeName" name="custom_animal_type" placeholder="مثال: أرنب، حصان" value="{{ old('custom_animal_type', $strayPet->custom_animal_type ?? '') }}">
                    </div>
                    {{-- اسم السلالة --}}
                     <div class="col-md-6">
                        <label for="breedName" class="form-label">اسم السلالة (إن وجدت):</label>
                        <input type="text" class="form-control" id="breedName" name="breed_name" placeholder="مثال: شيرازي، جولدن ريتريفر" value="{{ old('breed_name', $strayPet->breed_name ?? '') }}">
                    </div>
                    {{-- الجنس --}}
                    <div class="col-md-6">
                        <label for="gender" class="form-label">الجنس:</label>
                       <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="">اختر...</option>
                            <option value="male" {{ old('gender', $strayPet->gender ?? ($prefill['gender'] ?? '')) == 'male' ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender', $strayPet->gender ?? ($prefill['gender'] ?? '')) == 'female' ? 'selected' : '' }}>أنثى</option>
                            <option value="unknown" {{ old('gender', $strayPet->gender ?? ($prefill['gender'] ?? '')) == 'unknown' ? 'selected' : '' }}>غير معروف</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- العمر التقديري --}}
                    <div class="col-md-6">
                        <label for="estimatedAge" class="form-label">العمر التقديري:</label>
                        <input type="text" class="form-control @error('estimated_age') is-invalid @enderror" id="estimatedAge" name="estimated_age" placeholder="مثال: سنتان، 6 أشهر" value="{{ old('estimated_age', $strayPet->estimated_age ?? ($prefill['estimated_age'] ?? '')) }}">
                        @error('estimated_age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- اللون --}}
                    <div class="col-md-6">
                        <label for="color" class="form-label">اللون:</label>
                        <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" name="color" placeholder="مثال: أبيض، أسود مرقط" value="{{ old('color', $strayPet->color ?? '') }}">
                        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- العلامات المميزة --}}
                     <div class="col-md-12">
                        <label for="distinguishingMarks" class="form-label">العلامات المميزة (إن وجدت):</label>
                        <textarea class="form-control" id="distinguishingMarks" name="distinguishing_marks" rows="2" placeholder="وصف أي علامات فارقة أو مميزة">{{ old('distinguishing_marks', $strayPet->distinguishing_marks ?? '') }}</textarea>
                    </div>
                    {{-- صورة الحيوان --}}
                    <div class="col-md-12">
                        <label for="image" class="form-label">صورة الحيوان:</label>
                        @if(isset($strayPet) && $strayPet->image_path)
                            <img src="{{ asset('storage/' . $strayPet->image_path) }}" alt="صورة الحيوان" class="img-thumbnail mb-2" style="max-width: 200px;">
                            <p class="small text-muted">الصورة الحالية. يمكنك رفع صورة جديدة لاستبدالها.</p>
                        @endif
                        <input class="form-control" type="file" id="image" name="image">
                    </div>
                </div>
            </div>
        </div>

        <!-- القسم الثاني: الإجراءات الطبية والوقائية -->
        <div class="card form-section-card">
            <div class="card-header">
                الإجراءات الطبية والوقائية
            </div>
            <div class="card-body p-4">
                {{-- العملية الجراحية --}}
                <div class="mb-3">
                    <label for="surgeryDetails" class="form-label">العملية الجراحية (إن وجدت):</label>
                    <textarea class="form-control" id="surgeryDetails" name="surgeryDetails" rows="3" placeholder="تفصيل العملية، التاريخ، والملاحظات (مثال: تعقيم، تاريخ: 10/10/2023، تمت بنجاح)">{{ old('surgeryDetails', $strayPet->medical_procedures['surgery_details'] ?? '') }}</textarea>
                </div>
                <hr>
                <h6 class="mb-3" style="color: var(--brand-teal);">علاج الطفيليات:</h6>
                {{-- الطفيليات الداخلية --}}
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="internalParasiteTreatment" class="form-label">الطفيليات الداخلية (اسم العلاج):</label>
                        <input type="text" class="form-control" id="internalParasiteTreatment" name="internalParasiteTreatment" placeholder="إدخال يدوي لاسم الدواء" value="{{ old('internalParasiteTreatment', $strayPet->parasite_treatments['internal']['treatment'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="internalParasiteDate" class="form-label">تاريخ العلاج الداخلي:</label>
                        <input type="date" class="form-control" id="internalParasiteDate" name="internalParasiteDate" value="{{ old('internalParasiteDate', $strayPet->parasite_treatments['internal']['date'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="internalParasiteDose" class="form-label">الجرعة (الداخلية):</label>
                        <input type="text" class="form-control" id="internalParasiteDose" name="internalParasiteDose" placeholder="مثال: قرص واحد، 2 مل" value="{{ old('internalParasiteDose', $strayPet->parasite_treatments['internal']['dose'] ?? '') }}">
                    </div>
                </div>
                {{-- الطفيليات الخارجية --}}
                <div class="row g-3 mt-2">
                     <div class="col-md-4">
                        <label for="externalParasiteTreatment" class="form-label">الطفيليات الخارجية (اسم العلاج):</label>
                        <input type="text" class="form-control" id="externalParasiteTreatment" name="externalParasiteTreatment" placeholder="إدخال يدوي لاسم الدواء" value="{{ old('externalParasiteTreatment', $strayPet->parasite_treatments['external']['treatment'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="externalParasiteDate" class="form-label">تاريخ العلاج الخارجي:</label>
                        <input type="date" class="form-control" id="externalParasiteDate" name="externalParasiteDate" value="{{ old('externalParasiteDate', $strayPet->parasite_treatments['external']['date'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="externalParasiteDose" class="form-label">الجرعة (الخارجية):</label>
                        <input type="text" class="form-control" id="externalParasiteDose" name="externalParasiteDose" placeholder="مثال: أمبول واحد، رشة" value="{{ old('externalParasiteDose', $strayPet->parasite_treatments['external']['dose'] ?? '') }}">
                    </div>
                </div>
                <hr>
                {{-- علاجات أخرى --}}
                 <div class="mb-3">
                    <label for="otherTreatments" class="form-label">علاجات أخرى:</label>
                    <textarea class="form-control" id="otherTreatments" name="otherTreatments" rows="3" placeholder="تفصيل أي علاجات إضافية تم تقديمها (مثال: علاج جرب، مضاد حيوي لالتهاب، فيتامينات)">{{ old('otherTreatments', $strayPet->medical_procedures['other_treatments'] ?? '') }}</textarea>
                </div>
                <hr>
                <h6 class="mb-3" style="color: var(--brand-teal);">اللقاحات:</h6>
                 <div id="vaccineEntries">
                    @php $vaccines = old('vaccine_type', $strayPet->vaccinations_details ?? []); @endphp
                    @if(empty($vaccines))
                        @php $vaccines = [null]; @endphp {{-- ابدأ بمدخل فارغ واحد إذا لم يكن هناك بيانات سابقة --}}
                    @endif

                    @foreach($vaccines as $key => $vaccine)
                    <div class="vaccine-entry border p-3 mb-3 rounded">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">نوع اللقاح:</label>
                                <select class="form-select vaccine-type" name="vaccine_type[]">
                                    <option value="">اختر...</option>
                                    <option value="rabies" {{ ($vaccine['type'] ?? '') == 'rabies' ? 'selected' : '' }}>السعار</option>
                                    <option value="heptavalent" {{ ($vaccine['type'] ?? '') == 'heptavalent' ? 'selected' : '' }}>سباعي</option>
                                    <option value="other" {{ ($vaccine['type'] ?? '') == 'other' ? 'selected' : '' }}>لقاح آخر (يُحدد)</option>
                                </select>
                            </div>
                            <div class="col-md-3 other-vaccine-name-container" style="display: {{ ($vaccine['type'] ?? '') == 'other' ? 'block' : 'none' }};">
                                 <label class="form-label">اسم اللقاح الآخر:</label>
                                 <input type="text" class="form-control other-vaccine-name" name="other_vaccine_name[]" placeholder="اسم اللقاح" value="{{ $vaccine['custom_name'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">الشركة المصنعة:</label>
                                <input type="text" class="form-control vaccine-manufacturer" name="vaccine_manufacturer[]" placeholder="مثال: Nobivac, Zoetis" value="{{ $vaccine['manufacturer'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">تاريخ الإعطاء:</label>
                                <input type="date" class="form-control vaccine-date-given" name="vaccine_date_given[]" value="{{ $vaccine['date_given'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">تاريخ الجرعة القادمة (إن وجدت):</label>
                                <input type="date" class="form-control vaccine-date-next" name="vaccine_date_next[]" value="{{ $vaccine['date_next'] ?? '' }}">
                            </div>
                        </div>
                        @if($key > 0)
                            <button type="button" class="btn btn-danger btn-sm mt-2 remove-vaccine-entry"><i class="fas fa-minus me-1"></i> إزالة لقاح</button>
                        @endif
                    </div>
                    @endforeach
                 </div>
                 <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="addVaccineEntry"><i class="fas fa-plus me-1"></i> إضافة لقاح آخر</button>
            </div>
        </div>

        <!-- القسم الثالث: الجهة الطبية المشرفة -->
        <div class="card form-section-card">
            <div class="card-header">
                الجهة الطبية المشرفة
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="vetName" class="form-label">اسم الطبيب البيطري أو المؤسسة:</label>
                        <input type="text" class="form-control @error('vetName') is-invalid @enderror" id="vetName" name="vetName" placeholder="مثال: د. أحمد محمود / عيادة الأمل البيطرية" value="{{ old('vetName', $strayPet->medical_supervisor_info['vet_name'] ?? ($prefill['vet_name'] ?? '')) }}">
                        @error('vetName')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="supervisingSociety" class="form-label">اسم الجمعية المشرفة (إن وجدت):</label>
                        <input type="text" class="form-control" id="supervisingSociety" name="supervisingSociety" placeholder="مثال: جمعية حماية الحيوان بالقاهرة" value="{{ old('supervisingSociety', $strayPet->medical_supervisor_info['supervising_society'] ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- القسم الرابع: معلومات التواصل في حالات الطوارئ -->
        <div class="card form-section-card">
            <div class="card-header">
                معلومات التواصل في حالات الطوارئ
            </div>
            <div class="card-body p-4">
                 <div class="col-md-12">
                    <label for="emergencyContact" class="form-label">رقم الهاتف / واتساب للطوارئ:</label>
                    <input type="tel" class="form-control @error('emergency_contact') is-invalid @enderror" id="emergencyContact" name="emergency_contact" placeholder="مثال: 01xxxxxxxxx" value="{{ old('emergency_contact', $strayPet->emergency_contact_phone ?? ($prefill['emergency_contact_phone'] ?? '')) }}">
                    @error('emergency_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <!-- زر الإرسال -->
        <div class="text-center mt-4 mb-5">
            <button type="submit" class="btn btn-submit btn-lg px-5">
                <i class="fas fa-save me-2"></i> حفظ البيانات
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    document.getElementById('currentYear').textContent = new Date().getFullYear();

    // إظهار حقل "تحديد نوع الحيوان الآخر" عند اختيار "أخرى"
    const animalTypeSelect = document.getElementById('animalType');
    const otherAnimalTypeContainer = document.getElementById('otherAnimalTypeContainer');
    animalTypeSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            otherAnimalTypeContainer.style.display = 'block';
            // otherAnimalTypeContainer.querySelector('input').required = true; // تم إزالة required
        } else {
            otherAnimalTypeContainer.style.display = 'none';
            // otherAnimalTypeContainer.querySelector('input').required = false; // تم إزالة required
            otherAnimalTypeContainer.querySelector('input').value = '';
        }
    });
    // تشغيل عند التحميل الأولي
    if (animalTypeSelect.value === 'other') {
        otherAnimalTypeContainer.style.display = 'block';
        // otherAnimalTypeContainer.querySelector('input').required = true; // تم إزالة required
    }


    // التعامل مع حقل "اسم اللقاح الآخر" لكل مدخل لقاح
    function handleOtherVaccineName(vaccineEntry) {
        const vaccineTypeSelect = vaccineEntry.querySelector('.vaccine-type');
        const otherVaccineNameContainer = vaccineEntry.querySelector('.other-vaccine-name-container');
        const otherVaccineNameInput = vaccineEntry.querySelector('.other-vaccine-name');

        if (vaccineTypeSelect.value === 'other') {
            otherVaccineNameContainer.style.display = 'block';
            // otherVaccineNameInput.required = true; // تم إزالة required
        } else {
            otherVaccineNameContainer.style.display = 'none';
            // otherVaccineNameInput.required = false; // تم إزالة required
            otherVaccineNameInput.value = '';
        }
    }

    // تطبيق الدالة على جميع مدخلات اللقاح الموجودة (للتحقق عند التحميل الأولي)
    document.querySelectorAll('.vaccine-entry').forEach(entry => {
        handleOtherVaccineName(entry);
        entry.querySelector('.vaccine-type').addEventListener('change', function() {
            handleOtherVaccineName(entry);
        });
    });

    // إضافة مدخل لقاح جديد
    const addVaccineButton = document.getElementById('addVaccineEntry');
    const vaccineEntriesContainer = document.getElementById('vaccineEntries');

    addVaccineButton.addEventListener('click', function() {
        const newVaccineEntry = document.createElement('div');
        newVaccineEntry.classList.add('vaccine-entry', 'border', 'p-3', 'mb-3', 'rounded');
        newVaccineEntry.innerHTML = `
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">نوع اللقاح:</label>
                    <select class="form-select vaccine-type" name="vaccine_type[]">
                        <option value="">اختر...</option>
                        <option value="rabies">السعار</option>
                        <option value="heptavalent">سباعي</option>
                        <option value="other">لقاح آخر (يُحدد)</option>
                    </select>
                </div>
                <div class="col-md-3 other-vaccine-name-container" style="display: none;">
                     <label class="form-label">اسم اللقاح الآخر:</label>
                     <input type="text" class="form-control other-vaccine-name" name="other_vaccine_name[]" placeholder="اسم اللقاح">
                </div>
                <div class="col-md-3">
                    <label class="form-label">الشركة المصنعة:</label>
                    <input type="text" class="form-control vaccine-manufacturer" name="vaccine_manufacturer[]" placeholder="مثال: Nobivac, Zoetis">
                </div>
                <div class="col-md-3">
                    <label class="form-label">تاريخ الإعطاء:</label>
                    <input type="date" class="form-control vaccine-date-given" name="vaccine_date_given[]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">تاريخ الجرعة القادمة (إن وجدت):</label>
                    <input type="date" class="form-control vaccine-date-next" name="vaccine_date_next[]">
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-vaccine-entry"><i class="fas fa-minus me-1"></i> إزالة لقاح</button>
        `;

        // ربط مستمعات الأحداث للمدخل الجديد
        newVaccineEntry.querySelector('.vaccine-type').addEventListener('change', function() {
            handleOtherVaccineName(newVaccineEntry);
        });
        newVaccineEntry.querySelector('.remove-vaccine-entry').addEventListener('click', function() {
            newVaccineEntry.remove();
        });

        vaccineEntriesContainer.appendChild(newVaccineEntry);
    });

    // إزالة مدخل لقاح
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-vaccine-entry')) {
            event.target.closest('.vaccine-entry').remove();
        }
    });

</script>
@endpush
@endsection