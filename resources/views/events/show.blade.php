@extends('layouts.app')
@section('title', __('Events'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">
        {{ __('Event Log') }}
        @if( request('page') )
        &middot; Page {{ request('page') }}
        @endif
        @if(isset($sourceFilter))
            <span class="font-weight-normal inline mx-2 pull-right badge badge-secondary">
                {{ ucwords($sourceFilter) }}
            </span>
        @endif
    </h5>

    <h5 class="text-sm inline">
        @if(isset($sourceList))
            <form method="get">
                <div class="input-group input-group-sm mb-2 w-25">
                    <select class="form-control bg-white form-control-sm" name="source">
                        <option value="">Any Source</option>
                        @foreach($sourceList as $source)
                            @if(isset($sourceList) && $sourceList === $source)
                                <option selected="selected" value="{{ $source }}">{{ucwords($source)}}</option>
                            @else
                                <option value="{{ $source }}">{{ucwords($source)}}</option>
                            @endif

                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                        <i class="fas fa-filter"></i>
                    </button>

                </div>
            </form>
        @endif
    </h5>

    <div class="card py-0 my-0 mx-0">
        <div class="card-body p-0 m-0">

            <div class="table-responsive text-left">
                <table class="table table-striped table-hover m-0">
                    <thead>
                    <tr class="text-center">
                        <th class="fw-bold text-muted-light w-25">{{ __('Timestamp') }}</th>
                        <th class="fw-bold text-muted-light w-25">{{ __('User') }}</th>
                        <th class="fw-bold text-muted-light w-25">{{ __('Source') }}</th>
                        <th class="fw-bold text-muted-light text-left w-25">{{ __('Event') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if( $events->count() )
                        @foreach( $events as $event )
                            <tr class="align-text-bottom">
                                <td class="text-muted text-small fw-bold text-nowrap">
                                    {{ $event->created_at->timezone( Auth::user()->timezone )->format('m/d/Y g:i:s A T') }}
                                </td>
                                <td class="text-muted-light text-truncate text-center">
                                    @if( $event->user )
                                    {{ $event->user->name }}
                                    @else
                                    &mdash;
                                    @endif
                                </td>
                                <td class=" align-text-bottom">
                                    <span class="text-small text-muted-light">{{ $event->source }}</span>
                                </td>
                                <td class="text-truncate text-muted text-small">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#detailsEventsModal{{ $event->id}}"><i
                                            class="fas fa-search text-muted-light"></i></a>
                                    {{ $event->event }}
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-muted text-center text-small fw-bold">
                                <i class="fas fa-ban text-muted-light"></i> No events found
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row col my-4">
        {{ $events->links() }}
    </div>

    @foreach( $events as $event )
        @include('events.modals.details')
    @endforeach

@endsection
