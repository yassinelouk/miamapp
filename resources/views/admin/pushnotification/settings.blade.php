@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{__('Push Notification')}} - {{__('Settings')}}</h4>
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
        <a href="#">{{__('Push Notification')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Settings')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">

          <div class="card-header">
            <div class="card-title">{{__('Push Notification')}} - {{__('Settings')}}</div>
          </div>
          <div class="card-body pt-5 pb-5">
            <div class="row">
              <div class="col-lg-6 offset-lg-3">
                <form id="pushSettingsForm" action="{{route('admin.pushnotification.updateSettings')}}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group">
                        <div class="col-12 mb-2">
                          <label for="image"><strong>{{__('Image')}} **</strong></label>
                        </div>
                        <div class="col-md-12 showImage mb-3">
                          <img src="{{ asset('assets/front/img/'. 'pushnotification_icon.png') . '?' . time() }}" alt="..." class="img-thumbnail">
                        </div>
                        <input type="file" name="file" id="image" class="form-control">
                        @if ($errors->has('file'))
                        <p class="mb-0 text-danger em">{{$errors->first('file')}}</p>
                        @endif
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>VAPID - {{__('Public Key')}}</label>
                    <textarea class="form-control" rows="2" disabled>{{env('VAPID_PUBLIC_KEY')}}</textarea>
                  </div>

                  <div class="form-group">
                    <label>VAPID - {{__('Private Key')}}</label>
                    <input type="text" class="form-control" disabled value="{{env('VAPID_PRIVATE_KEY')}}">
                  </div>


                </form>
              </div>
            </div>
          </div>

          <div class="card-footer">
            <div class="form-group from-show-notify row">
              <div class="col-12 text-center">
                <a class="btn btn-primary mt-2" href="{{route('admin.pushnotification.generateKeys')}}">{{__('Generate Keys')}}</a>
                <button type="submit" class="btn btn-success mt-2" form="pushSettingsForm">{{__('Update')}}</button>
              </div>
            </div>
          </div>

      </div>
    </div>
  </div>

@endsection
