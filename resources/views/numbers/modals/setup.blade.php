<div class="modal fade" data-backdrop="static" id="setupPhoneNumberModal{{ $number['identifier'] }}" tabindex="-1" role="dialog" aria-labelledby="setupPhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header">
                <h5 class="modal-title" id="setupPhoneNumberModalLabel{{ $number['identifier'] }}">{{ __('Disable Phone Number') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/numbers/{{ $number['id'] }}/setup" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3>Are you sure you want to <strong class="text-danger">setup</strong> this Phone Number?</h3>
                    <p class="text-muted">
                        This will remove and re-configure the SMS settings of this number.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Setup Phone Number (Destructive)</button>
                </div>
            </form>
        </div>
    </div>
</div>
