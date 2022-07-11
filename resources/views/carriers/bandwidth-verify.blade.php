@extends('layouts.app')
@section('title', __('Verify Bandwidth Account'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Verify Bandwidth Account') }}</h5>
    <div class="row justify-content-start mb-2">

        <div class="card w-100 py-0">
            <div class="card-body py-0">
                <form method="POST" action="/carriers" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="carrier_api" value="bandwidth">
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
                                        Use this Bandwidth Account
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <label>Bandwidth Account</label>
                                <dl class="w-100 bg-light rounded p-4">
                                    <dt class="text-muted-light">Bandwidth Account ID</dt>
                                    <dd class="">
                                        <code class="text-primary fw-bold">{{ $account['bandwidth_api_account_id'] }}</code>
                                        <input type="hidden" name="bandwidth_api_account_id"
                                               value="{{ $account['bandwidth_api_account_id'] }}">
                                    </dd>
                                    <dt class="text-muted-light">Bandwidth Application ID</dt>
                                    <dd class="">
                                        <code class="text-primary fw-bold">{{ $account['bandwidth_api_application_id'] }}</code>
                                        <input type="hidden" name="bandwidth_api_application_id"
                                               value="{{ $account['bandwidth_api_application_id'] }}">
                                    </dd>
                                    <dt class="text-muted-light">Bandwidth API Username</dt>
                                    <dd class="">
                                        <code class="text-primary fw-bold">{{ $account['bandwidth_api_username'] }}</code>
                                        <input type="hidden" name="bandwidth_api_application_id"
                                               value="{{ $account['bandwidth_api_application_id'] }}">
                                    </dd>

                                    <dt class="text-muted-light">Bandwidth API Password</dt>
                                    <dd class="">
                                        <code class="text-primary fw-bold">{{ $account['bandwidth_api_password'] }}</code>
                                        <input type="hidden" name="bandwidth_api_password"
                                               value="{{ $account['bandwidth_api_password'] }}">
                                    </dd>
                                </dl>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
