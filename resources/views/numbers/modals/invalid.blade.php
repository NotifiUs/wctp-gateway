<div class="modal fade" data-backdrop="static" id="invalidPhoneNumberModal" tabindex="-1" role="dialog" aria-labelledby="invalidPhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header border-bottom-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h3>Why is my number listed as <strong>Invalid</strong>?</h3>
                <p class="text-muted">
                    Numbers are marked as invalid for any of the following reasons:
                </p>
                <ul>
                    <li>A Twilio number is not SMS enabled <span class="text-muted">(rare)</span>.</li>
                    <li>The number is part of a Twilio <a href="https://www.twilio.com/docs/sms/services">Messaging Service</a>.</li>
                    <li>A Twilio Messaging Service does not have any associated phone numbers or short codes.</li>
                    <li>A ThinQ number is not marked as <strong>provisioned</strong>.</li>
                </ul>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
