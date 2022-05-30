@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
@include('admin.inc.validation_message')
@include('admin.inc.auth_message')
<!-- Body: Body -->
<div class="body d-flex py-3">
    <div class="container-xxl">
        <div class="row align-items-center">
            <div class="border-0 mb-4">
                <div class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">
                    <span class="btn py-2 px-5 text-uppercase btn-set-task w-sm-100">Add</span>
                    <h3 class="fw-bold mb-0"></h3>
                    <a href="{{route('admin.daily.purchase.reports.index')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">lIST</a>
                </div>
            </div>
        </div> <!-- Row end  -->
        <div class="row g-3 mb-3">
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-header py-3 d-flex justify-content-between bg-transparent border-bottom-0">
                        <h6 class="mb-0 fw-bold ">Daily Price</h6>
                        <h6 class="mb-0 fw-bold ">{{date('Y-M-d')}}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.daily.purchase.reports.store')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @foreach($products as $key => $val)
                            <input type="hidden" name="product_id[]" value="{{$val->id}}">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Buy Price Kg</label>
                                    <input type="text" value="" name="product_buy_price[{{$val->id}}]" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Product Name</label>
                                    <input type="text" value="{{$val->name}}" readonly name="product_name[{{$val->id}}]" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Price Selling KG</label>
                                    <input type="text" name="product_selling_price[{{$val->id}}]" value="{{$val->kg_price}}" readonly name="kg_price[{{$val->id}}]" class="form-control">
                                </div>                                
                            </div>
                            @endforeach                         
                            <br>
                            <button type="submit" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Save</button>
                            <!-- </div> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Row end  -->
</div>
</div>
@endsection
@section('script')
@endsection