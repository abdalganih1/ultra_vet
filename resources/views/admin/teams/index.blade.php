@extends('layouts.app')

@section('title', __('messages.manage_teams'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>@lang('messages.manage_teams')</h1>
        <a href="{{ route('admin.teams.create') }}" class="btn btn-primary">@lang('messages.add_new_team')</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>@lang('messages.team_name')</th>
                        <th>@lang('messages.governorate')</th>
                        <th>@lang('messages.actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teams as $team)
                        <tr>
                            <td>{{ $team->name }}</td>
                            <td>{{ $team->governorate->name ?? __('messages.no_data') }}</td>
                            <td>
                                <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-sm btn-warning">@lang('messages.edit')</a>
                                <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('messages.confirm_delete')');">
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
        @if($teams->hasPages())
            <div class="card-footer">
                {{ $teams->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
