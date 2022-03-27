@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{__('Fidelity points')}}</h4>
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
        <a href="#">{{__('Customers')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Fidelity points')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{__('Fidelity points')}}</div>
            </div>
            <div class="card-body">
                <form action="{{route('admin.fidelity.update')}}" method="POST" id="fidelityUpdateForm">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <div class="form-group">
                            <label>{{__('Fidelity points')}} - {{__('Status')}} **</label>
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="is_fidelity" value="1" class="selectgroup-input" {{$be->is_fidelity == 1 ? 'checked' : ''}}>
                                        <span class="selectgroup-button">{{__('Enable')}}</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="is_fidelity" value="0" class="selectgroup-input" {{$be->is_fidelity == 0 ? 'checked' : ''}}>
                                        <span class="selectgroup-button">{{__('Disable')}}</span>
                                    </label>
                                </div>
                            <label>{{__('Fidelity points')}} **</label>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                  <span class="input-group-text">1 {{__('Fidelity points')}} =</span>
                                </div>
                                <input type="text" name="base_fidelity_rate" class="form-control ltr" value="{{$be->base_fidelity_rate}}">
                                <div class="input-group-append">
                                  <span class="input-group-text">{{$be->base_currency_text}}</span>
                                </div>
                            </div>

                            @if ($errors->has('base_currency_rate'))
                              <p class="mb-0 text-danger">{{$errors->first('base_currency_rate')}}</p>
                            @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="col-12 text-center">
                    <div class="form-group">
                        <button form="fidelityUpdateForm" type="submit" class="btn btn-success">{{__('Update')}}</button>
                    </div>
                </div>
            </div>
        </div>
      <div class="card">
        <form class="" action="{{route('admin.fidelitytime.update')}}" method="post">
          @csrf
          <div class="card-header">
              <div class="row">
                  <div class="col-lg-12">
                      <div class="card-title">{{__('Fidelity Time Management')}}</div>
                  </div>
              </div>
          </div>
          <div class="card-body pt-5 pb-5">

            <div class="row">
              <div class="col-lg-8 offset-lg-2">
                <h4 class="text-warning text-center">{{__('Fidelity points will be active during this periods')}}.</h4>
                @csrf
                @foreach ($fidelitytimes as $ft)
                    <div class="row align-items-center">
                        <div class="col-lg-3">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button style="cursor: auto;" class="btn btn-block btn-primary text-capitalize" type="button">{{__($ft->day)}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group d-flex">
                                        <input class="form-control fidelitytimepicker" name="start_time[]" value="{{$ft->start_time}}" autocomplete="off" placeholder="Start Time">
                                        <button type="button" class="btn btn-sm btn-danger mt-1" onclick="event.target.previousElementSibling.value = ''">{{__('Delete')}}</button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group d-flex">
                                        <input class="form-control fidelitytimepicker" name="end_time[]" value="{{$ft->end_time}}" placeholder="End Time" autocomplete="off">
                                        <button type="button" class="btn btn-sm btn-danger mt-1" onclick="event.target.previousElementSibling.value = ''">{{__('Delete')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <p class="mb-0 text-warning text-center" style="font-size: 16px;">{{__('If you do not use fidelity points at a specific day, leave input fields blank for that day')}}. </p>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="form">
              <div class="form-group from-show-notify row">
                <div class="col-12 text-center">
                  <button type="submit" id="displayNotif" class="btn btn-success">{{__('Update')}}</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
    <script>

        $(document).ready(function() {
            $('.fidelitytimepicker').mdtimepicker({ is24hour:true});
        });
    </script>
@endsection
