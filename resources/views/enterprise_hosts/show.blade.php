@extends('layouts.app')
@section('title', __('Enterprise Hosts'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Enterprise Hosts') }}</h5>
    <div class="row justify-content-start mb-2">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header bg-transparent">
                    <small class="d-inline text-muted-light font-weight-bold text-uppercase">
                       Columbus
                    </small>

                    <div class="dropdown float-right">
                        <a class="btn btn-sm btn-light border dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm bg-light" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">{{ __('Edit Settings') }}</a>
                            <a class="dropdown-item" href="#">{{ __('View Credentials') }}</a>
                            <a class="dropdown-item" href="#">{{ __('Disable Host') }}</a>
                        </div>
                    </div>
                </h5>

                <div class="card-body my-0 py-4 text-center">
                   <i class="fas fa-4x fa-cube text-primary"></i>
                    <div class="my-2">
                        <small class="text-muted-light">
                            https://<span class="font-weight-bold">enterprise.test/wctp</span>
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="d-inline text-muted-light font-weight-bold text-uppercase">
                        <i class="fas fa-bookmark"></i> {{ __('Amtelco') }}
                    </small>
                    <div class="float-right">
                        <small class="d-inline text-success font-weight-bold text-uppercase">
                            <i class="fas fa-check-circle"></i> {{ __('Enabled') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card mb-4 border-muted bg-light py-4">

                <div class="card-body text-muted py-5 my-0 text-center">
                    <a href="#">
                        <i class="fas fa-3x fa-plus text-muted-light"></i>
                    </a>

                    <h5 class="my-3">
                        <a href="#" class="text-muted">
                            {{ __('Add Enterprise Host') }}
                        </a>
                    </h5>
                </div>
            </div>
        </div>

    </div>


@endsection
