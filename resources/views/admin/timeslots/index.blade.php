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
                    <a href="{{route('admin.timeslots.create')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Add</a>
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
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($timeslots as $val)
                                <tr>
                                    <td class="td_text_align"><strong>{{$val->id}}</strong></a></td>
                                    <td class="td_text_align">{{date('h:i A',strtotime($val->from))}}</td>
                                    <td class="td_text_align">{{date('h:i A',strtotime($val->to))}}</td>
                                    <td>{{$val->status == 1 ? 'Published' : 'Unpublish'}}</td>
                                    <td class="td_text_align">
                                        <a class="btn-xs sharp me-1" href="{{ route('admin.timeslots.edit',$val->id) }}"><i class="icofont-edit text-success"></i></a>
                                      {!! Form::open(['method' => 'DELETE','route' => ['admin.timeslots.destroy', $val->id],'style'=>'display:inline']) !!}<button onclick="return confirm('Are you sure to delete Time Slots?')" class="delete btn-xs sharp" type="submit"><i class="icofont-ui-delete text-danger"></i> </button>
                                        {!! Form::close() !!}
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

</script>
@endsection
