<div class="modal fade" data-backdrop="static" id="deleteUserModal{{ $user->id }}" tabindex="-1" role="dialog"
     aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/system/user/delete/{{ $user->id }}" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-danger">Delete</strong> this user?</h3>
                    <p class="text-muted">
                        You can't retrieve any of the settings.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete User (forever)</button>
                </div>
            </form>
        </div>
    </div>
</div>
