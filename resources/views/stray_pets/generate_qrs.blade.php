@extends('layouts.app')

@section('title', 'توليد رموز QR للحيوانات الشاردة')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-qrcode me-2"></i>توليد رموز QR للحيوانات الشاردة</h4>
        </div>
        <div class="card-body">
            <p class="text-muted">هنا يمكنك توليد عدد من رموز QR لحيوانات شاردة جديدة. يجب تحديد الجمعية المشرفة والفريق المنفذ لهذه المجموعة من الرموز.</p>
            <hr>
            <form action="{{ route('qrcodes.stray.generate.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="num_qrcodes" class="form-label">عدد رموز QR المراد توليدها:</label>
                    <input type="number" class="form-control @error('num_qrcodes') is-invalid @enderror" id="num_qrcodes" name="num_qrcodes" min="1" max="500" value="{{ old('num_qrcodes', 1) }}" required>
                    @error('num_qrcodes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <hr class="my-4">
                <h5 class="mb-3" style="color: #2c3e50;"><i class="fas fa-sitemap me-2"></i>الجهة المشرفة والفريق (مطلوب)</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="governorate_id_prefill" class="form-label">المحافظة:</label>
                        <select class="form-select @error('governorate_id_prefill') is-invalid @enderror" id="governorate_id_prefill" name="governorate_id_prefill" required
                                @if(Auth::user()->role !== 'admin' && $selectedGovernorate) disabled @endif>
                            @if(Auth::user()->role === 'admin')
                                <option value="">اختر المحافظة...</option>
                                @foreach($governorates as $governorate)
                                    <option value="{{ $governorate->id }}" {{ old('governorate_id_prefill') == $governorate->id ? 'selected' : '' }}>{{ $governorate->name }}</option>
                                @endforeach
                            @elseif($selectedGovernorate)
                                <option value="{{ $selectedGovernorate->id }}" selected>{{ $selectedGovernorate->name }}</option>
                            @endif
                        </select>
                        @if(Auth::user()->role !== 'admin' && $selectedGovernorate)
                            <input type="hidden" name="governorate_id_prefill" value="{{ $selectedGovernorate->id }}">
                        @endif
                        @error('governorate_id_prefill')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="supervising_association_prefill" class="form-label">الجمعية المشرفة:</label>
                        <input type="text" class="form-control @error('supervising_association_prefill') is-invalid @enderror" id="supervising_association_prefill" name="supervising_association_prefill" required readonly
                               value="{{ old('supervising_association_prefill', (Auth::user()->role !== 'admin' && $supervisingAssociation) ? $supervisingAssociation : '') }}">
                        @error('supervising_association_prefill')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="independent_team_id_prefill" class="form-label">الفريق المستقل المنفذ:</label>
                        <select class="form-select @error('independent_team_id_prefill') is-invalid @enderror" id="independent_team_id_prefill" name="independent_team_id_prefill" required
                                @if(Auth::user()->role !== 'admin' && $selectedTeam) disabled @endif>
                            @if(Auth::user()->role === 'admin')
                                <option value="">اختر الفريق...</option>
                                @foreach($independentTeams as $team)
                                    <option value="{{ $team->id }}" data-governorate="{{ $team->governorate_id }}" {{ old('independent_team_id_prefill') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                @endforeach
                            @elseif($selectedTeam)
                                <option value="{{ $selectedTeam->id }}" selected>{{ $selectedTeam->name }}</option>
                            @endif
                        </select>
                        @if(Auth::user()->role !== 'admin' && $selectedTeam)
                            <input type="hidden" name="independent_team_id_prefill" value="{{ $selectedTeam->id }}">
                        @endif
                        @error('independent_team_id_prefill')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3" style="color: #2c3e50;"><i class="fas fa-pencil-alt me-2"></i>بيانات موحدة للتعبئة المسبقة (اختياري)</h5>
                
                <div class="row g-3">
                    {{-- Other prefill fields like animal type, gender, etc. --}}
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-cogs me-2"></i>ابدأ التوليد</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const governorateSelect = document.getElementById('governorate_id_prefill');
        const teamSelect = document.getElementById('independent_team_id_prefill');
        const associationInput = document.getElementById('supervising_association_prefill');
        const teamOptions = Array.from(teamSelect.options);

        function updateFormFields() {
            const selectedGovernorateId = governorateSelect.value;
            const selectedOption = governorateSelect.options[governorateSelect.selectedIndex];
            
            if (!selectedOption) return;

            const selectedGovernorateName = selectedOption.text.trim();

            // Update supervising association input for all users, if a valid governorate is selected.
            if (selectedGovernorateName && selectedGovernorateId) {
                associationInput.value = `فريق ultravet لمحافظة ${selectedGovernorateName}`;
            } else {
                // For admins, clear the input if no governorate is selected.
                if (!governorateSelect.disabled) {
                    associationInput.value = '';
                }
            }

            // Filter the teams list only if the governorate select is enabled (for admins).
            if (!governorateSelect.disabled) {
                let currentTeamValue = teamSelect.value;
                teamSelect.innerHTML = '<option value="">اختر الفريق...</option>'; // Clear existing options

                teamOptions.forEach(option => {
                    if (option.value === "" || (option.dataset.governorate === selectedGovernorateId)) {
                        teamSelect.add(option.cloneNode(true));
                    }
                });
                
                // Restore selection if it exists in the filtered list.
                teamSelect.value = currentTeamValue;
            }
        }

        // Add the change event listener only for admins.
        if (!governorateSelect.disabled) {
            governorateSelect.addEventListener('change', updateFormFields);
        }

        // Call the function on page load for everyone to ensure fields are correctly set.
        updateFormFields();
    });
</script>
@endpush