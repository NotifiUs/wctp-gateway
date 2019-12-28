@extends('layouts.app')
@section('title', 'Dashboard')
@push('css')
@endpush
@push('scripts')
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
                        <i class="fas fa-3x fa-comments text-info"></i>
                        <h5 class="text-muted mt-2 mb-0">{{ $messageCount }}</h5>
                        <a href="/analytics" class="text-info">Recent Messages</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body text-center my-0">
                        <i class="fas fa-3x fa-dumpster-fire text-danger"></i>
                        <h5 class="text-muted mt-2 mb-0">{{ $errorCount }}</h5>
                        <a href="/errors" class="text-danger">Recent Errors</a>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-8">
                <div class="card mb-2" style="height:100%;background-color:rgba(255, 0, 0, 0.05);">
                    <div class="card-body text-left px-4 my-0">
                        <h5 class="text-danger font-weight-bolder mb-2">{{ __('Message Sending Disabled') }}</h5>
                        @foreach( $checklist as $item )
                           <p class="my-0">

                               <i class="fas fa-times-circle text-danger"></i> <strong>{!!  $item['description']  !!} <small class="font-weight-bold"><a class="text-uppercase text-danger" href="{{ $item['link'] }}">Fix</a></small></strong>
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
                        <i class="fas fa-question-circle"></i> {{ __('Learn how to') }} <a href="#">{{ __('configure')}}</a> {{ __('your WCTP integration') }}
                    </small>
                </div>
            </div>
        </div>
    </div>



    <h5 class="text-muted-light mt-2">{{ __('Recent Events') }}
        <small>
            <a href="/events" class="float-right text-muted">{{ __('View event log') }}</a>
        </small>
    </h5>
    <div class="card py-0 my-0">
        <div class="card-body p-0 m-0">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <div class="table-responsive text-left">
                <table class="table table-striped table-hover m-0">
                    <thead>
                        <tr>
                            <th class="font-weight-bold text-muted">{{ __('Timestamp') }}</th>
                            <th class="font-weight-bold text-muted">{{ __('Source') }}</th>
                            <th class="font-weight-bold text-muted">{{ __('Description') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $sources[0] = 'Enterprise Host';
                        $sources[1] = 'Webhook';
                        $sources[2] = 'Queue';

                        $messages[0] = 'Outbound message submitted by enterprise host';
                        $messages[1] = 'Inbound SMS from Twilio';
                        $messages[2] = 'Message processed from queue';
                    @endphp
                    @for( $i=0; $i<mt_rand(7,12); $i++)
                        @php
                            $index = mt_rand(0,count($sources)-1);
                        @endphp
                        <tr>
                            <td><small class="text-muted">{{ \Carbon\Carbon::now( Auth::user()->timezone )->subMinutes($i)->format('m/d/Y g:i:s A T') }}</small></td>
                            <td class="text-muted">{{ $sources[$index] }}</td>
                            <td class="text-dark">{{ $messages[$index] }}</td>
                        </tr>
                    @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
