@extends('layouts.app')

@section('title', 'معلومات حيوان شارد - UltraVet')

@section('content')
<header class="bg-light py-3 mb-4 border-bottom">
    <div class="container text-center">
        <img src="{{ asset('images/img.jpg') }}" alt="UltraVet Logo" style="max-height: 60px;" class="mb-2">
        <h1 class="h3" style="color: var(--brand-teal);">
            <span class="lang-ar">معلومات حول الحيوان الشارد</span>
            <span class="lang-en">Stray Animal Information</span>
        </h1>
        <p class="text-muted">
            <span class="lang-ar">مبادرة UltraVet لرعاية الحيوانات</span>
            <span class="lang-en">UltraVet Animal Care Initiative</span>
        </p>
        <p class="lead fw-bold">
            <span class="lang-ar">الرقم التسلسلي: {{ $strayPet->serial_number ?? $strayPet->uuid }}</span>
            <span class="lang-en">Serial Number: {{ $strayPet->serial_number ?? $strayPet->uuid }}</span>
        </p>
    </div>
</header>

<div class="container">
    <div class="info-card">
        <div class="card-header-custom text-center">
            <span class="lang-ar">معلومات الحيوان: <span class="fw-bold">{{ $strayPet->serial_number ?? 'غير مسمى' }}</span></span>
            <span class="lang-en">Animal Info: <span class="fw-bold">{{ $strayPet->serial_number ?? 'Unnamed' }}</span></span>
        </div>
        <div class="row g-0">
            {{-- عمود الصورة --}}
            <div class="col-md-5 dog-image-container">
                <img src="{{ $strayPet->image_path ? asset('storage/' . $strayPet->image_path) : asset('images/dog-placeholder.jpg') }}" alt="Stray Animal" class="dog-image">
            </div>

            {{-- عمود المعلومات --}}
            <div class="col-md-7">
                <div class="card-body p-4">
                    {{-- البيانات الأساسية --}}
                    <dl class="row info-list">
                        <dt class="col-sm-4"><i class="fas fa-tag me-2"></i><span class="lang-ar">النوع:</span><span class="lang-en">Type:</span></dt>
                        <dd class="col-sm-8">
                            <span class="lang-ar">{{ $strayPet->animal_type ?? '-' }} @if($strayPet->custom_animal_type) ({{ $strayPet->custom_animal_type }}) @endif</span>
                            <span class="lang-en">{{ str_replace('_', ' ', $strayPet->animal_type ?? '-') }} @if($strayPet->custom_animal_type) ({{ $strayPet->custom_animal_type }}) @endif</span>
                        </dd>

                        <dt class="col-sm-4"><i class="fas fa-venus-mars me-2"></i><span class="lang-ar">الجنس:</span><span class="lang-en">Gender:</span></dt>
                        <dd class="col-sm-8"><span class="lang-ar">{{ $strayPet->gender ?? '-' }}</span><span class="lang-en">{{ ucfirst($strayPet->gender ?? '-') }}</span></dd>

                        <dt class="col-sm-4"><i class="fas fa-calendar-alt me-2"></i><span class="lang-ar">العمر التقديري:</span><span class="lang-en">Estimated Age:</span></dt>
                        <dd class="col-sm-8"><span class="lang-ar">{{ $strayPet->estimated_age ?? '-' }}</span><span class="lang-en">{{ $strayPet->estimated_age ?? '-' }}</span></dd>

                        <dt class="col-sm-4"><i class="fas fa-palette me-2"></i><span class="lang-ar">اللون:</span><span class="lang-en">Color:</span></dt>
                        <dd class="col-sm-8"><span class="lang-ar">{{ $strayPet->color ?? '-' }}</span><span class="lang-en">{{ $strayPet->color ?? '-' }}</span></dd>

                        @if($strayPet->breed_name)
                        <dt class="col-sm-4"><i class="fas fa-paw me-2"></i><span class="lang-ar">السلالة:</span><span class="lang-en">Breed:</span></dt>
                        <dd class="col-sm-8"><span class="lang-ar">{{ $strayPet->breed_name }}</span><span class="lang-en">{{ $strayPet->breed_name }}</span></dd>
                        @endif

                        @if($strayPet->distinguishing_marks)
                        <dt class="col-sm-4"><i class="fas fa-pencil-alt me-2"></i><span class="lang-ar">علامات مميزة:</span><span class="lang-en">Dist. Marks:</span></dt>
                        <dd class="col-sm-8"><span class="lang-ar">{{ $strayPet->distinguishing_marks }}</span><span class="lang-en">{{ $strayPet->distinguishing_marks }}</span></dd>
                        @endif

                        <dt class="col-sm-4"><i class="fas fa-map-marker-alt me-2"></i><span class="lang-ar">مكان العثور:</span><span class="lang-en">Found Location:</span></dt>
                        <dd class="col-sm-8"><span class="lang-ar">{{ $strayPet->city_province ?? '-' }}</span><span class="lang-en">{{ $strayPet->city_province ?? '-' }}</span></dd>

                        <dt class="col-sm-4"><i class="fas fa-home me-2"></i><span class="lang-ar">مكان الإيواء:</span><span class="lang-en">Shelter Location:</span></dt>
                        <dd class="col-sm-8"><span class="lang-ar">{{ $strayPet->relocation_place ?? '-' }}</span><span class="lang-en">{{ $strayPet->relocation_place ?? '-' }}</span></dd>
                    </dl>

                    <hr style="border-color: var(--brand-light-blue);">
                    <h5 style="color: var(--brand-teal);">
                        <span class="lang-ar">الحالة الصحية والإجراءات:</span>
                        <span class="lang-en">Health Status & Procedures:</span>
                    </h5>
                    <dl class="row info-list mt-3">
                        @if($strayPet->medical_procedures && ($strayPet->medical_procedures['surgery_details'] || $strayPet->medical_procedures['other_treatments']))
                        <dt class="col-sm-4"><i class="fas fa-cut me-2"></i><span class="lang-ar">العمليات/العلاجات:</span><span class="lang-en">Surgeries/Treatments:</span></dt>
                        <dd class="col-sm-8">
                            <span class="lang-ar">
                                @if($strayPet->medical_procedures['surgery_details']){{ $strayPet->medical_procedures['surgery_details'] }}@endif
                                @if($strayPet->medical_procedures['other_treatments'] && $strayPet->medical_procedures['surgery_details'])<br>@endif
                                @if($strayPet->medical_procedures['other_treatments']){{ $strayPet->medical_procedures['other_treatments'] }}@endif
                                @if(empty($strayPet->medical_procedures['surgery_details']) && empty($strayPet->medical_procedures['other_treatments'])) - @endif
                            </span>
                            <span class="lang-en">
                                @if($strayPet->medical_procedures['surgery_details']){{ $strayPet->medical_procedures['surgery_details'] }}@endif
                                @if($strayPet->medical_procedures['other_treatments'] && $strayPet->medical_procedures['surgery_details'])<br>@endif
                                @if($strayPet->medical_procedures['other_treatments']){{ $strayPet->medical_procedures['other_treatments'] }}@endif
                                @if(empty($strayPet->medical_procedures['surgery_details']) && empty($strayPet->medical_procedures['other_treatments'])) - @endif
                            </span>
                        </dd>
                        @endif

                        @if($strayPet->parasite_treatments && ($strayPet->parasite_treatments['internal']['treatment'] || $strayPet->parasite_treatments['external']['treatment']))
                        <dt class="col-sm-4"><i class="fas fa-bug me-2"></i><span class="lang-ar">علاج الطفيليات:</span><span class="lang-en">Parasite Treatment:</span></dt>
                        <dd class="col-sm-8">
                            <span class="lang-ar">
                                داخلي: {{ $strayPet->parasite_treatments['internal']['treatment'] ?? '-' }}
                                ({{ $strayPet->parasite_treatments['internal']['date'] ?? '-' }})
                                @if($strayPet->parasite_treatments['internal']['dose'])، جرعة: {{ $strayPet->parasite_treatments['internal']['dose'] }}@endif
                                <br>
                                خارجي: {{ $strayPet->parasite_treatments['external']['treatment'] ?? '-' }}
                                ({{ $strayPet->parasite_treatments['external']['date'] ?? '-' }})
                                @if($strayPet->parasite_treatments['external']['dose'])، جرعة: {{ $strayPet->parasite_treatments['external']['dose'] }}@endif
                            </span>
                            <span class="lang-en">
                                Internal: {{ $strayPet->parasite_treatments['internal']['treatment'] ?? '-' }}
                                ({{ $strayPet->parasite_treatments['internal']['date'] ?? '-' }})
                                @if($strayPet->parasite_treatments['internal']['dose']), Dose: {{ $strayPet->parasite_treatments['internal']['dose'] }}@endif
                                <br>
                                External: {{ $strayPet->parasite_treatments['external']['treatment'] ?? '-' }}
                                ({{ $strayPet->parasite_treatments['external']['date'] ?? '-' }})
                                @if($strayPet->parasite_treatments['external']['dose']), Dose: {{ $strayPet->parasite_treatments['external']['dose'] }}@endif
                            </span>
                        </dd>
                        @endif

                        @if($strayPet->vaccinations_details && count($strayPet->vaccinations_details) > 0)
                        <dt class="col-sm-4"><i class="fas fa-syringe me-2"></i><span class="lang-ar">اللقاحات:</span><span class="lang-en">Vaccinations:</span></dt>
                        <dd class="col-sm-8">
                            <ul class="list-unstyled mb-0">
                                @foreach($strayPet->vaccinations_details as $vaccine)
                                <li>
                                    <span class="lang-ar">
                                        - {{ $vaccine['type'] == 'other' ? ($vaccine['custom_name'] ?? 'لقاح آخر') : (['rabies' => 'السعار', 'heptavalent' => 'سباعي'][$vaccine['type']] ?? $vaccine['type']) }}
                                        ({{ $vaccine['date_given'] ?? '-' }})
                                        @if($vaccine['manufacturer']), شركة: {{ $vaccine['manufacturer'] }}@endif
                                        @if($vaccine['date_next']), جرعة قادمة: {{ $vaccine['date_next'] }}@endif
                                    </span>
                                    <span class="lang-en">
                                        - {{ $vaccine['type'] == 'other' ? ($vaccine['custom_name'] ?? 'Other Vaccine') : (ucfirst($vaccine['type']) ) }}
                                        ({{ $vaccine['date_given'] ?? '-' }})
                                        @if($vaccine['manufacturer']), Manufacturer: {{ $vaccine['manufacturer'] }}@endif
                                        @if($vaccine['date_next']), Next Dose: {{ $vaccine['date_next'] }}@endif
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        </dd>
                        @endif
                    </dl>

                    <hr style="border-color: var(--brand-light-blue);">
                    <h5 style="color: var(--brand-teal);">
                        <span class="lang-ar">معلومات التواصل والجهة المشرفة:</span>
                        <span class="lang-en">Contact & Supervisory Info:</span>
                    </h5>
                    <dl class="row info-list mt-3">
                        <dt class="col-sm-4"><i class="fas fa-user-md me-2"></i><span class="lang-ar">الطبيب/المؤسسة:</span><span class="lang-en">Vet/Org:</span></dt>
                        <dd class="col-sm-8">
                            <span class="lang-ar">{{ $strayPet->medical_supervisor_info['vet_name'] ?? '-' }}</span>
                            <span class="lang-en">{{ $strayPet->medical_supervisor_info['vet_name'] ?? '-' }}</span>
                        </dd>
                        @if($strayPet->medical_supervisor_info['supervising_society'])
                        <dt class="col-sm-4"><i class="fas fa-hands-helping me-2"></i><span class="lang-ar">الجمعية المشرفة:</span><span class="lang-en">Supervising Society:</span></dt>
                        <dd class="col-sm-8">
                            <span class="lang-ar">{{ $strayPet->medical_supervisor_info['supervising_society'] }}</span>
                            <span class="lang-en">{{ $strayPet->medical_supervisor_info['supervising_society'] }}</span>
                        </dd>
                        @endif
                        <dt class="col-sm-4"><i class="fas fa-phone-alt me-2"></i><span class="lang-ar">طوارئ:</span><span class="lang-en">Emergency:</span></dt>
                        <dd class="col-sm-8">
                            <span class="lang-ar">{{ $strayPet->emergency_contact_phone ?? '-' }}</span>
                            <span class="lang-en">{{ $strayPet->emergency_contact_phone ?? '-' }}</span>
                        </dd>
                        <dt class="col-sm-4"><i class="fas fa-user-tie me-2"></i><span class="lang-ar">أدخل البيانات بواسطة:</span><span class="lang-en">Data entered by:</span></dt>
                        <dd class="col-sm-8">
                             <span class="lang-ar">{{ $strayPet->creator->name ?? 'غير معروف' }}</span>
                             <span class="lang-en">{{ $strayPet->creator->name ?? 'Unknown' }}</span>
                        </dd>
                        <dt class="col-sm-4"><i class="fas fa-clock me-2"></i><span class="lang-ar">آخر تحديث:</span><span class="lang-en">Last updated:</span></dt>
                        <dd class="col-sm-8">
                             <span class="lang-ar">{{ $strayPet->updated_at->format('d/m/Y H:i') }}</span>
                             <span class="lang-en">{{ $strayPet->updated_at->format('d/m/Y H:i') }}</span>
                        </dd>
                    </dl>

                    <div class="text-center qr-code-section mt-3">
                        <h5 style="color: var(--brand-teal);">
                            <span class="lang-ar">للتواصل أو للتبني:</span>
                            <span class="lang-en">For Contact or Adoption:</span>
                        </h5>
                        <img src="{{ $strayPet->qrCodeLink ? asset('storage/' . $strayPet->qrCodeLink->qr_image_path) : 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . route('stray-pets.public-view', $strayPet->uuid) }}" alt="QR Code" class="img-fluid" style="max-width: 180px;">
                        <p class="mt-2 small text-muted">
                            <span class="lang-ar">امسح الرمز أو تواصل معنا</span>
                            <span class="lang-en">Scan the code or contact us</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center my-5">
        {{-- زر تعديل البيانات يظهر فقط للمدير ومدخل البيانات --}}
        @auth
            @if(Auth::user()->isAdmin() || Auth::user()->isDataEntry())
            <a href="{{ route('stray-pets.data-entry-form', ['uuid' => $strayPet->uuid]) }}" class="btn btn-primary btn-lg px-5 me-3">
                <i class="fas fa-edit me-2"></i>
                <span class="lang-ar">تعديل بيانات الحيوان</span>
                <span class="lang-en">Edit Animal Data</span>
            </a>
            @endif
        @endauth
        
        {{-- زر الموقع الرئيسي (يمكن أن يكون هنا أو في مكان آخر) --}}
        <a href="#" class="btn btn-secondary btn-lg px-5 disabled" aria-disabled="true">
            <i class="fas fa-globe me-2"></i>
            <span class="lang-ar">موقع UltraVet الرئيسي - قريباً جداً!</span>
            <span class="lang-en">UltraVet Main Site - Coming Soon!</span>
        </a>
    </div>
</div>

@push('scripts')
<script>
    // دالة تحديد اللغة (من قالب layouts.app)
    function setLanguage(lang) {
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
        // يمكنك حفظ اللغة المختارة في localStorage إذا أردت تذكرها عبر الجلسات
        // localStorage.setItem('preferredLanguage', lang);
        
        // تحديث النص في الـ dt/dd إذا كانت الأيقونة مضمنة مباشرة
        // لا حاجة هنا لأننا نستخدم span
    }
    // افتراضيًا، ابدأ باللغة العربية
    setLanguage('ar');

    // لـ currentYearAr و currentYearEn، يجب أن تكون الأوسمة موجودة في layouts/app.blade.php
    // إذا كنت تريدها في هذه الصفحة أيضاً، أضف الأوسمة <span id="currentYearAr"></span> و <span id="currentYearEn"></span> هنا.
    // لكن الأفضل أن تكون في الـ Layout فقط.
</script>
@endpush
@endsection