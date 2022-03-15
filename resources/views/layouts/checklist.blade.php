@if( count( $checklist ) )
    <h5 class="text-muted-light mb-2">
        {{ __('System Warnings') }}
    </h5>
    <div class="card mb-2 h-75">
        <div class="card-body bg-striped text-left px-4 my-0">
            <div class="my-4 mx-auto">
                @foreach( $checklist as $item )
                    <p class="my-1 text-muted">
                        <i class="fas fa-times-circle text-orange"></i> <span>{!!  $item['description']  !!} <small
                                class="fw-bold"><a class="text-uppercase text-orange"
                                                            href="{{ $item['link'] }}">Fix</a></small></span>
                    </p>
                @endforeach
            </div>
        </div>
    </div>
@else
    <h5 class="text-muted-light mb-2">
        {{ __('System Warnings') }}
    </h5>
    <div class="card my-0 bg-light">
        <div class="card-body text-center px-4 my-4">
            <div class="py-4 mx-auto">
                <i class="fas fa-3x fa-check-circle text-muted-light mx-auto"></i>
                <h1 class="my-3 text-dark mx-auto">
                    {{ __('All Clear') }}
                </h1>
            </div>

        </div>
    </div>
@endif
