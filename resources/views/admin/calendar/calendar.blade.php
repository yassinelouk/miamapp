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
            <div class="offset-lg-4 col-lg-4 text-right d-none">
                <button class="btn btn-primary" id="newEventBtn" data-toggle="modal" data-target="#createModal">{{__('add new')}}</button>
            </div>
            <div class="offset-lg-4 col-lg-4 text-right d-none">
                <button class="btn btn-primary" id="editEventBtn" data-toggle="modal" data-target="#editEvent">{{__('edit')}}</button>
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
                {{-- <div class="form-group">
                  <label for="">{{__('Starting date')}} *</label>
                  <input id="startingDateInput" type="datetime-local" class="form-control" name="starting_date" value="" placeholder="{{__('Starting date')}}...">
                  <p id="errtable_no" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Ending date')}} **</label>
                  <input id="endingDateInput" type="datetime-local" class="form-control" name="ending_date" value="" placeholder="{{__('Ending date')}}...">
                  <p id="errtable_no" class="mb-0 text-danger em"></p>
                </div> --}}
                <div class="d-flex">
                  <div class="form-group">
                    <label for="">{{__('Starting time')}} *</label>
                    <input id="startingTimeInput" type="time" class="form-control" required name="starting_date" value="09:00">
                    <p id="errtable_no" class="mb-0 text-danger em"></p>
                  </div>
                  <div class="form-group">
                    <label for="">{{__('Ending time')}} *</label>
                    <input id="endingTimeInput" type="time" class="form-control" required name="ending_date" value="" min="09:00">
                    <p id="errtable_no" class="mb-0 text-danger em"></p>
                  </div>
                </div>
                <div class="form-group">
                    <label for="">{{__('Employees')}} *</label>
                    <select id="employeesList" name="admins" class="form-control" required>
                        <option value="" selected disabled>{{__('Choose an employee')}}</option>
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
                <div id="hiddenInputs" class="d-none">
                  <div id="hiddenVars">

                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    <div class="modal fade" id="editEvent" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">{{__('Edit an event')}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form  class="modal-form create" action="{{ route('calendar.update') }}" method="POST">
                @csrf
                <input id="eventId-update" type="text" name="event_id" value="" hidden />
                <input id="eventDay-update" type="text" name="day" value="" hidden />
                <div class="d-flex">
                  <div class="form-group">
                    <label for="">{{__('Starting time')}} *</label>
                    <input id="startingTimeInput-update" type="time" class="form-control" required name="starting_date" value="">
                    <p id="errtable_no" class="mb-0 text-danger em"></p>
                  </div>
                  <div class="form-group">
                    <label for="">{{__('Ending time')}} *</label>
                    <input id="endingTimeInput-update" type="time" class="form-control" required name="ending_date" value="">
                    <p id="errtable_no" class="mb-0 text-danger em"></p>
                  </div>
                </div>
                <div class="form-group">
                    <label for="">{{__('Employees')}} *</label>
                    <select id="employeesList-update" name="admins" class="form-control" required>
                        <option value="" selected disabled>{{__('Choose an employee')}}</option>
                        @foreach ($admins as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->first_name }} {{ $admin->last_name }}</option>
                        @endforeach
                    </select>
                    <p id="errstatus" class="mb-0 text-danger em"></p>
                </div>
                <div class="modal-footer" style="justify-content: space-between;">
                  <div>
                    <button type="button" onclick="deleteEvent()" class="btn btn-danger">{{__('Delete')}}</button>
                  </div>
                  <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                  </div>
                </div>
              </form>
              <form id="deleteEventForm" action="{{ route('calendar.delete') }}" method="POST" class="d-none">
                @csrf
                <input id="eventId-delete" type="text" name="event_id" value="" hidden />
              </form>
            </div>
          </div>
        </div>
      </div>
</div>
@endsection

@section('js')

<script>

    function formatDate(date) {
      let month = ((date.getMonth() + 1).toString()).length == 1 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
      let day = (date.getDate().toString()).length == 1 ? '0' + date.getDate() : date.getDate();

      return date.getFullYear() + '-' + month + '-' + day;
    }

    function getDateHour (date) {
      let hours = (date.getHours().toString()).length == 1 ? '0' + date.getHours() : date.getHours();
      let minutes = (date.getMinutes().toString()).length == 1 ? '0' + date.getMinutes() : date.getMinutes();

      return hours + ':' + minutes;
    }

    function deleteEvent() {
      $('#deleteEventForm').trigger('submit');
    }

    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var events = @json($events_json);
      var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          // right: 'dayGridMonth,timeGridWeek,timeGridDay'
          right: 'dayGridMonth'
        },
        // initialDate: '2020-09-12',
        height: '700px',
        navLinks: false, // can click day/week names to navigate views
        selectable: true,
        selectMirror: true,
        initialView: 'dayGridMonth',
        select: function(info) {
          if ((info.end - info.start) / (1000 * 3600 * 24) > 1) {
            let tomorrow = new Date(info.start);
            tomorrow.setHours(9);
            $('#hiddenVars').remove();
            $('#hiddenInputs').append("<div id='hiddenVars'></div>");
            while ((info.end - tomorrow) / (1000 * 3600 * 24) >= 1) {
              $('#hiddenVars').append("<input type='text' value='" + formatDate(tomorrow) + "' name='days[]' hidden>");
              tomorrow.setDate(tomorrow.getDate() + 1);
            }
            $('#hiddenVars').append("<input type='text' value='" + formatDate(tomorrow) + "' name='days[]' hidden>");
            $('#exampleModalLongTitle').text('Ajouter un évènement du ' + (info.start).toLocaleDateString("fr") + ' au ' + tomorrow.toLocaleDateString("fr"));
            $('#startingTimeInput').val('09:00');
            $('#endingTimeInput').val('');
            $('#employeesList').prop('selectedIndex', 0);
            $('#newEventBtn').trigger('click');
          }
          else {
            $('#hiddenVars').remove();
            $('#hiddenInputs').append("<div id='hiddenVars'></div>");
            $('#hiddenVars').append("<input type='text' value='" + formatDate(info.start) + "' name='days[]' hidden>");
            $('#exampleModalLongTitle').text('Ajouter un évènement pour le ' + (info.start).toLocaleDateString("fr"));
            $('#startingTimeInput').val('09:00');
            $('#endingTimeInput').val('');
            $('#employeesList').prop('selectedIndex', 0);
            $('#newEventBtn').trigger('click');
          }
        },
        eventClick: function(info) {
          let infos = info.event.id.split("|;|");
          $('#eventId-update').val(infos[0]);
          $('#eventId-delete').val(infos[0]);
          $('#eventDay-update').val(formatDate(info.event.start));
          $('#startingTimeInput-update').val(getDateHour(info.event.start));
          $('#endingTimeInput-update').val(getDateHour(info.event.end));
          $('#employeesList-update option[value="' + infos[1] + '"]').prop('selected', true).trigger("change");
          $('#editEventBtn').trigger('click');
        },
        eventTimeFormat: {
          hour: '2-digit',
          minute: '2-digit',
          hour12: false
        },
        displayEventEnd: true,
        eventDisplay: 'block',
        editable: false,
        dayMaxEvents: true, // allow "more" link when too many events
        events: events
      });

      calendar.render();
    });
  </script>
@endsection
