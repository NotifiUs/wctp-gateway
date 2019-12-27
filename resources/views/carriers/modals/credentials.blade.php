<div class="modal fade" data-backdrop="static" id="credentialsCarrierModal{{ $carrier->id }}" tabindex="-1" role="dialog" aria-labelledby="credentialsCarrierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="credentialsCarrierModalLabel{{ $carrier->id }}">{{ __('Credentials for Carrier') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="text-dark col-4 text-center">
                        @if( $carrier->api == 'twilio' )
                            AccountID
                        @else
                            Account
                        @endif
                    </dt>
                    <dd class="col-8 text-left"><code class="text-muted bg-light border-light">{{ $carrier->account }}</code></dd>

                    <dt class="text-dark col-4 text-center">
                        @if( $carrier->api == 'twilio' )
                            AuthToken
                        @else
                            Secret
                        @endif
                    </dt>
                    <dd class="col-8 text-left"><code class="text-muted bg-light border-light">{{ decrypt( $carrier->secret) }}</code></dd>
                </dl>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
