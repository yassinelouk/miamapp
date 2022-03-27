@extends('admin.layout')

@section('content')
<div class="page-header">
  <h4 class="page-title">{{__('Order Time Management')}}</h4>
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
      <a href="#">{{__('Order Time Management')}}</a>
    </li>
  </ul>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="card-title">{{__('Emergency Order Close')}}</div>
      </div>
      <div class="card-body">
        <form action="{{route('admin.orderclose')}}" method="POST" id="orderCloseForm">
          @csrf
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              @php
              if(!empty(old())) {
              $orderClose = old('order_close');
              } else {
              $orderClose = $be->order_close;
              }
              @endphp
              <div class="form-group">
                <label for="">{{__('Emergency Order Close')}} **</label>
                <select name="order_close" class="form-control">
                  <option value="0" {{$orderClose == 0 ? 'selected' : ''}}>{{__('Disable')}}</option>
                  <option value="1" {{$orderClose == 1 ? 'selected' : ''}}>{{__('Enable')}}</option>
                </select>
                <p class="text-warning mb-0">
                  {{__('If Enabled, then below Order Times will not work. The order will be closed')}}.</p>
                <p class="text-warning mb-0">
                  {{__('If Disabled, then the website will be able to take orders according to the below Order Times')}}.
                </p>
              </div>
            </div>
            <div class="col-lg-6 offset-lg-3" id="message">
              @php
              if(!empty(old())) {
              $orderCloseMessage = old('order_close_message');
              } else {
              $orderCloseMessage = $be->order_close_message;
              }
              @endphp
              <div class="form-group">
                <label for="">{{__('Message for Customers')}} **</label>
                <input type="text" class="form-control" name="order_close_message"
                  placeholder="{{__('Enter a message you want to show to customers')}}" value="{{$orderCloseMessage}}">
                @if ($errors->has('order_close_message'))
                <p class="text-danger mb-0">{{$errors->first('order_close_message')}}</p>
                @endif
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="card-footer">
        <div class="col-12 text-center">
          <div class="form-group">
            <button form="orderCloseForm" type="submit" class="btn btn-success">{{__('Update')}}</button>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <form class="" action="{{route('admin.ordertime.update')}}" id="ordertimeUpdateForm" method="post">
        @csrf
        <div class="card-header">
          <div class="row">
            <div class="col-lg-12">
              <div class="card-title">{{__('Order Time Management')}}</div>
            </div>
          </div>
        </div>
        <div class="card-body pt-5 pb-5">

          <div class="row">
            <div class="col-lg-8 offset-lg-2">
              <h4 class="text-warning text-center">{{__('Orders will be received between these times')}}.</h4>
              @php
              $days = ["monday","tuesday","wednesday","thursday","friday","saturday","sunday"];
              @endphp
              @foreach ($days as $day)
              <div class="row align-items-center justify-content-between">
                <div class="col-lg-3">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <button style="cursor: auto;" class="btn btn-block btn-primary text-capitalize"
                          type="button">{{__($day)}}</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <button class="btn btn-success addTF" data-day="{{$day}}"><i class="fas fa-plus"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @foreach($ordertimes as $ot)
              @if($ot->day == $day)
              <div class="col-lg-12">
                <div class="row align-items-center">
                  <input type="hidden" name="ids[]" value="{{$ot->id}}">
                  <div class="col-5">
                    <div class="form-group d-flex">
                      <input class="form-control ordertimepicker" name="start_time[]" value="{{$ot->start_time}}"
                        autocomplete="off" placeholder="Start Time">
                      <button type="button" class="btn btn-sm btn-danger mt-1"
                        onclick="event.target.previousElementSibling.value = ''"> <i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                  <div class="col-5">
                    <div class="form-group d-flex">
                      <input class="form-control ordertimepicker" name="end_time[]" value="{{$ot->end_time}}"
                        placeholder="End Time" autocomplete="off">
                      <button type="button" class="btn btn-sm btn-danger mt-1"
                        onclick="event.target.previousElementSibling.value = ''"> <i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                  @if (App\Models\OrderTime::where('day', $day)->count() > 1 && App\Models\OrderTime::where('day',
                  $day)->first()->id != $ot->id)
                  <div class="col-2">
                      <button type="button" onclick="deleteOrdertimeFrame({{$ot->id}});" class="btn btn-outline-danger btn-sm delete-ordertf-btn">
                        <i class="fas fa-trash"></i>
                      </button>
                  </div>
                  @endif
                </div>
              </div>
              @endif
              @endforeach
              @endforeach
              <p class="mb-0 text-warning text-center" style="font-size: 16px;">
                {{__('If you do not take orders at a specific day, leave input fields blank for that day')}}. </p>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="form">
            <div class="form-group from-show-notify row">
              <div class="col-12 text-center">
                <button type="submit" form="ordertimeUpdateForm" id="displayNotif"
                  class="btn btn-success">{{__('Update')}}</button>
              </div>
            </div>
          </div>
        </div>
      </form>
      <form id="odertimeframeDeleteForm" class="deleteform d-inline-block" action="{{route('admin.ordertime.delete')}}" method="post">@csrf</form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{__('Add Time Frame')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{route('admin.ordertime.store')}}" id="ajaxForm" method="POST">
          @csrf
          <input type="hidden" name="day" value="">
          <div class="form-group">
            <label for="">{{__('Start Time')}} *</label>
            <!--<input type="text" name="start" class="form-control timepicker" autocomplete="off">
                <p id="errstart" class="mb-0 text-danger em"></p>-->
            <input type="text" class="form-control ordertimepicker" name="start" autocomplete="off"
              placeholder="{{__('Start Time')}}">
            <button type="button" class="btn btn-sm btn-danger mt-1"
              onclick="event.target.previousElementSibling.value = ''">{{__('Delete')}}</button>
            <p id="errstart" class="mb-0 text-danger em"></p>
          </div>
          <div class="form-group">
            <label for="">{{__('End Time')}} *</label>
            <!--<input type="text" name="end" class="form-control timepicker" autocomplete="off">
                <p id="errend" class="mb-0 text-danger em"></p>-->
            <input type="text" class="form-control ordertimepicker" name="end" placeholder="{{__('End Time')}}"
              autocomplete="off">
            <button type="button" class="btn btn-sm btn-danger mt-1"
              onclick="event.target.previousElementSibling.value = ''">{{__('Delete')}}</button>
            <p id="errstart" class="mb-0 text-danger em"></p>
          </div>
        </form>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary btn-danger" data-dismiss="modal">{{__('Close')}}</button>
        <button id="submitBtn" type="button" class="btn btn-primary btn-success">{{__('Add')}}</button>
      </div>
    </div>
  </div>
</div>
@endsection



@section('scripts')
<script>
  function toggleMessage(status) {
            if(status == 1) {
                $("#message").show();
            } else {
                $("#message").hide();
            }
        }
  function deleteOrdertimeFrame(id) {
    let fd = new FormData();
    fd.append('ordertf_id',id);
    $.ajax({
            url: "{{route('admin.ordertime.delete')}}",
            data: fd,
            type: 'POST',
            processData: false,
            contentType: false,
            _token: '{{csrf_token()}}',
            success: function(response) {
              location.reload();
            }
        })
  }
        $(document).ready(function() {
            $('.ordertimepicker').mdtimepicker({ is24hour:true});

            toggleMessage($("select[name='order_close']").val());

            $("select[name='order_close']").on('change', function() {
                let status = $(this).val();
                toggleMessage(status);
            });
            $(".addTF").on('click', function(e) {
                e.preventDefault();
                $("#createModal").modal('show');
                $("input[name='day']").val($(this).data('day'));
            });
        });
</script>
@endsection