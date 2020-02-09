<form method="POST" action="/account/mfa/remove">
    @csrf
    <button type="submit" role="button" class="btn btn-danger">
        Remove Multi-Factor Authentication
    </button>
</form>
