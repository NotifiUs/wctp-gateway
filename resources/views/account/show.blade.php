@extends('layouts.app')

@section('title', 'Account')

@push('css')
@endpush

@push('scripts')
@endpush

@section('content')
    <div class="container max-width-dashboard">
        <div class="row justify-content-center">
            <div class="col-md-4">
                @include('layouts.account-side')
            </div>
            <div class="col-md-8">
                @include('layouts.error')
                @include('layouts.status')
                <h5 class="text-muted-light mt-2">{{ __('Account Profile') }}</h5>
                <div class="card">
                    <div class="card-body text-center">
                        <img alt="{{ $user->name }}" title="Gravatar for {{ $user->name }}"
                             class="rounded-circle img-thumbnail shadow-sm"
                             src="https://www.gravatar.com/avatar/{{ md5( $user->email )  }}?d=retro"/>
                        <h3 class="display-4 text-primary mb-3">
                            <small>
                                {{ $user->name }}
                                @if( $user->email_verified_at )
                                    <i title="Verified Email" class="fas fa-check-circle text-success"
                                       style="font-size:2rem;"></i>
                                @else
                                    <a class="btn-link" href="/account/verify-email">
                                        <i title="Verify your email" class="fas fa-exclamation-circle text-danger"
                                           style="font-size:2rem;"></i>
                                    </a>
                                @endif
                            </small>
                        </h3>
                        <h5 class="text-muted">
                            <strong class="text-dark"><i
                                    class="fas fa-envelope text-muted"></i></strong> {{ $user->email }}

                        </h5>
                        <p class="text-muted-light font-weight-bold">
                            <i class="fas fa-clock text-muted-light"></i> {{ $user->timezone }}
                        </p>

                        <p class="mt-4">
                            <small class="text-muted">
                                <strong>{{ __('Created') }}</strong>
                                &middot; {{ $user->created_at->timezone( Auth::user()->timezone )->format("m/d/Y g:i:s A T") }}
                                <br>
                                <strong>{{ __('Updated') }}</strong>
                                &middot; {{ $user->updated_at->timezone( Auth::user()->timezone )->format("m/d/Y g:i:s A T") }}
                            </small>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('account.modals.name')
    @include('account.modals.email')
    @include('account.modals.password')
    @include('account.modals.timezone')
@endsection
