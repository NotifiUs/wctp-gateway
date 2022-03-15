<div class="modal fade" data-backdrop="static" id="invalidPhoneNumberModal" tabindex="-1" role="dialog"
     aria-labelledby="invalidPhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header border-bottom-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <div class="modal-body">
                <h3>My number listed as <strong>Invalid</strong></h3>
                <p class="text-muted">
                    Please make sure to meet the following conditions:
                </p>
                <h5>ThinQ</h5>
                <ul>
                    <li>A ThinQ number not marked as <strong>provisioned</strong></li>
                </ul>

                <h5>Twilio</h5>
                <ul>
                    <li>The number belongs to a Twilio <a href="https://www.twilio.com/docs/sms/services">Messaging
                            Service</a></li>
                    <li>A <a href="https://www.twilio.com/docs/sms/services">Twilio Messaging Service</a> lacks a phone
                        numbers
                    </li>
                    <li>No SMS enabled on Twilio number <span class="text-muted">(rare)</span></li>
                </ul>


            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
