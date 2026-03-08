@extends('master')
@section('title', 'Suppliers | Enapel')
@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-14">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Suppliers</h4>
                        </div><!--end col-->
                        <div class="col-auto">
                            <form class="row g-4">
                                <div class="col-auto">
                                    <a class="btn bg-primary-subtle text-primary dropdown-toggle d-flex align-items-center arrow-none"
                                        data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                        aria-expanded="false" data-bs-auto-close="outside">
                                        <i class="iconoir-filter-alt me-1"></i> Filter
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-start">
                                        <div class="p-4">
                                            <div class="form-check mb-4">
                                                <input type="checkbox" class="form-check-input" checked id="filter-all">
                                                <label class="form-check-label" for="filter-all">
                                                    All
                                                </label>
                                            </div>
                                            <div class="form-check mb-4">
                                                <input type="checkbox" class="form-check-input" checked id="filter-one">
                                                <label class="form-check-label" for="filter-one">
                                                    New
                                                </label>
                                            </div>
                                            <div class="form-check mb-4">
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
                            </form>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 16px;">
                                        <div class="form-check mb-0 ms-n1">
                                            <input type="checkbox" class="form-check-input" name="select-all"
                                                id="select-all">
                                        </div>
                                    </th>
                                    <th>Supplier</th>
                                    <th>Company</th>
                                    <th>Contact No</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplier as $supplier)
                                <tr>
                                    <td style="width: 16px;">
                                        <div class="form-check mb-0 ms-n1">
                                            <input type="checkbox" class="form-check-input" name="select-all"
                                                id="select-all">
                                        </div>
                                    </td>
                                    <td>{{ $supplier['supplier'] }}</td>
                                    <td>{{ $supplier['company'] }}</td>
                                    <td>{{ $supplier['phone'] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#update{{ $loop->index }}">Edit</button>
                                        <form id="delete-form" action=" {{ route('supplier.delete', $supplier['id']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete()">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="update{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="addBoard" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title m-0" id="addBoardTitle">Suppliers</h6>
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
                                                            <form id="update-form" action="{{ route('supplier.update', ['id' => $supplier['id']]) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="row">
                                                                    <div class="mb-3 row">
                                                                        <label for="name-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">Supplier</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="text"
                                                                                name="supplier"
                                                                                value="{{ $supplier['supplier'] }}"
                                                                                id="name-{{ $loop->index }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3 row">
                                                                        <label for="phone-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">Company</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="text"
                                                                                name="company"
                                                                                value="{{ $supplier['company'] }}"
                                                                                id="phone-{{ $loop->index }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3 row">
                                                                        <label for="phone-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">Phone</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="number"
                                                                                name="phone"
                                                                                value="{{ $supplier['phone'] }}"
                                                                                id="phone-{{ $loop->index }}">
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
            text: "You are about to update the supplier!",
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
            text: "You are about to delete the supplier!",
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