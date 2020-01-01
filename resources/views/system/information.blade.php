@extends('layouts.app')
@section('title', __('System Information'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('System Information') }}</h5>
    <div class="row justify-content-start mb-4">

        <div class="col-md-6">
            <ul class="list-group rounded">
                <li class="list-group-item">
                    <i class="fas fa-server text-info"></i> {{ __('Server') }} <small class="float-right font-weight-bold text-muted">{{ $server['hostname'] }}</small>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-ethernet text-info"></i> {{ __('IP Address') }} <small class="float-right text-muted">{{ $server['ip'] }}</small>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-microchip text-info"></i> {{ __('CPU') }} <small class="float-right text-muted">{{ $server['cpu'] }}</small>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-memory text-info"></i> {{ __('Memory') }} <small class="float-right text-muted">{{ $server['memory'] }}</small>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-hdd text-info"></i> {{ __('Disk Space') }}
                    <div class="float-right w-25">
                        <div class="progress">
                            <div class="progress-bar bg-info" title="{{ $server['disk']['value'] }}GB used of {{ $server['disk']['total'] }}GB ({{ $server['disk']['percent'] }}%)" role="progressbar" style="width: {{ $server['disk']['percent'] }}%;" aria-valuenow="{{ $server['disk']['value'] }}" aria-valuemin="0" aria-valuemax="{{ $server['disk']['total'] }}"></div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="col-md-6">
            <ul class="list-group rounded">
                <li class="list-group-item">
                    <i class="fas fa-hourglass-half text-info"></i> {{ __('Uptime') }} <small class="float-right text-muted">{{ $advanced['uptime'] }}</small>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-weight-hanging text-info"></i> {{ __('Load') }} <small class="float-right text-muted">{{ $advanced['load'] }}</small>
                </li>
                <li class="list-group-item">
                    <i class="fab fa-php text-info"></i> {{ __('PHP Configuration') }} <span class="float-right font-weight-bold text-muted"><a href="/system/phpinfo"><code class="text-primary">phpinfo();</code></a></span>
                </li>
                <li class="list-group-item">
                    <i class="fab fa-linux text-info"></i> {{ __('Linux Version') }} <small class="float-right text-muted">{{ $advanced['version'] }}</small>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-box text-info"></i> {{ __('App') }} <small class="float-right text-muted">{{ $advanced['appversion'] }}</small>
                </li>
            </ul>
        </div>
    </div>


    <div class="row justify-content-start mb-4">
        <div class="col-md-6">
            <h5 class="text-muted-light mt-2 mt-md-0">{{ __('App Services') }}</h5>
            <ul class="list-group rounded">
                <li class="list-group-item">
                    <i class="fas fa-layer-group text-info"></i> <strong class="text-muted text-small">{{ __('horizon-queue') }}</strong>
                    <small class="float-right text-muted">
                        @if( $queue )
                            <i class="fas fa-check-circle text-success"></i> Running
                        @else
                            <i class="fas fa-times-circle text-danger"></i> Inactive
                        @endif
                    </small>
                </li>
                @foreach( $advanced['services'] as $service => $details )
                <li class="list-group-item">
                    <i class="{{ $details['icon'] }} text-info"></i> <strong class="text-muted text-small">{{ $service }}</strong>
                    <small title="{{$details['desc']}}" style="cursor:help;" class="float-right text-muted">
                        @if( $details['status'] )
                            <i class="fas fa-check-circle text-success"></i> Running
                        @else
                            <i class="fas fa-times-circle text-danger"></i> Inactive
                        @endif
                    </small>
                </li>
                @endforeach
            </ul>
        </div>




        <div class="col-md-6">
            @include('layouts.checklist')
        </div>
    </div>


@endsection
