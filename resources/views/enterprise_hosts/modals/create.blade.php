<div class="modal fade" data-backdrop="static" id="createEnterpriseHostModal" tabindex="-1" role="dialog" aria-labelledby="createEnterpriseHostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-primary">
            <div class="modal-header">
                <h5 class="modal-title" id="createEnterpriseHostModalLabel">{{ __('Add Enterprise Host') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/carriers" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" required name="name" class="form-control" value="{{ old('name') }}">
                        <small class="form-text text-muted">A friendly name to refer to this host.</small>
                    </div>
                    <div class="form-group">
                        <label>Url</label>
                        <input type="text" required name="url" class="form-control" value="{{ old('url') }}">
                        <small class="form-text text-muted">
                            The WCTP endpoint for this host. Must be SSL/TLS protected.<br>
                            e.g. <i>https://example.com/wctp</i>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Host</button>
                </div>
            </form>
        </div>
    </div>
</div>
