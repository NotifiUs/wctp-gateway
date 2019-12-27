@extends('layouts.app')
@section('title', __('Carrier API Providers'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Carrier API Providers') }}</h5>
    <div class="row justify-content-start mb-2">

        @if( isset( $carriers ) )
            @foreach( $carriers as $carrier )
                <div class="col-md-6">
                    <div class="card mb-4">
                        <h5 class="card-header bg-transparent">
                            <small class="d-inline text-muted-light font-weight-bold text-uppercase">
                                {{ __('Priority') }} <span class="text-primary">{{ $carrier->priority }}</span>
                            </small>

                            <div class="dropdown float-right">
                                <a class="btn btn-sm btn-light border dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-cog"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right shadow-sm bg-light" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editCarrierModal{{ $carrier->id }}">{{ __('Edit Settings') }}</a>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#credentialsCarrierModal{{ $carrier->id }}">{{ __('View Credentials') }}</a>
                                    <div class="dropdown-divider"></div>
                                    @if( $carrier->enabled )
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#disableCarrierModal{{ $carrier->id }}">{{ __('Disable Carrier') }}</a>
                                    @else
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#enableCarrierModal{{ $carrier->id }}">{{ __('Enable Carrier') }}</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#deleteCarrierModal{{ $carrier->id }}">{{ __('Delete Carrier') }}</a>
                                    @endif
                                </div>
                            </div>
                        </h5>

                        <div class="card-header bg-transparent my-0 py-4 text-center">
                            @if( $carrier->api == 'twilio' )
                                <a href="https://www.twilio.com">
                                    <img class="img-fluid" style="height:4rem;"  src="/images/twilio-badge.png" title="{{ __('Powered by Twilio') }}" alt="{{ __('Powered by Twilio') }}">
                                </a>
                            @elseif( $carrier->api == 'thinq')
                                <a href="https://www.thinq.com">
                                    <img class="img-fluid" style="height:4rem" src="/images/thinq-badge.svg" title="{{ __('Powered by ThinQ') }}" alt="{{ __('Powered by ThinQ') }}">
                                </a>
                            @else
                                <h3>Unknown Carrier API</h3>
                            @endif
                        </div>
                        <div class="card-body my-0 py-2 text-center">
                            <p class="text-center">
                                <small>
                                    <strong class="text-muted">{{ $carrier->name }}</strong>
                                    <br>
                                    <code class="bg-light text-dark p-1">{{ $carrier->twilio_account_sid ?? $carrier->thinq_account_id }}</code>
                                </small>
                            </p>
                        </div>
                        <div class="card-footer">
                            @if( $carrier->beta )
                                <small class="d-inline text-muted-light font-weight-bold text-uppercase">
                                    <i class="fas fa-flask"></i> {{ __('Beta') }}
                                </small>
                            @endif
                            <div class="float-right">
                                <small class="d-inline font-weight-bold text-uppercase">
                                    @if( $carrier->enabled)
                                        <span class="text-success">
                                            <i class="fas fa-check-circle"></i> {{ __('Enabled') }}
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            <i class="fas fa-times-circle"></i> {{ __('Disabled') }}
                                        </span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                @include('carriers.modals.credentials')
                @include('carriers.modals.delete')
                @include('carriers.modals.disable')
                @include('carriers.modals.edit')
                @include('carriers.modals.enable')

            @endforeach
        @endif

        <div class="col-md-6">
            <div class="card mb-4 border-muted bg-light py-5">

                <div class="card-body text-muted py-5 my-0 text-center">
                    <a href="#" data-toggle="modal" data-target="#verifyCarrierModal">
                        <i class="fas fa-3x fa-plus text-muted-light"></i>
                    </a>

                    <h5 class="my-3">
                        <a href="#" data-toggle="modal" data-target="#verifyCarrierModal" class="text-muted">
                            {{ __('Add Carrier') }}
                        </a>
                    </h5>
                </div>
            </div>
        </div>

        @include('carriers.modals.verify')

    </div>

@endsection
