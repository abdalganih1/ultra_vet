@extends('layouts.app')

@section('title', __('messages.manage_governorates'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>@lang('messages.manage_governorates')</h1>
        <a href="{{ route('admin.governorates.create') }}" class="btn btn-primary">@lang('messages.add_new_governorate')</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>@lang('messages.name')</th>
                        <th>@lang('messages.teams')</th>
                        <th>@lang('messages.actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($governorates as $governorate)
                        <tr>
                            <td>{{ $governorate->name }}</td>
                            <td>{{ $governorate->independent_teams_count }}</td>
                            <td>
                                <a href="{{ route('admin.governorates.edit', $governorate) }}" class="btn btn-sm btn-warning">@lang('messages.edit')</a>
                                <form action="{{ route('admin.governorates.destroy', $governorate) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('messages.confirm_delete')');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">@lang('messages.delete')</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">@lang('messages.no_data')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($governorates->hasPages())
        <div class="card-footer">
            {{ $governorates->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
