<div class="modal fade" data-backdrop="static" id="failMessageModal{{ $message->id}}" tabindex="-1" role="dialog"
     aria-labelledby="failMessageModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-danger shadow-sm">
            <div class="modal-header border-bottom-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <form action="/messages/fail/{{ $message->id }}" method="POST">
                <div class="modal-body">
                    <h3>Fail this message?</h3>
                    {{ csrf_field() }}
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" role="button" class="btn btn-danger">Fail Message</button>
                </div>
            </form>
        </div>
    </div>
</div>
