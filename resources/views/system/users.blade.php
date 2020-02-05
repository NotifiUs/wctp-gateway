<div class="card py-0 my-0 mx-0">
    <div class="card-body p-0 m-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0">
                <thead class="text-left">
                    <tr>
                        <th class="font-weight-bold text-muted-light">
                            Email
                        </th>
                        <th class="text-right">
                            <a href="#" data-toggle="modal" data-target="#createUserModal"
                               class="text-small btn btn-sm btn-outline-secondary">
                                <i class="fas fa-plus"></i> Create User
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach( $users as $user )
                        <tr class="text-left">
                            <td class="text-muted"
                                title="{{$user->name}}">
                                <a href="#" data-toggle="modal" data-target="#editUserModal{{ $user->id }}">
                                    {{ $user->email }}
                                </a>
                                @if( Auth::user()->id == $user->id ) {!!  '<small>(you)</small>'  !!} @endif
                            </td>
                            <td class="text-right">
                                <a href="#" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteUserModal{{ $user->id }}">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('system.modals.create-user')
@foreach( $users as $user )
    @include('system.modals.edit-user')
    @include('system.modals.delete-user')
@endforeach
