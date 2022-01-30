@extends('layouts.app')
@section('title', __('Verify Twilio Account'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Verify Twilio Account') }}</h5>
    <div class="row justify-content-start mb-2">
        @if( $account['sid'] == $account['ownerAccountSid'] )
            <div class="alert alert-warning w-100" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <strong>Master Account</strong> detected. We recommend using a <strong>SubAccount</strong> whenever possible.
            </div>
        @endif

        @if( $account['type'] !== 'Full' )
            <div class="alert alert-info w-100" role="alert">
                <i class="fas fa-exclamation-circle"></i> <strong>Trial Account</strong> detected. Please upgrade your Twilio account before going into production.
            </div>
        @endif

        @if( $account['status'] !== 'active' )
            <div class="alert alert-danger w-100" role="alert">
                <i class="fas fa-exclamation"></i>  Account <strong>inactive</strong>. You must restore the account to active before continuing.
            </div>
        @else
        <div class="card w-100 py-0">
            <div class="card-body py-0">
                <form method="POST"action="/carriers" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="api" value="twilio">
                    <div class="container-fluid">

                        <div class="row justify-content-center">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" value="{{ old('name') }}" name="name" required class="form-control">

                                    <small class="form-text text-muted">
                                        Reference the carrier instance by this name
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Priority</label>
                                    <input type="text" value="{{ old('priority') }}" name="priority" required class="form-control">

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
                                        Use this Twilio Account
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <label>Twilio Account</label>
                                <dl class="w-100 bg-light rounded p-4">
                                    <dt class="text-muted-light">Friendly Name</dt>
                                    <dl class="text-dark font-weight-bolder">{{ $account['friendlyName'] }}</dl>
                                    <dt class="text-muted-light">Account SID</dt>
                                    <dl class="">
                                        <code class="text-primary font-weight-bold">{{ $account['sid'] }}</code>
                                        <input type="hidden" name="twilio_account_sid" value="{{ $account['sid'] }}">
                                    </dl>
                                    <dt class="text-muted-light">Auth Token</dt>
                                    <dl class="">
                                        <code class="text-primary font-weight-bold">{{ str_pad( substr( $account['authToken'],0, 6), 32, '*') }}</code>
                                        <input type="hidden" name="twilio_auth_token" value="{{ $account['authToken'] }}">
                                    </dl>
                                    <dt class="text-muted-light">Created</dt>
                                    <dl class="">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse( $account['dateCreated'] )->timezone(Auth::user()->timezone )->format("m/d/Y g:i:s A T") }}
                                        </small>
                                    </dl>
                                    <dt class="text-muted-light">Last Updated</dt>
                                    <dl class="">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse( $account['dateUpdated'] )->timezone(Auth::user()->timezone )->format("m/d/Y g:i:s A T")  }}
                                        </small>
                                    </dl>
                                </dl>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

@endsection
