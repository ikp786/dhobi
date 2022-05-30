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
                    <a href="{{route('admin.offers.index')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">lIST</a>
                </div>
            </div>
        </div> <!-- Row end  -->
        <div class="row g-3 mb-3">
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-header py-3 d-flex justify-content-between bg-transparent border-bottom-0">
                        <h6 class="mb-0 fw-bold ">offers</h6>
                    </div>
                    <div class="card-body">
                        {!! Form::model($offers, ['method' => 'PATCH','route' => ['admin.offers.update', $offers->id],'files'=>true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Minimum Order Value</label>
                                {{ Form::text('minimum_order_value',$offers->minimum_order_value,['class' => 'form-control']) }}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity Type</label>
                                {{ Form::radio('quantity_type',  'pav') }} Pav
                                {{ Form::radio('quantity_type',  'half_kg') }} Half Kg
                                {{ Form::radio('quantity_type', 'kg') }} Kg
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Product</label>
                                {{ Form::select('product_id', $products, $offers->product_id, ['class' => 'form-control']) }}
                            </div>
                            <div class="col-md-6">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                                    <h6 class="m-0 fw-bold">Status{{$offers->status}} </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" {{ ($offers->status=="1") ? "checked" : "" }} type="radio" name="status" value="1">
                                        <label class="form-check-label">
                                            Published
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" {{ ($offers->status=="0") ? "checked" : "" }} type="radio" value="0" name="status">
                                        <label class="form-check-label">
                                            Unpublish
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                {{ Form::textarea('description',$offers->description,['rows' => 4,'id' => 'description', 'class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Save</button>
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