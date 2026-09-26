<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventCategory\StoreEventCategoryRequest;
use App\Http\Requests\EventCategory\UpdateEventCategoryRequest;
use App\Models\EventCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = $request->get('status');

        $query = EventCategory::withCount(['events', 'activities'])->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', (int) $status);
        }

        $categories = $query->paginate(15)->withQueryString();

        return view('pages.admin.event_categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('pages.admin.event_categories.create');
    }

    public function store(StoreEventCategoryRequest $request): RedirectResponse
    {
        EventCategory::create($request->validated());

        return redirect()
            ->route('event-categories.index')
            ->with('success', 'Event category created successfully.');
    }

    public function edit(EventCategory $eventCategory): View
    {
        return view('pages.admin.event_categories.edit', compact('eventCategory'));
    }

    public function update(UpdateEventCategoryRequest $request, EventCategory $eventCategory): RedirectResponse
    {
        $eventCategory->update($request->validated());

        return redirect()
            ->route('event-categories.index')
            ->with('success', 'Event category updated successfully.');
    }

    public function destroy(EventCategory $eventCategory): RedirectResponse
    {
        if ($eventCategory->events()->count() > 0 || $eventCategory->activities()->count() > 0) {
            return redirect()
                ->route('event-categories.index')
                ->with('error', 'Cannot delete category because it is associated with events or event activities.');
        }

        $eventCategory->delete();

        return redirect()
            ->route('event-categories.index')
            ->with('success', 'Event category deleted successfully.');
    }
}
