@extends('layouts.app')

@section('title', __('messages.edit_user'))

@section('content')
<div class="container">
    <h1>@lang('messages.edit_user'): {{ $user->name }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">@lang('messages.name')</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">@lang('messages.username')</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">@lang('messages.email')</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">@lang('messages.password')</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        <small class="form-text text-muted">@lang('messages.password_leave_blank')</small>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="role" class="form-label">@lang('messages.role')</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                            <option value="data_entry" {{ old('role', $user->role) == 'data_entry' ? 'selected' : '' }}>Data Entry</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="team_request" {{ old('role', $user->role) == 'team_request' ? 'selected' : '' }}>Team Request</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="governorate_id" class="form-label">@lang('messages.governorate')</label>
                        <select class="form-select @error('governorate_id') is-invalid @enderror" id="governorate_id" name="governorate_id">
                            <option value="">@lang('messages.select_governorate')</option>
                            @foreach ($governorates as $governorate)
                                <option value="{{ $governorate->id }}" {{ old('governorate_id', $user->governorate_id) == $governorate->id ? 'selected' : '' }}>{{ $governorate->name }}</option>
                            @endforeach
                        </select>
                        @error('governorate_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="independent_team_id" class="form-label">@lang('messages.team_name')</label>
                        <select class="form-select @error('independent_team_id') is-invalid @enderror" id="independent_team_id" name="independent_team_id">
                            <option value="">@lang('messages.select_team')</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}" {{ old('independent_team_id', $user->independent_team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('independent_team_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
