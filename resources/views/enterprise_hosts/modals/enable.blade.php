<div class="modal fade" data-backdrop="static" id="enableEnterpriseHostModal{{ $host->id }}" tabindex="-1" role="dialog"
     aria-labelledby="enableEnterpriseHostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-success">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="enableEnterpriseHostModalLabel{{ $host->id }}">{{ __('Enable Enterprise Host') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/hosts/{{ $host->id }}/enable" role="form">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <h3><strong class="text-success">Enable</strong> this Enterprise Host?</h3>
                    <p class="text-muted">
                        This will enable processing of inbound and outbound messages for this host.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Enable Host</button>
                </div>
            </form>
        </div>
    </div>
</div>
