@foreach ($users as $user)
<tr class="odd">

    <td class="sorting_1">
        <div class="d-flex justify-content-start align-items-center user-name">
            @if ($user->image)
                <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-3"><img
                            src="{{ asset($user->image != '' ? $user->image : 'user.png') }}"
                            alt="Avatar" class="rounded-circle">
                    </div>
                </div>
            @else
                <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-3"><span
                            class="avatar-initial rounded-circle bg-label-danger">
                            {{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                </div>
            @endif



            <div class="d-flex flex-column"><a href=""
                    class="text-body text-truncate"><span
                        class="fw-semibold user-name-text">{{ $user->name }}
                        {{ $user->last_name }}</span></a><small
                    class="text-muted">&#64;{{ $user->email }}</small>
            </div>
        </div>

    </td>

    <td>{{ $user->country_code }}{{ $user->phone_number }}</td>
    <td>{{ $user->country }}</td>
    <td>{{ $user->address }}</td>
    
 


    <td class="" style="">
        <div class="d-flex align-items-center">

            <a href="" data-bs-toggle="modal"
                data-bs-target="#deleteModal{{ $user->uuid }}"
                class="text-body delete-record">
                <i class="ti ti-trash x`ti-sm mx-2"></i>
            </a>




        </div>


        <div class="modal fade" data-bs-backdrop='static'
            id="deleteModal{{ $user->uuid }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content deleteModal verifymodal">
                    <div class="modal-header">
                        <div class="modal-title" id="modalCenterTitle">Are you
                            sure you want to delete
                            this account?
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="body">After delete this account user cannot
                            access anything in application</div>
                    </div>
                    <hr class="hr">

                    <div class="container">
                        <div class="row">
                            <div class="first">
                                <a href="" class="btn"
                                    data-bs-dismiss="modal"
                                    style="color: #a8aaae ">Cancel</a>
                            </div>
                            <div class="second">
                                <a class="btn text-center"
                                    href="{{ url('admin/user/delete', $user->uuid) }}">Delete</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach