@extends('layouts.app')

@section('title', 'Home')

@push('css')
@endpush

@push('scripts')
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h5 class="text-muted">{{ __('Navigation') }}</h5>
            <ul class="list-group">


                <a href="/home" class="list-group-item list-group-item-action @if( request()->path() == 'home' ) {{ " active " }} @endif">
                    <i class="fas fa-tachometer-alt text-primary"></i> {{ __('Dashboard') }}
                </a>
                <a href="/carriers" class="list-group-item list-group-item-action @if( request()->path() == 'carriers' ) {{ " active " }} @endif">
                    <i class="fas fa-broadcast-tower text-primary"></i> {{ __('Carrier Setup') }}
                </a>
                <a href="/hosts" class="list-group-item list-group-item-action @if( request()->path() == 'hosts' ) {{ " active " }} @endif">
                    <i class="fas fa-network-wired text-primary"></i> {{ __('Enterprise Host') }}
                </a>
                <a href="/queue" class="list-group-item list-group-item-action">
                    <i class="fas fa-exchange-alt text-primary"></i> {{ __('Message Queue') }}
                </a>
                <a href="/system" class="list-group-item list-group-item-action @if( request()->path() == 'system' ) {{ " active " }} @endif">
                    <i class="fas fa-cogs text-primary"></i> {{ __('System Configuration') }}
                </a>
            </ul>


            <h5 class="text-muted mt-4">{{ __('System Information') }}</h5>
            <ul class="list-group">
                <li class="list-group-item">
                    <i class="fas fa-server text-indigo"></i> {{ __('Server') }} <span class="float-right text-muted">hostname.local</span>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-database text-indigo"></i> {{ __('Database') }} <span class="float-right text-muted">{{ mt_rand(5,100) }}MB</span>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-microchip text-indigo"></i> {{ __('CPU') }} <span class="float-right text-muted">{{ mt_rand(2,4) }}.5Ghz</span>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-memory text-indigo"></i> {{ __('Memory') }} <span class="float-right text-muted">{{ mt_rand(8,8) }}GB</span>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-hdd text-indigo"></i> {{ __('Disk Space') }} <span class="float-right text-muted">{{ mt_rand(5,100) }}GB</span>
                </li>

            </ul>


        </div>
        <div class="col-md-8">
            <h5 class="text-muted">{{ __('Quick Glance') }}</h5>
            <div class="row justify-content-center mb-4">

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center my-0">
                            <i class="fas fa-3x fa-check-circle text-success"></i>
                            <h5 class="text-muted mt-2 mb-0">{{ __('Queue Status') }}</h5>
                            <a href="/queue" class="text-success">{{ __('Running') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center my-0">
                            <i class="fas fa-3x fa-sim-card text-info"></i>
                            <h5 class="text-muted mt-2 mb-0">{{__('Active Carrier')}}</h5>
                            <a href="/carriers" class="text-info">Twilio</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center my-0">
                            <i class="fas fa-3x fa-server text-indigo"></i>
                            <h5 class="text-muted mt-2 mb-0">{{ __('Enterprise Host') }}</h5>
                            <a href="/hosts" class="text-indigo">NotifiUs</a>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="text-muted">{{ __('Event Log') }}</h5>
            <div class="card py-0 my-0">
                <!--<div class="card-header">{{ __('Dashboard') }}</div>-->
                <div class="card-body p-0 m-0">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="table-responsive text-left">
                        <table class="table table-striped table-hover m-0">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold text-muted">{{ __('Timestamp') }}</th>
                                    <th class="font-weight-bold text-muted">{{ __('Source') }}</th>
                                    <th class="font-weight-bold text-muted">{{ __('Description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php

                            @endphp
                            @for( $i=0; $i<mt_rand(7,12); $i++)
                                <tr>
                                    <td><small class="text-muted">{{ \Carbon\Carbon::now( Auth::user()->timezone )->subMinutes($i)->format('m/d/Y g:i:s A T') }}</small></td>
                                    <td class="text-muted">WCTP</td>
                                    <td class="text-dark">Processed inbound message from carrier </td>
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
