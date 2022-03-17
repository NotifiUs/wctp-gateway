

<div class="tab-pane fade show active" id="pills-twilio" role="tabpanel"
     aria-labelledby="pills-twilio-tab">
    <form method="POST" action="/carriers/verify" role="form">
        {{ csrf_field() }}
        <div class="col-12 mx-0 px-0">
            <div class="card mb-4">
                <div class="card-body my-0">
                    <input type="hidden" name="carrier_api" value="twilio">
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Account SID') }}</label>
                        <input type="text" name="twilio_account_sid" class="form-control">
                        <small class="form-text text-muted">
                            We recommend using a dedicated Twilio sub-account off of your master.
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Auth Token') }}</label>
                        <input type="password" name="twilio_auth_token" class="form-control">
                        <small class="form-text text-muted">
                            The Auth Token associated with the Account SID above.
                        </small>
                    </div>

                </div>
            </div>
        </div>
        @include('carriers.modals.forms.form-submit-buttons')
    </form>
</div>


