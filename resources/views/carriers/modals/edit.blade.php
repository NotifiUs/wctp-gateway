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
                        <small class="form-text text-muted">A friendly name to refer to this carrier instance.</small>
                    </div>
                    <div class="form-group">
                        <label>Priority</label>
                        <input type="text" required name="priority" class="form-control" value="{{ $carrier->priority }}">
                        <small class="form-text text-muted">
                            The general system priority for this carrier.
                            Like DNS MX records, lower values are higher priority. (10, 20, 30, etc.)
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" role="button" class="btn btn-info">Update Carrier</button>
                </div>
            </form>
        </div>
    </div>
</div>
