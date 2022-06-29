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
                    <span class="primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Users</span>
                    <h3 class="fw-bold mb-0"></h3>
                </div>
            </div>
        </div> <!-- Row end  -->
        <div class="row g-3 mb-3" style="width: 165%;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="myDataTable" class="table table-hover align-middle mb-0" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Photo</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Total Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $val)
                                <tr>
                                    <td><strong>{{$val->id}}</strong></a></td>
                                    <td><img src="{{!empty($val->profile_pic) ? asset('storage/app/public/user_images/' . $val->profile_pic) : asset('public/assets/default/default.png')}}" class="avatar lg rounded me-2" alt="profile-image"></td>
                                    <td>{{$val->name}}</td>
                                    <td>{{$val->email}}</td>
                                    <td>{{$val->mobile}}</td>
                                    <td>{{$val->address}}</td>
                                    <td>
                                        <div class="form-check form-switch"><input data-id="{{ $val->id }}" user_id="{{ $val->id }}" status="{{ $val->status }}" style="background-color: #16d9c8;" status="{{ $val->status }}" class="user-status form-check-input" data-toggle="toggle" data-on="Active" data-off="Inactive" data-onstyle="warning" data-offstyle="dark" type="checkbox" {{ $val->status == 'Active' ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>{{$val->orders->count() > 0 ? $val->orders->count() : 0 }}</td>
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
        $(document).on('click', '.user-status', function (){

        
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