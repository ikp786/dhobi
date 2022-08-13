@extends('admin.layouts.app')
@section('style')
<style>
    .multiple-selected {
        background: #fff;
        border: 0.0625rem solid #ccc7c7;
        padding: 0.3125rem 1.25rem;
        /* color: #6e6e6e; */
        /* height: 3.5rem; */
        border-radius: 1rem;
    }
</style>
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
                    <span class="btn  py-2 px-5 text-uppercase btn-set-task w-sm-100">Add</span>
                    <h3 class="fw-bold mb-0"></h3>
                    <a href="{{route('admin.products.index')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">lIST</a>
                </div>
            </div>
        </div> <!-- Row end  -->
        <div class="row g-3 mb-3">
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-header py-3 d-flex justify-content-between bg-transparent border-bottom-0">
                        <h6 class="mb-0 fw-bold ">Products</h6>
                    </div>
                    <div class="card-body">
                        {!! Form::model($products, ['method' => 'PATCH','route' => ['admin.products.update', $products->id],'files'=>true]) !!}
                        @csrf
                        <!-- <div class="row g-3 align-items-center"> -->
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                {{ Form::text('name',$products->name,['class' => 'form-control']) }}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price</label>
                                {{ Form::number('price', $products->price,['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                {{ Form::select('category_id', $categories, $products->category_id, ['placeholder'=>'Select Category','class' => 'form-select','id' => 'category_id']) }}
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Sub Category</label>
                                {{ Form::select('sub_category_id', $sub_categories, $products->sub_category_id, ['placeholder'=>'Select Sub Category','class' => 'form-select','id' => 'sub_category_dropdown']) }}
                                <!-- <select name="sub_category_id" class="form-select" id="sub_category_dropdown"> -->
                                </select>
                            </div>

                            <div class="col-md-4" id="service-select">
                                <label class="form-label">Add on service</label>
                                @php $add_on_service = []; @endphp
                                @foreach($products->addOnServiceMappingInProduct as $add_on_service_ids)
                                @php
                                $add_on_service[] = $add_on_service_ids->add_on_service_id;
                                @endphp
                                @endforeach
                                {{--Form::select('add_on_service[]', $add_on_services, $allowed_anenities, ['class' => 'multiple-selected','multiple'])--}}



                                {{ Form::select('add_on_service[]', $add_on_services, $add_on_service, ['class' => 'multiple-selected','id' => 'services','multiple']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                                    <h6 class="m-0 fw-bold">Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" @if($products->status == 1) {{'checked'}} @endif  name="status" value="1">
                                        <label class="form-check-label">
                                            Published
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" @if($products->status == 0) {{'checked'}} @endif name="status" value="0">
                                        <label class="form-check-label">
                                            Unpublish
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Image</label>
                                {{ Form::file('image',['class' => 'form-control']) }}
                            </br>
                                <img src="{{asset('storage/app/public/product_images/'.$products->image)}}" class="avatar lg rounded me-2" alt="image not found">
                            </div>
                            <div class="col-md-4">
                                <br>
                                <label class="form-label"></label>
                                <button type="submit" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Save</button>
                            </div>
                        </div>
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
    var service_select = new SlimSelect({
        select: '#service-select select',
        //showSearch: false,
        placeholder: 'Select service',
        deselectLabel: '<span>&times;</span>',
        hideSelectedOption: true,
    })
    $('#service-select #service-select-all').click(function() {
        var options = [];
        $('#service-select select option').each(function() {
            options.push($(this).attr('value'));
        });
        service_select.set(options);
    })
    $('#service-select #service-deselect-all').click(function() {
        service_select.set([]);
    })

    $(document).ready(function() {
        $('#category_id').on('change', function() {
            var category_id = this.value;
            $("#sub_category_dropdown").html('');
            $.ajax({
                url: "{{route('admin.sub_category_by_category')}}",
                type: "POST",
                data: {
                    category_id: category_id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#sub_category_dropdown').html('<option value="">Select Sub Category</option>');
                    $.each(result.category, function(key, value) {
                        $("#sub_category_dropdown").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        });
    });
</script>
@endsection
