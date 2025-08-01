@extends('layouts.app')

@section('title', __('messages.edit_governorate'))

@section('content')
<div class="container">
    <h1>@lang('messages.edit_governorate')</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.governorates.update', $governorate) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">@lang('messages.governorate_name')</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $governorate->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
                <a href="{{ route('admin.governorates.index') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
            </form>
        </div>
    </div>
</div>
@endsection
