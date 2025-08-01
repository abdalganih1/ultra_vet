@extends('layouts.app')

@section('title', __('messages.team_dashboard'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $team->name }} - @lang('messages.dashboard')</h1>
        <small class="text-muted">@lang('messages.governorate'): {{ $team->governorate->name ?? __('messages.no_data') }}</small>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">@lang('messages.your_teams_pets')</h5>
                    <p class="card-text fs-4">{{ $stats['total_pets'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">@lang('messages.pets_data_entered')</h5>
                    <p class="card-text fs-4">{{ $stats['data_entered'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">@lang('messages.team_members')</h5>
                    <p class="card-text fs-4">{{ $stats['team_members'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@lang('messages.quick_actions')</div>
                <div class="card-body">
                    <a href="{{ route('stray-pets.index') }}" class="btn btn-outline-primary m-1">@lang('messages.view_pet_registry')</a>
                    <a href="{{ route('qrcodes.stray.generate.form') }}" class="btn btn-outline-success m-1">@lang('messages.generate_new_qr_codes')</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@lang('messages.recent_activity')</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($recent_pets as $pet)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('stray-pets.show', $pet->uuid) }}">{{ $pet->serial_number ?? $pet->uuid }}</a>
                                    <br>
                                    <small class="text-muted">{{ $pet->created_at->diffForHumans() }}</small>
                                </div>
                                @if($pet->data_entered_status)
                                    <span class="badge bg-success">@lang('messages.pets_data_entered')</span>
                                @else
                                    <span class="badge bg-danger">@lang('messages.pending_data')</span>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item">@lang('messages.no_recent_activity')</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
