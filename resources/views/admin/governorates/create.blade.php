@extends('layouts.app')

@section('title', __('messages.add_new_governorate'))

@section('content')
<div class="container">
    <h1>@lang('messages.add_new_governorate')</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.governorates.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">@lang('messages.governorate_name')</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                <a href="{{ route('admin.governorates.index') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
            </form>
        </div>
    </div>
</div>
@endsection
