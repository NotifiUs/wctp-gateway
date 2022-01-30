<div class="modal fade" data-backdrop="static" id="editEnterpriseHostModal{{ $host->id }}" tabindex="-1" role="dialog" aria-labelledby="editEnterpriseHostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-info">
            <div class="modal-header">
                <h5 class="modal-title" id="editEnterpriseHostModalLabel{{ $host->id }}">{{ __('Edit Enterprise Host') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/hosts/{{ $host->id }}/edit" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" required name="name" class="form-control" value="{{ $host->name }}">
                        <small class="form-text text-muted">A friendly name to refer to this host.</small>
                    </div>
                    <div class="form-group">
                        <label>Url</label>
                        <input type="text" required name="url" class="form-control" value="{{ $host->url }}">
                        <small class="form-text text-muted">
                            The WCTP endpoint for this host. Must use SSL/TLS (https://).<br>
                            e.g. <i>https://example.com/wctp</i>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" role="button" class="btn btn-info">Update Host</button>
                </div>
            </form>
        </div>
    </div>
</div>
