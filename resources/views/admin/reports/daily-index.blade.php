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
                    <a href="{{route('admin.daily.purchase.reports.create')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Add</a>
                </div>
            </div>
        </div> <!-- Row end  -->
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="example" class="table table-hover align-middle mb-0" style="width: 100%;">
                            <thead>
                                <tr>
                                    <!-- <th>Id</th>
                                    <th>Product Name</th>
                                    <th>Buy Price</th>
                                    <th>Selling Price</th> -->
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $key => $val)
                                <tr>
                                    <!-- <td><strong>{{--$val->id--}}</strong></a></td>
                                    <td>{{--$val->product_name--}}</td>
                                    <td>{{--$val->product_buy_price--}}</td>
                                    <td>{{--$val->product_selling_price--}}</td> -->
                                    <td><a href="{{route('admin.daily.purchase.reports.edit',$key)}}"> {{$key}}</a></td>
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
    $('#example').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    } );
} );
</script>
@endsection