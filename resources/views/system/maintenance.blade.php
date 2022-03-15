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
            <a class="btn btn-orange btn-block my-2 fw-bold" href="#" data-bs-toggle="modal"
               data-bs-target="#disableMaintenanceModeModal">
                Disable Maintenance Mode
            </a>
        @else
            <a href="#" data-bs-toggle="modal" data-bs-target="#enableMaintenanceModeModal"
               class="btn btn-outline-danger btn-block my-2 fw-bold">
                Enable Maintenance Mode
            </a>
        @endif
    </li>
</ul>
