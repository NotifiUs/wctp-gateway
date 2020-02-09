@extends('layouts.app')

@section('title', __('Multi-Factor Authentication'))

@push('css')
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-12 col-md-10 col-lg-10 col-xl-8">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="/mfa">
                        @csrf
                        <div class="form-group row">
                            <label for="mfa_code" class="col-md-4 col-form-label text-md-right">{{ __('MFA Code') }}</label>

                            <div class="col-md-6">
                                <input id="mfa_code" type="text" class="form-control @error('mfa_code') is-invalid @enderror" name="mfa_code" value="{{ old('mfa_code') }}" required autofocus>

                                @error('mfa_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Verify Code') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    <form name="logout" class=" my-3 d-block mx-auto text-center" method="POST" action="/logout">
                        @csrf
                        Need to start over? <a href="#" onclick="javascript:document.logout.submit();" role="button" class="btn-link">
                            Click here
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
