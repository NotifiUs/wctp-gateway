<div class="modal fade" data-backdrop="static" id="setupPhoneNumberModal{{ $number['identifier'] }}" tabindex="-1" role="dialog" aria-labelledby="setupPhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header border-bottom-0">
                <!--
                <h5 class="modal-title" id="setupPhoneNumberModalLabel{{ $number['identifier'] }}">{{ __('Disable Phone Number') }}</h5>
                -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/numbers/{{ $number['id'] }}/setup" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3>Are you sure you want to <strong class="text-danger">setup</strong> this Phone Number?</h3>
                    <p class="text-muted">
                        @php
                            $numberCarrier = \App\Carrier::find( $number['carrier_id'] );
                        @endphp
                        @if($numberCarrier->api == "twilio" )
                            This will remove any existing SMS settings and re-configure the SMS webhooks of this number to route inbound messages to our application.
                            No voice settings are changed or updated.
                        @else
                            This will add this system's IP address to ThinQ whitelist and setup a SMS routing profile for inbound messages to route to our application.
                            No existing IP or Routing profiles will be removed. No voice settings are changed or updated.
                        @endif

                    </p>
                    @if($numberCarrier->api == "twilio" )
                        <div class="alert alert-secondary">
                            <small><strong>Twilio</strong> numbers voice settings are not changed or modified.
                                We generally recommend you forward to a call center account in case a customer calls the number back instead of texting.</small>
                        </div>
                    @else
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
