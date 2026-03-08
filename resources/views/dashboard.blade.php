@extends('master')
@section('title', 'Dashboard | Enapel')
@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex justify-content-center border-dashed-bottom pb-3">
                        <div class="col-9">
                            <p class="text-dark mb-0 fw-semibold fs-14">Checkouts</p>
                            <h3 class="mt-2 mb-0 fw-bold">24k</h3>
                        </div>
                        <!--end col-->
                        <div class="col-3 align-self-center">
                            <div
                                class="d-flex justify-content-center align-items-center thumb-xl bg-light rounded-circle mx-auto">
                                <i class="iconoir-hexagon-dice h1 align-self-center mb-0 text-secondary"></i>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                    <p class="mb-0 text-truncate text-muted mt-3"><span class="text-success">8.5%</span>
                        New Sales Today</p>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex justify-content-center border-dashed-bottom pb-3">
                        <div class="col-9">
                            <p class="text-dark mb-0 fw-semibold fs-14">Sales
                                Rate</p>
                            <h3 class="mt-2 mb-0 fw-bold">36.45%</h3>
                        </div>
                        <!--end col-->
                        <div class="col-3 align-self-center">
                            <div
                                class="d-flex justify-content-center align-items-center thumb-xl bg-light rounded-circle mx-auto">
                                <i class="iconoir-percentage-circle h1 align-self-center mb-0 text-secondary"></i>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                    <p class="mb-0 text-truncate text-muted mt-3"><span class="text-danger">8%</span>
                        Sales Rate Weekly</p>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body border-dashed-bottom pb-3">
                    <div class="row d-flex justify-content-between">
                        <div class="col-auto">
                            <div class="d-flex justify-content-center align-items-center thumb-xl border border-secondary rounded-circle">
                                <i class="icofont-money-bag h1 align-self-center mb-0 text-secondary"></i>
                            </div>
                            <h5 class="mt-2 mb-0 fs-14">Total Revenue</h5>
                        </div><!--end col-->
                        <div class="col align-self-center">
                            <div id="line-1" class="apex-charts float-end"></div>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
                <div class="card-body">
                    <div class="row d-flex justify-content-center ">
                        <div class="col-12 col-md-6">
                            <h2 class="fs-22 mt-0 mb-1 fw-bold">₦100,282,000</h2>
                        </div><!--end col-->
                        <div class="col-12 col-md-6 align-self-center text-start text-md-end">
                            <button type="button" class="btn btn-primary btn-sm px-2 mt-2 mt-md-0 ">View Report</button>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end card-->
        </div>

        <!--end col-->
    </div>
    <!--end row-->
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Profit Overview</h4>
                        </div>
                        <!--end col-->
                        <div class="col-auto">
                            <div class="dropdown">
                                <a href="#" class="btn bt btn-light dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="icofont-calendar fs-5 me-1"></i>
                                    This Year<i class="las la-angle-down ms-1"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Today</a>
                                    <a class="dropdown-item" href="#">Last Week</a>
                                    <a class="dropdown-item" href="#">Last Month</a>
                                    <a class="dropdown-item" href="#">This Year</a>
                                </div>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!--end card-header-->
                <div class="card-body pt-0">
                    <div id="audience_overview" class="apex-charts"></div>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->

        <!--end col-->
    </div>
    <!--end row-->

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-h-100">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Local Store Analytics</h4>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!--end card-header-->
                <div class="card-body pt-0">
                    <div class="table-responsive browser_users">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-top-0">Store</th>
                                    <th class="border-top-0">Units Sold</th>
                                    <th class="border-top-0">Revenue</th>
                                    <th class="border-top-0">Profit Margin</th>
                                </tr>
                                <!--end tr-->
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Store A</td>
                                    <td>1085<small class="text-muted"> (52%)</small></td>
                                    <td>$5,200</td>
                                    <td>25%<small class="text-success"> (+5%)</small></td>
                                </tr>
                                <!--end tr-->
                                <tr>
                                    <td>Store B</td>
                                    <td>780<small class="text-muted"> (38%)</small></td>
                                    <td>$3,800</td>
                                    <td>30%<small class="text-success"> (+8%)</small></td>
                                </tr>
                                <!--end tr-->
                                <tr>
                                    <td>Store C</td>
                                    <td>654<small class="text-muted"> (32%)</small></td>
                                    <td>$3,200</td>
                                    <td>20%<small class="text-danger"> (-3%)</small></td>
                                </tr>
                                <!--end tr-->
                                <tr>
                                    <td>Store D</td>
                                    <td>489<small class="text-muted"> (22%)</small></td>
                                    <td>$2,400</td>
                                    <td>18%<small class="text-success"> (+2%)</small></td>
                                </tr>
                                <!--end tr-->
                                <tr>
                                    <td>Store E</td>
                                    <td>320<small class="text-muted"> (15%)</small></td>
                                    <td>$1,600</td>
                                    <td>12%<small class="text-danger"> (-4%)</small></td>
                                </tr>
                                <!--end tr-->
                            </tbody>
                        </table>
                        <!--end table-->
                    </div>
                    <!--end /div-->
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-lg-6">
            <div class="card card-h-100">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Store Analytics</h4>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!--end card-header-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-top-0">Store</th>
                                    <th class="border-top-0">Units Sold</th>
                                    <th class="border-top-0">Revenue</th>
                                    <th class="border-top-0">Profit Margin</th>
                                </tr>
                                <!--end tr-->
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Store A</td>
                                    <td>1085<small class="text-muted"> (52%)</small></td>
                                    <td>$5,200</td>
                                    <td>25%<small class="text-success"> (+5%)</small></td>
                                </tr>
                                <!--end tr-->
                                <tr>
                                    <td>Store B</td>
                                    <td>780<small class="text-muted"> (38%)</small></td>
                                    <td>$3,800</td>
                                    <td>30%<small class="text-success"> (+8%)</small></td>
                                </tr>
                                <!--end tr-->
                                <tr>
                                    <td>Store C</td>
                                    <td>654<small class="text-muted"> (32%)</small></td>
                                    <td>$3,200</td>
                                    <td>20%<small class="text-danger"> (-3%)</small></td>
                                </tr>
                                <!--end tr-->
                                <tr>
                                    <td>Store D</td>
                                    <td>489<small class="text-muted"> (22%)</small></td>
                                    <td>$2,400</td>
                                    <td>18%<small class="text-success"> (+2%)</small></td>
                                </tr>
                                <!--end tr-->
                                <tr>
                                    <td>Store E</td>
                                    <td>320<small class="text-muted"> (15%)</small></td>
                                    <td>$1,600</td>
                                    <td>12%<small class="text-danger"> (-4%)</small></td>
                                </tr>
                                <!--end tr-->
                            </tbody>
                        </table>
                        <!--end table-->
                    </div>
                    <!--end /div-->
                    <p class="m-0 fs-12 fst-italic ps-2 text-muted">Last data updated - 13min ago <a href="#!"
                            class="link-danger ms-1 "><i class="align-middle iconoir-refresh"></i></a></p>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Popular Products</h4>
                        </div><!--end col-->
                        <div class="col-auto">
                            <div class="dropdown">
                                <a href="#" class="btn bt btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="icofont-calendar fs-5 me-1"></i> This Year<i class="las la-angle-down ms-1"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Today</a>
                                    <a class="dropdown-item" href="#">Last Week</a>
                                    <a class="dropdown-item" href="#">Last Month</a>
                                    <a class="dropdown-item" href="#">This Year</a>
                                </div>
                            </div>
                        </div><!--end col-->
                    </div> <!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-top-0">Product</th>
                                    <th class="border-top-0">Price</th>
                                    <th class="border-top-0">Sell</th>
                                    <th class="border-top-0">Status</th>
                                    <th class="border-top-0">Action</th>
                                </tr><!--end tr-->
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/products/01.png" height="40" class="me-3 align-self-center rounded" alt="...">
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="m-0">History Book</h6>
                                                <a href="#" class="fs-12 text-primary">ID: A3652</a>
                                            </div><!--end media body-->
                                        </div>
                                    </td>
                                    <td>$50 <del class="text-muted fs-10">$70</del></td>
                                    <td>450 <small class="text-muted">(550)</small></td>
                                    <td><span class="badge bg-primary-subtle text-primary px-2">Stock</span></td>
                                    <td>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr><!--end tr-->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/products/02.png" height="40" class="me-3 align-self-center rounded" alt="...">
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="m-0">Colorful Pots</h6>
                                                <a href="#" class="fs-12 text-primary">ID: A5002</a>
                                            </div><!--end media body-->
                                        </div>
                                    </td>
                                    <td>$99 <del class="text-muted fs-10">$150</del></td>
                                    <td>750 <small class="text-muted">(00)</small></td>
                                    <td><span class="badge bg-danger-subtle text-danger px-2">Out of Stock</span></td>
                                    <td>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr><!--end tr-->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/products/04.png" height="40" class="me-3 align-self-center rounded" alt="...">
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="m-0">Pearl Bracelet</h6>
                                                <a href="#" class="fs-12 text-primary">ID: A6598</a>
                                            </div><!--end media body-->
                                        </div>
                                    </td>
                                    <td>$199 <del class="text-muted fs-10">$250</del></td>
                                    <td>280 <small class="text-muted">(220)</small></td>
                                    <td><span class="badge bg-primary-subtle text-primary px-2">Stock</span></td>
                                    <td>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr><!--end tr-->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/products/06.png" height="40" class="me-3 align-self-center rounded" alt="...">
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="m-0">Dancing Man</h6>
                                                <a href="#" class="fs-12 text-primary">ID: A9547</a>
                                            </div><!--end media body-->
                                        </div>
                                    </td>
                                    <td>$40 <del class="text-muted fs-10">$49</del></td>
                                    <td>500 <small class="text-muted">(1000)</small></td>
                                    <td><span class="badge bg-danger-subtle text-danger px-2">Out of Stock</span></td>
                                    <td>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr><!--end tr-->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/products/05.png" height="40" class="me-3 align-self-center rounded" alt="...">
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="m-0">Fire Lamp</h6>
                                                <a href="#" class="fs-12 text-primary">ID: A2047</a>
                                            </div><!--end media body-->
                                        </div>
                                    </td>
                                    <td>$80 <del class="text-muted fs-10">$59</del></td>
                                    <td>800 <small class="text-muted">(2000)</small></td>
                                    <td><span class="badge bg-danger-subtle text-danger px-2">Out of Stock</span></td>
                                    <td>
                                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                    </td>
                                </tr><!--end tr-->
                            </tbody>
                        </table> <!--end table-->
                    </div><!--end /div-->
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->

        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Top Selling by Country</h4>
                        </div><!--end col-->
                        <div class="col-auto">
                            <div class="dropdown">
                                <a href="#" class="btn bt btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="icofont-calendar fs-5 me-1"></i> This Month<i class="las la-angle-down ms-1"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Today</a>
                                    <a class="dropdown-item" href="#">Last Week</a>
                                    <a class="dropdown-item" href="#">Last Month</a>
                                    <a class="dropdown-item" href="#">This Year</a>
                                </div>
                            </div>
                        </div><!--end col-->
                    </div> <!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tbody>
                                <tr class="">
                                    <td class="px-0">
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/flags/us_flag.jpg" class="me-2 align-self-center thumb-md rounded-circle" alt="...">
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="m-0 text-truncate">USA</h6>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress bg-primary-subtle w-100" style="height:5px;" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-primary" style="width: 85%"></div>
                                                    </div>
                                                    <small class="flex-shrink-1 ms-1">85%</small>
                                                </div>
                                            </div><!--end media body-->
                                        </div><!--end media-->
                                    </td>
                                    <td class="px-0 text-end"><span class="text-body ps-2 align-self-center text-end">$5860.00</span></td>
                                </tr><!--end tr-->
                                <tr class="">
                                    <td class="px-0">
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/flags/spain_flag.jpg" class="me-2 align-self-center thumb-md rounded-circle" alt="...">
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="m-0 text-truncate">Spain</h6>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress bg-primary-subtle w-100" style="height:5px;" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-primary" style="width: 78%"></div>
                                                    </div>
                                                    <small class="flex-shrink-1 ms-1">78%</small>
                                                </div>
                                            </div><!--end media body-->
                                        </div><!--end media-->
                                    </td>
                                    <td class="px-0 text-end"><span class="text-body ps-2 align-self-center text-end">$5422.00</span></td>
                                </tr><!--end tr-->
                                <tr class="">
                                    <td class="px-0">
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/flags/french_flag.jpg" class="me-2 align-self-center thumb-md rounded-circle" alt="...">
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="m-0 text-truncate">French</h6>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress bg-primary-subtle w-100" style="height:5px;" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-primary" style="width: 71%"></div>
                                                    </div>
                                                    <small class="flex-shrink-1 ms-1">71%</small>
                                                </div>
                                            </div><!--end media body-->
                                        </div><!--end media-->
                                    </td>
                                    <td class="px-0 text-end"><span class="text-body ps-2 align-self-center text-end">$4587.00</span></td>
                                </tr><!--end tr-->
                                <tr class="">
                                    <td class="px-0">
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/flags/germany_flag.jpg" class="me-2 align-self-center thumb-md rounded-circle" alt="...">
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="m-0 text-truncate">Germany</h6>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress bg-primary-subtle w-100" style="height:5px;" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-primary" style="width: 65%"></div>
                                                    </div>
                                                    <small class="flex-shrink-1 ms-1">65%</small>
                                                </div>
                                            </div><!--end media body-->
                                        </div><!--end media-->
                                    </td>
                                    <td class="px-0 text-end"><span class="text-body ps-2 align-self-center text-end">$3655.00</span></td>
                                </tr><!--end tr-->
                                <tr class="">
                                    <td class="px-0">
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/flags/baha_flag.jpg" class="me-2 align-self-center thumb-md rounded-circle" alt="...">
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="m-0 text-truncate">Bahamas</h6>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress bg-primary-subtle w-100" style="height:5px;" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-primary" style="width: 48%"></div>
                                                    </div>
                                                    <small class="flex-shrink-1 ms-1">48%</small>
                                                </div>
                                            </div><!--end media body-->
                                        </div><!--end media-->
                                    </td>
                                    <td class="px-0 text-end"><span class="text-body ps-2 align-self-center text-end">$3325.00</span></td>
                                </tr><!--end tr-->
                            </tbody>
                        </table> <!--end table-->
                    </div><!--end /div-->
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Monthly Avg. Income</h4>
                        </div><!--end col-->
                        <div class="col-auto">
                            <div class="dropdown">
                                <a href="#" class="btn bt btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="icofont-calendar fs-5 me-1"></i> This Year<i class="las la-angle-down ms-1"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Today</a>
                                    <a class="dropdown-item" href="#">Last Week</a>
                                    <a class="dropdown-item" href="#">Last Month</a>
                                    <a class="dropdown-item" href="#">This Year</a>
                                </div>
                            </div>
                        </div><!--end col-->
                    </div> <!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">
                    <div id="monthly_income" class="apex-charts"></div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <div class="card shadow-none border mb-3 mb-lg-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col text-center">
                                            <span class="fs-18 fw-semibold">$24,500</span>
                                            <h6 class="text-uppercase text-muted mt-2 m-0">Today's Revenue</h6>
                                        </div><!--end col-->
                                    </div> <!-- end row -->
                                </div><!--end card-body-->
                            </div> <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-md-6 col-lg-3">
                            <div class="card shadow-none border mb-3 mb-lg-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col text-center">
                                            <span class="fs-18 fw-semibold">82.8%</span>
                                            <h6 class="text-uppercase text-muted mt-2 m-0">Conversion Rate</h6>
                                        </div><!--end col-->
                                    </div> <!-- end row -->
                                </div><!--end card-body-->
                            </div> <!--end card-body-->
                        </div><!--end col-->

                        <div class="col-md-6 col-lg-3">
                            <div class="card shadow-none border mb-3 mb-lg-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col text-center">
                                            <span class="fs-18 fw-semibold">$9982.00</span>
                                            <h6 class="text-uppercase text-muted mt-2 m-0">Total Expenses</h6>
                                        </div><!--end col-->
                                    </div> <!-- end row -->
                                </div><!--end card-body-->
                            </div> <!--end card-->
                        </div><!--end col-->
                        <div class="col-md-6 col-lg-3">
                            <div class="card shadow-none border mb-3 mb-lg-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col text-center">
                                            <span class="fs-18 fw-semibold">$80.5</span>
                                            <h6 class="text-uppercase text-muted mt-2 m-0">Avg. Value</h6>
                                        </div><!--end col-->
                                    </div> <!-- end row -->
                                </div><!--end card-body-->
                            </div> <!--end card-body-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end card-->
        </div>
    </div>
    <!--end row-->
</div><!-- container -->

<!--Start Rightbar-->
<!--Start Rightbar/offcanvas-->
<div class="offcanvas offcanvas-end" tabindex="-1" id="Appearance" aria-labelledby="AppearanceLabel">
    <div class="offcanvas-header border-bottom justify-content-between">
        <h5 class="m-0 font-14" id="AppearanceLabel">Appearance</h5>
        <button type="button" class="btn-close text-reset p-0 m-0 align-self-center" data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <h6>Account Settings</h6>
        <div class="p-2 text-start mt-3">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="settings-switch1">
                <label class="form-check-label" for="settings-switch1">Auto updates</label>
            </div><!--end form-switch-->
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="settings-switch2" checked>
                <label class="form-check-label" for="settings-switch2">Location Permission</label>
            </div><!--end form-switch-->
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="settings-switch3">
                <label class="form-check-label" for="settings-switch3">Show offline Contacts</label>
            </div><!--end form-switch-->
        </div><!--end /div-->
        <h6>General Settings</h6>
        <div class="p-2 text-start mt-3">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="settings-switch4">
                <label class="form-check-label" for="settings-switch4">Show me Online</label>
            </div><!--end form-switch-->
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="settings-switch5" checked>
                <label class="form-check-label" for="settings-switch5">Status visible to all</label>
            </div><!--end form-switch-->
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="settings-switch6">
                <label class="form-check-label" for="settings-switch6">Notifications Popup</label>
            </div><!--end form-switch-->
        </div><!--end /div-->
    </div><!--end offcanvas-body-->
</div>
<!--end Rightbar/offcanvas-->
<!--end Rightbar-->
<!--Start Footer-->
@endsection