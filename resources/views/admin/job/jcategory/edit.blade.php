@extends('admin.layout')

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
        <a href="#">{{__('Category')}}</a>
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
          <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.scategory.index')}}">
            <span class="btn-label">
              <i class="fas fa-backward" style="font-size: 12px;"></i>
            </span>
            {{__('back')}
          </a>
        </div>
        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form class="mb-3 dm-uploader drag-and-drop-zone" enctype="multipart/form-data" action="{{route('admin.scategory.uploadUpdate', $scategory->id)}}" method="POST">
                @csrf
                <div class="form-row px-2">
                  <div class="col-12 mb-2">
                    <label for=""><strong>{{__('Image')}} **</strong></label>
                  </div>
                  <div class="col-md-12 d-md-block d-sm-none mb-3">
                    <img src="{{asset('assets/front/img/service_category_icons/'.$scategory->image)}}" alt="..." class="img-thumbnail">
                  </div>
                  <div class="col-sm-12">
                    <div class="from-group mb-2">
                      <input type="text" class="form-control progressbar" aria-describedby="fileHelp" placeholder="No image uploaded..." readonly="readonly" />

                      <div class="progress mb-2 d-none">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                          role="progressbar"
                          style="width: 0%;"
                          aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
                          0%
                        </div>
                      </div>

                    </div>

                    <div class="mt-4">
                      <div role="button" class="btn btn-primary mr-2">
                        <i class="fa fa-folder-o fa-fw"></i> {{__('Browse Files')}}
                        <input type="file" title='Click to add Files'  />
                      </div>
                      <small class="status text-muted">{{__('Select a file or drag it over this area')}}..</small>
                    </div>
                  </div>
                </div>
              </form>

              <form id="ajaxForm" class="" action="{{route('admin.scategory.update')}}" method="post">
                @csrf
                <input type="hidden" name="scategory_id" value="{{$scategory->id}}">
                <div class="form-group">
                  <label for="">{{__('Name')}} **</label>
                  <input type="text" class="form-control" name="name" value="{{$scategory->name}}" placeholder="{{__('Name')}}..;">
                  <p id="errname" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Description')}} **</label>
                  <input type="text" class="form-control" name="short_text" value="{{$scategory->short_text}}" placeholder="{{__('Description')}}...">
                  <p id="errshort_text" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Status')}} **</label>
                  <select class="form-control" name="status">
                    <option value="" selected disabled>Select a status</option>
                    <option value="1" {{$scategory->status == 1 ? 'selected' : ''}}>{{__('Active'}}</option>
                    <option value="0" {{$scategory->status == 0 ? 'selected' : ''}}>{{__('Deactive')}}</option>
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
