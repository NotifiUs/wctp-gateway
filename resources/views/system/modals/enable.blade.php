<div class="modal fade" data-backdrop="static" id="enableMaintenanceModeModal" tabindex="-1" role="dialog" aria-labelledby="enableMaintenanceModeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header border-bottom-0">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/system/maintenance/enable" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3 class="mb-4">Are you sure you want to <strong class="text-danger">enable</strong> maintenance mode?</h3>

                    <div class="container-fluid mx-0 px-0">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Retry After</label>
                                    <input type="text" name="retry" placeholder="15" class="form-control" value="{{ old('retry') }}">
                                    <small class="form-text text-muted">
                                        Number of minutes maintenance is expected to last.
                                        Used for including the <code class="text-indigo font-weight-bold text-uppercase">HTTP Retry-After</code> header in the response.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted">
                                    While maintenance mode is active, the system will be <strong class="text-danger">unavailable for all users</strong> except you.
                                    WCTP endpoints and carrier API webhooks will continue to work, but no queue jobs or messages will be processed until maintenance mode is disabled.
                                </p>
                                <p class="text-muted">
                                    <i class="fas fa-info-circle text-muted-light"></i> If you get locked out, you can disable maintenance mode from the server console as described in the <a href="https://laravel.com/docs/configuration#maintenance-mode">Laravel documentation</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Enable Maintenance Mode</button>
                </div>
            </form>
        </div>
    </div>
</div>
