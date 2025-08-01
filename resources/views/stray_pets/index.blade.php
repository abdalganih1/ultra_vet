@extends('layouts.app')

@section('title', __('messages.pet_registry'))

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>@lang('messages.pet_registry')</h1>
        <a href="{{ route('qrcodes.stray.generate.form') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>@lang('messages.generate_new_qrs')</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <form action="{{ route('stray-pets.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                <div class="flex-grow-1">
                    <input type="text" name="search" class="form-control" placeholder="@lang('messages.search_placeholder')" value="{{ request('search') }}">
                </div>
                <div>
                    <select name="status_filter" class="form-select">
                        <option value="">@lang('messages.all_statuses')</option>
                        <option value="entered" {{ request('status_filter') == 'entered' ? 'selected' : '' }}>@lang('messages.pets_data_entered')</option>
                        <option value="pending" {{ request('status_filter') == 'pending' ? 'selected' : '' }}>@lang('messages.pending_data')</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-info">@lang('messages.filter')</button>
                    <a href="{{ route('stray-pets.index') }}" class="btn btn-secondary">@lang('messages.clear')</a>
                </div>
            </form>
        </div>
        <form action="{{ route('stray-pets.bulk-destroy') }}" method="POST" id="bulk-action-form">
            @csrf
            <div class="card-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="select-all">@lang('messages.select_all')</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="deselect-all">@lang('messages.deselect_all')</button>
                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('@lang('messages.confirm_move_to_trash')');">
                        <i class="fas fa-trash me-1"></i> @lang('messages.move_selected_to_trash')
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="print-selected">
                        <i class="fas fa-print me-1"></i> @lang('messages.print_selected_pdf')
                    </button>
                </div>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 g-4">
                    @forelse ($strayPets as $pet)
                        <div class="col">
                            <div class="card h-100 pet-card {{ $pet->data_entered_status ? 'border-success' : 'border-danger' }}">
                                <div class="card-header text-center">
                                    <input type="checkbox" class="form-check-input pet-checkbox" name="uuids[]" value="{{ $pet->uuid }}">
                                </div>
                                @if($pet->image_path)
                                    <img src="{{ asset('storage/' . $pet->image_path) }}" class="card-img-top" alt="Pet Image" style="height: 150px; object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center" style="height: 150px; background-color: #f8f9fa;">
                                        <i class="fas fa-paw fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">@lang('messages.serial_number_short'): {{ $pet->serial_number ?? 'N/A' }}</h6>
                                    <p class="card-text small text-muted">
                                        UUID: {{ Str::limit($pet->uuid, 8, '...') }}<br>
                                        @lang('messages.team'): {{ $pet->independentTeam->name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="{{ route('stray-pets.public-view', $pet->uuid) }}" class="btn btn-sm btn-outline-info" title="@lang('messages.public_view')"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('stray-pets.data-entry', $pet->id) }}" class="btn btn-sm btn-outline-warning" title="@lang('messages.edit_data')"><i class="fas fa-edit"></i></a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center">@lang('messages.no_pets_found')</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @if($strayPets->hasPages())
            <div class="card-footer">
                {{ $strayPets->links() }}
            </div>
            @endif
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('select-all').addEventListener('click', function() {
        document.querySelectorAll('.pet-checkbox').forEach(checkbox => checkbox.checked = true);
    });

    document.getElementById('deselect-all').addEventListener('click', function() {
        document.querySelectorAll('.pet-checkbox').forEach(checkbox => checkbox.checked = false);
    });

    document.getElementById('print-selected').addEventListener('click', function() {
        const form = document.getElementById('bulk-action-form');
        const originalAction = form.action;
        
        form.action = "{{ route('qrcodes.stray.print.pdf') }}";
        form.target = '_blank'; // Open in a new tab
        form.submit();
        
        // Reset action and target after submission
        form.action = originalAction;
        form.target = '_self';
    });
</script>
@endpush
