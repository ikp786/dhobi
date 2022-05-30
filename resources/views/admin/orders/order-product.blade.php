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
                    <span class="btn py-2 px-5 text-uppercase btn-set-task w-sm-100">Order Product</span>
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
                                    <th>Order Id</th>
                                    <th>Order Date</th>
                                    <th>Product Name</th>
                                    <th>Product Quantity</th>
                                    <th>Product Amount</th>
                                    <th>Address</th>
                                    <th>Pincode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $val)
                                <tr>
                                    <td><strong>{{$val->orders->order_number}}</strong></a></td>
                                    <td> {{$val->created_at}} </td>
                                    <td>{{isset($val->product->name) ? $val->product->name : '';}}</td>
                                    <td>{{$val->product_quantity}}</td>
                                    <td>₹ {{$val->total_amount}}</td>
                                    <td>{{isset($val->orders->addresses->address) ? $val->orders->addresses->address : ''}}</td>
                                    <td>{{isset($val->orders->addresses->pincode) ? $val->orders->addresses->pincode : ''}}</td>
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
        $('.asign-driver').change(function() {
            if (!confirm("Do you want Asign  driver")) {
                return false;
            }
            var driver_id = $(this).val();
            var order_id = $(this).attr('order_id');
            if (driver_id != '') {
                $.ajax({
                    type: 'POST',
                    dataType: "json",
                    url: "{{ route('admin.orders.asign-driver') }}",
                    data: {
                        'order_id': order_id,
                        'driver_id': driver_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        swal("Success!", "Driver asign", "success");
                    }
                });
            }
        });
    });
</script>
@endsection