@extends('admin.layout')



@section('sidebar', 'overlay-sidebar')



@section('content')

  <div class="page-header">

    <h4 class="page-title">{{__('QR Code Builder')}}</h4>

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

        <a href="#">{{__('QR Code Builder')}}</a>

      </li>

    </ul>

    <a href="{{route('admin.table.index')}}" class="btn btn-primary" style="position: absolute; right: 50px;">{{__('back')}}</a>

  </div>

  <div class="row">

    <div class="col-lg-3">

      <div class="card">

          <div class="card-header">

              <h4 class="card-title">{{__('QR Code Builder')}}</h4>

          </div>

          <div class="card-body">

              <form id="qrGeneratorForm" method="POST" enctype="multipart/form-data">

                  @csrf

                  <input type="hidden" name="table_id" value="{{$be->id}}">

                  <div class="form-group">

                      <label for="">{{__('Color')}}</label>

                      <input type="text" class="form-control jscolor" name="color" value="{{$be->qr_color}}" onchange="generateQr()">
                      <p class="mb-0 text-warning">{{__('If the QR Code cannnot be scanned, then choose a darker color')}}</p>

                  </div>

                  <div class="form-group">

                      <label for="">{{__('Size')}}</label>

                      <input class="form-control p-0 range-slider" name="size" type="range" min="200" max="350" value="{{$be->qr_size}}" onchange="generateQr()">

                      <span class="text-white size-text float-right">{{$be->qr_size}}</span>

                  </div>

                  <div class="form-group">

                      <label for="">{{__('White Space')}}</label>

                      <input class="form-control p-0 range-slider" name="margin" type="range" min="0" max="5" value="{{$be->qr_margin}}" onchange="generateQr()">

                      <span class="text-white size-text float-right">{{$be->qr_margin}}</span>

                  </div>

                  <div class="form-group">

                      <label for="">{{__('Style')}}</label>

                      <select name="style" class="form-control" onchange="generateQr()">

                          <option value="square" {{$be->qr_style == 'square' ? 'selected' : ''}}>{{__('Square')}}</option>

                          <option value="round" {{$be->qr_style == 'round' ? 'selected' : ''}}>{{__('Round')}}</option>

                      </select>

                  </div>

                  <div class="form-group">

                      <label for="">{{__('Eye Style')}}</label>

                      <select name="eye_style" class="form-control" onchange="generateQr()">

                          <option value="square" {{$be->qr_eye_style == 'square' ? 'selected' : ''}}>{{__('Square')}}</option>

                          <option value="circle" {{$be->qr_eye_style == 'circle' ? 'selected' : ''}}>{{__('Round')}}</option>

                      </select>

                  </div>

                  <div class="form-group">

                      <label for="">{{__('Type')}}</label>

                      <select name="type" class="form-control" onchange="generateQr()">

                          <option value="default" {{$be->qr_type == 'default' ? 'selected' : ''}}>{{__('Default')}}</option>
                          <option value="image" {{$be->qr_type == 'image' ? 'selected' : ''}}>{{__('Image')}}</option>
                          <option value="text" {{$be->qr_type == 'text' ? 'selected' : ''}}>{{__('Text')}}</option>

                      </select>

                  </div>


                  <div id="type-image" class="types">
                      <div class="form-group">

                        <div class="col-12 mb-2">

                          <label for="image"><strong> {{__('Image')}}</strong></label>

                        </div>

                        <div class="col-md-12 showImage mb-3">

                          <img src="{{$be->qr_inserted_image ? asset('assets/front/img/'.$be->qr_inserted_image) :  asset('assets/admin/img/noimage.jpg')}}" alt="..." class="img-thumbnail qr">

                        </div>

                        <input type="file" name="image" id="image" class="form-control" onchange="generateQr()">

                      </div>

                      <div class="form-group">

                          <label for="">{{__('Image')}}: {{__('Size')}}</label>

                          <input class="form-control p-0 range-slider" name="image_size" type="range" min="1" max="20" value="{{$be->qr_inserted_image_size}}" onchange="generateQr()">

                          <span class="text-white size-text float-right d-block">{{$be->qr_inserted_image_size}}</span>
                          <p class="mb-0 text-warning">{{__('If the QR Code cannnot be scanned, then reduce this size')}}</p>

                      </div>

                      <div class="form-group">

                          <label for="">{{__('Image')}}: {{__('Horizontal Position')}}</label>

                          <input class="form-control p-0 range-slider" name="image_x" type="range" min="0" max="100" value="{{$be->qr_inserted_image_x}}" onchange="generateQr()">

                          <span class="text-white size-text float-right">{{$be->qr_inserted_image_x}}</span>

                      </div>

                      <div class="form-group">

                          <label for="">{{__('Image')}}: {{__('Vertical Position')}}</label>

                          <input class="form-control p-0 range-slider" name="image_y" type="range" min="0" max="100" value="{{$be->qr_inserted_image_y}}" onchange="generateQr()">

                          <span class="text-white size-text float-right">{{$be->qr_inserted_image_y}}</span>

                      </div>
                  </div>


                  <div id="type-text" class="types">
                      <div class="form-group">

                        <label>{{__('Text')}}</label>

                        <input type="text" name="text" value="{{$be->qr_text}}" class="form-control" onchange="generateQr()">

                      </div>

                      <div class="form-group">

                        <label>{{__('Text')}}: {{__('Color')}}</label>

                        <input type="text" name="text_color" value="{{$be->qr_text_color}}" class="form-control jscolor" onchange="generateQr()">

                      </div>

                      <div class="form-group">

                          <label for="">{{__('Text')}}: {{__('Size')}}</label>

                          <input class="form-control p-0 range-slider" name="text_size" type="range" min="1" max="15" value="{{$be->qr_text_size}}" onchange="generateQr()">

                          <span class="text-white size-text float-right d-block">{{$be->qr_text_size}}</span>
                          <p class="mb-0 text-warning">{{__('If the QR Code cannnot be scanned, then reduce this size')}}</p>

                      </div>

                      <div class="form-group">

                          <label for="">{{__('Text')}}: {{__('Horizontal Poistion')}}</label>

                          <input class="form-control p-0 range-slider" name="text_x" type="range" min="0" max="100" value="{{$be->qr_text_x}}" onchange="generateQr()">

                          <span class="text-white size-text float-right">{{$be->qr_text_x}}</span>

                      </div>

                      <div class="form-group">

                          <label for="">{{__('Text')}}: {{__('Vertical Position')}}</label>

                          <input class="form-control p-0 range-slider" name="text_y" type="range" min="0" max="100" value="{{$be->qr_text_y}}" onchange="generateQr()">

                          <span class="text-white size-text float-right">{{$be->qr_text_y}}</span>

                      </div>
                  </div>


              </form>

          </div>

      </div>



    </div>

    <div class="col-lg-5">
        <div class="card bg-white">

            <div class="card-header" style="border-bottom: 1px solid #ebecec!important;">

                <h4 class="card-title" style="color: #575962;">{{__('Preview')}}</h4>

            </div>

            <div class="card-body text-center py-5">

                <div class="p-3 border-rounded d-inline-block border" style="background-color: #f8f9fa!important;">

                    <img id="preview" src="{{asset('assets/front/img/' . $be->qr_image)}}" alt="">

                </div>

            </div>
            <div class="card-footer text-center" style="border-top: 1px solid #ebecec!important;">
              <a id="downloadBtn" class="btn btn-success" download="qr-image.png" href="{{asset('assets/front/img/' . $be->qr_image)}}">{{__('Download')}}</a>
            </div>
        </div>
        <span id="text-size" style="visibility: hidden;">{{$be->text}}</span>

    </div>

    <div class="col-lg-4" style="display:none;">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title d-flex justify-content-between"><span>{{__('Included QR Code Banners')}} (PSDs)</span> <a class="btn btn-success" href="{{asset('assets/admin/img/qr_banners/QR_Banners_PSDs.zip')}}" download="qr_banners_psds.zip">Download</a></h5>
        </div>
        <div class="card-body">
          <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
          </ol>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img class="d-block w-100" src="{{asset('assets/admin/img/qr_banners/1.png')}}" alt="First slide">
            </div>
            <div class="carousel-item">
              <img class="d-block w-100" src="{{asset('assets/admin/img/qr_banners/2.png')}}" alt="First slide">
            </div>
            <div class="carousel-item">
              <img class="d-block w-100" src="{{asset('assets/admin/img/qr_banners/3.png')}}" alt="First slide">
            </div>
          </div>
          <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">{{__('Next')}}</span>
          </a>
        </div>
        </div>
        <div class="card-footer text-center">
          <a class="btn btn-success" href="{{asset('assets/admin/img/qr_banners/QR_Banners_PSDs.zip')}}" download="qr_banners_psds.zip">{{__('Download')}} PSDs</a>
        </div>
      </div>

    </div>

  </div>



@endsection



@section('scripts')

  <script type="text/javascript">
      function loadDiv(type) {

        $(".types").removeClass('d-block');
        $(".types").addClass('d-none');

        $("#" + "type-" + type).removeClass("d-none");
        $("#" + "type-" + type).addClass("d-block");
      }


      $(document).ready(function() {

        let type = $("select[name='type']").val();
        loadDiv(type);


        $(".range-slider").on("input", function() {

            $(this).next(".size-text").html($(this).val());

        });

      });

      function generateQr() {

          loadDiv($("select[name='type']").val());
          $(".request-loader").addClass('show');


          let fd = new FormData(document.getElementById('qrGeneratorForm'));

          fd.append('size', $("input[name='size']").val());

          fd.append('margin', $("input[name='margin']").val());

          fd.append('image_size', $("input[name='image_size']").val());

          fd.append('image_x', $("input[name='image_x']").val());

          fd.append('image_y', $("input[name='image_y']").val());
          if ($("select[name='type']").val() == 'text') {
            $("#text-size").text($("input[name='text']").val());
            let fontSize = ($("input[name='size']").val() * $("input[name='text_size']").val()) / 100;
            $("#text-size").css("font-size", fontSize);

            let textWidth = $("#text-size").width() == 0 ? 1 : $("#text-size").width();

            fd.append('text_width', textWidth);
          }



          $(".range-slider").attr('disabled', true);



          $.ajax({

              url: "{{route('admin.qrcode.generate')}}",

              type: 'POST',

              data: fd,

              contentType: false,

              processData: false,

              success: function(data) {

                $(".request-loader").removeClass('show');

                $(".range-slider").attr('disabled', false);

                $("#preview").attr('src', data);
                $("#downloadBtn").attr('href', data);

              }

          });

      }

  </script>

@endsection

