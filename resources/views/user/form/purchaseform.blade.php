@extends('master')
@section('title', 'New order | Enapel')
@section('content')

<div class="row">
    <div class="col-md-6 col-lg-6 m-auto">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Add New Order</h4>
                    </div><!--end col-->
                </div> <!--end row-->
            </div><!--end card-header-->
            <div class="card-body pt-0">
                <form  action="{{route('neworder')}}" class="form" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label for="username" class="form-label">Supplier</label>
                        <input class="form-control" type="text"  placeholder="Enter Supplier" name="supplier" value="">
                        
                    </div>
                    <div class="mb-2">
                        <label for="company" class="form-label">Company</label>
                        <input class="form-control" type="text"  placeholder="Enter company name" name="company" value="">
                        
                    </div>
                    <div class="mb-2">
                        <label for="Phone" class="form-label">Phone</label>
                        <input class="form-control" type="number"  placeholder="Enter Phone" name="phone" value="">
                        
                    </div>
                    <div class="mb-3">
                        <label for="Product" class="form-label">Product</label>
                        <input class="form-control" type="text"  placeholder="Enter Product " name="product" value="">
                        
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input class="form-control" type="number"  placeholder="Enter Quantity " name="quantity" value="">
                        
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input class="form-control" type="number"  placeholder="Enter Amount " name="amount" value="">
                        
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form><!--end form-->
            </div><!--end card-body-->
        </div><!--end card-->
    </div> <!--end col-->
</div><!--end row-->

@endsection
<!-- @section('body_script')
<script src="{{asset('assets/js/pages/form-validation.js')}}"></script>
@endsection -->