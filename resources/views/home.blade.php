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
    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Quick Glance') }}</h5>
    <div class="row justify-content-center mb-2">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center my-0">
                    @if( $queue == 1 )
                        <i class="fas fa-3x fa-check-circle text-success"></i>
                        <h5 class="text-muted mt-2 mb-0">{{ __('Message Queue') }}</h5>
                        <a href="/queue" class="text-success">{{ __('Running') }}</a>
                    @else
                        <i class="fas fa-3x fa-times text-danger"></i>
                        <h5 class="text-muted mt-2 mb-0">{{ __('Message Queue') }}</h5>
                        <a href="/queue" class="text-danger">{{ __('Inactive') }}</a>
                    @endif
                </div>
            </div>
        </div>
        @if(count($checklist) == 0 )
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body text-center my-0">
                        <i class="fas fa-3x fa-chevron-circle-up text-primary"></i>
                        <h5 class="text-muted mt-2 mb-0">{{ $outboundCount }}</h5>
                        <a href="/analytics/outbound" class="text-primary">Outbound Messages</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body text-center my-0">
                        <i class="fas fa-3x fa-chevron-circle-down text-primary"></i>
                        <h5 class="text-muted mt-2 mb-0">{{ $inboundCount }}</h5>
                        <a href="/analytics/inbound" class="text-primary">Inbound Messages</a>
                    </div>
                </div>
            </div>

        @else
            <div class="col-md-8">
                <div class="card mb-2 border-orange bg-orange h-100">
                    <div class="card-body text-left px-4 my-0">
                        <h5 class="text-orange font-weight-bolder mb-2">
                            {{ __('System Warnings') }}
                        </h5>
                        @foreach( $checklist as $item )
                           <p class="my-0" style="color:#b37400;">
                               <i class="fas fa-times-circle text-orange"></i> <strong>{!!  $item['description']  !!} <small class="font-weight-bold"><a class="text-uppercase text-orange" href="{{ $item['link'] }}">Fix</a></small></strong>
                           </p>
                        @endforeach
                    </div>
                </div>
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

                        <!-- Need to support more than 1 endpoint -->
                        <!--
                        <dt class="col-sm-12 col-md-4">
                            {{ __('Enterprise Host Endpoint') }}
                        </dt>
                        <dd class="col-sm-12 col-md-8 text-muted">
                            https://<span class="font-weight-bold">enterprise.test/wctp</span>
                        </dd>
                        -->

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
            <div class="table-responsive text-left text-small">
                <table class="table table-striped table-hover m-0 table-fixed">
                    <thead>
                        <tr>
                            <th class="font-weight-bold text-muted-light" style="max-width:20%;">{{ __('Timestamp') }}</th>
                            <th class="font-weight-bold text-muted-light" style="max-width:20%;">{{ __('Source') }}</th>
                            <th class="font-weight-bold text-muted-light" style="max-width:40%;">{{ __('Event') }}</th>
                            <th class="font-weight-bold text-muted-light" style="max-width:20%;">{{ __('User') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if( $events->count() )
                        @foreach( $events as $event )
                            <tr>
                                <td><small class="text-muted">{{ $event->created_at->timezone( Auth::user()->timezone )->format('m/d/Y g:i:s A T') }}</small></td>
                                <td class="text-muted-light">
                                    {{ $event->source }}
                                </td>
                                <td class="text-muted text-small text-truncate">{{ $event->event }}</td>
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
