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
            <h5 class="text-muted-light">{{ __('Navigation') }}</h5>
            <ul class="list-group">
                <a href="/home" class="list-group-item list-group-item-action @if( request()->path() == 'home' ) {{ " active " }} @endif">
                    <i class="fas fa-tachometer-alt text-primary"></i> {{ __('Dashboard') }}
                </a>
                <a href="/events" class="list-group-item list-group-item-action @if( request()->path() == 'analytics' ) {{ " active " }} @endif">
                    <i class="fas fa-chart-bar text-primary"></i> {{ __('Analytics') }}
                </a>
                <a href="/carriers" class="list-group-item list-group-item-action @if( request()->path() == 'carriers' ) {{ " active " }} @endif">
                    <i class="fas fa-sim-card text-primary"></i> {{ __('Carriers') }}
                </a>
                <a href="/hosts" class="list-group-item list-group-item-action @if( request()->path() == 'hosts' ) {{ " active " }} @endif">
                    <i class="fas fa-cube text-primary"></i> {{ __('Enterprise Host') }}
                </a>
                <a href="/queue" class="list-group-item list-group-item-action">
                    <i class="fas fa-exchange-alt text-primary"></i> {{ __('Message Queue') }}
                </a>
                <a href="/system" class="list-group-item list-group-item-action @if( request()->path() == 'system' ) {{ " active " }} @endif">
                    <i class="fas fa-cogs text-primary"></i> {{ __('System Settings') }}
                </a>
                <a href="/events" class="list-group-item list-group-item-action @if( request()->path() == 'events' ) {{ " active " }} @endif">
                    <i class="fas fa-stream text-primary"></i> {{ __('Events') }}
                </a>
            </ul>


            <h5 class="text-muted-light mt-4">{{ __('System Information') }}</h5>
            <ul class="list-group">
                <li class="list-group-item">
                    <i class="fas fa-server text-indigo"></i> {{ __('Server') }} <span class="float-right text-muted">hostname.local</span>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-ethernet text-indigo"></i> {{ __('IP Address') }} <span class="float-right text-muted">192.168.10.10</span>
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
            <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Quick Glance') }}</h5>
            <div class="row justify-content-center mb-2">

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center my-0">
                            @if( mt_rand(0,1))
                                <i class="fas fa-3x fa-check-circle text-success"></i>
                                <h5 class="text-muted mt-2 mb-0">{{ __('Message Queue') }}</h5>
                                <a href="/queue" class="text-success">{{ __('Running') }}</a>
                            @else
                                <i class="fas fa-3x fa-times text-danger"></i>
                                <h5 class="text-muted mt-2 mb-0">{{ __('Message Queue') }}</h5>
                                <a href="/queue" class="text-danger">{{ __('Inactive') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-2">
                        <div class="card-body text-center my-0">
                            <i class="fas fa-3x fa-sim-card text-primary"></i>
                            <h5 class="text-muted mt-2 mb-0">{{__('Active Carrier')}}</h5>
                            <a href="/carriers" class="text-primary">Twilio</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-2">
                        <div class="card-body text-center my-0">
                            <i class="fas fa-3x fa-cube text-primary"></i>
                            <h5 class="text-muted mt-2 mb-0">{{ __('Enterprise Host') }}</h5>
                            <a href="/hosts" class="text-primary">NotifiUs</a>
                        </div>
                    </div>
                </div>
            </div>


            <h5 class="text-muted-light mt-2">{{ __('WCTP Gateway') }}
            </h5>
            <div class="row justify-content-center mb-2">

                <div class="col">
                    <div class="card mb-2">
                        <div class="card-body my-0">
                            <dl class="row">

                                <dt class="col-sm-12 col-md-4">
                                    WCTP Actor (Acting as)
                                </dt>
                                <dd class="col-sm-12 col-md-8 text-muted">
                                    Carrier Gateway
                                </dd>


                                <dt class="col-sm-12 col-md-4">
                                    Version Support
                                </dt>
                                <dd class="col-sm-12 col-md-8 text-muted">
                                    <a href="http://www.wctp.org/release/wctp-v1r3_update1.pdf">
                                        WCTP v1r3 Update 1 <small><i class="fas fa-external-link-alt"></i></small>
                                    </a>
                                </dd>


                                <dt class="col-sm-12 col-md-4">
                                    Carrier Gateway Endpoint
                                </dt>
                                <dd class="col-sm-12 col-md-8 text-muted">
                                    https://<span class="font-weight-bold">gateway.test/wctp</span>
                                </dd>

                                <dt class="col-sm-12 col-md-4">
                                    Enterprise Host Endpoint
                                </dt>
                                <dd class="col-sm-12 col-md-8 text-muted">
                                    https://<span class="font-weight-bold">enterprise.test/wctp</span>
                                </dd>

                                <dt class="col-sm-12 col-md-4">
                                    Security Information
                                </dt>
                                <dd class="col-sm-12 col-md-8 text-muted">
                                    <i class="fas fa-lock"></i> TLS Required &middot; <i class="fas fa-user-lock"></i> Password Protected
                                </dd>

                                <dt class="col-sm-12 col-md-4">
                                    Network Ports
                                </dt>
                                <dd class="col-sm-12 col-md-8 text-muted">
                                     443/TCP
                                </dd>

                            </dl>

                            <small class="text-muted">
                                <i class="fas fa-question-circle"></i> Learn how to <a href="#">configure</a> your WCTP integration
                            </small>
                        </div>
                    </div>
                </div>
            </div>



            <h5 class="text-muted-light mt-2">{{ __('Recent Events') }}
                <small>
                    <a href="/events" class="float-right text-muted">View event log</a>
                </small>
            </h5>
            <div class="card py-0 my-0">
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
                                $sources[0] = 'Enterprise Host';
                                $sources[1] = 'Webhook';
                                $sources[2] = 'Queue';

                                $messages[0] = 'Outbound message submitted by enterprise host';
                                $messages[1] = 'Inbound SMS from Twilio';
                                $messages[2] = 'Message processed from queue';
                            @endphp
                            @for( $i=0; $i<mt_rand(7,12); $i++)
                                @php
                                    $index = mt_rand(0,count($sources)-1);
                                @endphp
                                <tr>
                                    <td><small class="text-muted">{{ \Carbon\Carbon::now( Auth::user()->timezone )->subMinutes($i)->format('m/d/Y g:i:s A T') }}</small></td>
                                    <td class="text-muted">{{ $sources[$index] }}</td>
                                    <td class="text-dark">{{ $messages[$index] }}</td>
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
