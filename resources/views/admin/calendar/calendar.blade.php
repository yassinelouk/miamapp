@extends('admin.layout')

@section('css')
<link href='{{ asset('assets/admin/css/calendar.css') }}' rel='stylesheet' />
<script src='{{asset('assets/admin/js/calendar.js') }}'></script>
@endsection
@section('content')
<div class="card-header" style="background: white">
    <div class="row">
        <div class="col-lg-4">
            <div class="card-title d-inline-block">{{__('Calendar')}}</div>
        </div>
        @if (Auth::user()->role_id == null)
            <div class="offset-lg-4 col-lg-4 text-right">
                <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">{{__('add new')}}</button>
            </div>
        @endif
    </div>
    <div class="mt-3" id='calendar'></div>
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
              <form  class="modal-form create" action="{{ route('calendar.store') }}" method="POST">
                @csrf
                <div class="form-group">
                  <label for="">{{__('Event Name')}} **</label>
                  <input type="text" class="form-control" name="event_name" value="" placeholder="{{__('Event Name')}}...">
                  <p id="errtable_no" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Starting date')}} **</label>
                  <input type="datetime-local" class="form-control" name="starting_date" value="" placeholder="{{__('Starting date')}}...">
                  <p id="errtable_no" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Ending date')}} **</label>
                  <input type="datetime-local" class="form-control" name="ending_date" value="" placeholder="{{__('Ending date')}}...">
                  <p id="errtable_no" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">{{__('Employees')}} **</label>
                    <select name="admins" class="form-control">
                        <option value="" selected disabled>{{__('Employees')}}</option>
                        @foreach ($admins as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->first_name }} {{ $admin->last_name }}</option>
                        @endforeach
                    </select>
                    <p id="errstatus" class="mb-0 text-danger em"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
      </div>
</div>
@endsection

@section('js')
<script>

    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var events = @json($events_json);
      var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        // initialDate: '2020-09-12',
        navLinks: true, // can click day/week names to navigate views
        selectable: false,
        selectMirror: true,
        initialView: 'timeGridWeek',
        eventClick: function(info) {
            alert('Name: ' + info.event.title);
            alert('Start: ' + info.event.start.toLocaleString());
            alert('End: ' + info.event.end.toLocaleString());

            // change the border color just for fun
            info.el.style.borderColor = 'red';
        },
        editable: false,
        dayMaxEvents: true, // allow "more" link when too many events
        events: events
      });

      calendar.render();
    });
  </script>
@endsection
