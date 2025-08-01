@extends('layouts.app')

@section('title', __('messages.add_new_team'))

@section('content')
<div class="container">
    <h1>@lang('messages.add_new_team')</h1>

    <form action="{{ route('admin.teams.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="name" class="form-label">@lang('messages.team_name')</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="governorate_id" class="form-label">@lang('messages.governorate')</label>
            <select name="governorate_id" id="governorate_id" class="form-select @error('governorate_id') is-invalid @enderror" required>
                <option value="">@lang('messages.select_governorate')</option>
                @foreach ($governorates as $governorate)
                    <option value="{{ $governorate->id }}" {{ old('governorate_id') == $governorate->id ? 'selected' : '' }}>
                        {{ $governorate->name }}
                    </option>
                @endforeach
            </select>
            @error('governorate_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">@lang('messages.save')</button>
        <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
    </form>
</div>
@endsection
