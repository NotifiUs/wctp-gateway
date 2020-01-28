<div class="modal fade" data-backdrop="static" id="editEmailModal" tabindex="-1" role="dialog" aria-labelledby="editEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-info">
            <div class="modal-header">
                <h5 class="modal-title" id="editEmailModalLabel">{{ __('Edit your email') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/account/email" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Name</label>
                        <input type="email" required name="email" class="form-control" value="{{ Auth::user()->email }}">
                        <small class="form-text text-muted">Your email address for password resets and notifications.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" role="button" class="btn btn-info">Update Email</button>
                </div>
            </form>
        </div>
    </div>
</div>
