@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{__('add new')}}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="#">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Website Pages')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Home Page')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('add new')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{__('add new')}}</div>
          <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.member.index') . '?language=' . request()->input('language')}}">
            <span class="btn-label">
              <i class="fas fa-backward"></i>
            </span>
            {{__('back')}}
          </a>
        </div>
        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">


              <form id="ajaxForm" class="" action="{{route('admin.member.store')}}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <div class="col-12 mb-2">
                        <label for="image"><strong>{{__('Image')}}</strong></label>
                      </div>
                      <div class="col-md-12 showImage mb-3">
                        <img src="{{ asset('assets/admin/img/noimage.jpg')}}" alt="..." class="img-thumbnail">
                      </div>
                      <input type="file" name="image" id="image" class="form-control image">
                      <p id="errimage" class="mb-0 text-danger em"></p>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <label for="">{{__('Language')}} **</label>
                    <select name="language_id" class="form-control">
                        <option value="" selected disabled>{{__('Select a Language')}}</option>
                        @foreach ($langs as $lang)
                            <option value="{{$lang->id}}">{{$lang->name}}</option>
                        @endforeach
                    </select>
                    <p id="errlanguage_id" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Name')}} **</label>
                  <input type="text" class="form-control" name="name" value="" placeholder="{{__('Name')}}...">
                  <p id="errname" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Rank')}} **</label>
                  <input type="text" class="form-control" name="rank" value="" placeholder="{{__('Rank')}}...">
                  <p id="errrank" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Facebook</label>
                  <input type="text" class="form-control ltr" name="facebook" value="" placeholder="facebook url">
                  <p id="errfacebook" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Twitter</label>
                  <input type="text" class="form-control ltr" name="twitter" value="" placeholder="twitter url">
                  <p id="errtwitter" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Instagram</label>
                  <input type="text" class="form-control ltr" name="instagram" value="" placeholder="instagram url">
                  <p id="errinstagram" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Linkedin</label>
                  <input type="text" class="form-control ltr" name="linkedin" value="" placeholder="linkedin url">
                  <p id="errlinkedin" class="mb-0 text-danger em"></p>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="form">
            <div class="form-group from-show-notify row">
              <div class="col-12 text-center">
                <button type="submit" id="submitBtn" class="btn btn-success">{{__('Submit')}}</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

@endsection
