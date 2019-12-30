@extends('layouts.app')
@section('title', __('Phone Numbers'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('In-Use Numbers') }}
    <!--<small>
            <a href="#" class="float-right text-muted">{{ __('Help') }} <i class="fas fa-question-circle"></i></a>
        </small>-->
    </h5>

    @if( count( $active ) )
    <div class="card py-0 my-0">
        <div class="card-body p-0 m-0">
            <div class="table-responsive text-left">
                <table class="table table-striped table-hover m-0">
                    <thead>
                    <tr>
                        <th class="font-weight-bold text-muted">{{ __('Phone Number') }}</th>
                        <th class="font-weight-bold text-muted">{{ __('API Provider') }}</th>
                        <th class="font-weight-bold text-muted">{{ __('Carrier Name') }}</th>
                        <th class="font-weight-bold text-muted">{{ __('Status') }}</th>
                        <!--<th class="font-weight-bold text-muted"></th>-->
                        <th style="max-width:20%;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $active as $number )
                        @php
                            $c = \App\Carrier::find( $number['carrier_id'] );
                            $type = \App\Number::find( $number['id'] )->getFriendlyType();
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $number['e164'] }}</td>
                            <td class="font-weight-bold text-small">{{ ucwords( $c->api ) }} {{ $type }}</td>
                            <td class="">{{ $c->name  }} </td>
                            <td>

                                @if( $number['enabled'])
                                    <small class="font-weight-bold text-uppercase text-success">
                                        <i class="fas fa-check-circle"></i> {{ __('Enabled') }}
                                    </small>
                                @else
                                    <small class="font-weight-bold text-uppercase text-danger">
                                        <i class="fas fa-times-circle"></i> {{ __('Disabled') }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-light border dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right shadow-sm bg-light" aria-labelledby="dropdownMenuLink">

                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#setupPhoneNumberModal{{ $number['identifier']}}">{{ __('Setup Number') }}</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#infoPhoneNumberModal{{ $number['identifier']}}">{{ __('Number Information') }}</a>

                                        <div class="dropdown-divider"></div>
                                        @if( $number['enabled'] )
                                            <a class="dropdown-item" style="color:#fd7e14;" href="#" data-toggle="modal" data-target="#disablePhoneNumberModal{{ $number['identifier'] }}">{{ __('Disable Number') }}</a>
                                        @else
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#enablePhoneNumberModal{{ $number['identifier'] }}">{{ __('Enable Number') }}</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="#" data-toggle="modal"  data-target="#deletePhoneNumberModal{{ $number['identifier'] }}">{{ __('Release Number') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-primary py-4" role="alert">
            <i class="fas fa-exclamation-circle"></i> Please add numbers from the <strong>Available Numbers</strong> list below.
        </div>
    @endif





    <h5 class="text-muted-light mt-4">{{ __('Available Numbers') }}
        <!--<small>
            <a href="#" class="float-right text-muted">{{ __('Help') }} <i class="fas fa-question-circle"></i></a>
        </small>-->
    </h5>

    @if( count( $available ) > 0 )
    <div class="card py-0 my-0">
        <div class="card-body p-0 m-0">

            <div class="table-responsive text-left">
                <table class="table table-striped table-hover m-0">
                    <thead>
                    <tr>
                        <th class="font-weight-bold text-muted">{{ __('Description') }}</th>
                        <th class="font-weight-bold text-muted">{{ __('API Provider') }}</th>
                        <th class="font-weight-bold text-muted">{{ __('Carrier Name') }}</th>
                        <th style="max-width:25%;"></th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach( $available as $number )
                            <tr>
                                <td class="text-muted">{{ $number['number'] }}</td>
                                <td class="font-weight-bold text-small">{{ ucwords( $number['api'] ) }} {{ ucwords( $number['type'] ) }}</td>

                                <td>{{ $number['carrier']->name }}</td>
                                <td class=" text-center">
                                    @if($number['sms_enabled'])
                                        <a href="#" title="Number is available to use with the WCTP gateway" data-toggle="modal" data-target="#usePhoneNumberModal{{ $number['id'] }}" class="btn font-weight-bold btn-sm btn-outline-success">
                                            Available
                                        </a>
                                    @else
                                        <a title="Number is not SMS enabled, provisioned, or it may be part of a Messaging Service" class="btn btn-sm btn-secondary text-white font-weight-bold" href="#" data-toggle="modal" data-target="#invalidPhoneNumberModal">
                                            Invalid
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-primary py-4" role="alert">
            <i class="fas fa-exclamation-circle"></i> You must provision numbers from your carrier before they will show up here.
        </div>
    @endif

    @foreach( $available as $number )
        @include('numbers.modals.use')
    @endforeach
    @foreach( $active as $number)
        @include('numbers.modals.delete')
        @include('numbers.modals.disable')
        @include('numbers.modals.enable')
        @include('numbers.modals.setup')
        @include('numbers.modals.info')
    @endforeach

    @include('numbers.modals.invalid')

@endsection
