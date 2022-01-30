<div class="modal fade" data-backdrop="static" id="deletePhoneNumberModal{{ $number['identifier'] }}" tabindex="-1" role="dialog" aria-labelledby="deletePhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header border-bottom-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/numbers/{{ $number['id'] }}/delete" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-danger">Release</strong> this Phone Number?</h3>
                    <p class="text-muted">
                        Return the number to the Available list and stop using for WCTP.
                    </p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Release Phone Number</button>
                </div>
            </form>
        </div>
    </div>
</div>
