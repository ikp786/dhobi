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
                    <span class="btn py-2 px-5 text-uppercase btn-set-task w-sm-100">Old Orders</span>
                </div>
            </div>
        </div> <!-- Row end  -->

        <form action="{{route('admin.orders.old')}}" method="GET">

            <div class="widget-content widget-content-area">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        {{ Form::label('From Date', null, []) }}
                        {{ Form::date('start_date',$request->start_date,['class' => 'form-control date','placeholder' => "Select Order Date"]) }}
                    </div>
                    <div class="col-lg-3 col-md-6">
                        {{ Form::label('To Date', null, []) }}
                        {{ Form::date('end_date',$request->end_date,['class' => 'form-control date','placeholder' => "Checkout Date"]) }}
                    </div>
                    <div class="col-lg-3 col-md-6 mt-3">
                        <button class="btn btn-primary" type="submit">
                            Filter
                        </button>
                        <a href="{{route('admin.orders.old')}}">
                            <button class="btn btn-danger" type="button" id="">
                                Clear
                            </button>
                        </a>
                    </div>
                </div>
            </div>

        </form>
        <br>
        <a href="{{route('admin.order.download-old-csv')}}?{{($_SERVER['QUERY_STRING'])}}"><button class="btn btn-sm btn-primary" type="button"> CSV </button></a>
        <br>
        <br>
        <div class="row g-3 mb-3" style="width: 165%;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="example2" class="table table-hover align-middle mb-0" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Order Id</th>
                                    <th>User Name</th>
                                    {{-- <th>Email</th> --}}
                                    <th>Mobile</th>
                                    <th>Order<br> Amount</th>
                                    <th>Payment<br> Method</th>
                                    <th>Order <br> Date</th>
                                    <th>Deliver <br> Date</th>
                                    <th>Pickup <br> Date</th>
                                    <th>Pickup <br> Time</th>
                                    <th>Deliver <br> Time</th>
                                    <th>Order <br> Status</th>
                                    <th>Payment <br> Status</th>
                                    <th>Location</th>
                                    <th>Address</th>
                                    <th>Pincode</th>
                                    <th>Driver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $val)
                                <tr>
                                    <td><strong> <a href="{{route('admin.orders.order-product',$val->id)}}"> {{$val->order_number}}</a></strong></td>
                                    <td>{{$val->users->name}}</td>
                                    {{--  <td>{{$val->users->email}}</td>  --}}
                                    <td>{{$val->users->mobile}}</td>
                                    <td>₹ {{$val->order_amount}}</td>
                                    <td>{{$val->payment_method}}</td>
                                    <td> {{$val->created_at}} </td>
                                    <td>{{ date('d/m/Y',strtotime($val->delivery_date))}}</td>

                                    <td> {{ date('d/m/Y',strtotime($val->pickup_date))}}</td>
                                    <td>{{$val->pickup_time}}</td>
                                    <td>{{$val->delivery_time}}</td>
                                    <td>{{$val->order_delivery_status}}</td>
                                    <td>{{$val->payment_status}}</td>
                                    <td>{{isset($val->addresses->location) ? $val->addresses->location : ''}}</td>
                                    <td>{{isset($val->addresses->address) ? $val->addresses->address : ''}}</td>
                                    <td>{{isset($val->addresses->pincode) ? $val->addresses->pincode : ''}}</td>
                                    <td>{{$val->drivers->name}}</td>
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
        $('#example2').DataTable({
            dom: 'Bfrtip',
            buttons: [
                // 'csvHtml5'
            ],
            order: [
                [5, 'desc']
            ]
        });
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
