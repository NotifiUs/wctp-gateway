<div class="modal fade" data-backdrop="static" id="deleteCarrierModal{{ $carrier->id }}" tabindex="-1" role="dialog"
     aria-labelledby="deleteCarrierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/carriers/{{ $carrier->id }}/delete" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-danger">Delete</strong> this carrier?</h3>
                    <p class="text-muted">
                        Permanently delete the carrier - you can't retrieve any of the settings.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Carrier (forever)</button>
                </div>
            </form>
        </div>
    </div>
</div>
