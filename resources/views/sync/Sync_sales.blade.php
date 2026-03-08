@extends('master')
@section('title', 'Reports/Sales | Enapel')
@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <h4 class="card-title">Synchronize Sales</h4>
                        </div><!--end col-->

                        <div class="col-auto">
                            <a class="btn bg-primary-subtle text-primary dropdown-toggle d-flex align-items-center arrow-none"
                                data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"
                                data-bs-auto-close="outside">
                                <i class="iconoir-filter-alt me-1"></i> Filter
                            </a>
                            <div class="dropdown-menu dropdown-menu-start">
                                <div class="p-2">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" checked id="filter-all">
                                        <label class="form-check-label" for="filter-all">All</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" checked id="filter-one">
                                        <label class="form-check-label" for="filter-one">New</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" checked id="filter-two">
                                        <label class="form-check-label" for="filter-two">Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" checked id="filter-three">
                                        <label class="form-check-label" for="filter-three">Inactive</label>
                                    </div>
                                </div>
                            </div>
                        </div><!--end col-->

                        <div class="col-auto">
                            <button class="btn btn-success">
                                <i class="fas fa-sync menu-icon"></i> <span>Sync all</span>
                            </button>
                        </div><!--end col-->

                        <div class="col">
                            <div class="progress">
                                <div class="progress-bar bg-gray progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50%
                                </div>
                            </div>
                        </div><!--end col-->

                        <div class="col-auto">
                            <i class="la la-refresh text-secondary la-spin progress-icon-spin"></i>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">

                    <div class="table-responsive">
                        <table class="table datatable" id="datatable_1">
                            <thead>
                                <tr style="text-align: right;">
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                    <th>Date</th>
                                    <th>Sync</th>
                                    <th>Sync Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $entry)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $entry['product'] }}</td>
                                    <td>{{ $entry['amount'] }}</td>
                                    <td>{{ $entry['price'] }}</td>
                                    <td>{{ $entry['date'] }}</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>
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

<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/simple-datatables/umd/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatable.init.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection