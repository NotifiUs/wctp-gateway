<div class="modal fade" data-backdrop="static" id="deleteEnterpriseHostModal{{ $host->id }}" tabindex="-1" role="dialog"
     aria-labelledby="deleteEnterpriseHostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="deleteEnterpriseHostModalLabel{{ $host->id }}">{{ __('Delete Enterprise Host') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <form method="POST" action="/hosts/{{ $host->id }}/delete" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-danger">Delete</strong> this Enterprise Host?</h3>
                    <p class="text-muted">
                        Permanently delete the host - you can't retrieve any of the settings!
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Host (forever)</button>
                </div>
            </form>
        </div>
    </div>
</div>
