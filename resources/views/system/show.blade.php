@extends('layouts.app')
@section('title', __('System Administration'))
@push('css')
@endpush
@push('scripts')
@endpush
@push('modals')
    @include('system.modals.enable')
    @include('system.modals.disable')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('System Administration') }}</h5>
    <div class="row justify-content-start my-2">

        <div class="col-md-6">
            <ul class="list-group rounded">
                <li class="list-group-item">
                    <i class="fas fa-layer-group text-indigo"></i> {{ __('Queue Viewer') }}
                    <small class="float-end">
                        <a class="btn-link" href="/queue" target="_blank">View</a>
                    </small>
                </li>

            </ul>
        </div>

        <div class="col-md-6">
            <ul class="list-group rounded">
                <li class="list-group-item">
                    <i class="fas fa-bug text-indigo"></i> {{ __('Application Debug') }}
                    <small class="float-end">
                        <a class=" btn-link" href="/debug" target="_blank">View</a>
                    </small>
                </li>
            </ul>
        </div>

    </div>


    <div class="row justify-content-start my-2">

        <div class="col-md-6">
            <ul class="list-group rounded">
                <li class="list-group-item">
                    <i class="fas fa-stream text-indigo"></i> {{ __('Events') }}
                    <small class="float-end">
                        <a class="btn-link" href="/events">View</a>
                    </small>
                </li>

            </ul>
        </div>

        <div class="col-md-6">
            <ul class="list-group rounded">
                <li class="list-group-item">
                    <i class="fas fa-server text-indigo"></i> {{ __('System Information') }}
                    <small class="float-end">
                        <a class=" btn-link" href="/system/information">View</a>
                    </small>
                </li>
            </ul>
        </div>

    </div>



    <div class="row justify-content-start my-2">

        <div class="col-md-6">
            <h5 class="text-muted-light mt-4">{{ __('User Management') }}</h5>
            @include('system.users')
        </div>

        <div class="col-md-6">
            <h5 class="text-muted-light mt-4">{{ __('Maintenance Mode') }}</h5>
            @include('system.maintenance')
        </div>

    </div>


@endsection
