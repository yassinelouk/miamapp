@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">{{__('Announcement Popup')}}</h4>
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
            <a href="#">{{__('Announcement Popup')}}</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">{{__('add new')}}</a>
        </li>
    </ul>
</div>
@php
    $type = request()->input('type');
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-10">
                        <div class="card-title">{{__('Add')}}: {{__('Popup')}} (Type - {{$type}})</div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-5 pb-5">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3">

                        <form id="ajaxForm" class="modal-form" action="{{route('admin.popup.store')}}" method="post" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="type" value="{{$type}}">

                            @if ($type == 1 || $type == 4 || $type == 5 || $type == 7)

                                {{-- Image Part --}}
                                <div class="form-group">
                                    <div class="mb-2">
                                      <label for="image"><strong>{{__('Image')}} **</strong></label>
                                    </div>
                                    <div class="showImage mb-3">
                                      <img src="{{asset('assets/admin/img/noimage.jpg')}}" alt="..." class="img-thumbnail">
                                    </div>
                                    <input type="file" name="image" id="image" class="form-control image">
                                    <p class="text-warning mb-0">{{__('JPG, PNG, JPEG images are allowed')}}</p>
                                    <p id="errimage" class="mb-0 text-danger em"></p>
                                </div>
                            @endif

                            @if ($type == 2 || $type == 3 || $type == 6)
                            {{-- Background Image Part --}}
                            <div class="form-group">
                                <div class="mb-2">
                                  <label for="image"><strong>{{__('Background Image')}} **</strong></label>
                                </div>
                                <div class="showImage mb-3">
                                  <img src="{{asset('assets/admin/img/noimage.jpg')}}" alt="..." class="img-thumbnail">
                                </div>
                                <input type="file" name="background_image" id="image" class="form-control image">
                                <p class="text-warning mb-0">{{__('JPG, PNG, JPEG images are allowed')}}</p>
                                <p id="errbackground_image" class="mb-0 text-danger em"></p>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">{{__('Language')}} **</label>
                                        <select name="language_id" class="form-control">
                                            <option value="" selected disabled>{{__('Select a language')}}</option>
                                            @foreach ($langs as $lang)
                                            <option value="{{$lang->id}}">{{$lang->name}}</option>
                                            @endforeach
                                        </select>
                                        <p id="errlanguage_id" class="mb-0 text-danger em"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">{{__('Popup')}}: {{__('Name')}} **</label>
                                        <input type="text" class="form-control" name="name" value="" placeholder="{{__('Name')}}...">
                                        <p class="text-warning mb-0">{{__('This will not be shown in the popup in Website, it will help you to indentify the popup in Admin Panel')}}.</p>
                                        <p id="errname" class="mb-0 text-danger em"></p>
                                    </div>
                                </div>
                            </div>


                            @if ($type == 2 || $type == 3 || $type == 4 || $type == 5 || $type == 6 || $type == 7)
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="">{{__('Title')}} </label>
                                        <input type="text" class="form-control" name="title" value="" placeholder="{{__('Title')}}...">
                                        <p id="errtitle" class="mb-0 text-danger em"></p>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="">{{__('Text')}} </label>
                                        <textarea class="form-control" name="text" cols="30" rows="3" placeholder="{{__('Text')}}..."></textarea>
                                        <p id="errtext" class="mb-0 text-danger em"></p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($type == 6 || $type == 7)
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">{{__('End Date')}} **</label>
                                        <input type="text" class="form-control ltr datepicker" name="end_date" value="" placeholder="{{__('End Date')}}..." readonly autocomplete="off">
                                        <p id="errend_date" class="mb-0 text-danger em"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">{{__('End Time')}} **</label>
                                        <input type="text" class="form-control ltr timepicker" name="end_time" value="" placeholder="{{__('End Time')}}..." readonly autocomplete="off">
                                        <p id="errend_time" class="mb-0 text-danger em"></p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($type == 2 || $type == 3)
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>{{__('Background Color')}} **</label>
                                        <input class="jscolor form-control ltr" name="background_color" value="451d53">
                                        <p class="em text-danger mb-0" id="errbackground_color"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">{{__('Background Color')}} - {{__('Opacity')}} **</label>
                                        <input type="number" class="form-control ltr" name="background_opacity" value="" placeholder="{{__('Opacity')}}">
                                        <p id="errbackground_opacity" class="mb-0 text-danger em"></p>
                                        <ul class="mb-0">
                                            <li class="text-warning mb-0">{{__('Value must be between 0 to 1')}}</li>
                                            <li class="text-warning mb-0">{{__('The more the opacity value is, the less the transparency level will be')}}.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($type == 7)
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>{{__('Background Color')}} **</label>
                                        <input class="jscolor form-control ltr" name="background_color" value="451d53">
                                        <p class="em text-danger mb-0" id="errbackground_color"></p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($type == 2 || $type == 3 || $type == 4 || $type == 5 || $type == 6 || $type == 7)
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">{{__('Button')}}: {{__('Text')}} </label>
                                        <input type="text" class="form-control" name="button_text" value="" placeholder="{{__('Text')}}...">
                                        <p id="errbutton_text" class="mb-0 text-danger em"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">{{__('Button')}}: {{__('Color')}} </label>
                                        <input type="text" class="form-control jscolor ltr" name="button_color" value="451d53" placeholder="{{__('Color')}}...">
                                        <p id="errbutton_color" class="mb-0 text-danger em"></p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($type == 2 || $type == 4 || $type == 6 || $type == 7)
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">{{__('Button')}}: URL </label>
                                            <input type="text" class="form-control ltr" name="button_url" value="" placeholder="URL...">
                                            <p id="errbutton_url" class="mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="">{{__('Delay (miliseconds)')}} **</label>
                                <input type="number" class="form-control ltr" name="delay" value="" placeholder="{{__('Delay (miliseconds)')}}...">
                                <p id="errdelay" class="mb-0 text-danger em"></p>
                                <p class="text-warning mb-0">{{__('This will decide the delay time to show the popup')}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Serial Number')}} **</label>
                                <input type="number" class="form-control ltr" name="serial_number" value="" placeholder="{{__('Serial Number')}}...">
                                <p id="errserial_number" class="mb-0 text-danger em"></p>
                                <ul>
                                    <li class="text-warning mb-0">{{__('If there are multiple active popups, then the popups will be shown in the website according to Serial Number')}}</li>
                                    <li class="text-warning">{{__('The higher the serial number, the later the popups will be visible in Website')}}</li>
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="form-group from-show-notify row">
                    <div class="col-12 text-center">
                        <button id="submitBtn" type="button" class="btn btn-primary">{{__('Submit')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image LFM Modal -->
<div class="modal fade lfm-modal" id="lfmModal1" tabindex="-1" role="dialog" aria-labelledby="lfmModalTitle" aria-hidden="true">
    <i class="fas fa-times-circle"></i>
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <iframe src="{{url('laravel-filemanager')}}?serial=1" style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
            </div>
        </div>
    </div>
</div><!-- Image LFM Modal -->
<div class="modal fade lfm-modal" id="lfmModal2" tabindex="-1" role="dialog" aria-labelledby="lfmModalTitle" aria-hidden="true">
    <i class="fas fa-times-circle"></i>
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <iframe src="{{url('laravel-filemanager')}}?serial=2" style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
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
                    $("form.modal-form input").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form.modal-form select").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form.modal-form textarea").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form.modal-form .summernote").each(function() {
                        $(this).siblings('.note-editor').find('.note-editable').addClass('rtl text-right');
                    });

                } else {
                    $("form.modal-form input, form.modal-form select, form.modal-form textarea").removeClass('rtl');
                    $("form.modal-form .summernote").siblings('.note-editor').find('.note-editable').removeClass('rtl text-right');
                }
            })
        });
    });
</script>
@endsection