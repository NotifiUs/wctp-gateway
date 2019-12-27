<div class="modal fade" data-backdrop="static" id="verifyCarrierModal" tabindex="-1" role="dialog" aria-labelledby="verifyCarrierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-primary">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyCarrierModalLabel">{{ __('Verify Carrier') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/carriers/verify" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}


                    <ul class="nav nav-pills mb-3 nav-justified text-center" id="carriers-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-twilio-tab" data-toggle="pill" href="#pills-twilio" role="tab" aria-controls="pills-twilio" aria-selected="true">
                                <img style="height:1.75rem;"  src="/images/twilio-badge.png" title="{{ __('Powered by Twilio') }}" alt="{{ __('Powered by Twilio') }}">
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-thinq-tab" data-toggle="pill" href="#pills-thinq" role="tab" aria-controls="pills-thinq" aria-selected="false">
                                <img style="height:1.75rem;" src="/images/thinq-badge.svg" title="{{ __('Powered by ThinQ') }}" alt="{{ __('Powered by ThinQ') }}">
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-twilio" role="tabpanel" aria-labelledby="pills-twilio-tab">
                            <div class="col-12 mx-0 px-0">
                                <div class="card mb-4">
                                    <div class="card-body my-0">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">{{ __('Account SID') }}</label>
                                            <input type="text" name="twilio_account_sid" class="form-control">
                                            <small class="form-text text-muted">
                                                We recommend using a dedicated Twilio sub-account off of your master.
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">{{ __('Auth Token') }}</label>
                                            <input type="password" name="twilio_auth_token" class="form-control">
                                            <small class="form-text text-muted">
                                                The Auth Token associated with the Account SID above.
                                            </small>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-thinq" role="tabpanel" aria-labelledby="pills-thinq-tab">
                            <div class="col-12 mx-0 px-0">
                                <div class="card mb-4">
                                    <div class="card-body my-0">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">{{ __('Account ID') }}</label>
                                            <input type="text" name="thinq_account_id" class="form-control">
                                            <small class="form-text text-muted">
                                                Found in the <a target="_blank" href="https://i.thinq.com/#/user_profile">ThinQ User Profile</a> section
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">{{ __('API User') }}</label>
                                            <input type="text"  name="thinq_api_username" class="form-control">
                                            <small class="form-text text-muted">
                                                Found in the <a target="_blank" href="https://i.thinq.com/#/apiTokens">ThinQ API Tokens</a> section
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">{{ __('API Token') }}</label>
                                            <input type="password"  name="thinq_api_token" class="form-control">
                                            <small class="form-text text-muted">
                                                Found in the <a target="_blank" href="https://i.thinq.com/#/apiTokens">ThinQ API Tokens</a> section
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" role="button" class="btn btn-primary">Verify Carrier</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function(){
    $('#carriers-tab').on('hide.bs.tab', function (e) {
       $('#pills-tabContent :input').val('');
    })
});
</script>
@endpush
