
<div class="tab-pane fade" id="pills-thinq" role="tabpanel" aria-labelledby="pills-thinq-tab">
    <form method="POST" action="/carriers/verify" role="form">
        {{ csrf_field() }}
        <div class="col-12 mx-0 px-0">
            <div class="card mb-4">
                <div class="card-body my-0">
                    <div class="form-group">
                        <input type="hidden" name="carrier_api" value="thinq">
                        <label class="fw-bold text-muted">{{ __('Account ID') }}</label>
                        <input type="text" name="thinq_account_id" class="form-control" value="{{ old('thinq_account_id') }}">
                        <small class="form-text text-muted">
                            Found in the <a target="_blank"
                                            href="https://i.thinq.com/#/user_profile">ThinQ User
                                Profile</a> section
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('API User') }}</label>
                        <input type="text" name="thinq_api_username" class="form-control" value="{{ old('thinq_api_username') }}">
                        <small class="form-text text-muted">
                            Found in the <a target="_blank" href="https://i.thinq.com/#/apiTokens">ThinQ
                                API Tokens</a> section
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('API Token') }}</label>
                        <input type="password" name="thinq_api_token" class="form-control" value="{{ old('thinq_api_token') }}">
                        <small class="form-text text-muted">
                            Found in the <a target="_blank" href="https://i.thinq.com/#/apiTokens">ThinQ
                                API Tokens</a> section
                        </small>
                    </div>
                </div>
            </div>
        </div>
        @include('carriers.modals.forms.form-submit-buttons')
    </form>
</div>

