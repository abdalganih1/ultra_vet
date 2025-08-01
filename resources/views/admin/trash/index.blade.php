@extends('layouts.app')

@section('title', __('messages.trash_bin'))

@section('content')
<div class="container">
    <h1 class="mb-4">@lang('messages.trash_bin')</h1>

    @include('partials.alerts')

    <div class="card">
        <div class="card-header">
            @lang('messages.deleted_pets')
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>@lang('messages.serial_number_short')</th>
                        <th>UUID</th>
                        <th>@lang('messages.team')</th>
                        <th>@lang('messages.date_deleted')</th>
                        <th>@lang('messages.actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trashedPets as $pet)
                        <tr>
                            <td>{{ $pet->serial_number ?? 'N/A' }}</td>
                            <td>{{ $pet->uuid }}</td>
                            <td>{{ $pet->independentTeam->name ?? 'N/A' }}</td>
                            <td>{{ $pet->deleted_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.trash.restore', $pet->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">@lang('messages.restore')</button>
                                </form>
                                <form action="{{ route('admin.trash.destroy', $pet->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('messages.confirm_permanent_delete')');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">@lang('messages.delete_permanently')</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">@lang('messages.trash_empty')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($trashedPets->hasPages())
        <div class="card-footer">
            {{ $trashedPets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
