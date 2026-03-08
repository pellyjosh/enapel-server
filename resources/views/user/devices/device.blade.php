@extends('master')
@section('title', 'Devices | Enapel')
@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Devices</h4>
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
                                        device</button>
                                </div><!--end col-->
                            </form>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">

                    <div class="table-responsive">
                        <table class="table datatable" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>Device Name</th>
                                    <th>User</th>
                                    <th>Time Being Used</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Device A</td>
                                    <td>John Doe</td>
                                    <td>2 hours</td>
                                    <td>2024/12/27</td>
                                    <td>Active</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device B</td>
                                    <td>Jane Smith</td>
                                    <td>1 hour</td>
                                    <td>2024/12/26</td>
                                    <td>Inactive</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device C</td>
                                    <td>Michael Brown</td>
                                    <td>30 minutes</td>
                                    <td>2024/12/25</td>
                                    <td>Active</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device D</td>
                                    <td>Emily Davis</td>
                                    <td>3 hours</td>
                                    <td>2024/12/24</td>
                                    <td>Inactive</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device E</td>
                                    <td>Chris Wilson</td>
                                    <td>1 day</td>
                                    <td>2024/12/23</td>
                                    <td>Active</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device F</td>
                                    <td>Anna Taylor</td>
                                    <td>5 hours</td>
                                    <td>2024/12/22</td>
                                    <td>Inactive</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device G</td>
                                    <td>David Clark</td>
                                    <td>4 hours</td>
                                    <td>2024/12/21</td>
                                    <td>Active</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device H</td>
                                    <td>Sophia Johnson</td>
                                    <td>6 hours</td>
                                    <td>2024/12/20</td>
                                    <td>Inactive</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device I</td>
                                    <td>Lucas Martin</td>
                                    <td>2 hours</td>
                                    <td>2024/12/19</td>
                                    <td>Active</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device J</td>
                                    <td>Olivia Thompson</td>
                                    <td>8 hours</td>
                                    <td>2024/12/18</td>
                                    <td>Inactive</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device K</td>
                                    <td>Emma Garcia</td>
                                    <td>1 hour</td>
                                    <td>2024/12/17</td>
                                    <td>Active</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Device L</td>
                                    <td>Noah White</td>
                                    <td>3 hours</td>
                                    <td>2024/12/16</td>
                                    <td>Inactive</td>
                                    <td class="text-end">
                                        <a href="#"><i class="las la-info-circle text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    <div class="modal fade" id="addBoard" tabindex="-1" role="dialog" aria-labelledby="addBoard" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="addBoardTitle">Center Modal</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div><!--end modal-header-->
                <div class="modal-body">
                    <div class="col-14">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Textual Inputs</h4>
                                    </div><!--end col-->
                                </div> <!--end row-->
                            </div><!--end card-header-->
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="mb-3 row">
                                        <label for="example-text-input" class="col-sm-4 col-form-label text-end">Text</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="text" value="Artisanal kale" id="example-text-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-email-input" class="col-sm-4 col-form-label text-end">Email</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="email" value="bootstrap@example.com"
                                                id="example-email-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-tel-input" class="col-sm-4 col-form-label text-end">Telephone</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="tel" value="1-(555)-555-5555" id="example-tel-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-password-input" class="col-sm-4 col-form-label text-end">Password</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="password" value="hunter4" id="example-password-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-number-input" class="col-sm-4 col-form-label text-end">Number</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="number" value="44" id="example-number-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-datetime-local-input" class="col-sm-4 col-form-label text-end">Date and
                                            time</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="datetime-local" value="4011-08-19T13:45:00"
                                                id="example-datetime-local-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="exampleColorInput" class="col-sm-4 col-form-label text-end">Color</label>
                                        <div class="col-sm-8">
                                            <input type="color" class="form-control form-control-color" id="exampleColorInput"
                                                value="#0b51b7" title="Choose your color">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label text-end">Select</label>
                                        <div class="col-sm-8">
                                            <select class="form-select" aria-label="Default select example">
                                                <option selected>Open this select menu</option>
                                                <option value="1">One</option>
                                                <option value="4">Two</option>
                                                <option value="3">Three</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-text-input-lg" class="col-sm-4 col-form-label text-end">Large</label>
                                        <div class="col-sm-8">
                                            <input class="form-control form-control-lg" type="text" placeholder=".form-control-lg"
                                                id="example-text-input-lg">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-text-input-sm" class="col-sm-4 col-form-label text-end">Small</label>
                                        <div class="col-sm-8">
                                            <input class="form-control form-control-sm" type="text" placeholder=".form-control-sm"
                                                id="example-text-input-sm">
                                        </div>
                                    </div>



                                    <div class="mb-3 row">
                                        <label for="example-search-input" class="col-sm-4 col-form-label text-end">Search</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="search" value="How do I shoot web"
                                                id="example-search-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-url-input" class="col-sm-4 col-form-label text-end">URL</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="url" value="https://getbootstrap.com"
                                                id="example-url-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-date-input" class="col-sm-4 col-form-label text-end">Date</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="date" value="4011-08-19" id="example-date-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-month-input" class="col-sm-4 col-form-label text-end">Month</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="month" value="4011-08" id="example-month-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-week-input" class="col-sm-4 col-form-label text-end">Week</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="week" value="4011-W33" id="example-week-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="example-time-input" class="col-sm-4 col-form-label text-end">Time</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="time" value="13:45:00" id="example-time-input">
                                        </div>
                                    </div>
                                    <div class="mb-3 row has-warning">
                                        <label for="inputHorizontalWarning" class="col-sm-4 col-form-label text-end">Email</label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control form-control-warning" id="inputHorizontalWarning"
                                                placeholder="name@example.com">
                                            <small class="form-text text-muted">Example help text that remains unchanged.</small>
                                        </div>
                                    </div>

                                    <div class="mb-3 row has-success">
                                        <label for="inputHorizontalSuccess" class="col-sm-4 col-form-label text-end">Email</label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control is-valid" id="inputHorizontalSuccess"
                                                placeholder="name@example.com">
                                            <div class="valid-feedback">Success! You've done it.</div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row has-error">
                                        <label for="inputHorizontalDnger" class="col-sm-4 col-form-label text-end">Email</label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control is-invalid" id="inputHorizontalDnger"
                                                placeholder="name@example.com">
                                            <div class="invalid-feedback">Sorry, that username's taken. Try another?</div>
                                        </div>
                                    </div>

                                </div> <!--end row-->
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div>
                </div><!--end modal-body-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm">Save changes</button>
                </div><!--end modal-footer-->
            </div><!--end modal-content-->
        </div><!--end modal-dialog-->
    </div>
</div><!-- container -->

@endsection
@section('body_script')

<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/simple-datatables/umd/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatable.init.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection