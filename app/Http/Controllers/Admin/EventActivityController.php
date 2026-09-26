<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventActivity\StoreEventActivityRequest;
use App\Http\Requests\EventActivity\UpdateEventActivityRequest;
use App\Models\Event;
use App\Models\EventActivity;
use App\Models\EventCategory;
use App\Models\House;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventActivityController extends Controller
{
    public const CATEGORIES = [
        'Sports',
        'Academic',
        'Cultural',
        'Literary',
        'Other',
    ];

    public const STATUSES = [
        'Scheduled',
        'Ongoing',
        'Completed',
        'Cancelled',
    ];

    public function index(Request $request): View
    {
        $eventId         = $request->get('event_id');
        $eventCategoryId = $request->get('event_category_id');
        $category        = $request->get('category');
        $status          = $request->get('status');
        $search          = trim((string) $request->string('search'));

        $query = EventActivity::with(['event', 'categoryRel', 'winnerHouse', 'runnerUpHouse', 'thirdPlaceHouse'])->latest();

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        if ($eventCategoryId) {
            $query->where('event_category_id', $eventCategoryId);
        } elseif ($category) {
            $query->where('category', $category);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhere('rules_notes', 'like', "%{$search}%");
            });
        }

        $activities = $query->paginate(15)->withQueryString();

        $events         = Event::orderBy('start_date', 'desc')->get();
        $categoriesList = EventCategory::where('status', 1)->orderBy('name')->get();
        $categories     = $categoriesList->pluck('name')->toArray();
        if (empty($categories)) {
            $categories = self::CATEGORIES;
        }
        $statuses = self::STATUSES;

        return view('pages.admin.event_activities.index', compact(
            'activities',
            'events',
            'categoriesList',
            'categories',
            'statuses',
            'eventId',
            'eventCategoryId',
            'category',
            'status',
            'search'
        ));
    }

    public function create(Request $request): View
    {
        $selectedEventId = $request->get('event_id');
        $events          = Event::orderBy('start_date', 'desc')->get();
        $houses          = House::where('status', 'active')->orWhereNull('status')->orderBy('name')->get();
        $categoriesList  = EventCategory::where('status', 1)->orderBy('name')->get();
        $categories      = $categoriesList->pluck('name')->toArray();
        if (empty($categories)) {
            $categories = self::CATEGORIES;
        }
        $statuses = self::STATUSES;

        return view('pages.admin.event_activities.create', compact(
            'events',
            'houses',
            'categoriesList',
            'categories',
            'statuses',
            'selectedEventId'
        ));
    }

    public function store(StoreEventActivityRequest $request): RedirectResponse
    {
        $validated   = $request->validated();
        $categoryIds = $request->input('categories', []);

        if (empty($categoryIds) && !empty($validated['event_category_id'])) {
            $categoryIds = [(int) $validated['event_category_id']];
        }

        $firstCatId  = $categoryIds[0] ?? ($validated['event_category_id'] ?? null);
        $firstCatObj = $firstCatId ? EventCategory::find($firstCatId) : null;
        
        $validated['event_category_id'] = $firstCatId;
        $validated['category']          = $firstCatObj ? $firstCatObj->name : ($validated['category'] ?? 'Sports');

        $activity = EventActivity::create($validated);
        if (!empty($categoryIds)) {
            $activity->categories()->sync($categoryIds);
        }

        return redirect()
            ->route('event-activities.index', ['event_id' => $activity->event_id])
            ->with('success', "Event activity '{$activity->name}' added successfully!");
    }

    public function show(EventActivity $eventActivity): View
    {
        $eventActivity->load(['event', 'categoryRel', 'categories', 'winnerHouse', 'runnerUpHouse', 'thirdPlaceHouse']);
        return view('pages.admin.event_activities.show', compact('eventActivity'));
    }

    public function edit(EventActivity $eventActivity): View
    {
        $eventActivity->load('categories');
        $events         = Event::orderBy('start_date', 'desc')->get();
        $houses         = House::where('status', 'active')->orWhereNull('status')->orderBy('name')->get();
        $categoriesList = EventCategory::where('status', 1)->orderBy('name')->get();
        $categories     = $categoriesList->pluck('name')->toArray();
        if (empty($categories)) {
            $categories = self::CATEGORIES;
        }
        $statuses = self::STATUSES;

        return view('pages.admin.event_activities.edit', compact(
            'eventActivity',
            'events',
            'houses',
            'categoriesList',
            'categories',
            'statuses'
        ));
    }

    public function update(UpdateEventActivityRequest $request, EventActivity $eventActivity): RedirectResponse
    {
        $validated   = $request->validated();
        $categoryIds = $request->input('categories', []);

        if (empty($categoryIds) && !empty($validated['event_category_id'])) {
            $categoryIds = [(int) $validated['event_category_id']];
        }

        $firstCatId  = $categoryIds[0] ?? ($validated['event_category_id'] ?? $eventActivity->event_category_id);
        $firstCatObj = $firstCatId ? EventCategory::find($firstCatId) : null;

        $validated['event_category_id'] = $firstCatId;
        $validated['category']          = $firstCatObj ? $firstCatObj->name : ($validated['category'] ?? $eventActivity->category);

        $eventActivity->update($validated);
        $eventActivity->categories()->sync($categoryIds);

        return redirect()
            ->route('event-activities.show', $eventActivity)
            ->with('success', "Activity '{$eventActivity->name}' updated successfully!");
    }

    public function destroy(EventActivity $eventActivity): RedirectResponse
    {
        $eventId = $eventActivity->event_id;
        $eventActivity->delete();

        return redirect()
            ->route('event-activities.index', ['event_id' => $eventId])
            ->with('success', 'Event activity deleted successfully!');
    }
}
