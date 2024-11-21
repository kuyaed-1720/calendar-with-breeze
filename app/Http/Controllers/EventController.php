<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        // Retrieve all events
        $events = Event::all();
        return view('calendar', ['events' => $events]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Create a new event
        Event::create([
            'title' => $request->title,
            'start_date' => $request->start,
            'end_date' => $request->end,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true]);
    }
}

