<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventConfigRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventConfigController extends Controller
{
    public function edit(): View
    {
        return view('admin.event.config', [
            'event' => Event::firstOrFail()
        ]);
    }

    public function update(EventConfigRequest $request): RedirectResponse
    {
        $event = Event::firstOrFail();
        $event->update($request->validated());

        // Auto-update status si quota atteint
        if ($event->tickets_sold >= $event->max_tickets) {
            $event->update(['status' => 'sold_out']);
        }

        return back()->with('success', 'Configuration mise à jour.');
    }
}