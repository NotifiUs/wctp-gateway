<div class="modal fade" data-backdrop="static" id="credentialsCarrierModal{{ $carrier->id }}" tabindex="-1"
     role="dialog" aria-labelledby="credentialsCarrierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-dark">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="credentialsCarrierModalLabel{{ $carrier->id }}">{{ __('Carrier Credentials') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <dl class="row">

                    @if( $carrier->api == 'twilio' )
                        <dt class="text-dark col-4 text-center">AccountID</dt>
                        <dd class="col-8 text-left"><code
                                class="text-dark bg-light border-light">{{ $carrier->twilio_account_sid}}</code></dd>

                        <dt class="text-dark col-4 text-center">AuthToken</dt>
                        <dd class="col-8 text-left"><code
                                class="text-dark bg-light border-light">{{ decrypt( $carrier->twilio_auth_token) }}</code>
                        </dd>

                    @else
                        <dt class="text-dark col-4 text-center">Account ID</dt>
                        <dd class="col-8 text-left"><code
                                class="text-dark bg-light border-light">{{ $carrier->thinq_account_id }}</code></dd>

                        <dt class="text-dark col-4 text-center">API Username</dt>
                        <dd class="col-8 text-left"><code
                                class="text-dark bg-light border-light">{{ $carrier->thinq_api_username }}</code></dd>

                        <dt class="text-dark col-4 text-center">API Token</dt>
                        <dd class="col-8 text-left"><code
                                class="text-dark bg-light border-light">{{ decrypt(  $carrier->thinq_api_token) }}</code>
                        </dd>

                    @endif
                </dl>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
