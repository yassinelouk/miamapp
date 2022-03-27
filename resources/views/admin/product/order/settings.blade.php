@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">{{__('Settings')}}</h4>
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
            <a href="#">{{__('Orders')}}</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">{{__('Settings')}}</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-title">{{__('Settings')}}</div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-5 pb-5">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3">
                        <form action="{{route('admin.reset.token')}}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>{{__('Token No. Restarts From')}} **</label>
                                <div class="row">
                                    <div class="col-md-9 col-sm-12 mt-2">
                                        <input type="number" class="form-control" name="token_no" placeholder="{{__('Token No. Restarts From')}}" value="{{$bs->token_no_start}}" required>
                                    </div>
                                    <div class="col-3 mt-2">
                                        <button type="submit" class="btn btn-success">{{__('Reset')}}</button>
                                    </div>
                                </div>
                                <p class="text-warning mb-0">
                                    {{__('With each order from Table Token No. will be increased by one')}}.
                                    <br>
                                    {{__('You can change the starting point anytime')}}.
                                </p>
                            </div>
                        </form>

                        <form id="settingsForm" action="{{route('admin.order.update.settings')}}" method="post">
                            @csrf
                            <div class="form-group">
                                <label>{{__('Postal Code Based Delivery')}} **</label>
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="postal_code" value="1" class="selectgroup-input" {{$bs->postal_code == 1 ? 'checked' : ''}}>
                                        <span class="selectgroup-button">{{__('Enable')}}</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="postal_code" value="0" class="selectgroup-input" {{$bs->postal_code == 0 ? 'checked' : ''}}>
                                        <span class="selectgroup-button">{{__('Disable')}}</span>
                                    </label>
                                </div>
                                <p class="text-warning mb-0">{{__('If you disable it, then you will be able to set shipping / delivery charges without postal code')}}.</p>
                                @if ($errors->has('postal_code'))
                                <p class="mb-0 text-danger">{{$errors->first('postal_code')}}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>{{__('Delivery Date & Time Field')}} **</label>
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="delivery_date_time_status" value="1" class="selectgroup-input" {{$be->delivery_date_time_status == 1 ? 'checked' : ''}}>
                                        <span class="selectgroup-button">{{__('Enable')}}</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="delivery_date_time_status" value="0" class="selectgroup-input" {{$be->delivery_date_time_status == 0 ? 'checked' : ''}}>
                                        <span class="selectgroup-button">{{__('Disable')}}</span>
                                    </label>
                                </div>
                                <p class="text-warning mb-0">{{__('This will decide whether delivery date / time fields will be shown in checkout page')}}.</p>
                                @if ($errors->has('delivery_date_time_status'))
                                <p class="mb-0 text-danger">{{$errors->first('delivery_date_time_status')}}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>{{__('Delivery Date / Time Field Validation')}} **</label>
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="delivery_date_time_required" value="1" class="selectgroup-input" {{$be->delivery_date_time_required == 1 ? 'checked' : ''}}>
                                        <span class="selectgroup-button">{{__('Required')}}</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="delivery_date_time_required" value="0" class="selectgroup-input" {{$be->delivery_date_time_required == 0 ? 'checked' : ''}}>
                                        <span class="selectgroup-button">{{__('Optional')}}</span>
                                    </label>
                                </div>
                                <p class="text-warning mb-0">{{__('This will decide whether delivery date / time fields are required or optional')}}.</p>
                                @if ($errors->has('delivery_date_time_required'))
                                <p class="mb-0 text-danger">{{$errors->first('delivery_date_time_required')}}</p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="form">
                    <div class="form-group from-show-notify row">
                        <div class="col-12 text-center">
                            <button form="settingsForm" type="submit" class="btn btn-success">{{__('Update')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
