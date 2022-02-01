@extends('layouts.app')

@section('title', 'Verify your email')

@push('css')
@endpush

@push('scripts')
@endpush

@section('content')
    <div class="container max-width-dashboard">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-2">
                    <div class="card-body text-center">
                        <div class="">
                            <i class="fas fa-envelope-open fa-5x text-primary"></i>
                        </div>

                        <h3 class="text-dark mt-2 mb-4">
                            {{ __('Thank you') }}<br>
                            <small class="text-muted">
                                {{ __("Email verified!") }}
                            </small>
                        </h3>

                        <a href="/account" class="btn btn-primary">
                            {{ __('Go to account') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
