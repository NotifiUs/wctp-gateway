<div class="modal fade" data-backdrop="static" id="verifyCarrierModal" tabindex="-1" role="dialog"
     aria-labelledby="verifyCarrierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-primary">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyCarrierModalLabel">{{ __('Verify Carrier') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
                <div class="modal-body">
                    <ul class="nav nav-pills mb-3 nav-justified text-center" id="carriers-tab" role="tablist">
                        @include('carriers.modals.nav.twilio')
                        @include('carriers.modals.nav.thinq')
                        @include('carriers.modals.nav.bandwidth')
                        @include('carriers.modals.nav.sunwire')

                        @include('carriers.modals.nav.webhook')

                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        @include('carriers.modals.forms.twilio')
                        @include('carriers.modals.forms.thinq')
                        @include('carriers.modals.forms.bandwidth')
                        @include('carriers.modals.forms.sunwire')
                        @include('carriers.modals.forms.webhook')
                    </div>
                </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', (loaded) => {
            let carriersTab = document.getElementById('carriers-tab');
            carriersTab.addEventListener('hide.bs.tab', (hide) => {
                let inputs = document.querySelectorAll('input');
                inputs.forEach( (input) => {
                    input.value('');
                });
            });
        });
    </script>
@endpush
