@if( session('status') )
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">

        </button>
        <i class="fas fa-check"></i> {!! session('status')  !!}
    </div>
@endif
