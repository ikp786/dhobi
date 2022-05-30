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
                    <span class="py-2 px-5 text-uppercase btn-set-task w-sm-100">List</span>
                    <h3 class="fw-bold mb-0"></h3>
                    <a href="{{route('admin.delivery-boys.create')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Add</a>
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
                                    <th>Full Name</th>
                                    <th>Unique Id</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $cnt = 1; @endphp
                                @forelse($delivery as $val)
                                <tr>
                                    <td><strong>{{$cnt}}</strong></a></td>
                                    <td>{{$val->name}}</td>
                                    <td>{{$val->unique_id}}</td>
                                    <td>{{$val->email}}</td>
                                    <td>{{$val->mobile}}</td>

                                    <td>
                                        <div class="form-check form-switch"><input data-id="{{ $val->id }}" user_id="{{ $val->id }}" status="{{ $val->status }}" style="background-color: #16d9c8;" status="{{ $val->status }}" class="user-status form-check-input" data-toggle="toggle" data-on="Active" data-off="Inactive" data-onstyle="warning" data-offstyle="dark" type="checkbox" {{ $val->status == 'Active' ? 'checked' : '' }}>
                                        </div>
                                    </td>

                                    <td>
                                        <a class="btn-xs sharp me-1" href="{{ route('admin.delivery-boys.edit',$val->id) }}"><i class="icofont-edit text-success"></i></a>
                                        {!! Form::open(['method' => 'DELETE','route' => ['admin.delivery-boys.destroy', $val->id],'style'=>'display:inline']) !!}<button onclick="return confirm('Are you sure to delete Delivery Boy?')" class="delete btn-xs sharp" type="submit"><i class="icofont-ui-delete text-danger"></i> </button>
                                        {!! Form::close() !!}
                                    </td>
                                    </td>
                                </tr>
                                @php $cnt++; @endphp
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

<script>
    $('.user-status').click(function() {

        var id = $(this).attr('user_id');
        var status = $(this).attr('status') == "Active" ? 'Inactive' : 'Active';
        // if ($(this).is(':checked')) {
        // 	alert('checked');
        // } else {
        // 	alert('not checked');
        // }
        if (!confirm('Are you sure to change status this User')) {
            return true;
        }
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: '{{route("admin.user_status_change")}}',
            data: {
                'status': status,
                'id': id
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                Swal.fire(
                    'GREAT!', 'Status change successfully', 'success')
                location.reload();

            }
        });
    });
</script>
@endsection