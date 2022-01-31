<div class="modal fade" data-backdrop="static" id="hostAssignmentModal{{ $number['identifier'] }}" tabindex="-1"
     role="dialog" aria-labelledby="hostAssignmentModalLabel{{ $number['id'] }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-info">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title"
                    id="hostAssignmentModalLabel{{ $number['identifier'] }}">{{ __('Change Host Assignment') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/numbers/{{ $number['id'] }}/assign" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Enterprise Host</label>
                        <select required name="enterprise_host_id" class="form-control">
                            @foreach( \App\EnterpriseHost::all() as $eh )
                                @if( $number['enterprise_host_id'] == $eh->id )
                                    <option selected value="{{ $eh->id }}">{{ $eh->name }}</option>
                                @else
                                    <option value="{{ $eh->id }}">{{ $eh->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" role="button" class="btn btn-info">Update Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
