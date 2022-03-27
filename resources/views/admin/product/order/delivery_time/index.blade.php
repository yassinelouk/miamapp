@extends('admin.layout')

@section('content')
<div class="page-header">
  <h4 class="page-title">
    {{__('Delivery Time Frame Management')}}
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{route('admin.dashboard')}}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('My Orders')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">
          {{__('Delivery Time Frame Management')}}
        </a>
      </li>
    </ul>
</div>
<div class="row">
  <div class="col-md-12">

    <div class="card">
      <div class="card-header">
        <h3>{{__('Delivery Time Frame Management')}}</h3>
      </div>
      <div class="card-body">
        <div class="alert alert-warning text-center py-4 normal">
          <h4 class="text-warning mb-0">
            <strong>{{__('These delivery time frames will be shown to customers in checkout page. Customer can choose the delivery Time Frames')}}</strong>
          </h4>
        </div>
        <div class="alert alert-warning text-center py-4 normal">
          <h4 class="text-warning mb-0">
            <strong>{{__("The delivery date, delivery time field can be enabled / disabled from 'Order Management > Settings'")}}</strong>
          </h4>
          <h4 class="text-warning mb-0">
            <strong>{{__("Admin can make it required / optional in food checkout page, from 'Order Management > Settings'")}}</strong>
          </h4>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="table-responsive">
              <table class="table table-striped mt-3">
                <thead>
                  <tr>
                    <th scope="col">{{__('Day')}}</th>
                    <th scope="col">{{__('Delivery Time Frame Management')}}</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td>{{__('monday')}}</td>
                    <td class="d-flex align-items-center" >
                      <button class="btn btn-sm btn-primary" data-day="monday">{{__('add new')}}</button>
                      <a class="btn btn-sm btn-info"
                        href="{{route('admin.timeframes', ['day' => 'monday'])}}">{{__('Edit')}}</a>
                    </td>
                  </tr>
                  <tr>
                    <td>{{__('tuesday')}}</td>
                    <td class="d-flex align-items-center">
                      <button class="btn btn-sm btn-primary" data-day="tuesday">{{__('add new')}}</button>
                      <a class="btn btn-sm btn-info"
                        href="{{route('admin.timeframes', ['day' => 'tuesday'])}}">{{__('Edit')}}</a>
                    </td>
                  </tr>
                  <tr>
                    <td>{{__('wednesday')}}</td>
                    <td class="d-flex align-items-center">
                      <button class="btn btn-sm btn-primary" data-day="wednesday">{{__('add new')}}</button>
                      <a class="btn btn-sm btn-info"
                        href="{{route('admin.timeframes', ['day' => 'wednesday'])}}">{{__('Edit')}}</a>
                    </td>
                  </tr>
                  <tr>
                    <td>{{__('thursday')}}</td>
                    <td class="d-flex align-items-center" >
                      <button class="btn btn-sm btn-primary" data-day="thursday">{{__('add new')}}</button>
                      <a class="btn btn-sm btn-info"
                        href="{{route('admin.timeframes', ['day' => 'thursday'])}}">{{__('Edit')}}</a>
                    </td>
                  </tr>
                  <tr>
                    <td>{{__('friday')}}</td>
                    <td class="d-flex align-items-center">
                      <button class="btn btn-sm btn-primary" data-day="friday">{{__('add new')}}</button>
                      <a class="btn btn-sm btn-info"
                        href="{{route('admin.timeframes', ['day' => 'friday'])}}">{{__('Edit')}}</a>
                    </td>
                  </tr>
                  <tr>
                    <td>{{__('saturday')}}</td>
                    <td class="d-flex align-items-center">
                      <button class="btn btn-sm btn-primary" data-day="saturday">{{__('add new')}}</button>
                      <a class="btn btn-sm btn-info"
                        href="{{route('admin.timeframes', ['day' => 'saturday'])}}">{{__('Edit')}}</a>
                    </td>
                  </tr>
                  <tr>
                    <td>{{__('sunday')}}</td>
                    <td class="d-flex align-items-center">
                      <button class="btn btn-sm btn-primary" data-day="sunday">{{__('add new')}}</button>
                      <a class="btn btn-sm btn-info"
                        href="{{route('admin.timeframes', ['day' => 'sunday'])}}">{{__('Edit')}}</a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@includeIf('admin.product.order.delivery_time.create')
@endsection


@section('scripts')
<script>
  $(document).ready(function() {
        $(".addTF").on('click', function(e) {
            e.preventDefault();
            $("#createModal").modal('show');
            $("input[name='day']").val($(this).data('day'));
        });
    });
</script>
@endsection
