<ul class="list-group rounded">
    <li class="list-group-item">

        @if( $maintenanceMode )
            <i class="fas fa-exclamation-circle text-orange"></i> Maintenance active
        @else
            <i class="fas fa-check-circle text-success"></i> Application active
        @endif

    </li>
    <li class="list-group-item @if( $maintenanceMode ){{ 'bg-dark bg-striped ' }}@endif">
        @if( $maintenanceMode )
            <a class="btn btn-orange btn-block my-2 font-weight-bold" href="#" data-toggle="modal"
               data-target="#disableMaintenanceModeModal">
                Disable Maintenance Mode
            </a>
        @else
            <a href="#" data-toggle="modal" data-target="#enableMaintenanceModeModal"
               class="btn btn-outline-danger btn-block my-2 font-weight-bold">
                Enable Maintenance Mode
            </a>
        @endif
    </li>
</ul>
