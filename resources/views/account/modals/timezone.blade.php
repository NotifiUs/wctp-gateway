<div class="modal fade" data-backdrop="static" id="editTimezoneModal" tabindex="-1" role="dialog" aria-labelledby="editTimezoneModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-info">
            <div class="modal-header">
                <h5 class="modal-title" id="editTimezoneModalLabel">{{ __('Edit your name') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/account/timezone" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Timezone</label>
                        <select required name="timezone" class="form-control">
                            @foreach( timezone_identifiers_list() as $tz )
                                @if( Auth::user()->timezone == $tz )
                                    <option selected value="{{ $tz }}">{{ $tz }}</option>
                                @else
                                    <option value="{{ $tz }}">{{ $tz }}</option>
                                @endif
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Select your timezone to display throughout the application.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" role="button" class="btn btn-info">Update Timezone</button>
                </div>
            </form>
        </div>
    </div>
</div>
