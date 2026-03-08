@extends('master')
@section('title', 'Sync | Enapel')
@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Sync</h4>
                        </div><!--end col-->
                    </div><!--end row-->
                </div>

                <div class="card-body pt-0">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Section</th>
                                    <th>Last Sync</th>
                                    <th>Sync</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>User Activity</td>
                                    <td>2024-12-27 10:45 AM</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span></button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row p-2">
                                            <div class="col-md-10">
                                                <div class="progress">
                                                    <div class="progress-bar bg-gray progress-bar-striped progress-bar-animated" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50%</div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="text-center">
                                                    <i class="la la-refresh text-secondary la-spin progress-icon-spin"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Synced</span></td>
                                </tr>
                                <tr>
                                    <td>Sales Report</td>
                                    <td>2024-12-27 9:30 AM</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span></button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row p-2">
                                            <div class="col-md-10">
                                                <div class="progress">
                                                    <div class="progress-bar bg-gray progress-bar-striped progress-bar-animated" role="progressbar" style="width:90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">90% </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="text-center">
                                                    <i class="la la-refresh text-secondary la-spin progress-icon-spin"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>Stock Report</td>
                                    <td>2024-12-26 8:15 PM</td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-success"><i class="fas fa-sync menu-icon"></i>
                                                <span>Sync</span></button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row p-2">
                                            <div class="col-md-10">
                                                <div class="progress">
                                                    <div class="progress-bar bg-gray progress-bar-striped progress-bar-animated" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50%</div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="text-center">
                                                    <i class="la la-refresh text-secondary la-spin progress-icon-spin"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">Failed</span></td>
                                </tr>
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