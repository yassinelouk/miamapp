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

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/admin/css/categories.css') }}">
@endsection

@section('js')
  <script src={{ asset('assets/admin/js/dragula.min.js') }}></script>
  <script>
    $(function () {
        dragula([document.getElementById("drag-parent")]).on('drop', function (el) {
          let original_position = parseInt(el.children[0].children[0].attributes[3].value, 10);
          let new_position = 0;
            let position_logs = [];
            parent = document.getElementById('drag-parent');
            for (let i = 0; i < parent.children.length; i++) {
                if (parent.children[i] == el) {
                    new_position = i + 1;
                    el.children[0].children[0].attributes[3].value = new_position;
                }
            }
            for (let i = 0; i < parent.children.length; i++) {
              element_position = parseInt(parent.children[i].children[0].children[0].attributes[3].value, 10);
              if (original_position < new_position) {
                  if (element_position > original_position && element_position <= new_position && parent.children[i] != el) {
                      parent.children[i].children[0].children[0].attributes[3].value = i + 1;
                  }
              } else {
                  if (element_position < original_position && element_position >= new_position && parent.children[i] != el) {
                      parent.children[i].children[0].children[0].attributes[3].value = i + 1;
                  }
              }
            }
            for (let i = 0; i < parent.children.length; i++) {
                position_logs.push([parseInt(parent.children[i].children[0].children[0].attributes[2].value, 10), parseInt(parent.children[i].children[0].children[0].attributes[3].value, 10)])
            }
            $.ajax({
              type: "POST",
              url: "{{ route('admin.category.update.positions') }}",
              data: {
                  _token: $('meta[name="csrf-token"]').attr("content"),
                  position_logs: position_logs,
              },
              success: function (response) {
                  console.log(response);
              },
              error: function (response) {
                  alert("Une erreur est survenue");
                  console.log(response);
              },
            });
        });
      })
  </script>
@endsection

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{__('Items')}} {{__('Categories')}}</h4>
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
        <a href="#">{{__('Categories')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card-title d-inline-block">{{__('Categories')}}</div>
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
                    <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus"></i> {{__('add new')}}</a>
                    <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete" data-href="{{route('admin.pcategory.bulk.delete')}}"><i class="flaticon-interface-5"></i> {{__('Delete')}}</button>
                </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($pcategories) == 0)
                <h3 class="text-center">{{__('NO PRODUCT CATEGORY FOUND')}}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">
                            <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">{{__('Name')}}</th>
                        <th scope="col">{{__('Status')}}</th>
                        <th scope="col">{{__('Type')}}</th>
                        <th scope="col">{{__('featured')}}</th>
                        <th scope="col">{{__('Action')}}</th>
                      </tr>
                    </thead>
                    <tbody id="drag-parent">
                      @foreach ($pcategories as $key => $category)
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{$category->id}}" data-pos="{{ $category->position }}">
                          </td>

                          <td>{{convertUtf8($category->name)}}</td>
                          <!--<td><img src="{{$category->image ? asset('assets/front/img/category/'.$category->image) : asset('assets/admin/img/noimage.jpg')}}" width="100" alt=""></td>-->

                          <td>
                            @if ($category->status == 1)
                              <h2 class="d-inline-block"><span class="badge badge-success px-4">{{__('Active')}}</span></h2>
                            @else
                              <h2 class="d-inline-block"><span class="badge badge-danger px-4">{{__('Deactive')}}</span></h2>
                            @endif
                          </td>

                          <td>{{$category->type == 0 ? __('drink') : __('dish')}}</td>
                          <td>
                              <form id="featureForm{{$category->id}}" action="{{route('admin.pcategory.feature')}}" method="POST">
                                  @csrf
                                  <input type="hidden" name="pcategory_id" value="{{$category->id}}">
                                  <select name="feature" id="" class="form-control-sm form-rounded border-0 text-light w-auto
                                    @if($category->is_feature == 1)
                                    bg-success
                                    @elseif ($category->is_feature == 0)
                                    bg-danger
                                    @endif
                                  " onchange="document.getElementById('featureForm{{$category->id}}').submit();">
                                      <option value="1" {{$category->is_feature == 1 ? 'selected' : ''}}>{{__('Yes')}}</option>
                                      <option value="0" {{$category->is_feature == 0 ? 'selected' : ''}}>{{__('No')}}</option>
                                  </select>
                              </form>
                          </td>
                          <td class="d-flex align-items-center" >
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.category.edit', $category->id) . '?language=' . request()->input('language')}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              {{__('Edit')}}
                            </a>
                            <form class="deleteform d-inline-block" action="{{route('admin.category.delete')}}" method="post">
                              @csrf
                              <input type="hidden" name="category_id" value="{{$category->id}}">
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
        <div class="card-footer">
          <div class="row">
            <div class="d-inline-block mx-auto">
              {{$pcategories->appends(['language' => request()->input('language')])->links()}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- Create Service Category Modal -->
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
          <form id="ajaxForm" class="modal-form" action="{{route('admin.category.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <!--<div class="col-lg-12">-->
              <!--  <div class="form-group">-->
              <!--    <div class="col-12 mb-2">-->
              <!--      <label for="image"><strong>{{__('Image')}}</strong></label>-->
              <!--    </div>-->
              <!--    <div class="col-md-12 showImage mb-3">-->
              <!--      <img src="{{asset('assets/admin/img/noimage.jpg')}}" alt="..." class="img-thumbnail">-->
              <!--    </div>-->
              <!--    <input type="file" name="image" id="image" class="form-control image">-->
              <!--    <p id="errimage" class="mb-0 text-danger em"></p>-->
              <!--  </div>-->
              <!--</div>-->
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
              <input type="text" class="form-control" name="name" value="" placeholder="{{__('Name')}}">
              <p id="errname" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
                <label for="">{{__('Type')}} **</label>

            <select name="type" class="form-control">
                  <option selected disabled>{{__('Select a type')}}</option>
                  <option value="0">{{__('drink')}}</option>
                  <option value="1">{{__('dish')}}</option>

              </select>

              <p id="errtype" class="mb-0 text-danger em"></p>


            </div>

            <div class="form-group">
              <label for="">{{__('Tax')}}</label>
              <input type="text" class="form-control" name="tax" value="" placeholder="{{__('Tax')}} (%)..." autocomplete="off">
              <p id="errtax" class="mb-0 text-danger em"></p>
            </div>

            <div class="form-group">
              <label for="">{{__('Status')}} **</label>
              <select class="form-control ltr" name="status">
                <option value="" selected disabled>{{__('Status')}}</option>
                <option value="1">{{__('Active')}}</option>
                <option value="0">{{__('Deactive')}}</option>
              </select>
              <p id="errstatus" class="mb-0 text-danger em"></p>
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
