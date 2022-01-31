<div class="modal fade" data-backdrop="static" id="disableEnterpriseHostModal{{ $host->id }}" tabindex="-1"
     role="dialog" aria-labelledby="disableEnterpriseHostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="disableEnterpriseHostModalLabel{{ $host->id }}">{{ __('Disable Enterprise Host') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/hosts/{{ $host->id }}/disable" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-danger">Disable</strong> this Enterprise Host?</h3>
                    <p class="text-muted">
                        This will disable processing of inbound and outbound messages for this host until re-enabled.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Disable Host</button>
                </div>
            </form>
        </div>
    </div>
</div>
