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
                @include('layouts.account-side')
            </div>
            <div class="col-md-8">
                @include('layouts.error')
                @include('layouts.status')
                <h5 class="text-muted-light mt-2">{{ __('Multi-Factor Authentication') }}</h5>
                <div class="card">
                    <div class="card-body text-center">

                        @if( $user->mfa_secret )
                            @include('account.mfa.active')
                        @else
                           @include('account.mfa.inactive')
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
