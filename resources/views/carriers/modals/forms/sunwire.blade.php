
<div class="tab-pane fade" id="pills-sunwire" role="tabpanel"
 aria-labelledby="pills-sunwire-tab">
    <form method="POST" action="/carriers/verify" role="form">
        {{ csrf_field() }}
        <div class="col-12 mx-0 px-0">
            <div class="card mb-4">
                <div class="card-body my-0">
                    <input type="hidden" name="carrier_api" value="sunwire">
                    <div class="form-group">
                        <label class="fw-bold text-muted">API Configuration Information</label>
                        <p class="">
                            To use <a href="https://www.sunwire.ca/">Sunwire</a>, you must contact them directly to provision an account and submit your IP address for whitelisting.
                            You will also receive a phone number or short code to use for sending, which you should manually add into the Numbers section.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @include('carriers.modals.forms.form-submit-buttons')
    </form>
</div>
