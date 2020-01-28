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
                <ul class="list-group rounded">
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-user text-primary"></i> {{ __('Change your name') }}
                    </a>
                    @if( ! $user->email_verified_at )
                        <a class="list-group-item list-group-item-action" href="/account/verify-email">
                            <i class="fas fa-exclamation-circle text-primary"></i> {{ __('Verify email address') }}
                        </a>
                    @else
                        <a class="list-group-item list-group-item-action" href="#">
                            <i class="fas fa-at text-primary"></i> {{ __('Update email address') }}
                        </a>
                    @endif
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-clock text-primary"></i> {{ __('Select timezone') }}
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-asterisk text-primary"></i> {{ __('Change password') }}
                    </a>
                    <!--
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-shield-alt text-primary"></i> {{ __('Multi-factor authentication') }}
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-laptop text-primary"></i> {{ __('Logout my sessions') }}
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <i class="fas fa-key text-primary"></i> {{ __('Generate API Key') }}
                    </a>
                    -->
                </ul>
            </div>
            <div class="col-md-8">
                <h5 class="text-muted-light mt-2">{{ __('Account Profile') }}</h5>
                <div class="card">
                    <div class="card-body text-center">
                        <img alt="{{ $user->name }}" title="Gravatar for {{ $user->name }}" class="rounded-circle img-thumbnail shadow-sm" src="https://www.gravatar.com/avatar/{{ md5( $user->email )  }}?d=mp" />
                        <h3 class="display-4 text-primary mb-3">
                            <small>
                                {{ $user->name }}
                            </small>
                        </h3>
                        <h5 class="text-muted">
                            <strong class="text-dark"><i class="fas fa-envelope text-muted"></i></strong> {{ $user->email }}
                            @if( $user->email_verified_at )
                                <i title="Verified Email" class="fas fa-check-circle text-success"></i>
                            @else
                                <a class="btn-link" href="/account/verify-email">
                                    <i title="Verify your email" class="fas fa-exclamation-circle text-danger"></i>
                                </a>
                            @endif
                        </h5>
                        <p class="text-muted-light font-weight-bold">
                            <i class="fas fa-clock text-muted-light"></i> {{ $user->timezone }}
                        </p>

                        <p class="mt-4">
                            <small class="text-muted">
                                <strong>{{ __('Created') }}</strong> &middot; {{ $user->created_at->timezone( Auth::user()->timezone )->format("m/d/Y g:i:s A T") }}
                                <br>
                                <strong>{{ __('Updated') }}</strong> &middot; {{ $user->updated_at->timezone( Auth::user()->timezone )->format("m/d/Y g:i:s A T") }}
                            </small>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
