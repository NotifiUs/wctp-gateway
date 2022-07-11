<div class="tab-pane fade" id="pills-webhook" role="tabpanel"
 aria-labelledby="pills-webhook-tab">
    <form method="POST" action="/carriers/verify" role="form">
        {{ csrf_field() }}
        <div class="col-12 mx-0 px-0">
            <div class="card mb-4">
                <div class="card-body my-0">
                    <p>
                        The webhook carrier will perform an HTTP POST with <code>application/json</code> of the message.
                        A valid SSL/TLS certificate is required. Authentication is provided through the common HTTP Basic Auth mechanism over TLS.
                    </p>

                    <input type="hidden" name="carrier_api" value="webhook">

                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Webhook Host') }}</label>
                        <input type="text" name="webhook_host" class="form-control" value="{{ old('webhook_host') }}">
                        <small class="form-text text-muted">
                            The base URL for the webhook host to receive outbound messages (i.e., https://example.com).
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Webhook Endpoint') }}</label>
                        <input type="text" name="webhook_endpoint" class="form-control" value="{{ old('webhook_endpoint') }}">
                        <small class="form-text text-muted">
                            The URL path to the webhook endpoint (i.e., /webhook)
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Webhook Basic Auth User') }}</label>
                        <input type="text" name="webhook_username" class="form-control" value="{{ old('webhook_username') }}">
                        <small class="form-text text-muted">
                            The HTTP Basic Auth username for webhook authentication
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Webhook Basic Auth Pass') }}</label>
                        <input type="password" name="webhook_password" class="form-control" value="{{ old('webhook_password') }}">
                        <small class="form-text text-muted">
                            The HTTP Basic Auth password for webhook authentication
                        </small>
                    </div>

                </div>
            </div>
        </div>
        @include('carriers.modals.forms.form-submit-buttons')
    </form>
</div>

