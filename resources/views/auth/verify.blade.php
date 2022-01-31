@extends('layouts.app')

@section('title', __('Verify Your Email Address'))

@push('css')
@endpush

@push('scripts')
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <h5 class="text-muted-light">{{ __('Email Verification') }}</h5>
                <div class="card">
                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('Fresh verification link sent to your email address.') }}
                            </div>
                        @endif

                        <strong class="text-dark">
                            {{ __('Before proceeding, please check your email for a verification link.') }}
                        </strong>
                        <br><br>
                        {{ __('If you did not receive the email') }},
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit"
                                    class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>
                            .
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
