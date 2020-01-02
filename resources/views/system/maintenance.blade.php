<ul class="list-group rounded">
    <li class="list-group-item">
        <i class="fas fa-tools text-indigo"></i> {{ __('Maintenance') }}

    @if( $maintenanceMode )
        <!--<i class="fas fa-exclamation-circle text-orange"></i> Maintenance is active-->
        @else
            <small class="float-right text-muted font-weight-bold">
                <i class="fas fa-check-circle text-success"></i> Application is active
            </small>
        @endif

    </li>
    <li class="list-group-item">
        <i class="fas fa-network-wired text-indigo"></i> {{ __('Your IP Address') }}
        <small class="float-right">
            {{ $clientIp }}
        </small>
        <small class="d-inline-block text-small text-muted">
            Automatically excluded from Maintenance Mode
        </small>
    </li>
    <li class="list-group-item @if( $maintenanceMode ){{ 'bg-dark bg-striped ' }}@endif">
        @if( $maintenanceMode )

            <a class="btn btn-orange btn-block my-2 font-weight-bold" href="#" data-toggle="modal" data-target="#disableMaintenanceModeModal">
                Disable Maintenance Mode
            </a>

        @else
            <a href="#" data-toggle="modal" data-target="#enableMaintenanceModeModal"
               class="btn btn-outline-danger btn-block my-2 font-weight-bold">
                Enable Maintenance Mode
            </a>

        @endif
    </li>
    @if( $maintenanceMode )
        <li class="list-group-item m-0 p-0">
            <table class="table table-striped rounded table-fixed m-0">
                <tbody>

                @foreach( Arr::dot($maintenanceMode) as $key => $val )
                    <tr>

                        @if( $key == 'time' )
                            <th class="px-4">Started</th>
                            <td class="text-left text-small text-nowrap">{{ Carbon\Carbon::createFromTimestampUTC( $val )->timezone(Auth::user()->timezone )->diffForHumans(null, null, true, 2)  }}</td>
                        @elseif( $key == 'retry')
                            <th class="px-4">Goal End</th>
                            <td class="text-left text-small text-nowrap">{{ Carbon\Carbon::createFromTimestampUTC( $maintenanceMode['time'] )->addMinutes( $val )->timezone( Auth::user()->timezone )->diffForHumans(null, null, true, 2 )  }}</td>
                        @elseif( Str::startsWith( $key, 'allowed' ))
                            <th class="px-4">Allowed IP</th>
                            @if( $clientIp  == $val )
                                <td class="text-left text-small text-truncate">{{ $val }} <i title="This is your IP address!" class="font-weight-bold text-success fas fa-user"></i></td>
                            @else
                                <td class="text-left text-small text-truncate">{{ $val }}</td>
                            @endif
                        @else
                            <th class="px-4">{{ ucwords( $key ) }}</th>
                            <td class="text-left text-small text-truncate">{{ $val }}</td>
                        @endif


                    </tr>
                @endforeach

                </tbody>
            </table>

        </li>
    @endif

</ul>
