
@extends('admin.layout')

@section('styles')
<style>
    .tooltip-inner {
        max-width: 500px;
    }
</style>
@endsection

@section('content')
  <div class="page-header">
    <h4 class="page-title">
      {{__('Serving Method')}}
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
          {{__('Serving Method')}}
        </a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <h3>{{__('Serving Method')}}</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
                <div id="refreshOrder">
                    <div class="table-responsive">
                      <table class="table table-striped mt-3">
                        <thead>
                          <tr>
                            <th scope="col">{{__('Name')}}</th>
                            <th scope="col">{{__('Status')}}</th>
                            <th scope="col">{{__('Offline Gateways')}}</th>
                            <th scope="col">{{__('Action')}}</th>
                          </tr>
                        </thead>

                        <tbody>
                            @foreach ($servingMethods as $sm)
                                <tr>
                                    <td>{{__($sm->name)}}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#statusModal{{$sm->id}}">{{__('Manage')}}</button>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#gatewaysModal{{$sm->id}}">{{__('Manage')}}</button>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal{{$sm->id}}">{{__('Edit')}}</a>
                                    </td>
                                </tr>
                                @includeIf('admin.product.order.serving_methods.partials.status')
                                @includeIf('admin.product.order.serving_methods.partials.gateways')
                                @includeIf('admin.product.order.serving_methods.partials.edit')
                            @endforeach
                        </tbody>
                      </table>
                    </div>
                </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
