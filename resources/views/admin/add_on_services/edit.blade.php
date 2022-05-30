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
                    <a href="{{route('admin.add_on_services.index')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">lIST</a>
                </div>
            </div>



        </div> <!-- Row end  -->
        <div class="row g-3 mb-3">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header py-3 d-flex justify-content-between bg-transparent border-bottom-0">
                        <h6 class="mb-0 fw-bold ">Add On Service</h6>
                    </div>
                    <div class="card-body">
                        {!! Form::model($add_on_services, ['method' => 'PATCH','route' => ['admin.add_on_services.update', $add_on_services->id],'files'=>true]) !!}
                        @csrf
                        <!-- <div class="row g-3 align-items-center"> -->
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Service Name</label>

                                {!! Form::text('title', $add_on_services->name, array('placeholder' => 'Service Name','class' => 'form-control')) !!}
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Price</label>

                                {!! Form::text('price', $add_on_services->price, array('placeholder' => 'price','class' => 'form-control')) !!}
                            </div>

                            <div class="col-md-6">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                                    <h6 class="m-0 fw-bold">Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" @if($add_on_services->status == 1) {{'checked'}} @endif type="radio" name="status" value="1">
                                        <label class="form-check-label">
                                            Published
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" @if($add_on_services->status == 0) {{'checked'}} @endif type="radio" name="status" value="0">
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
    $(document).ready(function() {
        //Ch-editer
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
        //Deleterow
        $("#tbproduct").on('click', '.deleterow', function() {
            $(this).closest('tr').remove();
        });
    });
    $(function() {
        $('.dropify').dropify();
        var drEvent = $('#dropify-event').dropify();
        drEvent.on('dropify.beforeClear', function(event, element) {
            return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
        });
        drEvent.on('dropify.afterClear', function(event, element) {
            alert('File deleted');
        });
        $('.dropify-fr').dropify({
            messages: {
                default: 'Glissez-dÃ©posez un fichier ici ou cliquez',
                replace: 'Glissez-dÃ©posez un fichier ou cliquez pour remplacer',
                remove: 'Supprimer',
                error: 'DÃ©solÃ©, le fichier trop volumineux'
            }
        });
    });
</script>
@endsection