@extends('admin.layout')
@section('content')

<div class="page-header">
   <h4 class="page-title">{{__('Select Popup type')}}</h4>
   <ul class="breadcrumbs">
      <li class="nav-home">
         <a href="#">
         <i class="flaticon-home"></i>
         </a>
      </li>
      <li class="separator">
         <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
         <a href="#">{{__('Announcement Popup')}}</a>
      </li>
      <li class="separator">
         <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
         <a href="#">{{__('Type')}}</a>
      </li>
   </ul>
</div>
<div class="product-type">

    <div class="row">
        <div class="col-lg-3">
            <a href="{{route('admin.popup.create') . '?type=1'}}" class="d-block">
                <div class="card card-stats">
                    <div class="card-body ">
                        <img src="{{asset('assets/admin/img/popups/popup-1.jpg')}}" alt="" width="100%">
                        <h5 class="text-center text-white mt-2 mb-0">Type - 1 <span class="d-block mt-1 text-warning">{{__('Image')}}</span></h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3">
            <a href="{{route('admin.popup.create') . '?type=2'}}" class="d-block">
                <div class="card card-stats">
                    <div class="card-body ">
                        <img src="{{asset('assets/admin/img/popups/popup-2.jpg')}}" alt="" width="100%">
                        <h5 class="text-center text-white mt-2 mb-0">Type - 2 <span class="d-block mt-1 text-warning">{{__('Text')}} + {{__('Background Image')}}</span></h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3">
            <a href="{{route('admin.popup.create') . '?type=3'}}" class="d-block">
                <div class="card card-stats">
                    <div class="card-body ">
                        <img src="{{asset('assets/admin/img/popups/popup-3.jpg')}}" alt="" width="100%">
                        <h5 class="text-center text-white mt-2 mb-0">Type - 3 <span class="d-block mt-1 text-warning">{{__('Newsletter')}} + {{__('Background Image')}}</span></h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3">
            <a href="{{route('admin.popup.create') . '?type=4'}}" class="d-block">
                <div class="card card-stats">
                    <div class="card-body ">
                        <img src="{{asset('assets/admin/img/popups/popup-4.jpg')}}" alt="" width="100%">
                        <h5 class="text-center text-white mt-2 mb-0">Type - 4 <span class="d-block mt-1 text-warning">{{__('Text')}} + {{__('Image')}}</span></h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3">
            <a href="{{route('admin.popup.create') . '?type=5'}}" class="d-block">
                <div class="card card-stats">
                    <div class="card-body ">
                        <img src="{{asset('assets/admin/img/popups/popup-5.jpg')}}" alt="" width="100%">
                        <h5 class="text-center text-white mt-2 mb-0">Type - 5 <span class="d-block mt-1 text-warning">{{__('Newsletter')}} + {{__('Image')}}</span></h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3">
            <a href="{{route('admin.popup.create') . '?type=6'}}" class="d-block">
                <div class="card card-stats">
                    <div class="card-body ">
                        <img src="{{asset('assets/admin/img/popups/popup-6.jpg')}}" alt="" width="100%">
                        <h5 class="text-center text-white mt-2 mb-0">Type - 6 <span class="d-block mt-1 text-warning">{{__('Countdown')}} + {{__('Background Image')}}</span></h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3">
            <a href="{{route('admin.popup.create') . '?type=7'}}" class="d-block">
                <div class="card card-stats">
                    <div class="card-body ">
                        <img src="{{asset('assets/admin/img/popups/popup-7.jpg')}}" alt="" width="100%">
                        <h5 class="text-center text-white mt-2 mb-0">Type - 7 <span class="d-block mt-1 text-warning">{{__('Countdown')}} + {{__('Image')}}</span></h5>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
