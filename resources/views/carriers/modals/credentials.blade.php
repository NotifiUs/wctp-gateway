<div class="modal fade" data-backdrop="static" id="credentialsCarrierModal{{ $carrier->id }}" tabindex="-1"
     role="dialog" aria-labelledby="credentialsCarrierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-dark">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="credentialsCarrierModalLabel{{ $carrier->id }}">{{ __('Carrier Credentials') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <div class="modal-body">
                <dl class="row">

                    @php
                        $credentials = [];
                        try{
                            $driverFactory = new \App\Drivers\DriverFactory( $carrier->api );
                            $driver = $driverFactory->loadDriver();
                            $credentials = $driver->showCarrierCredentials($carrier);
                        }
                        catch( Exception $e ){
                        }
                    @endphp

                    @foreach($credentials as $key => $credential)
                        <dt class="text-dark col-4 text-center">{{ $key }}</dt>
                        <dd class="col-8 text-left"><code
                                class="text-dark bg-light border-light">{{ $credential }}</code></dd>
                    @endforeach
                </dl>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
