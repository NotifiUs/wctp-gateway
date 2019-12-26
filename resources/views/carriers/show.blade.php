@extends('layouts.app')
@section('title', __('Carrier API Providers'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Carrier API Providers') }}</h5>
    <div class="row justify-content-start mb-2">

        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header bg-transparent">
                    <small class="d-inline text-muted-light font-weight-bold text-uppercase">
                        {{ __('Priority') }} <span class="text-primary">{{ __('01') }}</span>
                    </small>

                    <div class="dropdown float-right">
                        <a class="btn btn-sm btn-light border dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm bg-light" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">{{ __('Edit Settings') }}</a>
                            <a class="dropdown-item" href="#">{{ __('Disable Carrier') }}</a>
                            <a class="dropdown-item" href="#">{{ __('Change Priority') }}</a>
                        </div>
                    </div>
                </h5>

                <div class="card-header bg-transparent my-0 py-4 text-center">
                    <a href="https://www.twilio.com">
                        <img class="img-fluid" style="height:4rem;"  src="/images/twilio-badge.png" title="{{ __('Powered by Twilio') }}" alt="{{ __('Powered by Twilio') }}">
                    </a>
                </div>
                <div class="card-body my-0 py-2 text-center">
                    <p class="text-center">
                        <small>
                            <!-- no translation on account name, that's user set via twilio friendly_name -->
                            <strong class="text-muted">Twilio Test Account</strong>
                            <br>
                            <code class="bg-light text-dark p-1">{{ config('services.twilio.account') }}</code>
                        </small>
                    </p>
                </div>
                <div class="card-footer">
                    <div class="float-right">
                        <small class="d-inline text-success font-weight-bold text-uppercase">
                            <i class="fas fa-check-circle"></i> {{ __('Enabled') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header bg-transparent">
                    <small class="d-inline text-muted-light font-weight-bold text-uppercase">
                        {{ __('Priority') }} <span class="text-primary">{{ __('02') }}</span>
                    </small>
                    <div class="dropdown float-right">
                        <a class="btn btn-sm btn-light border dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm bg-light" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">{{ __('Edit Settings') }}</a>
                            <a class="dropdown-item" href="#">{{ __('Enable Carrier') }}</a>
                            <a class="dropdown-item disabled" href="#">{{ __('Change Priority') }}</a>
                        </div>
                    </div>
                </h5>
                <div class="card-header bg-transparent py-4 text-center">
                    <a href="https://www.thinq.com">
                        <img class="img-fluid" style="height:4rem" src="/images/thinq-badge.svg" title="{{ __('Powered by ThinQ') }}" alt="{{ __('Powered by ThinQ') }}">
                    </a>
                </div>
                <div class="card-body my-0 py-2 text-center">
                    <p class="text-center">
                        <small>
                            <!-- Set in ThinQ account? --->
                            <strong class="text-muted">ThinQ Test Account</strong>
                            <br>
                            <code class="bg-light text-dark p-1">2VN82HYF092</code>
                        </small>
                    </p>
                </div>
                <div class="card-footer">
                    <small class="d-inline text-muted-light font-weight-bold text-uppercase">
                        <i class="fas fa-flask"></i> {{ __('Beta') }}
                    </small>
                    <div class="float-right">
                        <small class="d-inline text-danger font-weight-bold text-uppercase">
                            <i class="fas fa-times-circle"></i> {{ __('Disabled') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!--
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header bg-transparent text-center">
                    <a href="https://www.twilio.com">
                        <img style="height:1.75rem;"  src="/images/twilio-badge.png" title="{{ __('Powered by Twilio') }}" alt="{{ __('Powered by Twilio') }}">
                    </a>
                </h5>

                <div class="card-body my-0">
                    <form>
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">{{ __('Account SID') }}</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">{{ __('Auth Token') }}</label>
                            <input type="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Use Twilio') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        -->

        <div class="col-md-6">
            <div class="card mb-4 border-muted bg-light py-4">

                <div class="card-body text-muted py-5 my-0 text-center">
                    <a href="#">
                        <i class="fas fa-3x fa-plus text-muted-light"></i>
                    </a>

                    <h5 class="my-3">
                        <a href="#" class="text-muted">
                            {{ __('Add Carrier') }}
                        </a>
                    </h5>
                </div>
            </div>
        </div>

        <!--
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header bg-transparent text-center">
                    <a href="https://www.thinq.com">
                        <img style="height:1.75rem;" src="/images/thinq-badge.svg" title="{{ __('Powered by ThinQ') }}" alt="{{ __('Powered by ThinQ') }}">
                    </a>
                </h5>
                <div class="card-body my-0">
                    <form>
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">{{ __('Account ID') }}</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">{{ __('Username') }}</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">{{ __('API Token') }}</label>
                            <input type="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Use ThinQ') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>-->


    </div>


@endsection
