<div class="modal fade" data-backdrop="static" id="credentialsEnterpriseHostModal{{ $host->id }}" tabindex="-1"
     role="dialog" aria-labelledby="credentialsEnterpriseHostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-dark">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="credentialsEnterpriseHostModalLabel{{ $host->id }}">{{ __('Credentials for Enterprise Host') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="text-dark col-4 text-center">senderID</dt>
                    <dd class="col-8 text-left"><code
                            class="text-muted bg-light border-light">{{ $host->senderID }}</code></dd>

                    <dt class="text-dark col-4 text-center">securityCode</dt>
                    <dd class="col-8 text-left"><code
                            class="text-muted bg-light border-light">{{ decrypt( $host->securityCode) }}</code></dd>
                </dl>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
