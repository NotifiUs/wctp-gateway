<div class="modal fade" data-backdrop="static" id="infoPhoneNumberModal{{ $number['identifier'] }}" tabindex="-1" role="dialog" aria-labelledby="infoPhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header border-bottom-0">

                <h5 class="modal-title" id="infoPhoneNumberModalLabel{{ $number['identifier'] }}">{{ __('Phone Number Information') }} {{ $number['e164'] }}</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="container-fluid px-0 mx-0 my-2">
                    <table class="table  rounded table-striped bg-white table-sm table-bordered text-truncate p-0 table-fixed">
                        <tbody>
                        @foreach( \App\Number::find( $number['id'])->getCarrierDetails() as $key => $val )
                            @if( ! is_array( $val ) && ! is_object( $val ) )
                                <tr>
                                    <th class="w-25 text-left">{{ $key }}</th>
                                    <td class="text-muted text-truncate w-75">
                                        <span title="{{ $val }}" class="text-truncate">{{ $val }}</span>
                                    </td>

                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
               </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
