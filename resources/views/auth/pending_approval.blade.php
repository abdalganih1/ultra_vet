@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('messages.request_pending_approval') }}</div>

                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading">{{ __('messages.thank_you_for_registering') }}</h4>
                        <p>{{ __('messages.team_creation_request_submitted') }}</p>
                        <hr>
                        <p class="mb-0">{{ __('messages.you_will_be_notified') }}</p>
                    </div>

                    <div class="d-flex justify-content-end">
                         <a class="btn btn-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                            {{ __('messages.logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
