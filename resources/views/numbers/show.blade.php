@php
    use App\Models\Number;
    use App\Models\Carrier;
    use App\Models\EnterpriseHost;
@endphp
@extends('layouts.app')
@section('title', __('Phone Numbers'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">
        {{ __('In-Use Numbers') }}
        <a href="/numbers/available" class="float-end text-muted text-small">Available Numbers</a>
    </h5>

    @if( count( $active ) )
        <div class="card py-0 my-0">
            <div class="card-body p-0 m-0">
                <div class="table-responsive text-left">
                    <table class="table table-striped table-hover m-0">
                        <thead>
                        <tr>
                            <th class="fw-bold text-muted-light">{{ __('Phone Number') }}/{{ __('Identifier') }}</th>
                            <th class="fw-bold text-muted-light">{{ __('Carrier Info') }}</th>
                            <th class="fw-bold text-muted-light">{{ __('Enterprise Host') }}</th>
                            <th class="fw-bold text-muted-light">{{ __('Status') }}</th>
                            <th style="max-width:20%;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $active as $number )
                            @php
                                $c = Carrier::find( $number['carrier_id'] );
                                $type = Number::find( $number['id'] )->getFriendlyType();
                                $eh = EnterpriseHost::find( $number['enterprise_host_id'])
                            @endphp
                            <tr>
                                <td title="{{ $type }}" class="text-dark">{{ $number['e164'] }} <small class="d-block text-xs text-muted">Identifier: {{ $number['identifier'] }}</small></td>
                                <td class="text-small"><strong>{{ ucwords( $c->api ) }}</strong>
                                    &middot; {{ $c->name  }}</td>
                                <td class="text-small">{{ $eh->name }} </td>
                                <td>

                                    @if( $number['enabled'])
                                        <small class="fw-bold text-uppercase text-success">
                                            <i class="fas fa-check-circle"></i> {{ __('Enabled') }}
                                        </small>
                                    @else
                                        <small class="fw-bold text-uppercase text-danger">
                                            <i class="fas fa-times-circle"></i> {{ __('Disabled') }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-light border dropdown-toggle" href="#" role="button"
                                           id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true"
                                           aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-right shadow-sm bg-light"
                                             aria-labelledby="dropdownMenuLink">

                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                               data-bs-target="#setupPhoneNumberModal{{ $number['identifier']}}">{{ __('Setup Number') }}</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                               data-bs-target="#infoPhoneNumberModal{{ $number['identifier']}}">{{ __('Number Information') }}</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                               data-bs-target="#hostAssignmentModal{{ $number['identifier']}}">{{ __('Host Assignment') }}</a>

                                            <div class="dropdown-divider"></div>
                                            @if( $number['enabled'] )
                                                <a class="dropdown-item" style="color:#fd7e14;" href="#"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#disablePhoneNumberModal{{ $number['identifier'] }}">{{ __('Disable Number') }}</a>
                                            @else
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                   data-bs-target="#enablePhoneNumberModal{{ $number['identifier'] }}">{{ __('Enable Number') }}</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                                   data-bs-target="#deletePhoneNumberModal{{ $number['identifier'] }}">{{ __('Release Number') }}</a>
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
            <i class="fas fa-exclamation-circle"></i> Please add numbers from the <a href="/numbers/available">available
                list</a>.
        </div>
    @endif

    @foreach( $active as $number)
        @include('numbers.modals.delete')
        @include('numbers.modals.disable')
        @include('numbers.modals.enable')
        @include('numbers.modals.setup')
        @include('numbers.modals.info')
        @include('numbers.modals.assign')
    @endforeach

    @include('numbers.modals.invalid')

@endsection
