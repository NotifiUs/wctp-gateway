@extends('layouts.app')
@section('title', __('Messages'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">
        @if( $filter ) {{ ucwords($filter) }} @endif {{ __('Messages') }}
        @if( request('page') )
        &middot; Page {{ request('page') }}
        @endif
        @if(isset($statusFilter))
            <span class="font-weight-normal inline mx-2 pull-right badge badge-secondary">
                {{ ucwords($statusFilter) }}
            </span>
        @endif
    </h5>

    <h5 class="pull-right text-sm inline">
        @if(isset($statusList))
            <form method="get">
                <div class="input-group input-group-sm mb-2 w-25">
                    <select class="form-control form-control-sm" name="status">
                        <option value="">Any Status</option>
                        @foreach($statusList as $status)
                            @if(isset($statusFilter) && $statusFilter === $status)
                                <option selected="selected" value="{{ $status }}">{{ucwords($status)}}</option>
                            @else
                                <option value="{{ $status }}">{{ucwords($status)}}</option>
                            @endif

                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-outline-secondary" type="submit">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
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
                        <th class="font-weight-bold text-muted-light">{{ __('Timestamp') }}</th>
                        <th class="font-weight-bold text-muted-light">{{ __('Direction') }}</th>
                        <th class="font-weight-bold text-muted-light">{{ __('From') }}</th>
                        <th class="font-weight-bold text-muted-light">{{ __('To') }}</th>
                        <th class="font-weight-bold text-muted-light text-left">{{ __('Status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if( $messages->count() )
                        @foreach( $messages as $message )
                            <tr class="align-text-bottom">
                                <td class="text-muted text-small text-nowrap">
                                    {{ $message->created_at->timezone( Auth::user()->timezone )->format('m/d/Y g:i:s A T') }}
                                </td>
                                <td class="text-small">
                                    @if($message->direction == 'outbound')
                                        <i class="fas fa-long-arrow-alt-up text-primary"></i> outbound
                                    @else
                                        <i class="fas fa-long-arrow-alt-down text-indigo"></i> inbound
                                    @endif
                                </td>
                                <td class=" text-truncate text-center">
                                    {{ $message->from }}
                                </td>
                                <td class=" align-text-bottom">
                                    {{ $message->to }}
                                </td>
                                <td class="text-truncate text-muted text-small text-right">
                                    @switch( $message->status )
                                        @case("sent")
                                        @case("DELIVRD")
                                        @case("delivered")
                                        <span class="badge badge-light border border-success shadow-sm px-2 py-1">
                                            @break
                                            @case("UNDELIV")
                                            @case("undelivered")
                                            @case("failed")
                                            @case("UNKNOWN")
                                            @case("DELETED")
                                            @case("EXPIRED")
                                            @case("REJECTD")
                                            <span class="badge badge-danger px-2 py-1">
                                            @break
                                                @default
                                            <span class="badge badge-light border border-secondary shadow-sm px-2 py-1">
                                    @endswitch

                                                {{ ucwords( $message->status ) }}
                                    </span>
                                    <a href="#" title="View message details" class="ml-2" data-toggle="modal"
                                       data-target="#detailsMessageModal{{ $message->id}}"><i
                                            class="fas fa-search text-muted-light"></i></a>
                                    <a href="#" title="Re-process message" class="ml-2" data-toggle="modal"
                                       data-target="#processMessageModal{{ $message->id}}"><i
                                            class="fas fa-recycle text-muted-light"></i></a>
                                    <a href="#" title="Mark message failed" class="ml-2" data-toggle="modal"
                                       data-target="#failMessageModal{{ $message->id}}"><i
                                            class="fas fa-ban text-muted-light"></i></a>
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-muted text-center text-small font-weight-bold">
                                <i class="fas fa-ban text-muted-light"></i> No messages found
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row col my-4">
        {{ $messages->links() }}
    </div>

    @foreach( $messages as $message )
        @include('messages.modals.details')
        @include('messages.modals.process')
        @include('messages.modals.fail')
    @endforeach

@endsection
