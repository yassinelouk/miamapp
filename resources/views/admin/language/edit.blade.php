@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{__('Edit')}}: {{__('Language')}}</h4>
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
        <a href="#">{{__('Language Management')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{$language->name}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Edit')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{__('Edit')}}: {{__('Language')}}</div>
          <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.language.index')}}">
            <span class="btn-label">
              <i class="fas fa-backward"></i>
            </span>
            {{__('back')}}
          </a>
        </div>
        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">

              <form id="ajaxForm" class="" action="{{route('admin.language.update')}}" method="post">
                @csrf
                <input type="hidden" name="language_id" value="{{$language->id}}">
                <div class="form-group">
                  <label for="">{{__('Name')}} **</label>
                  <input type="text" class="form-control" name="name" placeholder="{{__('Name')}}..." value="{{$language->name}}">
                  <p id="errname" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('code')}} **</label>
                  <input readonly type="text" class="form-control" name="code" placeholder="{{__('code')}}..." value="{{$language->code}}">
                  <p id="errcode" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Direction')}} **</label>
                  <select name="direction" class="form-control">
                      <option value="" selected disabled>{{__('Direction')}}...</option>
                      <option value="0" {{$language->rtl == 0 ? 'selected' : ''}}>{{__('LTR (Left to Right)')}}</option>
                      <option value="1" {{$language->rtl == 1 ? 'selected' : ''}}>{{__('RTL (Right to Left)')}}</option>
                  </select>
                  <p id="errdirection" class="mb-0 text-danger em"></p>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="form">
            <div class="form-group from-show-notify row">
              <div class="col-12 text-center">
                <button type="submit" id="submitBtn" class="btn btn-success">{{__('Update')}}</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

@endsection
