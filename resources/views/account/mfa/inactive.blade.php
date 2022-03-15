<h5 class="text-muted">
    Enter the key or scan the barcode in your authenticator app:
</h5>
<p class="lead">
    <strong class="fw-bold">MFA Key</strong> &middot; <small>{{ $mfa_shared }}</small>
</p>
<img class="img-fluid img-thumbnail mb-4" src="{{ $image }}">


<h5 class="text-muted">
    Enter a code from the app to enable MFA:
</h5>
<form class="text-justify" method="POST" action="/account/mfa">
    @csrf
    <input type="hidden" name="mfa_shared" value="{{ $mfa_shared }}">

    <div class="form-group row">
        <label for="mfa_code" class="col-form-label text-md-right">{{ __('MFA Code') }}</label>
    </div>

    <div class="form-group row">

        <div class="col-md-6 mx-auto">
            <input id="mfa_code" type="text" class="form-control @error('mfa_code') is-invalid @enderror"
                   name="mfa_code" value="{{ old('mfa_code') }}" required autofocus>

            @error('mfa_code')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group row mb-0">
        <button type="submit" class="btn btn-primary col-md-6 mx-auto fw-bold">
            {{ __('Enable Multi-factor authentication') }}
        </button>
    </div>
</form>

