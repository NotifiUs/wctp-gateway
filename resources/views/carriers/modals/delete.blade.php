<div class="modal fade" data-backdrop="static" id="deleteCarrierModal{{ $carrier->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteCarrierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCarrierModalLabel{{ $carrier->id }}">{{ __('Delete Carrier') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/hosts/{{ $carrier->id }}/delete" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3>Are you sure you want to <strong class="text-danger">delete</strong> this Carrier?</h3>
                    <p class="text-muted">
                        The host will be permanently deleted and you won't be able to retrieve any of the settings!
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Host (forever)</button>
                </div>
            </form>
        </div>
    </div>
</div>
