<div class="modal fade" data-backdrop="static" id="usePhoneNumberModal{{ $number['id'] }}" tabindex="-1" role="dialog" aria-labelledby="usePhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header">
                <h5 class="modal-title" id="usePhoneNumberModalLabel{{ $number['id'] }}">{{ __('Use Phone Number') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/numbers" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="e164" value="{{ $number['number'] }}">
                    <input type="hidden" name="identifier" value="{{ $number['id'] }}">
                    <input type="hidden" name="carrier_id" value="{{ $number['carrier']->id }}">
                    <h3>Are you sure you want to use this number?</h3>
                    <h4><strong class="text-danger">{{ $number['number'] }}</strong></h4>
                    <p class="text-muted">
                        Any existing SMS settings for this number will be removed completely.
                    </p>
                    <a class="btn btn-sm btn-outline-secondary" data-toggle="collapse" href="#collapse{{$number['id']}}" role="button" aria-expanded="false" aria-controls="collapse{{$number['id']}}">
                        <i class="fas fa-info-circle"></i> Toggle Details
                    </a>
                    <div class="collapse" id="collapse{{$number['id']}}">
                       <div class="container-fluid px-0 mx-0 my-2">
                            <table class="table  rounded table-striped bg-white table-sm table-bordered text-truncate p-0">
                                <tbody>
                                @foreach( $number['details'] as $key => $val )
                                    @if( ! is_array( $val ) && ! is_object( $val ) )
                                        <tr>
                                            <th class="w-25 text-center">{{ $key }}</th>
                                            <td class="text-muted text-truncate w-75">
                                                <small class="text-truncate">{{ substr( $val, 0, 64 ) }}</small>
                                            </td>

                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                       </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Use Number</button>
                </div>
            </form>
        </div>
    </div>
</div>
