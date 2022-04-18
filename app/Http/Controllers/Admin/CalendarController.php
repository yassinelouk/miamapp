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
                'id' => [$event->id . '|;|' .  $event->id_admin],
                'title' => $event->employee->first_name . ' ' . $event->employee->last_name,
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
                'id' => [$event->id . '|;|' .  $event->id_admin],
                'title' => $event->employee->first_name . ' ' . $event->employee->last_name,
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
        // dd($req->all());
        $validate = $req->validate([
            'starting_date' => 'required',
            'ending_date' => 'required',
            'admins' => 'required',
        ]);

        foreach ($req->days as $day) {
            $calendar=new Calendar();
            $calendar->starting_date= $day . ' ' . $req->input('starting_date') ;
            $calendar->ending_date= $day . ' ' . $req->input('ending_date');
            $calendar->id_admin=$req->input('admins');
            $calendar->save();
        }

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
    public function update(Request $request)
    {
       $event = Calendar::find($request->event_id);
        $event->starting_date= $request->day . ' ' . $request->input('starting_date') ;
        $event->ending_date= $request->day . ' ' . $request->input('ending_date');
        $event->id_admin=$request->input('admins');
        $event->save();

        return redirect()->route('admin.calendar.calendar');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $event = Calendar::find($request->event_id);
        $event->delete();

        return redirect()->route('admin.calendar.calendar');
    }
}
