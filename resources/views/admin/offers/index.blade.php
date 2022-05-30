@extends('admin.layouts.app')
@section('style')
<style>
    td {
        text-align: left !important;
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
                    <span class="btn py-2 px-5 text-uppercase btn-set-task w-sm-100">List</span>
                    <h3 class="fw-bold mb-0"></h3>
                    <a href="{{route('admin.offers.create')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Add</a>
                </div>
            </div>
        </div> <!-- Row end  -->
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="myDataTable" class="table table-hover align-middle mb-0" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Min Order Amount</th>
                                    <th>Quantity Type</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($offers as $val)
                                <tr>
                                    @php $image = isset($val->products->images->image) ? $val->products->images->image : ''; @endphp
                                    <td><strong>{{$val->id}}</strong></a></td>
                                    <td><img src="{{asset('storage/app/public/product_images/'.$image)}}" class="avatar lg rounded me-2" alt="image not found"></td>
                                    <td>{{isset($val->products->name) ? $val->products->name : ''}}</td>
                                    <td>{{$val->minimum_order_value}}</td>
                                    <td>{{$val->quantity_type}}</td>
                                    <td>{{$val->description}}</td>
                                    <td>{{$val->status == 1 ? 'Published' : 'Unpublish'}}</td>
                                    <td>
                                        <a class="btn-xs sharp me-1" href="{{ route('admin.offers.edit',$val->id) }}"><i class="icofont-edit text-success"></i></a>
                                        {!! Form::open(['method' => 'DELETE','route' => ['admin.offers.destroy', $val->id],'style'=>'display:inline']) !!}<button onclick="return confirm('Are you sure to delete Offers?')" class="delete btn-xs sharp" type="submit"><i class="icofont-ui-delete text-danger"></i> </button>
                                        {!! Form::close() !!}
                                    </td>
                                    </td>
                                </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- Row end  -->
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