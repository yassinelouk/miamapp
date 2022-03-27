@extends('admin.layout')

@if(!empty($data->language) && $data->language->rtl == 1)
@section('styles')
<style>
    form input,
    form textarea,
    form select {
        direction: rtl;
    }
    .nicEdit-main {
        direction: rtl;
        text-align: right;
    }
</style>
@endsection
@endif

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{__('Edit')}}: {{__('Category')}}</h4>
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
        <a href="#">{{__('Items Management')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Edit')}}: {{__('Category')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{__('Edit')}}: {{__('Category')}}</div>
          <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.category.index') . '?language=' . request()->input('language')}}">
            <span class="btn-label">
              <i class="fas fa-backward"></i>
            </span>
            Back
          </a>
        </div>
        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form id="ajaxForm"  action="{{route('admin.category.update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <div class="col-lg-12 col-sm-10">
                    <div class="form-group">
                      <div class="mb-2">
                        <label for="image"><strong>{{__('Image')}}</strong></label>
                      </div>
                      <div class="showImage mb-3">
                        @if (!empty($data->image))
                          <a class="remove-image" data-type="pcategory"><i class="far fa-times-circle"></i></a>
                        @endif
                        <img src="{{!empty($data->image) ? asset('assets/front/img/category/'.$data->image) : asset('assets/admin/img/noimage.jpg')}}" alt="..." class="img-thumbnail breadcumb">
                      </div>
                      <input type="file" name="image" id="image" class="form-control image">
                      <p id="errimage" class="mb-0 text-danger em"></p>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="">{{__('Name')}} **</label>
                  <input type="text" class="form-control" name="name" value="{{$data->name}}" placeholder="{{__('Name')}}">
                  <p id="errname" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Tax')}}</label>
                  <input type="text" class="form-control" name="tax" value="{{$data->tax}}" placeholder="{{__('Tax')}} (%)..." autocomplete="off">
                  <p id="errtax" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Type')}} **</label>
                  <select name="type" class="form-control">
                        <option value="0" @if( $data->type == 0 ) selected  @endif>{{__('drink')}}</option>
                        <option value="1" @if( $data->type == 1 ) selected  @endif>{{__('dish')}}</option>
                  </select>

                    <p id="errtype" class="mb-0 text-danger em"></p>
              </div>
                <input type="hidden" name="category_id" value="{{$data->id}}">

                <div class="form-group">
                  <label for="">{{__('Status')}} **</label>
                  <select class="form-control ltr" name="status">
                    <option value="" selected disabled>{{__('Status')}}</option>
                    <option value="1" {{$data->status ==1 ? 'selected' : ''}}>{{__('Active')}}</option>
                    <option value="0" {{$data->status ==0 ? 'selected' : ''}}>{{__('Deactive')}}</option>
                  </select>
                  <p id="errstatus" class="mb-0 text-danger em"></p>
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

@section('scripts')
<script>
$(function ($) {
  "use strict";

    $(".remove-image").on('click', function(e) {
        e.preventDefault();
        $(".request-loader").addClass("show");

        let type = $(this).data('type');
        let fd = new FormData();
        fd.append('type', type);
        fd.append('pcategory_id', {{$data->id}});

        $.ajax({
            url: "{{route('admin.pcategory.rmv.img')}}",
            data: fd,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function(data) {
                if (data == "success") {
                    window.location = "{{url()->current() . '?language=' . $data->language->code}}";
                }
            }
        })
    });
});
</script>
@endsection
