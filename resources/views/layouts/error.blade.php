@if( $errors->any() )
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>

        @foreach( $errors->all() as $error )
            <i class="fas fa-flag"></i> {{ $error }}<br>
        @endforeach
    </div>
@endif
