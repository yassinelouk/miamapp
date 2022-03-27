<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Calendar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = Admin::whereNotNull('role_id')->get();
        if (Auth::user()->role_id == null) {
            $events = Calendar::all();
        }
        else {
            $events = Calendar::where('id_admin', Auth::user()->id)->get();
        }
        $events_json = array();
        foreach ($events as $event) {
            array_push($events_json, array(
                'title' => $event->event_name,
                'start' => $event->starting_date,
                'end' => $event->ending_date,
            ));
        }
        return view('admin.calendar.calendar',compact('admins','events_json'));
    }

    public function indexApi()
    {

        $events = Calendar::all();
        $events_json = array();
        foreach ($events as $event) {
            array_push($events_json, array(
                'title' => $event->event_name,
                'start' => $event->starting_date,
                'end' => $event->ending_date,
            ));
        }
        return response()->json($events_json);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $validate = $req->validate([
            'event_name' => 'required',
            'starting_date' => 'required',
            'ending_date' => 'required',
            'admins' => 'required',
        ]);

        $calendar=new Calendar();
        $calendar->event_name=$req->input('event_name');
        $calendar->starting_date=$req->input('starting_date');
        $calendar->ending_date=$req->input('ending_date');
        $calendar->id_admin=$req->input('admins');

        $calendar->save();
         return redirect()->route('admin.calendar.calendar');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function show(Calendar $calendar)
    {
        $events = Calendar::where('id_admin', Auth::user()->id)->get();
        $events_json = array();
        foreach ($events as $event) {
            array_push($events_json, array(
                'title' => $event->event_name,
                'start' => $event->starting_date,
                'end' => $event->ending_date,
            ));
        }
        return view('admin.dashboard',compact('events'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function edit(Calendar $calendar)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Calendar $calendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Calendar $calendar)
    {
        //
    }
}
