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
        {{ __('Available Numbers') }} ({{ count($available) }})
        <a href="/numbers" class="float-right text-muted text-small">In-Use Numbers</a>
    </h5>

    @if( count( $available ) > 0 )
    <div class="card py-0 my-0">
        <div class="card-body p-0 m-0">

            <div class="table-responsive text-left">
                <table class="table table-striped table-hover m-0">
                    <thead>
                    <tr>
                        <th class="font-weight-bold text-muted-light">{{ __('Description') }}</th>
                        <th class="font-weight-bold text-muted-light">{{ __('API Provider') }}</th>
                        <th class="font-weight-bold text-muted-light">{{ __('Carrier Name') }}</th>
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

    @include('numbers.modals.invalid')

@endsection
