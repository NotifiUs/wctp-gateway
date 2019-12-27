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
                        <th class="font-weight-bold text-muted">{{ __('Carrier API') }}</th>
                        <th class="w-25"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $active as $number )
                        <tr>
                            <td class="text-muted">{{ $number['number'] }}</td>
                            <td class="font-weight-bold">{{ ucwords( $number['api'] ) }}</td>
                            <td class="">
                                <a href="#" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-search"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-dark">
                                    <i class="fas fa-pause"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-hand-paper"></i>
                                </a>
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
                        <th class="font-weight-bold text-muted">{{ __('Phone Number') }}</th>
                        <th class="font-weight-bold text-muted">{{ __('Type') }}</th>
                        <th class="font-weight-bold text-muted">{{ __('Carrier Name') }}</th>
                        <th class="w-25"></th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach( $available as $number )
                            <tr>
                                <td class="text-muted">{{ $number['number'] }}</td>
                                <td class="font-weight-bold">{{ ucwords( $number['api'] ) }}</td>
                                <td>{{ $number['carrier']->name }}</td>
                                <td class="">
                                    <a href="#" data-toggle="modal" data-target="#usePhoneNumberModal{{ $number['id'] }}" class="btn btn-sm btn-secondary">
                                        Use this number
                                    </a>
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

@endsection
