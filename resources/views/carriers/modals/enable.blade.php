<div class="modal fade" data-backdrop="static" id="enableCarrierModal{{ $carrier->id }}" tabindex="-1" role="dialog" aria-labelledby="enableCarrierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-success">
            <div class="modal-header">
                <h5 class="modal-title" id="enableCarrierModalLabel{{ $carrier->id }}">{{ __('Enable Carrier') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/carriers/{{ $carrier->id }}/enable" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3>Are you sure you want to <strong class="text-success">enable</strong> this Carrier?</h3>
                    <p class="text-muted">
                        This will enable processing of inbound and outbound messages for this carrier.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Enable Carrier</button>
                </div>
            </form>
        </div>
    </div>
</div>
