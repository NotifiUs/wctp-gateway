<div class="modal fade" data-backdrop="static" id="enableCarrierModal{{ $carrier->id }}" tabindex="-1" role="dialog"
     aria-labelledby="enableCarrierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-success">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <form method="POST" action="/carriers/{{ $carrier->id }}/enable" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-success">Enable</strong> this carrier?</h3>
                    <p class="text-muted">
                        This will enable processing of inbound and outbound messages for this carrier.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Enable Carrier</button>
                </div>
            </form>
        </div>
    </div>
</div>
