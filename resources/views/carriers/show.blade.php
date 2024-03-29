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
                            <small class="d-inline text-muted-light fw-bold text-uppercase">
                                {{ __('Priority') }} <span class="text-primary">{{ $carrier->priority }}</span>
                            </small>

                            <div class="dropdown float-end">
                                <a class="btn btn-sm btn-light border dropdown-toggle" href="#" role="button"
                                   id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true"
                                   aria-expanded="false">
                                    <i class="fas fa-cog"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right shadow-sm bg-light"
                                     aria-labelledby="dropdownMenuLink">
                                    @if( $carrier->beta )
                                        <h6 class="dropdown-header text-uppercase text-muted-light">
                                            <i class="fas fa-flask"></i> {{ __('Beta Carrier') }}
                                        </h6>
                                    @endif
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                       data-bs-target="#editCarrierModal{{ $carrier->id }}">{{ __('Edit Settings') }}</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                       data-bs-target="#credentialsCarrierModal{{ $carrier->id }}">{{ __('View Credentials') }}</a>
                                    <div class="dropdown-divider"></div>
                                    @if( $carrier->enabled )
                                        <a class="dropdown-item text-orange" href="#" data-bs-toggle="modal"
                                           data-bs-target="#disableCarrierModal{{ $carrier->id }}">{{ __('Disable Carrier') }}</a>
                                    @else
                                        <a class="dropdown-item @if( $carrier->numbers->where('enabled', 1)->count() == 0 ){{ ' disabled ' }}@endif"
                                           href="#" data-bs-toggle="modal"
                                           @if( $carrier->numbers->where('enabled', 1)->count() == 0 ){{ ' tabindex="-1" aria-disabled="true" ' }}@endif data-bs-target="#enableCarrierModal{{ $carrier->id }}">{{ __('Enable Carrier') }}</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                           data-bs-target="#deleteCarrierModal{{ $carrier->id }}">{{ __('Delete Carrier') }}</a>
                                    @endif

                                </div>
                            </div>
                        </h5>

                        <div class="card-header bg-transparent my-0 py-4 text-center">
                            @php
                                $imageDetails = [
                                    'url' => '/images/pager.png',
                                    'title' => 'Unknown Carrier'
                                ];
                                try{
                                    $driverFactory = new \App\Drivers\DriverFactory( $carrier->api );
                                    $driver = $driverFactory->loadDriver();
                                    $imageDetails = $driver->showCarrierImageDetails();
                                }
                                catch( Exception $e ){}
                            @endphp

                            <img class="img-fluid" style="height:4rem;" src="{{ $imageDetails['url'] }}"
                                 title="{{ $imageDetails['title'] }}" alt="{{ $imageDetails['title'] }}">

                        </div>
                        <div class="card-body my-0 py-2 text-center">
                            <p class="text-center">
                                <small>
                                    <strong class="text-muted">{{ $carrier->name }}</strong>
                                    <br>
                                    <code
                                        class="bg-light text-dark p-1">
                                        {{ $driver->showCarrierDetails($carrier) }}
                                    </code>
                                </small>
                            </p>
                        </div>
                        <div class="card-footer">

                            <small class="d-inline text-muted-light fw-bold text-uppercase">
                                @if( $carrier->numbers->where('enabled', 1)->count() )
                                    <a class=" text-muted-light" href="/numbers">
                                        {{ $carrier->numbers->where('enabled', 1)->count() }} Active Number(s)
                                    </a>
                                @else
                                    <i class="fas fa-exclamation-triangle text-orange"></i>   <a class="text-dark"
                                                                                                 href="/numbers">No
                                        Active Numbers</a>
                                @endif
                            </small>

                            <div class="float-end">
                                <small class="d-inline fw-bold text-uppercase">
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
                    <a href="#" data-bs-toggle="modal" data-bs-target="#verifyCarrierModal">
                        <i class="fas fa-3x fa-plus text-muted-light"></i>
                    </a>

                    <h5 class="my-3">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#verifyCarrierModal" class="text-muted">
                            {{ __('Add Carrier') }}
                        </a>
                    </h5>
                </div>
            </div>
        </div>

        @include('carriers.modals.verify')

    </div>

@endsection
