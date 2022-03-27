@extends('admin.layout')

@section('content')
<div class="page-header">
   <h4 class="page-title">Offline Gateways</h4>
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
         <a href="#">{{__('Payment Gateways')}}</a>
      </li>
      <li class="separator">
         <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
         <a href="#">{{__('Offline Gateways')}}</a>
      </li>
   </ul>
</div>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header">
            <div class="row">
               <div class="col-lg-6">
                  <div class="card-title d-inline-block">{{__('Offline Gateways')}}</div>
               </div>
               <div class="col-lg-6 mt-2 mt-lg-0">
                  <a href="#" class="btn btn-primary float-lg-right float-left btn-sm" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus"></i> {{__('add new')}}</a>
               </div>
            </div>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-lg-12">
                  @if (count($ogateways) == 0)
                  <h3 class="text-center">{{__('NO OFFLINE PAYMENT GATEWAY FOUND')}}</h3>
                  @else
                  <div class="table-responsive">
                     <table class="table table-striped mt-3" id="basic-datatables">
                        <thead>
                           <tr>
                              <th scope="col">{{__('Name')}}</th>
                              <th scope="col">{{__('Status')}}</th>
                              <th scope="col">{{__('Serial Number')}}</th>
                              <th scope="col">{{__('Action')}}</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach ($ogateways as $key => $ogateway)
                           <tr>
                              <td>
                                {{convertUtf8($ogateway->name)}}
                              </td>
                              <td>
                                <form id="productForm{{$ogateway->id}}" class="d-inline-block" action="{{route('admin.offline.status')}}" method="post">
                                @csrf
                                <input type="hidden" name="ogateway_id" value="{{$ogateway->id}}">
                                <input type="hidden" name="type" value="product">
                                <select class="form-control form-control-sm form-rounded border-0 text-light w-auto {{$ogateway->status == 1 ? 'bg-success' : 'bg-danger'}}" name="status" onchange="document.getElementById('productForm{{$ogateway->id}}').submit();">
                                    <option value="1" {{$ogateway->status == 1 ? 'selected' : ''}}>{{__('Active')}}</option>
                                    <option value="0" {{$ogateway->status == 0 ? 'selected' : ''}}>{{__('Deactive')}}</option>
                                </select>
                                </form>
                              </td>
                              <td>{{$ogateway->serial_number}}</td>
                              <td class="d-flex align-items-center" >
                                <a class="btn btn-secondary btn-sm" href="#editModal" data-toggle="modal" data-ogateway_id="{{$ogateway->id}}" data-name="{{$ogateway->name}}" data-short_description="{{$ogateway->short_description}}" data-instructions="{{replaceBaseUrl($ogateway->instructions)}}" data-is_receipt="{{$ogateway->is_receipt}}" data-serial_number="{{$ogateway->serial_number}}">
                                    <span class="btn-label">
                                    <i class="fas fa-edit"></i>
                                    </span>
                                    {{__('Edit')}}
                                </a>

                                 <form class="deleteform d-inline-block" action="{{route('admin.offline.gateway.delete')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="ogateway_id" value="{{$ogateway->id}}">
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


<!-- Create Offline Gateway Modal -->
@includeIf('admin.gateways.offline.create')



<!-- Edit Package Modal -->
@includeIf('admin.gateways.offline.edit')


@endsection
