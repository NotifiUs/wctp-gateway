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
                <h5 class="text-muted-light mt-2">{{ __('Multi-Factor Authentication') }}</h5>
                <div class="card">
                    <div class="card-body text-center">

                        @if(config('services.recaptcha_v3.site_key') === '' || config('services.recaptcha_v3.site_key') === '')
                            <div class="alert alert-warning border-warning ">
                                Feature not enabled.
                            </div>
                        @else
                            @if( $user->mfa_secret )
                                @include('account.mfa.active')
                            @else
                                @include('account.mfa.inactive')
                            @endif
                        @endif

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
