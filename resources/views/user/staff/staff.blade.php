@extends('master')
@section('title', 'Staff | Enapel')
@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Staff List</h4>
                        </div><!--end col-->
                        <div class="col-auto">
                            <form class="row g-2">
                                <div class="col-auto">
                                    <a class="btn bg-primary-subtle text-primary dropdown-toggle d-flex align-items-center arrow-none"
                                        data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                        aria-expanded="false" data-bs-auto-close="outside">
                                        <i class="iconoir-filter-alt me-1"></i> Filter
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-start">
                                        <div class="p-2">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" checked id="filter-all">
                                                <label class="form-check-label" for="filter-all">
                                                    All
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" checked id="filter-one">
                                                <label class="form-check-label" for="filter-one">
                                                    New
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" checked id="filter-two">
                                                <label class="form-check-label" for="filter-two">
                                                    Active
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" checked
                                                    id="filter-three">
                                                <label class="form-check-label" for="filter-three">
                                                    Inactive
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end col-->

                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addBoard"><i class="fa-solid fa-plus me-1"></i> Add
                                        Staff</button>
                                </div><!--end col-->
                            </form>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">

                    <div class="table-responsive">
                        <table class="table mb-0 checkbox-all" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 16px;">
                                        <div class="form-check mb-0 ms-n1">
                                            <input type="checkbox" class="form-check-input" name="select-all"
                                                id="select-all">
                                        </div>
                                    </th>
                                    <th>STAFF ID</th>
                                    <th>NAME</th>
                                    <th>PHONE</th>
                                    <th>DESIGNATION</th>
                                    <th>ROLE</th>
                                    <th>DATE OF BIRTH</th>
                                    <th>SALARY</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff as $staff)
                                <tr>
                                    <td style="width: 16px;">
                                        <div class="form-check mb-0 ms-n1">
                                            <input type="checkbox" class="form-check-input" name="select-all"
                                                id="select-all">
                                        </div>
                                    </td>
                                    <td>{{ $staff['staffid'] }}</td>
                                    <td>{{ $staff['name'] }}</td>
                                    <td>{{ $staff['phone'] }}</td>
                                    <td>{{ $staff['designation'] }}</td>
                                    <td>{{ $staff['role'] }}</td>
                                    <td>{{ $staff['dob'] }}</td>
                                    <td>{{ $staff['salary'] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#update{{ $loop->index }}">Edit</button>
                                        <form id="delete-form" action=" {{ route('staff.delete', $staff['id']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete()">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="update{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="addBoard" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title m-0" id="addBoardTitle">Staff List</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div><!--end modal-header-->
                                            <div class="modal-body">
                                                <div class="col-14">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <div class="row align-items-center">
                                                                <div class="col">
                                                                    <h4 class="card-title">Change Details</h4>
                                                                </div><!--end col-->
                                                            </div> <!--end row-->
                                                        </div><!--end card-header-->
                                                        <div class="card-body pt-0">
                                                            <form id="update-form" action="{{ route('staff.update', ['id' => $staff['id']]) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="row">
                                                                    <div class="mb-3 row">
                                                                        <label for="name-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">name</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="text"
                                                                                name="name"
                                                                                value="{{ $staff['name'] }}"
                                                                                id="name-{{ $loop->index }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3 row">
                                                                        <label for="phone-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">phone</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="number"
                                                                                name="phone"
                                                                                value="{{ $staff['phone'] }}"
                                                                                id="phone-{{ $loop->index }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3 row">
                                                                        <label for="designation-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">designation</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="text"
                                                                                name="designation"
                                                                                value="{{ $staff['designation'] }}"
                                                                                id="designation-{{ $loop->index }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3 row">
                                                                        <label for="role-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">role</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="text"
                                                                                name="role"
                                                                                value="{{ $staff['role'] }}"
                                                                                id="role-{{ $loop->index }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3 row">
                                                                        <label for="dob-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">Date of birth</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="date"
                                                                                name="dob"
                                                                                value="{{ $staff['dob'] }}"
                                                                                id="dob-{{ $loop->index }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3 row">
                                                                        <label for="salary-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">Salary</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="number"
                                                                                name="salary"
                                                                                value="{{ $staff['salary'] }}"
                                                                                id="salary-{{ $loop->index }}">
                                                                        </div>
                                                                    </div>
                                                                </div> <!--end row-->
                                                        </div><!--end card-body-->
                                                    </div><!--end card-->
                                                </div>
                                            </div><!--end modal-body-->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="confirmUpdate()">Save changes</button>
                                            </div><!--end modal-footer-->
                                            </form>
                                        </div><!--end modal-content-->
                                    </div><!--end modal-dialog-->
                                </div>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="modal fade" id="addBoard" tabindex="-1" role="dialog" aria-labelledby="addBoard" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title m-0" id="addBoardTitle">staffid Inventory</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div><!--end modal-header-->
                                <div class="modal-body">
                                    <div class="col-14">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <h4 class="card-title">Add new staff</h4>
                                                    </div><!--end col-->
                                                </div> <!--end row-->
                                            </div><!--end card-header-->
                                            <div class="card-body pt-0">
                                                <form action="{{ route('staff.store')}}" method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <div class="row">
                                                        <div class="mb-3 row">
                                                            <label for="" class="col-sm-4 col-form-label text-end">Name</label>
                                                            <div class="col-sm-8">
                                                                <input
                                                                    class="form-control"
                                                                    type="text"
                                                                    name="name"
                                                                    value=""
                                                                    placeholder="staff name">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3 row">
                                                            <label for="" class="col-sm-4 col-form-label text-end">Phone</label>
                                                            <div class="col-sm-8">
                                                                <input
                                                                    class="form-control"
                                                                    type="number"
                                                                    name="phone"
                                                                    value=""
                                                                    placeholder="telephone">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3 row">
                                                            <label for="" class="col-sm-4 col-form-label text-end">designation</label>
                                                            <div class="col-sm-8">
                                                                <input
                                                                    class="form-control"
                                                                    type="text"
                                                                    name="designation"
                                                                    value=""
                                                                    placeholder="designation">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3 row">
                                                            <label for="" class="col-sm-4 col-form-label text-end">role</label>
                                                            <div class="col-sm-8">
                                                                <input
                                                                    class="form-control"
                                                                    type="text"
                                                                    name="role"
                                                                    value=""
                                                                    placeholder="role">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3 row">
                                                            <label for="" class="col-sm-4 col-form-label text-end">Date of Birth</label>
                                                            <div class="col-sm-8">
                                                                <input
                                                                    class="form-control"
                                                                    type="date"
                                                                    name="dob"
                                                                    value=""
                                                                    placeholder="date of birth">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3 row">
                                                            <label for="" class="col-sm-4 col-form-label text-end">Salary</label>
                                                            <div class="col-sm-8">
                                                                <input
                                                                    class="form-control"
                                                                    type="number"
                                                                    name="salary"
                                                                    value=""
                                                                    placeholder="0.00">
                                                            </div>
                                                        </div>
                                                    </div> <!--end row-->
                                            </div><!--end card-body-->
                                        </div><!--end card-->
                                    </div>
                                </div><!--end modal-body-->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                                </div><!--end modal-footer-->
                                </form>
                            </div><!--end modal-content-->
                        </div><!--end modal-dialog-->
                    </div>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->

</div><!-- container -->

@endsection
@section('body_script')
<script>
    function confirmUpdate() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to update the staff!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('update-form').submit();
            }
        });
    }

    function confirmDelete() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete the staff!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
            }
        });
    }
</script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/simple-datatables/umd/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatable.init.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection