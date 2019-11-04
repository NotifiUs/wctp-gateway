@extends('layouts.app')

@section('title', 'Account')

@push('css')
@endpush

@push('scripts')
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h5 class="text-muted-light mt-2">{{ __('Account Actions') }}</h5>
                <ul class="list-group">
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-user text-primary"></i> Change your name
                    </a>
                    @if( ! $user->email_verified_at )
                        <a class="list-group-item list-group-item-action" href="/account/verify-email">
                            <i class="fas fa-exclamation-circle text-primary"></i> Verify email address
                        </a>
                    @else
                        <a class="list-group-item list-group-item-action" href="#">
                            <i class="fas fa-at text-primary"></i> Update email address
                        </a>
                    @endif
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-clock text-primary"></i> Select timezone
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-asterisk text-primary"></i> Change password
                    </a>
                    <a class="list-group-item list-group-item-action href="#">
                        <i class="fas fa-shield-alt text-primary"></i> Multi-factor authentication
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-laptop text-primary"></i> Logout my sessions
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-key text-primary"></i> Generate API key
                    </a>
                </ul>
            </div>
            <div class="col-md-8">
                <h5 class="text-muted-light mt-2">{{ __('Account Profile') }}</h5>
                <div class="card">
                    <div class="card-body text-center">
                        <img alt="{{ $user->name }}" title="Gravatar for {{ $user->name }}" class="rounded-circle img-thumbnail shadow-sm" src="https://www.gravatar.com/avatar/{{ md5( $user->email )  }}" />
                        <h3 class="display-4 text-primary">
                            {{ $user->name }}
                        </h3>
                        <h5 class="text-muted">
                            <strong class="text-dark"><i class="fas fa-envelope text-muted"></i> Email</strong> &middot; {{ $user->email }}
                            @if( $user->email_verified_at )
                                <i title="Verified Email" class="fas fa-check text-success"></i>
                            @else
                                <a class="btn-link" href="/account/verify-email">
                                    <i title="Verify your email" class="fas fa-exclamation-circle text-muted-light"></i>
                                </a>
                            @endif
                        </h5>
                        <h5 class="text-muted">
                            <strong class="text-dark"><i class="fas fa-globe-americas text-muted"></i> Timezone</strong> &middot; {{ $user->timezone }}
                        </h5>

                        <p class="my-4">
                            <small class="text-muted">
                                <strong>Created</strong> {{ $user->created_at->timezone( Auth::user()->timezone )->format("m/d/Y g:i:s A T") }} &nbsp; <strong>Updated</strong> {{ $user->updated_at->timezone( Auth::user()->timezone )->format("m/d/Y g:i:s A T") }}
                            </small>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
