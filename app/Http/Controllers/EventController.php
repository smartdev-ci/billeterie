<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('organizer-or-admin');
        
        $events = Event::all();
        return response()->json($events);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not used for API
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('organizer-or-admin');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'capacity' => 'required|integer|min:1',
            'ticket_price' => 'sometimes|integer|min:0',
            'faq' => 'nullable|array',
            'gallery' => 'nullable|array',
        ]);

        // Only one active event at a time
        if ($request->has('is_active') && $request->is_active) {
            Event::where('is_active', true)->update(['is_active' => false]);
        }

        $event = Event::create($validated);
        return response()->json($event, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id = null)
    {
        // If no ID provided, get the active event
        if (!$id) {
            $event = Event::where('is_active', true)->first();
            if (!$event) {
                return response()->json(['message' => 'No active event found'], 404);
            }
            return response()->json($event);
        }

        $event = Event::findOrFail($id);
        return response()->json($event);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not used for API
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Gate::authorize('organizer-or-admin');
        
        $event = Event::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'location' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'capacity' => 'sometimes|required|integer|min:1',
            'ticket_price' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'faq' => 'nullable|array',
            'gallery' => 'nullable|array',
        ]);

        // If setting as active, deactivate other events
        if (isset($validated['is_active']) && $validated['is_active']) {
            Event::where('id', '!=', $id)->where('is_active', true)->update(['is_active' => false]);
        }

        $event->update($validated);
        return response()->json($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Gate::authorize('organizer-or-admin');
        
        $event = Event::findOrFail($id);
        $event->delete();
        
        return response()->json(['message' => 'Event deleted successfully']);
    }

    /**
     * Get available tickets count for an event
     */
    public function availableTickets($id)
    {
        $event = Event::findOrFail($id);
        $available = $event->capacity - $event->tickets_sold;
        
        return response()->json([
            'event_id' => $event->id,
            'event_title' => $event->title,
            'available_tickets' => $available,
            'total_capacity' => $event->capacity,
            'sold_tickets' => $event->tickets_sold,
        ]);
    }
}
