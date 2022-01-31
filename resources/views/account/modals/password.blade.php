<div class="modal fade" data-backdrop="static" id="editPasswordModal" tabindex="-1" role="dialog"
     aria-labelledby="editPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-info">
            <div class="modal-header">
                <h5 class="modal-title" id="editPasswordModalLabel">{{ __('Change your password') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/account/password" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" required name="current_password" class="form-control" value="">
                        <small class="form-text text-muted">Enter your current password.</small>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" required name="password" class="form-control" value="">
                        <small class="form-text text-muted">Enter a new password. Please use a <a
                                href="https://bitwarden.com/">password manager</a>.</small>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" required name="password_confirmation" class="form-control" value="">
                        <small class="form-text text-muted">Confirm your new password.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" role="button" class="btn btn-info">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
