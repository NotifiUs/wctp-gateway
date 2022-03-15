<div class="modal fade" data-backdrop="static" id="usePhoneNumberModal{{ $number['id'] }}" tabindex="-1" role="dialog"
     aria-labelledby="usePhoneNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-danger">
            <div class="modal-header border-bottom-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <form method="POST" action="/numbers" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="e164" value="{{ $number['number'] }}">
                    <input type="hidden" name="identifier" value="{{ $number['id'] }}">
                    <input type="hidden" name="carrier_id" value="{{ $number['carrier']->id }}">
                    <h3>Do you want to use this number?</h3>
                    <h4><strong class="text-danger">{{ $number['number'] }}</strong></h4>
                    <p class="text-muted">
                        Remove existing SMS settings for this number.
                    </p>
                    <div class="form-group row col-md-6">
                        <label class="">Enterprise Host</label>
                        <select required name="enterprise_host_id" class="form-control">
                            @foreach( \App\Models\EnterpriseHost::all() as $eh )
                                <option value="{{ $eh->id }}">{{ $eh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group row col">
                        <p class="form-text text-small text-muted">
                            Select the Enterprise Host to associate with this number.
                            Inbound messages to this number route to this Enterprise Host.
                            Outbound messages from the Enterprise Host will use this number (along with any other
                            assigned numbers.)
                        </p>
                    </div>
                    <a class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" href="#collapse{{$number['id']}}"
                       role="button" aria-expanded="false" aria-controls="collapse{{$number['id']}}">
                        <i class="fas fa-info-circle"></i> Toggle Details
                    </a>
                    <div class="collapse" id="collapse{{$number['id']}}">
                        <div class="container-fluid px-0 mx-0 my-2">
                            <table
                                class="table  rounded table-striped bg-white table-sm table-bordered text-truncate p-0 table-fixed">
                                <tbody>
                                @foreach( $number['details'] as $key => $val )
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
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Use Number</button>
                </div>
            </form>
        </div>
    </div>
</div>
