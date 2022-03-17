@extends('layouts.app')
@section('title', __('Sunwire Carrier Setup'))
@push('css')
@endpush
@push('scripts')
@endpush

@section('content')
    @include('layouts.error')
    @include('layouts.status')

    <h5 class="text-muted-light mt-2 mt-md-0">{{ __('Sunwire Carrier Setup') }}</h5>
    <div class="row justify-content-start mb-2">

        <div class="card w-100 py-0">
            <div class="card-body py-0">
                <form method="POST" action="/carriers" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="carrier_api" value="sunwire">
                    <div class="container-fluid">

                        <div class="row justify-content-center">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" value="{{ old('name') }}" name="name" required
                                           class="form-control">

                                    <small class="form-text text-muted">
                                        Reference the carrier instance by this name
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Priority</label>
                                    <input type="text" value="{{ old('priority') }}" name="priority" required
                                           class="form-control">

                                    <small class="form-text text-muted">
                                        The general system priority for this carrier.
                                        Like DNS MX records, lower values mean higher priority. (10, 20, 30, etc.)
                                    </small>
                                </div>
                                <div class="form-group">
                                    <a class="btn btn-secondary" href="/carriers" role="button">
                                        Cancel
                                    </a>
                                    <button type="submit" role="button" class="btn btn-primary">
                                        Enable Sunwire
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <label>Sunwire Details</label>
                                <dl class="w-100 bg-light rounded p-4">
                                    <dt class="text-muted-light">IP Address White-Listing</dt>
                                    <dl class="text-dark">
                                        You must contact Sunwire with the public IP address of this system so they can whitelist you. Your messages will not work until this is done.
                                        Your IP address is: <code>
                                            @php
                                                $ipify = new \GuzzleHttp\Client(['base_uri' => 'https://api.ipify.org']);

                                                try{
                                                    $iresponse = $ipify->get( '/');
                                                }
                                                catch( Exception $e ){ }

                                                $ip = (string)$iresponse->getBody();
                                                $validator = \Illuminate\Support\Facades\Validator::make(['ip' => $ip], ['ip' => 'required|ip']);

                                                if( $validator->fails() ) {
                                                    echo 'Unknown';
                                                 }
                                                else
                                                {
                                                    echo $ip;
                                                }
                                            @endphp
                                        </code>

                                    </dl>
                                    <dt class="text-muted-light">Numbers Provisioning</dt>
                                    <dl class="">
                                        Sunwire does not provide an API method for configuring numbers. Please contact them directly for the correct number to use, and then add it manually in the <a href="/numbers">Phone Numbers section</a>.
                                    </dl>
                                    <dt class="text-muted-light">API Version</dt>
                                    <dl>
                                        Details taken from <code>Sunwire SMS Messaging API v1.7.pdf</code>. (Contact <a href="https://www.sunwire.ca/">Sunwire</a> for API documentation.)
                                    </dl>
                                </dl>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
