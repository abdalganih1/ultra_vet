@extends('layouts.app')

@section('title', __('messages.manage_users'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>@lang('messages.manage_users')</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">@lang('messages.add_new_user')</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>@lang('messages.name')</th>
                        <th>@lang('messages.username')</th>
                        <th>@lang('messages.email')</th>
                        <th>@lang('messages.role')</th>
                        <th>@lang('messages.team_name')</th>
                        <th>@lang('messages.actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-secondary">{{ $user->role }}</span></td>
                            <td>{{ $user->independentTeam->name ?? __('messages.no_data') }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">@lang('messages.edit')</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('messages.confirm_delete')');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">@lang('messages.delete')</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">@lang('messages.no_data')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
