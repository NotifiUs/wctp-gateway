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
    </h5>


    @if( $events->count() )
        <!--
        <div class="row col">
            {{ $events->links() }}
        </div>
        -->
    <div class="card py-0 my-0">
        <div class="card-body p-0 m-0">

            <div class="table-responsive text-left">
                <table class="table table-striped text-small table-hover m-0">
                    <thead>
                    <tr>
                        <th class="font-weight-bold text-muted-light" style="max-width:20%;">{{ __('Timestamp') }}</th>
                        <th class="font-weight-bold text-muted-light" style="max-width:20%;">{{ __('Source') }}</th>
                        <th class="font-weight-bold text-muted-light" style="max-width:40%;">{{ __('Event') }}</th>
                        <th class="font-weight-bold text-muted-light" style="max-width:20%;">{{ __('User') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $events as $event )
                        <tr>
                            <td><small class="text-muted">{{ $event->created_at->timezone( Auth::user()->timezone )->format('m/d/Y g:i:s A T') }}</small></td>
                            <td class="text-muted-light text-small">
                                {{ $event->source }}
                            </td>
                            <td class="text-muted text-small text-truncate"  title="{{ json_encode( json_decode($event->details, true ), JSON_PRETTY_PRINT) }}">
                                <small>
                                    <a href="#" data-toggle="modal" data-target="#infoPhoneNumberModal{{ $event->id}}">
                                        <i class="fas fa-search text-muted-light"></i>
                                    </a>
                                </small>
                                {{ $event->event }}

                            </td>
                            <td class="text-muted-light text-small text-truncate">
                                @if( $event->user_id )
                                @php
                                    $user = \App\User::find( $event->user_id );
                                    if( is_null( $user) )
                                    {
                                        echo "&mdash;";
                                    }
                                    else
                                    {
                                        echo $user->name;
                                    }
                                @endphp
                                @else
                                &mdash;
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        <div class="row col my-4">
            {{ $events->links() }}
        </div>
    @else
        <div class="alert alert-primary py-4" role="alert">
            <i class="fas fa-exclamation-circle"></i> There are no events to display!
        </div>
    @endif

    @foreach( $events as $event )
        @include('events.modals.details')
    @endforeach

@endsection
