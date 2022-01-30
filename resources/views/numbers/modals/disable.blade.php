<div class="modal fade" data-backdrop="static" id="disablePhoneNumberModal{{ $number['identifier'] }}" tabindex="-1" role="dialog" aria-labelledby="disablePhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-orange">
            <div class="modal-header border-bottom-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/numbers/{{ $number['id'] }}/disable" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-orange">Disable</strong> this Phone Number?</h3>
                    <p class="text-muted">
                        This will disable processing of inbound and outbound messages for this number until re-enabled.
                    </p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-orange">Disable Phone Number</button>
                </div>
            </form>
        </div>
    </div>
</div>
