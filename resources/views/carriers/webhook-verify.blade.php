@extends('layouts.app')
@section('title', __('Verify Webhook Setup'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Verify Webhook Setup') }}</h5>
    <div class="row justify-content-start mb-2">

        <div class="card w-100 py-0">
            <div class="card-body py-0">
                <form method="POST" action="/carriers" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="carrier_api" value="webhook">
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
                                        Configure Webhook
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <label>Webhook Details</label>
                                <dl class="w-100 bg-light rounded p-4">
                                    <dt class="text-muted-light">Host</dt>
                                    <dl class="text-dark fw-bolder">
                                        {{ $account['webhook_host'] }}
                                        <input type="hidden" name="webhook_host"
                                               value="{{ $account['webhook_host'] }}">
                                    </dl>
                                    <dt class="text-muted-light">Endpoint</dt>
                                    <dl class="">
                                        <code class="text-primary fw-bold">{{ $account['webhook_endpoint'] }}</code>
                                        <input type="hidden" name="webhook_endpoint"
                                               value="{{ $account['webhook_endpoint'] }}">
                                    </dl>
                                    <dt class="text-muted-light">Basic Auth User</dt>
                                    <dl class="">
                                        <code
                                            class="text-primary fw-bold">{{ $account['webhook_username'] }}</code>
                                        <input type="hidden" name="webhook_username"
                                               value="{{ $account['webhook_username'] }}">
                                    </dl>
                                    <dt class="text-muted-light">Basic Auth User</dt>
                                    <dl class="">
                                        <code
                                            class="text-primary fw-bold">{{ $account['webhook_password'] }}</code>
                                        <input type="hidden" name="webhook_password"
                                               value="{{ $account['webhook_password'] }}">
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
