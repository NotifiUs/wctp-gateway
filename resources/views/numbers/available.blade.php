@extends('layouts.app')
@section('title', __('Available Phone Numbers'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light">
        {{ __('Available Numbers') }} <small class="font-weight-normal">( Page {{ $_REQUEST['page'] ?? 1  }}
            of {{ $pages }} )</small>
        <a href="/numbers" class="float-end text-muted text-small">In-Use Numbers</a>
    </h5>
    <!--
    'identifier' => 'required|string|unique:numbers,identifier',
        'e164' => 'required|unique:numbers,e164',
        'carrier_id' => 'required|exists:carriers,id',
        'enterprise_host_id' => 'required|exists:enterprise_hosts,id'
    -->
    <form method="POST" action="/numbers" role="form">
        {{ csrf_field() }}
        <div class="col-12 mx-0 px-0">
            <div class="card mb-4">
                <div class="card-body my-0">
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Identifier') }}</label>
                        <input type="text" name="identifier" class="form-control">
                        <small class="form-text text-muted">
                            The unique identifier of the phone number. This is matched to incoming webhooks to determine the carrier.
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('E.164 Format') }}</label>
                        <input type="text" name="e164" class="form-control">
                        <small class="form-text text-muted">
                            +(country-code)(phone-number) i.e., +18885551234 or +443442251826
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Carrier') }}</label>
                        <select name="carrier_id" class="form-control">
                            <option value=""></option>

                            @foreach(\App\Models\Carrier::all() as $cr )
                                @php
                                    $canAutoProvision = false;
                                    try{
                                        $driverFactory = new \App\Drivers\DriverFactory( $cr->api );
                                        $driver = $driverFactory->loadDriver();
                                        $canAutoProvision = $driver->canAutoProvision();
                                    }
                                    catch( Exception $e ){
                                    }
                                @endphp
                                @if($canAutoProvision === false)
                                    <option value="{{ $cr->id }}">{{ $cr->name }}</option>
                                @endif

                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                           The carrier to assign to this number. Twilio and ThinQ numbers must be provisioned from the Available Numbers list.
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold text-muted">{{ __('Enterprise Host') }}</label>
                        <select  name="enterprise_host_id" class="form-control">
                            <option value=""></option>
                            @foreach(\App\Models\EnterpriseHost::all() as $eh )
                                <option value="{{ $eh->id }}">{{ $eh->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            The Enterprise Host this number applies to.
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" role="button" class="btn btn-primary">Create Number</button>
                    </div>


                </div>
            </div>
        </div>
    </form>

    @if($pages > 1)

        <nav aria-label="Page navigation example" class="my-2">
            <ul class="pagination pagination-sm">
                <li class="page-item"><a class="page-link" href="?page={{($_REQUEST['page'] ?? 1 ) - 1}}">Previous</a>
                </li>
                @for($i = 1; $i <= $pages; $i++ )

                    <li class="page-item @if( ($_REQUEST['page'] ?? 1 ) == $i ){{ 'active' }}@endif"><a
                            class="page-link" href="?page={{$i}}">{{ $i }}</a></li>

                @endfor
                <li class="page-item"><a class="page-link" href="?page={{($_REQUEST['page'] ?? 1 ) + 1}}">Next</a></li>
            </ul>
        </nav>

    @endif

    @if( count( $available ) > 0 )
        <div class="card py-0 my-0">
            <div class="card-body p-0 m-0">

                <div class="table-responsive text-left">
                    <table class="table table-striped table-hover m-0">
                        <thead>
                        <tr>
                            <th class="fw-bold text-muted-light">{{ __('Description') }}</th>
                            <th class="fw-bold text-muted-light">{{ __('API Provider') }}</th>
                            <th class="fw-bold text-muted-light">{{ __('Carrier Name') }}</th>
                            <th style="max-width:25%;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $available as $number )
                            <tr>
                                <td class="text-muted">{{ $number['number'] }}</td>
                                <td class="fw-bold text-small">{{ ucwords( $number['api'] ) }} {{ ucwords( $number['type'] ) }}</td>

                                <td>{{ $number['carrier']->name }}</td>
                                <td class=" text-center">
                                    @if($number['sms_enabled'])
                                        <a href="#" title="Number is available to use with the WCTP gateway"
                                           data-bs-toggle="modal" data-bs-target="#usePhoneNumberModal{{ $number['id'] }}"
                                           class="btn fw-bold btn-sm btn-outline-success">
                                            Available
                                        </a>
                                    @else
                                        <a title="Number is not SMS enabled, provisioned, or it may be part of a Messaging Service"
                                           class="btn btn-sm btn-secondary text-white fw-bold" href="#"
                                           data-bs-toggle="modal" data-bs-target="#invalidPhoneNumberModal">
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
            <i class="fas fa-exclamation-circle"></i> You must provision numbers from your carrier before they will show
            up here.
        </div>
    @endif

    @foreach( $available as $number )
        @include('numbers.modals.use')
    @endforeach

    @include('numbers.modals.invalid')

@endsection
