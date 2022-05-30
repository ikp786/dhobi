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
                    <span class="primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Support</span>
                    <h3 class="fw-bold mb-0"></h3>
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
                                    <th>Order id</th>
                                    <th>Full Name</th>
                                    <th>Message</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supports as $val)
                                <tr>
                                    <td><strong>{{$val->id}}</strong></a></td>
                                    <td>{{$val->order_id}}</td>
                                    <td>{{$val->users->name ?? ''}}</td>
                                    <td>{{$val->reason}}</td>
                                    <td>{{$val->created_at}}</td>
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