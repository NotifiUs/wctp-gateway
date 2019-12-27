<div class="modal fade" data-backdrop="static" id="editCarrierModal{{ $carrier->id }}" tabindex="-1" role="dialog" aria-labelledby="editCarrierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-info">
            <div class="modal-header">
                <h5 class="modal-title" id="editCarrierModalLabel{{ $carrier->id }}">{{ __('Edit Carrier') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/carriers/{{ $carrier->id }}/edit" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" required name="name" class="form-control" value="{{ $carrier->name }}">
                        <small class="form-text text-muted">A friendly name to refer to this host.</small>
                    </div>
                    <div class="form-group">
                        <label>Url</label>
                        <input type="text" required name="url" class="form-control" value="{{ $carrier->url }}">
                        <small class="form-text text-muted">
                            The WCTP endpoint for this host. Must be SSL/TLS protected.<br>
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
