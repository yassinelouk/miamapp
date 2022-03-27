@extends('admin.layout')

@section('content')


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


  <div class="page-header">
    <h4 class="page-title">{{__('Bottom Links')}}</h4>
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
        <a href="#">{{__('Footer')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Bottom Links')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card-title d-inline-block">{{__('Bottom Links')}}</div>
                </div>
                <div class="col-lg-3">
                    @if (!empty($langs))
                        <select name="language" class="form-control" onchange="window.location='{{url()->current() . '?language='}}'+this.value">
                            <option value="" selected disabled>{{__('Select a Language')}}</option>
                            @foreach ($langs as $lang)
                                <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>{{$lang->name}}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
                    <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#createModalB"><i class="fas fa-plus"></i> {{__('add new')}}</a>
                </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($bottoms) == 0)
                <h3 class="text-center">{{__('NO BOTTOM LINK FOUND')}}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{__('Name')}}</th>
                        <th scope="col">URL</th>
                        <th scope="col">{{__('Action')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($bottoms as $key => $aulink)
                        <tr>
                          <td>{{$loop->iteration}}</td>
                          <td>{{convertUtf8($aulink->name)}}</td>
                          <td>{{$aulink->url}}</td>
                          <td>
                            <a class="btn btn-secondary btn-sm editbtn" href="#editModalb" data-toggle="modal" data-ulink_id="{{$aulink->id}}" data-name="{{$aulink->name}}" data-url="{{$aulink->url}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              {{__('Edit')}}
                            </a>
                            <form class="deleteform d-inline-block" action="{{route('admin.blink.delete')}}" method="post">
                              @csrf
                              <input type="hidden" name="bottom_id" value="{{$aulink->id}}">
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


  <!-- Create bottom Link Modal -->
  <div class="modal fade" id="createModalB" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">{{__('add new')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="ajaxForm" class="modal-form create" action="{{route('admin.blink.store')}}" method="POST">
            @csrf
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
              <label for="">URL **</label>
              <input class="form-control ltr" name="url" placeholder="URL...">
              <p id="errurl" class="mb-0 text-danger em"></p>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
          <button id="submitBtn" type="button" class="btn btn-primary">{{__('Submit')}}</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Bottom Link Modal -->
  <div class="modal fade" id="editModalb" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">{{__('Edit')}}: {{__('Bottom Link')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="ajaxEditForm" action="{{route('admin.blink.update')}}" method="POST">
            @csrf
            <input id="inulink_id" type="hidden" name="link_id" value="">
            <div class="form-group">
              <label for="">{{__('Name')}} **</label>
              <input id="inname" type="text" class="form-control" name="name" value="" placeholder="{{__('Name')}}">
              <p id="eerrname" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">URL **</label>
              <input id="inurl" class="form-control ltr" name="url" placeholder="URL...">
              <p id="eerrurl" class="mb-0 text-danger em"></p>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
          <button id="updateBtn" type="button" class="btn btn-primary">{{__('Submit')}}</button>
        </div>
      </div>
    </div>
  </div>
@endsection
