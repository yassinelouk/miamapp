@extends('admin.layout')

@php
$langs = \App\Models\Language::all();
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
    <h4 class="page-title">{{__('Shipping Charge')}}</h4>
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
        <a href="#">{{__('Shipping Charge')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card-title d-inline-block">{{__('Shipping Charges')}}</div>
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
                </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
              <div class="col-12">
                  <div class="alert alert-primary text-center py-4 normal">
          <h4 class="mb-0">
            <strong>{{__("This page will be available if 'postal code' is disabled by")}} <strong>{{__('Admin')}} ({{__('Order Management')}} > {{__('Settings')}})</strong>. {{__('For demo version we are always showing this page')}}.</strong>
          </h4>
        </div>
        <div class="alert alert-primary text-center py-4 normal">
          <h4 class="mb-0">
            <strong>{{__("If you dont want to show any shipping charge in checkout page, then don't add any shipping charge here")}}</strong>
          </h4>
        </div>
              </div>
            <div class="col-lg-12">
              @if (count($shippings) == 0)
                <h3 class="text-center">{{__('No Shipping Charge')}}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">{{__('Name')}}</th>
                        <th scope="col">{{__('Description')}}</th>
                        <th scope="col">{{__('Shipping Charge')}} ({{$be->base_currency_text}})</th>
                        <th scope="col">{{__('Action')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($shippings as $key => $shipping)
                        <tr>
                          <td>
                            {{$shipping->title}}
                          </td>
                          <td>
                            {{$shipping->text}}
                          </td>

                          <td>
                            {{$shipping->charge}}
                          </td>

                          <td>
                            <a class="btn btn-secondary btn-sm editbtn" href="{{route('admin.shipping.edit', $shipping->id) . '?language=' . request()->input('language')}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              {{__('Edit')}}
                            </a>
                            <form class="deleteform d-inline-block" action="{{route('admin.shipping.delete')}}" method="post">
                              @csrf
                              <input type="hidden" name="shipping_id" value="{{$shipping->id}}">
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
              {{$shippings->appends(['language' => request()->input('language')])->links()}}
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
          <h5 class="modal-title" id="exampleModalLongTitle">{{__('Add Shipping Charge')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <form id="ajaxForm" class="modal-form" action="{{route('admin.shipping.store')}}" method="POST">
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
              <label for="">{{__('Name')}} **</label>
              <input type="text" class="form-control" name="title" value="" placeholder="{{__('Name')}}...">
              <p id="errtitle" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">{{__('Description')}}</label>
              <input type="text" class="form-control" name="text" value="" placeholder="{{__('Description')}}...">
            </div>

            <div class="form-group">
              <label for="">{{__('Shipping Charge')}} ({{$be->base_currency_text}}) **</label>
              <input type="text" class="form-control" name="charge" value="" placeholder="{{__('Shipping Charge')}}...">
              <p id="errcharge" class="mb-0 text-danger em"></p>
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
