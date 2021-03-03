<h5 class="text-muted-light mt-2">{{ __('Account Actions') }}</h5>
<ul class="list-group rounded">
    <a class="list-group-item list-group-item-action" href="#" data-toggle="modal" data-target="#editNameModal">
        <i class="fas fa-user text-primary"></i> {{ __('Change your name') }}
    </a>
    @if( ! $user->email_verified_at )
        <a class="list-group-item list-group-item-action" href="/account/verify-email">
            <i class="fas fa-exclamation-circle text-primary"></i> {{ __('Verify email address') }}
        </a>
    @else
        <a class="list-group-item list-group-item-action" href="#" data-toggle="modal" data-target="#editEmailModal">
            <i class="fas fa-at text-primary"></i> {{ __('Update email settings') }}
        </a>
    @endif
    <a class="list-group-item list-group-item-action" href="#" data-toggle="modal" data-target="#editTimezoneModal">
        <i class="fas fa-clock text-primary"></i> {{ __('Select timezone') }}
    </a>
    <a class="list-group-item list-group-item-action" href="#" data-toggle="modal" data-target="#editPasswordModal">
        <i class="fas fa-asterisk text-primary"></i> {{ __('Change password') }}
    </a>
    <a class="list-group-item list-group-item-action" href="/account/mfa">
        <i class="fas fa-shield-alt text-primary"></i> {{ __('Multi-factor authentication') }}
    </a>
</ul>
