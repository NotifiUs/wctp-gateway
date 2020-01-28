@extends('layouts.app')
@section('title', __('Enterprise Hosts'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Enterprise Hosts') }}</h5>
    <div class="row justify-content-start mb-2">


        @if( isset( $enterpriseHosts ) )
            @foreach( $enterpriseHosts as $host)
                <div class="col-md-6">
                    <div class="card mb-4 bg-white">
                        <h5 class="card-header bg-transparent">
                            <small class="d-inline text-muted-light font-weight-bold text-uppercase">
                               {{ $host->name }}
                            </small>

                            <div class="dropdown float-right">
                                <a class="btn btn-sm btn-light border dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-cog"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right shadow-sm bg-light" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editEnterpriseHostModal{{ $host->id }}">{{ __('Edit Settings') }}</a>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#credentialsEnterpriseHostModal{{ $host->id }}">{{ __('View Credentials') }}</a>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#setupEnterpriseHostModal{{ $host->id }}">{{ __('Setup Information') }}</a>
                                    <div class="dropdown-divider"></div>
                                    @if( $host->enabled )
                                        <a class="dropdown-item text-orange" href="#" data-toggle="modal" data-target="#disableEnterpriseHostModal{{ $host->id }}">{{ __('Disable Host') }}</a>
                                    @else
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#enableEnterpriseHostModal{{ $host->id }}">{{ __('Enable Host') }}</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#deleteEnterpriseHostModal{{ $host->id }}">{{ __('Delete Host') }}</a>
                                    @endif

                                </div>
                            </div>
                        </h5>

                        <div class="card-body my-0 py-4 text-center">
                           <i class="fas fa-4x fa-cube text-primary"></i>
                            <div class="my-2">
                                <h5 class="my-0 text-muted">{{ $host->senderID }}</h5>
                                <small class="text-muted-light">
                                    https://<span class="font-weight-bold">{{ str_replace('https://', '', $host->url) }}</span>
                                </small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small class="d-inline text-muted-light font-weight-bold text-uppercase">
                                <i class="fas fa-plug"></i> {{ ucwords( $host->type )}}
                            </small>
                            <div class="float-right">
                                <small class="d-inline font-weight-bold text-uppercase">
                                    @if( $host->enabled)
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
                @include('enterprise_hosts.modals.credentials')
                @include('enterprise_hosts.modals.setup')
                @include('enterprise_hosts.modals.delete')
                @include('enterprise_hosts.modals.disable')
                @include('enterprise_hosts.modals.edit')
                @include('enterprise_hosts.modals.enable')
            @endforeach
        @endif


        <div class="col-md-6">
            <div class="card mb-4 border-muted bg-light pt-5 pb-4">

                <div class="card-body text-muted py-5 my-0 text-center">
                    <a href="#" data-toggle="modal" data-target="#createEnterpriseHostModal">
                        <i class="fas fa-3x fa-plus text-muted-light"></i>
                    </a>

                    <h5 class="my-3">
                        <a href="#" data-toggle="modal" data-target="#createEnterpriseHostModal" class="text-muted">
                            {{ __('Add Enterprise Host') }}
                        </a>
                    </h5>
                </div>
            </div>
        </div>
            @include('enterprise_hosts.modals.create')

    </div>

@endsection
