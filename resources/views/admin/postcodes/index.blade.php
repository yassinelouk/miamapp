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
    <h4 class="page-title">{{__('Postal Codes')}}</h4>
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
        <a href="#">{{__('My Orders')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Postal Codes')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card-title d-inline-block">{{__('Postal Codes')}}</div>
                </div>
                <div class="col-lg-3">
                    @if (!empty($langs))
                        <select name="language" class="form-control" onchange="window.location='{{url()->current() . '?language='}}'+this.value">
                            <option value="" selected disabled>Select a Language</option>
                            @foreach ($langs as $lang)
                                <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>{{$lang->name}}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
                    <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus"></i> {{__('add new')}}</a>
                    <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete" data-href="{{route('admin.postalcode.bulk.delete')}}"><i class="flaticon-interface-5"></i> {{__('Delete')}}</button>
                </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-primary text-center py-4 normal">
                  <h4 class="mb-0">
                    {{__("This page will be available if 'postal code' is enabled by")}} <strong>{{__('Admin')}} ({{__('Order Management')}} > {{__('Settings')}})</strong>. {{__('For demo version we are always showing this page')}}.
                  </h4>
                </div>
              @if (count($postcodes) == 0)
                <h3 class="text-center">{{__('NO POSTAL CODE FOUND')}}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3" id="basic-datatables">
                    <thead>
                      <tr>
                        <th scope="col">
                            <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">{{__('Name')}}</th>
                        <th scope="col">{{__('Post Code')}}</th>
                        <th scope="col">{{__('Shipping Charges')}}</th>
                        <th scope="col">{{__('Serial Number')}}</th>
                        <th scope="col">{{__('Action')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($postcodes as $key => $postcode)
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{$postcode->id}}">
                          </td>
                          <td>{{$postcode->title}}</td>
                          <td>{{$postcode->postcode}}</td>
                          <td>{{$postcode->charge}}</td>
                          <td>{{$postcode->serial_number}}</td>
                          <td class='d-flex align-items-center' >
                            <a class="btn btn-secondary btn-sm" href="#editModal" data-toggle="modal" data-postcode_id="{{$postcode->id}}" data-title="{{$postcode->title}}" data-postcode="{{$postcode->postcode}}" data-charge="{{$postcode->charge}}" data-serial_number="{{$postcode->serial_number}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              {{__('Edit')}}
                            </a>
                            <form class="d-inline-block" action="{{route('admin.postalcode.delete')}}" method="post">
                              @csrf
                              <input type="hidden" name="postcode_id" value="{{$postcode->id}}">
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


  <!-- Create Faq Modal -->
  <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">{{__('Add Postal Code')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="ajaxForm" class="modal-form create" action="{{route('admin.postalcode.store')}}" method="POST">
            @csrf
            <div class="form-group">
                <label for="">{{__('Language')}} **</label>
                <select name="language_id" class="form-control">
                    <option value="" selected disabled>Select a language</option>
                    @foreach ($langs as $lang)
                        <option value="{{$lang->id}}">{{$lang->name}}</option>
                    @endforeach
                </select>
                <p id="errlanguage_id" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">{{__('Name')}} </label>
              <input type="text" class="form-control" name="title" value="" placeholder="Enter title">
              <p id="errtitle" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">{{__('Post Code')}} **</label>
              <input type="text" class="form-control ltr" name="postcode" value="" placeholder="Enter postcode">
              <p id="errpostcode" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">{{__('Shipping Charge')}} ({{$be->base_currency_text}}) **</label>
              <input type="text" class="form-control ltr" name="charge" value="" placeholder="Enter delivery charge">
              <p id="errcharge" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">{{__('Serial Number')}} **</label>
              <input type="number" class="form-control ltr" name="serial_number" value="" placeholder="Enter Serial Number">
              <p id="errserial_number" class="mb-0 text-danger em"></p>
              <p class="text-warning"><small>The higher the serial number is, the later the Postal Code will be shown.</small></p>
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

  <!-- Edit Faq Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">{{__('Edit')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="ajaxEditForm" class="" action="{{route('admin.postalcode.update')}}" method="POST">
            @csrf
            <input id="inpostcode_id" type="hidden" name="postcode_id" value="">
            <div class="form-group">
              <label for="">{{__('Name')}} </label>
              <input id="intitle" type="text" class="form-control" name="title" value="" placeholder="{{__('Name')}}...">
              <p id="eerrtitle" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">{{__('Post Code')}} **</label>
              <input id="inpostcode" type="text" class="form-control" name="postcode" value="" placeholder="{{__('Post Code')}}...">
              <p id="eerrpostcode" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">{{__('Shipping Charge')}} **</label>
              <input id="incharge" type="text" class="form-control" name="charge" value="" placeholder="{{__('Shipping Charge')}}...">
              <p id="eerrcharge" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">{{__('Serial Number')}} **</label>
              <input id="inserial_number" type="number" class="form-control ltr" name="serial_number" value="" placeholder="{{__('Serial Number')}}">
              <p id="eerrserial_number" class="mb-0 text-danger em"></p>
              <p class="text-warning"><small>{{__('The higher the serial number is, the later the Postal Code will be shown')}}.</small></p>
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

@section('scripts')
  <script>
    $(document).ready(function() {

        // make input fields RTL
        $("select[name='language_id']").on('change', function() {
            $(".request-loader").addClass("show");
            let url = "{{url('/')}}/admin/rtlcheck/" + $(this).val();
            console.log(url);
            $.get(url, function(data) {
                $(".request-loader").removeClass("show");
                if (data == 1) {
                    $("form.create input").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form.create select").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form.create textarea").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form.create .nicEdit-main").each(function() {
                        $(this).addClass('rtl text-right');
                    });

                } else {
                    $("form.create input, form.create select, form.create textarea").removeClass('rtl');
                    $("form.create .nicEdit-main").removeClass('rtl text-right');
                }
            })
        });

    });
  </script>
@endsection
