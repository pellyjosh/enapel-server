@extends('master')
@section('title', 'Inventory | Enapel')
@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Inventory Of Items</h4>
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
                                        Product</button>
                                </div><!--end col-->
                            </form>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">

                    <div class="table-responsive">
                        <table class="table datatable" id="datatable_1">
                            <thead>
                                <tr style="text-align: center;">
                                    <th>Product</th>
                                    <th>Amount in Stock</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td>{{ $item['product'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td>{{ $item['status'] }}</td>
                                    <td>{{ $item['price'] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#update{{ $loop->index }}">Edit</button>
                                        <form id="delete-form" action=" {{ route('inventory.delete', $item['id']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete()">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="update{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="addBoard" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title m-0" id="addBoardTitle">Product Inventory</h6>
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
                                                            <form id="update-form" action="{{ route('inventory.update', ['id' => $item['id']]) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="row">
                                                                    <div class="mb-3 row">
                                                                        <label for="product-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">Product</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="text"
                                                                                name="name"
                                                                                value="{{ $item['product'] }}"
                                                                                id="name-{{ $loop->index }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3 row">
                                                                        <label for="quantity-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">Quantity</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="number"
                                                                                name="quantity"
                                                                                value="{{ $item['quantity'] }}"
                                                                                id="quantity-{{ $loop->index }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3 row">
                                                                        <label for="price-{{ $loop->index }}" class="col-sm-4 col-form-label text-end">Price</label>
                                                                        <div class="col-sm-8">
                                                                            <input
                                                                                class="form-control"
                                                                                type="number"
                                                                                name="price"
                                                                                value="{{ $item['price'] }}"
                                                                                id="price-{{ $loop->index }}">
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
                                    <h6 class="modal-title m-0" id="addBoardTitle">Product Inventory</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div><!--end modal-header-->
                                <div class="modal-body">
                                    <div class="col-14">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <h4 class="card-title">Add new item</h4>
                                                    </div><!--end col-->
                                                </div> <!--end row-->
                                            </div><!--end card-header-->
                                            <div class="card-body pt-0">
                                                <form action="{{ route('inventory.store')}}" method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <div class="row">
                                                        <div class="mb-3 row">
                                                            <label for="" class="col-sm-4 col-form-label text-end">Product</label>
                                                            <div class="col-sm-8">
                                                                <input
                                                                    class="form-control"
                                                                    type="text"
                                                                    name="name"
                                                                    value=""
                                                                    placeholder="Product name">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3 row">
                                                            <label for="" class="col-sm-4 col-form-label text-end">Quantity</label>
                                                            <div class="col-sm-8">
                                                                <input
                                                                    class="form-control"
                                                                    type="number"
                                                                    name="qty"
                                                                    value=""
                                                                    placeholder="Quantity">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3 row">
                                                            <label for="" class="col-sm-4 col-form-label text-end">Price</label>
                                                            <div class="col-sm-8">
                                                                <input
                                                                    class="form-control"
                                                                    type="number"
                                                                    name="price"
                                                                    value=""
                                                                    placeholder="0.0">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3 row">
                                                            <label for="" class="col-sm-4 col-form-label text-end">Price</label>
                                                            <div class="col-sm-8">
                                                                <input
                                                                    class="form-control"
                                                                    type="number"
                                                                    name="user_id"
                                                                    value=""
                                                                    placeholder="User-id">
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
            text: "You are about to update the item!",
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
            text: "You are about to delete the item!",
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