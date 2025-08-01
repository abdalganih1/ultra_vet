@extends('layouts.app')

@section('title', __('messages.edit_profile'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">@lang('messages.edit_profile')</div>

                <div class="card-body">
                    @include('partials.alerts')

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        {{-- Name --}}
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">@lang('messages.name')</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        {{-- Username --}}
                        <div class="row mb-3">
                            <label for="username" class="col-md-4 col-form-label text-md-end">@lang('messages.username')</label>
                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $user->username) }}" required autocomplete="username">
                                @error('username')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">@lang('messages.email')</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        {{-- Phone Number --}}
                        <div class="row mb-3">
                            <label for="phone_number" class="col-md-4 col-form-label text-md-end">@lang('messages.phone_number')</label>
                            <div class="col-md-6">
                                <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                                @error('phone_number')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- New Password --}}
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">@lang('messages.new_password')</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                                <small class="form-text text-muted">@lang('messages.password_leave_blank_profile')</small>
                                @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        {{-- Confirm New Password --}}
                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">@lang('messages.confirm_new_password')</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    @lang('messages.save_changes')
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    {{-- Request to become a team --}}
                    @if($user->isRegularUser())
                    <div class="mt-4">
                        <h5>@lang('messages.become_a_team')</h5>
                        <p>@lang('messages.become_a_team_description')</p>
                        <form method="POST" action="{{ route('profile.request-team-upgrade') }}">
                            @csrf
                            <button type="submit" class="btn btn-info">
                                @lang('messages.request_to_become_a_team')
                            </button>
                        </form>
                    </div>
                    @elseif($user->role === 'team_request')
                    <div class="mt-4">
                        <h5>@lang('messages.request_pending')</h5>
                        <p>@lang('messages.request_pending_description')</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
