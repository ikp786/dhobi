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
                    <span class="btn py-2 px-5 text-uppercase btn-set-task w-sm-100">Edit</span>
                    <h3 class="fw-bold mb-0"></h3>
                    <a href="{{route('admin.settings.index')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">lIST</a>
                </div>
            </div>
        </div> <!-- Row end  -->
        <div class="row g-3 mb-3">
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-header py-3 d-flex justify-content-between bg-transparent border-bottom-0">
                        <h6 class="mb-0 fw-bold ">Setting</h6>
                    </div>
                    <div class="card-body">
                        {!! Form::model($setting, ['method' => 'PATCH','route' => ['admin.settings.update', $setting->id],'files'=>true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Delivey Charge</label>
                                {{ Form::text('deliver_charge',$setting->deliver_charge,['class' => 'form-control']) }}
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Change</button>
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