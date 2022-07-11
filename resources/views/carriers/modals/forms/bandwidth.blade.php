

<div class="tab-pane fade" id="pills-bandwidth" role="tabpanel"
     aria-labelledby="pills-bandwidth-tab">
    <form method="POST" action="/carriers/verify" role="form">
        {{ csrf_field() }}
        <div class="col-12 mx-0 px-0">
            <div class="card mb-4">
                <div class="card-body my-0">
                    <input type="hidden" name="carrier_api" value="bandwidth">
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Bandwidth API Username') }}</label>
                        <input type="text" name="bandwidth_api_username" class="form-control" value="{{ old('bandwidth_api_username') }}">
                        <small class="form-text text-muted">
                            Bandwidth recommends creating an <a href="https://dev.bandwidth.com/docs/account/credentials/#api-user-credentials" target="_blank">API-only user</a> for this purpose.
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Bandwidth API Password') }}</label>
                        <input type="password" name="bandwidth_api_password" class="form-control" value="{{ old('bandwidth_api_password') }}">
                        <small class="form-text text-muted">
                            Use a password manager when creating the password in Bandwidth's portal.
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Bandwidth Account ID') }}</label>
                        <input type="text" name="bandwidth_api_account_id" class="form-control" value="{{ old('bandwidth_api_account_id') }}">
                        <small class="form-text text-muted">
                            Information on <a href="https://dev.bandwidth.com/docs/account/credentials/#credentials-snapshot" target="_blank">Bandwidth's Account ID</a>
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Bandwidth Application ID') }}</label>
                        <input type="text" name="bandwidth_api_application_id" class="form-control" value="{{ old('bandwidth_api_application_id') }}">
                        <small class="form-text text-muted">
                            Created from your Bandwidth portal.
                        </small>
                    </div>

                </div>
            </div>
        </div>
        @include('carriers.modals.forms.form-submit-buttons')
    </form>
</div>


