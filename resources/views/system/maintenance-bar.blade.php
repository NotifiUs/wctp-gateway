@if( App::isDownForMaintenance())
    <div
        class="bg-danger border-bottom border-light bg-striped m-0 w-100 py-2 shadow-inner text-center fw-bold text-light"
        style="font-size:1.10rem">
        <i class="fas fa-cog fa-spin" style="color:lightpink;"></i> Maintenance mode active!
    </div>
@endif
