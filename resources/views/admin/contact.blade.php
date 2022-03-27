@extends('admin.layout')

@php
$selLang = \App\Models\Language::where('code', request()->input('language'))->first();
@endphp
@if(!empty($selLang) && $selLang->rtl == 1)
@section('styles')
<style>
    form:not(.modal-form) input,
    form:not(.modal-form) textarea,
    form:not(.modal-form) select,
    select[name='language'] {
        direction: rtl;
    }
    form:not(.modal-form) .note-editor.note-frame .note-editing-area .note-editable {
        direction: rtl;
        text-align: right;
    }
</style>
@endsection
@endif

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{__('Contact Page')}}</h4>
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
        <a href="#">{{__('Website Pages')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Contact Page')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <form   enctype="multipart/form-data" action="{{route('admin.contact.update', $lang_id)}}" method="POST">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-10">
                        <div class="card-title">{{__('Contact Page')}}</div>
                    </div>
                    <div class="col-lg-2">
                        @if (!empty($langs))
                            <select name="language" class="form-control" onchange="window.location='{{url()->current() . '?language='}}'+this.value">
                                <option value="" selected disabled>{{__('Select a Language')}}</option>
                                @foreach ($langs as $lang)
                                    <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>{{$lang->name}}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>
          <div class="card-body pt-5 pb-5">
            <div class="row">
              <div class="col-lg-6 offset-lg-3">
                @csrf

                <div class="form-group">
                  <label>{{__('Title')}} **</label>
                  <input class="form-control" name="contact_form_title" value="{{$abs->contact_form_title}}" placeholder="{{__('Title')}}...">
                  @if ($errors->has('contact_form_title'))
                    <p class="mb-0 text-danger">{{$errors->first('contact_form_title')}}</p>
                  @endif
                </div>
                <div class="form-group">
                  <label>{{__('Information')}}: {{__('Title')}} **</label>
                  <input class="form-control" name="contact_info_title" value="{{$abs->contact_info_title}}" placeholder="{{__('Title')}}...">
                  @if ($errors->has('contact_info_title'))
                    <p class="mb-0 text-danger">{{$errors->first('contact_info_title')}}</p>
                  @endif
                </div>


                <div class="form-group">
                  <label>{{__('Contact Information Text')}} **</label>
                  <input class="form-control" name="contact_text" value="{{$abs->contact_text}}" placeholder="{{__('Contact Information Text')}}...">
                  @if ($errors->has('contact_text'))
                    <p class="mb-0 text-danger">{{$errors->first('contact_text')}}</p>
                  @endif
                </div>


                <div class="form-group">
                    <label>{{__('Address')}} **</label>
                    <textarea class="form-control" name="contact_address" rows="3">{{$abs->contact_address}}</textarea>
                    <p class="mb-0 text-warning">{{__('Use newline to seperate multiple addresses')}}.</p>
                    @if ($errors->has('contact_address'))
                      <p class="mb-0 text-danger">{{$errors->first('contact_address')}}</p>
                    @endif
                  </div>

                  <div class="form-group">
                    <label>{{__('Phone')}} **</label>
                    <input class="form-control" name="contact_number" data-role="tagsinput" value="{{$abs->contact_number}}" placeholder="{{__('Phone')}}...">
                    <p class="mb-0 text-warning">{{__('Use comma (,) to seperate multiple contact numbers')}}.</p>
                    @if ($errors->has('contact_number'))
                      <p class="mb-0 text-danger">{{$errors->first('contact_number')}}</p>
                    @endif
                  </div>

                  <div class="form-group">
                    <label>{{__('Email Address')}} **</label>
                    <input class="form-control ltr" name="contact_mails" data-role="tagsinput" value="{{$abs->contact_mails}}" placeholder="{{__('Email Address')}}">
                    <p class="mb-0 text-warning">{{__('Use comma (,) to seperate multiple contact mails')}}.</p>
                    @if ($errors->has('contact_mails'))
                      <p class="mb-0 text-danger">{{$errors->first('contact_mails')}}</p>
                    @endif
                  </div>

                  <div class="form-group">
                    <label>{{__('Latitude')}} </label>
                    <input class="form-control" name="latitude" value="{{$abs->latitude}}" placeholder="Google Map {{__('Latitude')}}...">
                    @if ($errors->has('latitude'))
                      <p class="mb-0 text-danger">{{$errors->first('latitude')}}</p>
                    @endif
                  </div>

                  <div class="form-group">
                    <label>{{__('Longitude')}}</label>
                    <input class="form-control" name="longitude" value="{{$abs->longitude}}" placeholder="Google Map {{__('Longitude')}}...">
                    @if ($errors->has('longitude'))
                      <p class="mb-0 text-danger">{{$errors->first('longitude')}}</p>
                    @endif
                  </div>

                  <div class="form-group">
                    <label>{{__('Map Zoom')}}</label>
                    <input class="form-control" name="map_zoom" value="{{$abs->map_zoom}}" placeholder="Google Map : {{__('Map Zoom')}}...">
                    @if ($errors->has('map_zoom'))
                      <p class="mb-0 text-danger">{{$errors->first('map_zoom')}}</p>
                    @endif
                  </div>
              </div>
            </div>
          </div>
          <div class="card-footer pt-3">
            <div class="form">
              <div class="form-group from-show-notify row">
                <div class="col-12 text-center">
                  <button id="displayNotif" class="btn btn-success">{{__('Update')}}</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection
