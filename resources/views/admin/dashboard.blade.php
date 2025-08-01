@extends('layouts.app')

@section('title', __('messages.admin_dashboard'))

@section('content')
<div class="container">
    <h1 class="mb-4">@lang('messages.admin_dashboard')</h1>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">@lang('messages.total_users')</h5>
                    <p class="card-text fs-4">{{ $stats['users'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">@lang('messages.total_teams')</h5>
                    <p class="card-text fs-4">{{ $stats['teams'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">@lang('messages.total_pets')</h5>
                    <p class="card-text fs-4">{{ $stats['pets'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">@lang('messages.pets_data_entered')</h5>
                    <p class="card-text fs-4">{{ $stats['pets_data_entered'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Management Links --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@lang('messages.management')</div>
                <div class="card-body">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary m-1">@lang('messages.manage_users')</a>
                    <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-success m-1">@lang('messages.manage_teams')</a>
                    <a href="{{ route('admin.governorates.index') }}" class="btn btn-outline-info m-1">@lang('messages.manage_governorates')</a>
                    <a href="{{ route('stray-pets.index') }}" class="btn btn-outline-secondary m-1">@lang('messages.view_all_pets')</a>
                    <a href="{{ route('admin.trash.index') }}" class="btn btn-outline-danger m-1">@lang('messages.trash_bin')</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Team Stats by Governorate --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@lang('messages.team_stats_by_governorate')</div>
                <div class="card-body">
                    @foreach($teamStats as $governorate)
                        <h5 class="mt-3">{{ $governorate->name }}</h5>
                        <ul class="list-group">
                            @forelse($governorate->independentTeams as $team)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $team->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $team->stray_pets_count }} {{ trans_choice('messages.case', $team->stray_pets_count) }}</span>
                                </li>
                            @empty
                                <li class="list-group-item">@lang('messages.no_teams_in_governorate')</li>
                            @endforelse
                        </ul>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
