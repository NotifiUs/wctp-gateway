<div class="modal fade" data-backdrop="static" id="processMessageModal{{ $message->id}}" tabindex="-1" role="dialog" aria-labelledby="processMessageModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-orange shadow-sm">
            <div class="modal-header border-bottom-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/messages/process/{{ $message->id }}" method="POST">
            <div class="modal-body">
                <h3>Do you want to process this message again?</h3>
                {{ csrf_field() }}
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" role="button" class="btn btn-orange">Process Message</button>
            </div>
            </form>
        </div>
    </div>
</div>
