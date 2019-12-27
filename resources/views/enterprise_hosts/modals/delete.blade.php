<div class="modal fade" data-backdrop="static" id="deleteEnterpriseHostModal{{ $host->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteEnterpriseHostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEnterpriseHostModalLabel{{ $host->id }}">{{ __('Delete Enterprise Host') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/hosts/{{ $host->id }}/delete" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3>Are you sure you want to <strong class="text-danger">delete</strong> this Enterprise Host?</h3>
                    <p class="text-muted">
                        The host will be permanently deleted and you won't be able to retrieve any of the settings!
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Host (forever)</button>
                </div>
            </form>
        </div>
    </div>
</div>
