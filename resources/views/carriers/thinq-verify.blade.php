@extends('layouts.app')
@section('title', __('Verify ThinQ Account'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Verify ThinQ Account') }}</h5>
    <div class="row justify-content-start mb-2">

        <div class="card w-100 py-0">
            <div class="card-body py-0">
                <form method="POST" action="/carriers" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="api" value="thinq">
                    <div class="container-fluid">

                        <div class="row justify-content-center">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" value="{{ old('name') }}" name="name" required
                                           class="form-control">

                                    <small class="form-text text-muted">
                                        Reference the carrier instance by this name
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Priority</label>
                                    <input type="text" value="{{ old('priority') }}" name="priority" required
                                           class="form-control">

                                    <small class="form-text text-muted">
                                        The general system priority for this carrier.
                                        Like DNS MX records, lower values mean higher priority. (10, 20, 30, etc.)
                                    </small>
                                </div>
                                <div class="form-group">
                                    <a class="btn btn-secondary" href="/carriers" role="button">
                                        Cancel
                                    </a>
                                    <button type="submit" role="button" class="btn btn-primary">
                                        Use this ThinQ Account
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <label>ThinQ Account</label>
                                <dl class="w-100 bg-light rounded p-4">
                                    <dt class="text-muted-light">Account ID</dt>
                                    <dl class="text-dark fw-bolder">
                                        {{ $account['account_id'] }}
                                        <input type="hidden" name="thinq_account_id"
                                               value="{{ $account['account_id'] }}">
                                    </dl>
                                    <dt class="text-muted-light">API Username</dt>
                                    <dl class="">
                                        <code
                                            class="text-primary fw-bold">{{ $account['api_username'] }}</code>
                                        <input type="hidden" name="thinq_api_username"
                                               value="{{ $account['api_username'] }}">
                                    </dl>
                                    <dt class="text-muted-light">API Token</dt>
                                    <dl class="">
                                        <code
                                            class="text-primary fw-bold">{{ str_pad( substr( $account['api_token'],0, 6), 40, '*') }}</code>
                                        <input type="hidden" name="thinq_api_token"
                                               value="{{  $account['api_token'] }}">
                                    </dl>
                                    <dt class="text-muted-light">Current Balance</dt>
                                    <dl class="text-dark fw-bolder">
                                        <i class="fas fa-dollar-sign text-muted"></i> {{ $account['balance'] }}
                                    </dl>
                                </dl>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
