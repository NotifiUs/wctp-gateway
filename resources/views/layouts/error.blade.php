@if( $errors->any() )
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">

        </button>

        @foreach( $errors->all() as $error )
            <i class="fas fa-flag"></i> {{ $error }}<br>
        @endforeach
    </div>
@endif
