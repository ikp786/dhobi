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
                    <span class="btn  py-2 px-5 text-uppercase btn-set-task w-sm-100">Edit</span>
                    <h3 class="fw-bold mb-0"></h3>
                    <a href="{{route('admin.zipcodes.index')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">lIST</a>
                </div>
            </div>
        </div> <!-- Row end  -->
        <div class="row g-3 mb-3">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header py-3 d-flex justify-content-between bg-transparent border-bottom-0">
                        <h6 class="mb-0 fw-bold ">Edit Zipcodes</h6>
                    </div>
                    <div class="card-body">
                        {!! Form::model($zipcodes, ['method' => 'PATCH','route' => ['admin.zipcodes.update', $zipcodes->id],'files'=>true]) !!}
                        @csrf
                        <!-- <div class="row g-3 align-items-center"> -->
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Zipcode</label>

                                {!! Form::number('zipcode', $zipcodes->zipcode, array('class' => 'form-control')) !!}
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Delivery Charge</label>
                                {!! Form::number('delivery_charge', $zipcodes->delivery_charge, array('class' => 'form-control')) !!}
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Minimum Order Value</label>
                                {!! Form::number('minimum_order_value', $zipcodes->minimum_order_value, array('class' => 'form-control')) !!}
                            </div>

                            <div class="col-md-6">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                                    <h6 class="m-0 fw-bold">Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" @if($zipcodes->status == 1) {{'checked'}} @endif type="radio" name="status" value="1">
                                        <label class="form-check-label">
                                            Published
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" @if($zipcodes->status == 0) {{'checked'}} @endif type="radio" name="status" value="0">
                                        <label class="form-check-label">
                                            Unpublish
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Update</button>
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
<script>

</script>
@endsection
