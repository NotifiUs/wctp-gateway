<div class="modal fade" data-backdrop="static" id="setupPhoneNumberModal{{ $number['identifier'] }}" tabindex="-1" role="dialog" aria-labelledby="setupPhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header border-bottom-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/numbers/{{ $number['id'] }}/setup" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-danger">Setup</strong> this Phone Number?</h3>

                    @php
                        $numberCarrier = \App\Carrier::find( $number['carrier_id'] );
                    @endphp
                    @if(isset($numberCarrier) && $numberCarrier->api == "twilio" )
                        <p class="text-muted">
                            This will remove any existing SMS settings and re-configure the SMS webhooks of this number to route inbound messages to our application.
                            Voice settings not changed.
                        </p>
                        <div class="alert alert-secondary">
                            <small><strong>Twilio</strong> number voice settings do not change.
                                We generally recommend you forward to a call center account in case a customer calls the number back instead of texting.</small>
                        </div>
                    @elseif(isset($numberCarrier) && $numberCarrier->api == "thinq")
                        <p class="text-muted">
                            This will add this system's IP address to ThinQ whitelist and setup a SMS routing profile for inbound messages to route to our application.
                            No existing IPs removed. If a Routing Profile matches the number, update with the new webhook url.
                            e911 and CNAM selections reset their values - you will need to manually re-assign them.
                        </p>
                        <div class="alert alert-secondary">
                            <small><strong>ThinQ</strong> numbers will show as unconfigured in your ThinQ portal until you assign a default routing profile for voice.
                                We generally recommend you forward to a call center account in case a customer calls the number back instead of texting.</small>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Overwrite SMS Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
