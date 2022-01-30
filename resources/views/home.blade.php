@extends('layouts.app')
@section('title', 'Dashboard')
@push('css')
@endpush
@push('scripts')
@endpush
@push('modals')
    @include('setup-instructions')
@endpush

@section('content')
    <h5 class="text-muted-light mt-2 mt-md-0">
        {{ __('Quick Glance') }}
        <small class="text-small float-right text-muted">
            <a href="/home" title="{{ $activityPeriod->timezone(Auth::user()->timezone)->format("m/d/Y g:i:sA T") }} &mdash; {{ $activityPeriod->copy()->addHours(24)->format("m/d/Y g:i:sA T") }}">
                <i class="fas fa-sync text-small"></i> Refresh
            </a>
        </small>
    </h5>
    <div class="row justify-content-center mb-2">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center my-0">
                    @if( $queue == 1 )
                        <i class="fas fa-3x fa-check-circle text-success"></i>
                        <h5 class="text-muted mt-2 mb-0">{{ __('Message Queue') }}</h5>
                        <a href="/queue" target="_blank" class="text-success">{{ __('Running') }}</a>
                    @else
                        <i class="fas fa-3x fa-times text-danger"></i>
                        <h5 class="text-muted mt-2 mb-0">{{ __('Message Queue') }}</h5>
                        <a href="/queue" target="_blank" class="text-danger">{{ __('Inactive') }}</a>
                    @endif
                </div>
            </div>
        </div>
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body text-center my-0">
                        <i class="fas fa-3x fa-chevron-circle-up text-primary"></i>
                        <h5 class="text-muted mt-2 mb-0">{{ $outboundCount }}</h5>
                        <a href="/messages/outbound" class="text-primary">Outbound Messages</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body text-center my-0">
                        <i class="fas fa-3x fa-chevron-circle-down text-primary"></i>
                        <h5 class="text-muted mt-2 mb-0">{{ $inboundCount }}</h5>
                        <a href="/messages/inbound" class="text-primary">Inbound Messages</a>
                    </div>
                </div>
            </div>

        @if(count($checklist) !== 0 )

            <div class="col-md-12 my-2">
                @include('layouts.checklist')
            </div>

        @endif
    </div>


    <h5 class="text-muted-light mt-2">{{ __('WCTP Gateway') }}
    </h5>
    <div class="row justify-content-center mb-2">

        <div class="col">
            <div class="card mb-2">
                <div class="card-body my-0 border-bottom">
                    <dl class="row">

                        <dt class="col-sm-12 col-md-4">
                            {{ __('WCTP Actor (Acting as)') }}
                        </dt>
                        <dd class="col-sm-12 col-md-8 text-muted">
                            {{ __('Carrier Gateway') }}
                        </dd>

                        <dt class="col-sm-12 col-md-4">
                            {{ __('Version Support') }}
                        </dt>
                        <dd class="col-sm-12 col-md-8 text-muted">
                            <a href="http://www.wctp.org/release/wctp-v1r3_update1.pdf">
                                WCTP v1r3 Update 1 <small><i class="fas fa-external-link-alt"></i></small>
                            </a>
                        </dd>

                        <dt class="col-sm-12 col-md-4">
                            {{ __('Carrier Gateway Endpoint') }}
                        </dt>
                        <dd class="col-sm-12 col-md-8 text-muted">
                            https://<span class="font-weight-bold">{{ str_replace('https://', '', secure_url('/wctp')) }}</span>
                        </dd>
                        <dt class="col-sm-12 col-md-4">
                            {{ __('Network Ports') }}
                        </dt>
                        <dd class="col-sm-12 col-md-8 text-muted">
                            <strong>443</strong> &frasl; <span class="text-muted-light">TCP</span>
                        </dd>

                        <dt class="col-sm-12 col-md-4">
                            {{ __('Security Information') }}
                        </dt>
                        <dd class="col-sm-12 col-md-8 text-muted">
                            <span class="text-success"><i class="fas fa-lock"></i> <code class="text-success font-weight-bold">{{ __('SSL/TLS') }}</code> {{__( 'required')}}</span>
                            <br>
                            <span class="text-primary"><i class="fas fa-shield-alt"></i> <code class="text-primary font-weight-bold">{{ __('securityCode') }}</code> {{__( 'required')}}</span>
                        </dd>
                    </dl>

                    <small class="text-muted">
                        <i class="fas fa-question-circle"></i> {{ __('Learn how to') }} <a href="#" data-toggle="modal" data-target="#setupInstructionsModal">{{ __('configure')}}</a> {{ __('your WCTP integration') }}
                    </small>
                </div>
            </div>
        </div>
    </div>


    <h5 class="text-muted-light mt-2">{{ __('Recent Events') }}
        <small class="text-small">
            <a href="/events" class="float-right text-muted">{{ __('View event log') }}</a>
        </small>
    </h5>
    <div class="card py-0 my-0">
        <div class="card-body p-0 m-0">
            <div class="table-responsive text-left">
                <table class="table table-striped table-hover m-0 table-fixed">
                    <thead>
                        <tr class="text-center">
                            <th class="font-weight-bold text-muted-light w-25">{{ __('Timestamp') }}</th>
                            <th class="font-weight-bold text-muted-light w-25">{{ __('User') }}</th>
                            <th class="font-weight-bold text-muted-light w-25">{{ __('Source') }}</th>
                            <th class="font-weight-bold text-muted-light text-left w-25">{{ __('Event') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                    @if( $events->count() )
                        @foreach( $events as $event )
                            <tr>
                                <td class="text-muted text-small font-weight-bold text-nowrap">
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
                                <td class="text-truncate text-dark text-small">{{ $event->event }}</td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-muted text-center text-small font-weight-bold">
                                <i class="fas fa-ban text-muted-light"></i> No events found
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
