<div class="modal fade" data-backdrop="static" id="disableMaintenanceModeModal" tabindex="-1" role="dialog" aria-labelledby="disableMaintenanceModeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-orange">
            <div class="modal-header border-bottom-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/system/maintenance/disable" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-orange">Disable</strong> maintenance mode?</h3>
                    <p class="text-muted">
                        This will take the system out of maintenance mode and back into normal operations.
                    </p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-orange">Disable Maintenance Mode</button>
                </div>
            </form>
        </div>
    </div>
</div>
