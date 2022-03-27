
@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">
      Delivery Time
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
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">
            {{__('Edit')}}
        </a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <h3 class="text-capitalize float-left">{{__('Delivery Time Frame Management')}} ({{__(request()->input('day'))}})</h3>
            <a href="{{route('admin.deliverytime')}}" class="btn btn-info btn-sm float-right">{{__('back')}}</a>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
                @if (count($timeframes) == 0)
                    <h3 class="text-center">{{__('NO TIMEFRAME AVAILABLE')}}</h3>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">{{__('Start Time')}}</th>
                                    <th scope="col">{{__('End Time')}}</th>
                                    <th scope="col">{{__('Max Orders')}}</th>
                                    <th scope="col">{{__('Action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($timeframes as $tf)
                                <tr>
                                    <td>{{$tf->start}}</td>
                                    <td>{{$tf->end}}</td>
                                    <td>{{$tf->max_orders}}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm editbtn" data-toggle="modal" data-target="#editModal" data-start="{{$tf->start}}" data-end="{{$tf->end}}" data-max_orders="{{$tf->max_orders}}" data-id="{{$tf->id}}">{{__('Edit')}}</button>
                                        <form class="deleteform d-inline-block" action="{{route('admin.timeframe.delete')}}" method="post">
                                        @csrf
                                        <input type="hidden" name="timeframe_id" value="{{$tf->id}}">
                                        <button type="submit" class="btn btn-danger btn-sm deletebtn">
                                            <span class="btn-label">
                                            <i class="fas fa-trash"></i>
                                            </span>
                                            {{__('Delete')}}
                                        </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  @includeIf('admin.product.order.delivery_time.edit-timeframe')
@endsection
