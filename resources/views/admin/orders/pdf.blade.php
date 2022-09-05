<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dry Clean </title>
    <link rel="icon" href="{{ asset('assets/admin/favicon.ico') }}" type="image/x-icon"> <!-- Favicon-->
    @include('admin.inc.style')
    <style>
        .tawk-min-container {
            display: none !important;
        }
    </style>
</head>

<body>
    <div id="ebazar-layout" class="theme-blue">
        <!-- sidebar -->

        <!-- main body area -->
        <div class="main px-lg-4 px-md-4">
            <!-- Body: Header -->

            <!-- Body: Body -->
            <div class="body d-flex py-3">
                <div class="container-xxl">
                    {{--  <div class="row align-items-center">
                        <div class="border-0 mb-4">
                            <div
                                class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">
                                <span class="btn py-2 px-5 text-uppercase btn-set-task w-sm-100">Order Detail</span>
                                <h3 class="fw-bold mb-0"></h3>
                            </div>
                        </div>

                    </div> <!-- Row end  -->  --}}

                    <div class="row g-3 mb-3" style="width: 100%;">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">

                                    <h2> User Details </h2>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>User Name</th>
                                            <td>{{ $orders->users->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $orders->users->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile</th>
                                            <td>{{ $orders->users->mobile }}</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <h2> Product Information </h2>
                                    <table class="table table-borderless">
                                        @php $newArr = []; @endphp
                                        @foreach ($product_data as $key => $value2)
                                            <tr>
                                                <th>Category Name: </th>
                                                <td>{{ $key }}</td>
                                            </tr>
                                            @foreach ($value2 as $key2 => $value)
                                                @php$add_on_service = (array) json_decode($value->add_on_services);
                                                    foreach ($add_on_service as $key3 => $value3) {
                                                        $newArr[$value3] = $key3;
                                                    }
                                                @endphp
                                                <tr>

                                                    <th>Add On Service: </th>
                                                    <td>

                                                        @if (empty($newArr))
                                                            N/A
                                                        @else
                                                            @php echo implode(",",$newArr); @endphp
                                                        @endif


                                                    </td>
                                                </tr>
                                                <tr>

                                                <tr>
                                                    <th>Product Name:</th>
                                                    <td> {{ $value->products->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Quantity:</th>
                                                    <td>
                                                        {{ $value->product_quantity }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </table>

                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Order Id: </th>
                                            <td>{{ $orders->order_number }}</td>
                                        </tr>
                                        <tr>
                                            <th width="25%">Delivery Address:</th>
                                            <td>
                                                {{ isset($orders->addresses->address) ? $orders->addresses->address : '' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="25%">Pincode:</th>
                                            <td>
                                                {{ isset($orders->addresses->pincode) ? $orders->addresses->pincode : '' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="25%">Total Payment:</th>
                                            <td>RS {{ $orders->order_amount }}</td>
                                        </tr>
                                        <tr>
                                            <th width="25%">Order Date:</th>
                                            <td>
                                                {{ date('d/m/Y', strtotime($orders->created_at)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="25%">Pickup Date:</th>
                                            <td>{{ date('d/m/Y', strtotime($orders->pickup_date)) }}</td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="25%">Delivery Date:</th>
                                            <td>{{ date('d/m/Y', strtotime($orders->delivery_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <th width="25%">Pickup Time:</th>
                                            <td>{{ $orders->pickup_time }}</td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="25%">Delivery Time:</th>
                                            <td>{{ $orders->delivery_time }}</td>
                                        </tr>
                                        <tr>
                                            <th width="25%">Remark:</th>
                                            <td>
                                                @if (empty($orders->remark))
                                                    N/A
                                                @else
                                                    {{ $orders->remark }}
                                                @endif




                                            </td>
                                        </tr>
                                    </table>


                                    <h2> Amount Information </h2>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Total Product Amount: </th>
                                            <td>Rs: {{ $orders->total_product_amount }}</td>
                                        </tr>
                                        <tr>
                                            <th>Add On Service Charge:</th>

                                            <td>Rs: {{ $orders->add_on_service_amount }}</td>

                                        </tr>
                                        <tr>
                                            <th>Delivery Charge:</th>

                                            <td>Rs: {{ $orders->deliver_charge }}</td>

                                        </tr>

                                        <tr>
                                            <th>Coupon Discount:</th>

                                            <td>Rs: {{ $orders->coupon_amount }}</td>

                                        </tr>

                                        <tr>
                                            <th>Total Amount:</th>

                                            <td>Rs: {{ $orders->order_amount }}</td>

                                        </tr>
                                    </table>
                                    <h2> Order Status </h2>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Payment Status</th>
                                            <td>{{ $orders->payment_status }}</td>
                                        </tr>
                                        <tr>
                                            <th>Delivery Status</th>
                                            <td>{{ $orders->order_delivery_status }}</td>
                                        </tr>

                                        </tbody>
                                    </table>

                                    @isset($orders->drivers->name)
                                        <h2> Driver Details </h2>
                                        <table class="table table-borderless">
                                            <tr>
                                                <th>User Name</th>
                                                <td>{{ $orders->drivers->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>

                                                <td>
                                                    @if (empty($orders->drivers->email))
                                                        N/A
                                                    @else
                                                        {{ $orders->drivers->email }}
                                                    @endif


                                                </td>

                                            </tr>

                                            <tr>
                                                <th>Mobile</th>

                                                <td>{{ $orders->drivers->mobile }}</td>

                                            </tr>

                                            </tbody>
                                        </table>
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </div> <!-- Row end  -->
                </div>
            </div>

        </div>

    </div>

    @include('admin.inc.script')
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
</body>

<!-- Mirrored from www.pixelwibes.com/template/ebazar/html/dist/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 08 Mar 2022 07:02:20 GMT -->

</html>
