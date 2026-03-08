@extends('master')
@section('title', 'Expenses | Enapel')
@section('content')
<div class="col-md-6 col-lg-6">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title">Expenses</h4>
                </div><!--end col-->
            </div> <!--end row-->
        </div><!--end card-header-->
        <div class="card-body pt-0">
            <form action="{{route('expense.create')}}" method="POST">
                @csrf
                <div class="mb-3 row">
                    <label for="horizontalInput1" class="col-sm-2 col-form-label">Expense</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="horizontalInput1" placeholder="Enter Expense" name="type">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="horizontalInput2" class="col-sm-2 col-form-label">Amount</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="horizontalInput2" placeholder="Amount" name="amount">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-10 ms-auto">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div><!--end card-body-->
    </div><!--end card-->
</div>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/simple-datatables/umd/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatable.init.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection