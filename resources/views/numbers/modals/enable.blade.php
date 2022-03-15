<div class="modal fade" data-backdrop="static" id="enablePhoneNumberModal{{ $number['identifier'] }}" tabindex="-1"
     role="dialog" aria-labelledby="enablePhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-success">
            <div class="modal-header border-bottom-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <form method="POST" action="/numbers/{{ $number['id'] }}/enable" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-success">Enable</strong> this Phone Number?</h3>
                    <p class="text-muted">
                        Allow number to send and receive messages.
                    </p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Enable Phone Number</button>
                </div>
            </form>
        </div>
    </div>
</div>
