<div class="modal fade" data-backdrop="static" id="deletePhoneNumberModal{{ $number['identifier'] }}" tabindex="-1" role="dialog" aria-labelledby="deletePhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePhoneNumberModalLabel{{ $number['identifier'] }}">{{ __('Delete Phone Number') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/numbers/{{ $number['id'] }}/delete" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3>Are you sure you want to <strong class="text-danger">release</strong> this Phone Number?</h3>
                    <p class="text-muted">
                        The number will no longer be used to send and receive messages and return to the Available list.
                        The number is not removed from your carrier's account.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Release Phone Number</button>
                </div>
            </form>
        </div>
    </div>
</div>
