
    <h5 class="text-muted-light">{{ __('Navigation') }}</h5>
    <ul class="list-group rounded">
        <a href="/home" class="list-group-item list-group-item-action @if( request()->path() == 'home' ) {{ " active " }} @endif">
            <i class="fas fa-tachometer-alt text-primary"></i> {{ __('Dashboard') }}
        </a>
        <a href="/carriers" class="list-group-item list-group-item-action @if( request()->path() == 'carriers' ) {{ " active " }} @endif">
            <i class="fas fa-sim-card text-primary"></i> {{ __('Carrier APIs') }}
        </a>
        <a href="/numbers" class="list-group-item list-group-item-action @if( request()->path() == 'numbers' ) {{ " active " }} @endif">
            <i class="fas fa-hashtag text-primary"></i> {{ __('Phone Numbers') }}
        </a>
        <a href="/hosts" class="list-group-item list-group-item-action @if( request()->path() == 'hosts' ) {{ " active " }} @endif">
            <i class="fas fa-cube text-primary"></i> {{ __('Enterprise Hosts') }}
        </a>
        <!-- To simply, let's remove this for now. Revisit and remove this when content! -->
        <!--
        <a href="/queue" class="list-group-item list-group-item-action">
            <i class="fas fa-exchange-alt text-primary"></i> {{ __('Message Queue') }}
        </a>
        -->
    </ul>


    <h5 class="text-muted-light mt-4">{{ __('Under Construction') }}</h5>
    <ul class="list-group rounded">
        <a href="/analytics" class="list-group-item list-group-item-action @if( request()->path() == 'analytics' ) {{ " active " }} @endif">
            <i class="fas fa-chart-bar text-danger"></i> {{ __('Analytics') }}
        </a>
        <a href="/sticky" class="list-group-item list-group-item-action @if( request()->path() == 'sticky' ) {{ " active " }} @endif">
            <i class="fas fa-magnet text-danger"></i> {{ __('Sticky Sender') }}
        </a>
        <a href="/system" class="list-group-item list-group-item-action @if( request()->path() == 'system' ) {{ " active " }} @endif">
            <i class="fas fa-cogs text-danger"></i> {{ __('System Settings') }}
        </a>
        <a href="/events" class="list-group-item list-group-item-action @if( request()->path() == 'events' ) {{ " active " }} @endif">
            <i class="fas fa-stream text-danger"></i> {{ __('Events') }}
        </a>
    </ul>


    @if( isset( $server ) )
    <h5 class="text-muted-light mt-4">{{ __('System Information') }}</h5>
    <ul class="list-group rounded">
        <li class="list-group-item">
            <i class="fas fa-server text-info"></i> {{ __('Server') }} <small class="float-right text-muted">{{ $server['hostname'] }}</small>
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
                    <div class="progress-bar" role="progressbar" style="width: {{ $server['disk']['percent'] }}%;" aria-valuenow="{{ $server['disk']['value'] }}" aria-valuemin="0" aria-valuemax="{{ $server['disk']['total'] }}"></div>
                </div>
            </div>
        </li>
    </ul>
    @endif
