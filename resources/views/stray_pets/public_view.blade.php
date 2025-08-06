@extends('layouts.app', ['hide_auth_links' => true])

@section('title', __('messages.public_view_title'))

@push('styles')
<style>
.info-card {
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    border: 1px solid #e9ecef;
}
.card-header-custom {
    background-color: var(--brand-teal);
    color: white;
    padding: 1rem 1.5rem;
    font-size: 1.5rem;
    font-weight: 600;
}
.dog-image-container {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    padding: 1rem;
}
.dog-image {
    width: 100%;
    height: 450px;
    object-fit: cover;
    border-radius: 10px;
}
.info-list dt {
    color: #343a40;
    font-weight: 600;
}
.info-list dd {
    color: #495057;
}
.qr-code-section {
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
}
</style>
@endpush

@section('content')
<header class="bg-light py-3 mb-4 border-bottom">
    <div class="container text-center">
        <img src="{{ asset('images/img.jpg') }}" alt="Logo" style="max-height: 100px;" class="mb-2">
        <h1 class="h3" style="color: var(--brand-teal);">
            @lang('messages.public_view_add_hope')
        </h1>
        <p class="text-muted">
            @lang('messages.public_view_total_registered', ['total' => $totalPets])
        </p>
        <p class="lead fw-bold">
            @lang('messages.public_view_serial_number'): {{ $strayPet->serial_number ?? $strayPet->uuid }}
        </p>
    </div>
</header>

<div class="container">
    <div class="info-card">
        <div class="card-header-custom text-center">
            @lang('messages.public_view_animal_info', ['name' => $strayPet->serial_number ?? __('messages.unnamed')])
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Image Section --}}
                <div class="col-12 dog-image-container mb-4">
                    <img src="{{ $strayPet->image_path ? asset('storage/' . $strayPet->image_path) : asset('images/dog-placeholder.jpg') }}" alt="@lang('messages.animal_image_alt')" class="dog-image">
                </div>

                {{-- Info Section --}}
                <div class="col-12">
                    {{-- Basic Info --}}
                    <dl class="row info-list">
                        <dt class="col-sm-4"><i class="fas fa-tag me-2"></i>@lang('messages.animal_type'):</dt>
                        <dd class="col-sm-8">
                            {{ app()->getLocale() == 'en' ? ($strayPet->animal_type_en ?? $strayPet->animal_type) : $strayPet->animal_type }}
                            @if($strayPet->custom_animal_type) ({{ $strayPet->custom_animal_type }}) @endif
                        </dd>

                        <dt class="col-sm-4"><i class="fas fa-venus-mars me-2"></i>@lang('messages.gender'):</dt>
                        <dd class="col-sm-8">{{ $strayPet->gender ? __('messages.gender_' . strtolower($strayPet->gender)) : '-' }}</dd>

                        <dt class="col-sm-4"><i class="fas fa-palette me-2"></i>@lang('messages.color'):</dt>
                        <dd class="col-sm-8">{{ app()->getLocale() == 'ar' ? ($strayPet->color ?? '-') : ($strayPet->color_en ?? $strayPet->color ?? '-') }}</dd>

                        @if($strayPet->breed_name || $strayPet->breed_name_en)
                        <dt class="col-sm-4"><i class="fas fa-paw me-2"></i>@lang('messages.breed'):</dt>
                        <dd class="col-sm-8">{{ app()->getLocale() == 'ar' ? $strayPet->breed_name : ($strayPet->breed_name_en ?? $strayPet->breed_name) }}</dd>
                        @endif

                        @if($strayPet->distinguishing_marks || $strayPet->distinguishing_marks_en)
                        <dt class="col-sm-4"><i class="fas fa-pencil-alt me-2"></i>@lang('messages.distinguishing_marks'):</dt>
                        <dd class="col-sm-8">{{ app()->getLocale() == 'ar' ? $strayPet->distinguishing_marks : ($strayPet->distinguishing_marks_en ?? $strayPet->distinguishing_marks) }}</dd>
                        @endif

                        <dt class="col-sm-4"><i class="fas fa-map-marker-alt me-2"></i>@lang('messages.found_location'):</dt>
                        <dd class="col-sm-8">{{ app()->getLocale() == 'ar' ? ($strayPet->city_province ?? '-') : ($strayPet->city_province_en ?? $strayPet->city_province ?? '-') }}</dd>

                        <dt class="col-sm-4"><i class="fas fa-home me-2"></i>@lang('messages.shelter_location'):</dt>
                        <dd class="col-sm-8">{{ app()->getLocale() == 'ar' ? ($strayPet->relocation_place ?? '-') : ($strayPet->relocation_place_en ?? $strayPet->relocation_place ?? '-') }}</dd>
                    </dl>

                    <hr>
                    <h5 class="details-header">@lang('messages.health_status_and_procedures')</h5>
                    <dl class="row info-list mt-3">
                        @if($strayPet->medical_procedures && ($strayPet->medical_procedures['surgery_details'] || $strayPet->medical_procedures['other_treatments']))
                        <dt class="col-sm-4"><i class="fas fa-cut me-2"></i>@lang('messages.surgeries_treatments'):</dt>
                        <dd class="col-sm-8">
                            @if($strayPet->medical_procedures['surgery_details']){{ $strayPet->medical_procedures['surgery_details'] }}@endif
                            @if($strayPet->medical_procedures['other_treatments'] && $strayPet->medical_procedures['surgery_details'])<br>@endif
                            @if($strayPet->medical_procedures['other_treatments']){{ $strayPet->medical_procedures['other_treatments'] }}@endif
                        </dd>
                        @endif

                        @if($strayPet->parasite_treatments && ($strayPet->parasite_treatments['internal']['treatment'] || $strayPet->parasite_treatments['external']['treatment']))
                        <dt class="col-sm-4"><i class="fas fa-bug me-2"></i>@lang('messages.parasite_treatment'):</dt>
                        <dd class="col-sm-8">
                            @lang('messages.internal'): {{ $strayPet->parasite_treatments['internal']['treatment'] ?? '-' }} ({{ $strayPet->parasite_treatments['internal']['date'] ?? '-' }})
                            @if($strayPet->parasite_treatments['internal']['dose']), @lang('messages.dose'): {{ $strayPet->parasite_treatments['internal']['dose'] }}@endif
                            <br>
                            @lang('messages.external'): {{ $strayPet->parasite_treatments['external']['treatment'] ?? '-' }} ({{ $strayPet->parasite_treatments['external']['date'] ?? '-' }})
                            @if($strayPet->parasite_treatments['external']['dose']), @lang('messages.dose'): {{ $strayPet->parasite_treatments['external']['dose'] }}@endif
                        </dd>
                        @endif

                        @if($strayPet->vaccinations_details && count($strayPet->vaccinations_details) > 0)
                        <dt class="col-sm-4"><i class="fas fa-syringe me-2"></i>@lang('messages.vaccinations'):</dt>
                        <dd class="col-sm-8">
                            <ul class="list-unstyled mb-0">
                                @foreach($strayPet->vaccinations_details as $vaccine)
                                <li>
                                    - {{ $vaccine['type'] == 'other' ? ($vaccine['custom_name'] ?? __('messages.other_vaccine')) : (__('messages.vaccine_type_' . strtolower($vaccine['type']))) }}
                                    ({{ $vaccine['date_given'] ?? '-' }})
                                    @if($vaccine['manufacturer']), @lang('messages.manufacturer'): {{ $vaccine['manufacturer'] }}@endif
                                    @if($vaccine['date_next']), @lang('messages.next_dose'): {{ $vaccine['date_next'] }}@endif
                                </li>
                                @endforeach
                            </ul>
                        </dd>
                        @endif
                    </dl>

                    <hr>
                    <h5 class="details-header">@lang('messages.contact_and_supervisory_info')</h5>
                    <dl class="row info-list mt-3">
                        <dt class="col-sm-4"><i class="fas fa-user-md me-2"></i>@lang('messages.vet_or_org'):</dt>
                        <dd class="col-sm-8">{{ $strayPet->medical_supervisor_info['vet_name'] ?? '-' }}</dd>
                        
                        <dt class="col-sm-4"><i class="fas fa-phone-alt me-2"></i>@lang('messages.emergency'):</dt>
                        <dd class="col-sm-8">{{ $strayPet->emergency_contact_phone ?? '-' }}</dd>
                        
                        @if($strayPet->independentTeam)
                        <dt class="col-sm-4"><i class="fas fa-users me-2"></i>@lang('messages.implementing_team'):</dt>
                        <dd class="col-sm-8">{{ $strayPet->independentTeam->name }} ({{ $strayPet->independentTeam->governorate->name ?? '' }})</dd>
                        @endif
                    </dl>

                    <div class="text-center qr-code-section mt-4">
                        <h5 class="details-header">@lang('messages.for_contact_or_adoption')</h5>
                        <img src="{{ $strayPet->qrCodeLink ? asset('storage/' . $strayPet->qrCodeLink->qr_image_path) : 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . route('stray-pets.public-view', $strayPet->uuid) }}" alt="QR Code" class="img-fluid" style="max-width: 180px;">
                        <p class="mt-2 small text-muted">@lang('messages.qr_code_instruction')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center my-5">
        @auth
            @if(Auth::user()->role == 'admin' || (Auth::user()->role == 'data_entry' && Auth::user()->independent_team_id == $strayPet->independent_team_id))
            <a href="{{ route('stray-pets.data-entry', ['stray_pet' => $strayPet->id]) }}" class="btn btn-primary btn-lg px-5 me-3">
                <i class="fas fa-edit me-2"></i>
                @lang('messages.edit_animal_data')
            </a>
            @endif
        @endauth
        
        <a href="#" class="btn btn-secondary btn-lg px-5 disabled" aria-disabled="true">
            <i class="fas fa-globe me-2"></i>
            @lang('messages.main_site_coming_soon')
        </a>
    </div>
</div>
@endsection