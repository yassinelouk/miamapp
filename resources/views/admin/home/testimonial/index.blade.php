@extends('admin.layout')

@if(!empty($abs->language) && $abs->language->rtl == 1)
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
    <h4 class="page-title">{{__('Testimonials')}}</h4>
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
        <a href="#">{{__('Home Page')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Testimonials')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-10">
                    <div class="card-title">{{__('Title')}}</div>
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
        <form class="" action="{{route('admin.testimonialtext.update', $lang_id)}}" method="post" enctype="multipart/form-data">
          @csrf
          <div class="card-body">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="form-group">
                      <div class="col-12 mb-2">
                        <label for="image"><strong>{{__('Background Image')}}</strong></label>
                      </div>
                      <div class="col-md-12 showImage mb-3">
                        <img src="{{$abe->testimonial_bg_img ? asset('assets/front/img/'.$abe->testimonial_bg_img) : asset('assets/admin/img/noimage.jpg')}}" alt="..." class="img-thumbnail breadcumb">
                      </div>
                      <input type="file" name="testimonial_bg_img" id="image" class="form-control image">
                      <p id="errimage" class="mb-0 text-danger em"></p>
                    </div>
                </div>

              <div class="col-lg-6 offset-lg-3">
                <div class="form-group">
                  <label>{{__('Title')}} **</label>
                  <input class="form-control" name="testimonial_section_title" value="{{$abs->testimonial_title}}" placeholder="{{__('Title')}}...">
                  @if ($errors->has('testimonial_section_title'))
                    <p class="mb-0 text-danger">{{$errors->first('testimonial_section_title')}}</p>
                  @endif
                </div>
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

      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{__('Testimonials')}}</div>
          <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus"></i> {{__('add new')}}</a>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($testimonials) == 0)
                <h3 class="text-center">{{__('NO TESTIMONIAL FOUND')}}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{__('Image')}}</th>
                        <th scope="col">{{__('Name')}}</th>
                        <th scope="col">{{__('Rank')}}</th>
                        <th scope="col">{{__('Serial Number')}}</th>
                        <th scope="col">{{__('Action')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($testimonials as $key => $testimonial)
                        <tr>
                          <td>{{$loop->iteration}}</td>
                          <td><img src="{{asset('assets/front/img/testimonials/'.$testimonial->image)}}" alt="" width="40"></td>
                          <td>{{convertUtf8($testimonial->name)}}</td>
                          <td>{{convertUtf8($testimonial->rank)}}</td>
                          <td>{{$testimonial->serial_number}}</td>
                          <td class="d-flex align-items-center" >
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.testimonial.edit', $testimonial->id) . '?language=' . request()->input('language')}}">
                            <span class="btn-label">
                              <i class="fas fa-edit"></i>
                            </span>
                            {{__('Edit')}}
                            </a>
                            <form class="deleteform d-inline-block" action="{{route('admin.testimonial.delete')}}" method="post">
                              @csrf
                              <input type="hidden" name="testimonial_id" value="{{$testimonial->id}}">
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


  <!-- Create Testimonial Modal -->
  <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">{{__('add new')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <form id="ajaxForm" class="modal-form" action="{{route('admin.testimonial.store')}}" method="POST">
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
              <label for="">{{__('Comment')}} **</label>
              <textarea class="form-control" name="comment" rows="3" cols="80" placeholder="{{__('Comment')}}..."></textarea>
              <p id="errcomment" class="mb-0 text-danger em"></p>
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
              <label for="">{{__('Rating')}} ** </label>
              <input type="text" class="form-control ltr" name="rating" value="" placeholder="{{__('Rating')}}...">
              <p id="errrating" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">{{__('Serial Number')}} **</label>
              <input type="number" class="form-control ltr" name="serial_number" value="" placeholder="{{__('Serial Number')}}...">
              <p id="errserial_number" class="mb-0 text-danger em"></p>
              <p class="text-warning"><small>{{__('The higher the serial number is, the later the testimonial will be shown')}}.</small></p>
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
@endsection
