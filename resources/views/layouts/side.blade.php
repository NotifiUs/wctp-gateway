<h5 class="text-muted-light">{{ __('Core Resources') }}</h5>
<ul class="list-group rounded">
    <a href="/home"
       class="list-group-item list-group-item-action @if( Str::startsWith(request()->path(), 'home') ) {{ " active " }} @endif">
        <i class="fas fa-tachometer-alt text-primary"></i> {{ __('Dashboard') }}
    </a>
    <a href="/hosts"
       class="list-group-item list-group-item-action @if( Str::startsWith(request()->path(), 'hosts' )) {{ " active " }} @endif">
        <i class="fas fa-cube text-primary"></i> {{ __('Enterprise Hosts') }}
    </a>
    <a href="/carriers"
       class="list-group-item list-group-item-action @if( Str::startsWith(request()->path(), 'carriers') ) {{ " active " }} @endif">
        <i class="fas fa-sim-card text-primary"></i> {{ __('Carrier APIs') }}
    </a>
    <a href="/numbers"
       class="list-group-item list-group-item-action @if( Str::startsWith(request()->path(), 'numbers') ) {{ " active " }} @endif">
        <i class="fas fa-hashtag text-primary"></i> {{ __('Phone Numbers') }}
    </a>
</ul>


<h5 class="text-muted-light mt-4">{{ __('Advanced Tools') }}</h5>
<ul class="list-group rounded">
    <a href="/messages"
       class="list-group-item list-group-item-action @if( Str::startsWith(request()->path(), 'messages' )) {{ " active " }} @endif">
        <i class="fas fa-sms text-indigo"></i> {{ __('Messages') }}
    </a>
    <a href="/events"
       class="list-group-item list-group-item-action @if( Str::startsWith(request()->path(),'events') ) {{ " active " }} @endif">
        <i class="fas fa-stream text-indigo"></i> {{ __('Events') }}
    </a>
<!--
        <a href="/sticky" class="list-group-item list-group-item-action @if( Str::startsWith(request()->path(), 'sticky' )) {{ " active " }} @endif">
            <i class="fas fa-magnet text-indigo"></i> {{ __('Sticky Sender') }}
    </a>
-->
    <a href="/system"
       class="list-group-item list-group-item-action @if( Str::startsWith(request()->path(), 'system')) {{ " active " }} @endif">
        <i class="fas fa-cogs text-indigo"></i> {{ __('System Settings') }}
    </a>

</ul>


@if( Str::startsWith(request()->path(), 'home') && count( $server ) )
    <h5 class="text-muted-light mt-4">{{ __('System Information') }}</h5>
    <ul class="list-group rounded">
        <li class="list-group-item">
            <i class="fas fa-server text-info"></i> {{ __('Server') }} <small
                class="float-end fw-bold text-muted">{{ $server['hostname'] }}</small>
        </li>
        <li class="list-group-item">
            <i class="fas fa-ethernet text-info"></i> {{ __('IP Address') }} <small
                class="float-end text-muted">{{ $server['ip'] }}</small>
        </li>
        <li class="list-group-item">
            <i class="fas fa-microchip text-info"></i> {{ __('CPU') }} <small
                class="float-end text-muted">{{ $server['cpu'] }}</small>
        </li>
        <li class="list-group-item">
            <i class="fas fa-memory text-info"></i> {{ __('Memory') }} <small
                class="float-end text-muted">{{ $server['memory'] }}</small>
        </li>
        <li class="list-group-item">
            <i class="fas fa-hdd text-info"></i> {{ __('Disk Space') }}
            <div class="float-end w-25">
                <div class="progress">
                    <div class="progress-bar bg-info"
                         title="{{ $server['disk']['value'] }}GB used of {{ $server['disk']['total'] }}GB ({{ $server['disk']['percent'] }}%)"
                         role="progressbar" style="width: {{ $server['disk']['percent'] }}%;"
                         aria-valuenow="{{ $server['disk']['value'] }}" aria-valuemin="0"
                         aria-valuemax="{{ $server['disk']['total'] }}"></div>
                </div>
            </div>
        </li>
    </ul>
@endif
