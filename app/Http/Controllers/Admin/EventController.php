<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public const EVENT_TYPES = [
        'Academic',
        'Sports',
        'Cultural',
        'Islamic / Religious',
        'Parent-Teacher',
        'Holiday / Vacation',
        'Administrative',
        'Other',
    ];

    public const AUDIENCES = [
        'All Students',
        'Parents & Guardians',
        'Staff & Faculty',
        'Primary Section',
        'Secondary Section',
    ];

    public const STATUSES = [
        'Upcoming',
        'In-Progress',
        'Completed',
        'Postponed',
        'Cancelled',
    ];

    public function index(Request $request)
    {
        $categoriesList = EventCategory::where('status', 1)->orderBy('name')->get();
        $eventTypes     = self::EVENT_TYPES;
        $statuses       = self::STATUSES;

        $query = Event::with(['creator', 'categoryRel', 'categories'])
            ->orderBy('start_date', 'asc');

        if ($request->filled('event_category_id')) {
            $catId = $request->event_category_id;
            $query->where(function ($q) use ($catId) {
                $q->where('event_category_id', $catId)
                  ->orWhereHas('categories', function ($cq) use ($catId) {
                      $cq->where('event_categories.id', $catId);
                  });
            });
        } elseif ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $month = Carbon::parse($request->month);
            $query->whereYear('start_date', $month->year)
                  ->whereMonth('start_date', $month->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('organizer', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate(12)->appends($request->query());

        // Statistics
        $upcomingCount  = Event::where('status', 'Upcoming')->count();
        $completedMonth = Event::where('status', 'Completed')
            ->whereMonth('start_date', Carbon::now()->month)
            ->count();
        $sportsCount    = Event::where('event_type', 'Sports')->count();
        $totalBudgetPkr = Event::sum('budget_pkr');

        return view('pages.admin.events.index', compact(
            'events', 'categoriesList', 'eventTypes', 'statuses',
            'upcomingCount', 'completedMonth', 'sportsCount', 'totalBudgetPkr'
        ));
    }

    public function create()
    {
        $categoriesList = EventCategory::where('status', 1)->orderBy('name')->get();
        $eventTypes     = self::EVENT_TYPES;
        $audiences      = self::AUDIENCES;
        $statuses       = self::STATUSES;

        return view('pages.admin.events.create', compact('categoriesList', 'eventTypes', 'audiences', 'statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'categories'        => 'nullable|array',
            'categories.*'      => 'exists:event_categories,id',
            'event_category_id' => 'nullable|exists:event_categories,id',
            'event_type'        => 'nullable|string',
            'target_audience'   => 'required|string',
            'start_date'        => 'required|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'start_time'        => 'nullable',
            'end_time'          => 'nullable',
            'is_all_day'        => 'nullable|boolean',
            'location'          => 'required|string|max:255',
            'organizer'         => 'nullable|string|max:255',
            'status'            => 'required|in:Upcoming,In-Progress,Completed,Postponed,Cancelled',
            'budget_pkr'        => 'nullable|numeric|min:0',
            'description'       => 'nullable|string',
            'banner_image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner_image')) {
            $bannerPath = $request->file('banner_image')->store('event_banners', 'public');
        }

        $categoryIds = $request->input('categories', []);
        if (empty($categoryIds) && $request->event_category_id) {
            $categoryIds = [(int) $request->event_category_id];
        }

        $firstCatId  = $categoryIds[0] ?? $request->event_category_id;
        $firstCatObj = $firstCatId ? EventCategory::find($firstCatId) : null;
        $eventType   = $firstCatObj ? $firstCatObj->name : ($request->event_type ?: 'Academic');

        $event = Event::create([
            'title'             => $request->title,
            'event_category_id' => $firstCatId,
            'event_type'        => $eventType,
            'target_audience'   => $request->target_audience,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date ?: $request->start_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'is_all_day'        => $request->boolean('is_all_day'),
            'location'          => $request->location,
            'organizer'         => $request->organizer,
            'status'            => $request->status,
            'budget_pkr'        => $request->budget_pkr,
            'banner_image'      => $bannerPath,
            'description'       => $request->description,
            'created_by'        => Auth::id(),
        ]);

        if (!empty($categoryIds)) {
            $event->categories()->sync($categoryIds);
        }

        return redirect()->route('events.show', $event->id)
            ->with('success', "School event '{$event->title}' scheduled successfully!");
    }

    public function show($id)
    {
        $event = Event::with(['creator', 'categoryRel', 'categories', 'activities.winnerHouse', 'activities.runnerUpHouse', 'activities.thirdPlaceHouse'])->findOrFail($id);
        return view('pages.admin.events.show', compact('event'));
    }

    public function edit($id)
    {
        $event          = Event::with('categories')->findOrFail($id);
        $categoriesList = EventCategory::where('status', 1)->orderBy('name')->get();
        $eventTypes     = self::EVENT_TYPES;
        $audiences      = self::AUDIENCES;
        $statuses       = self::STATUSES;

        return view('pages.admin.events.edit', compact('event', 'categoriesList', 'eventTypes', 'audiences', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'title'             => 'required|string|max:255',
            'categories'        => 'nullable|array',
            'categories.*'      => 'exists:event_categories,id',
            'event_category_id' => 'nullable|exists:event_categories,id',
            'event_type'        => 'nullable|string',
            'target_audience'   => 'required|string',
            'start_date'        => 'required|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'start_time'        => 'nullable',
            'end_time'          => 'nullable',
            'is_all_day'        => 'nullable|boolean',
            'location'          => 'required|string|max:255',
            'organizer'         => 'nullable|string|max:255',
            'status'            => 'required|in:Upcoming,In-Progress,Completed,Postponed,Cancelled',
            'budget_pkr'        => 'nullable|numeric|min:0',
            'description'       => 'nullable|string',
            'banner_image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $categoryIds = $request->input('categories', []);
        if (empty($categoryIds) && $request->event_category_id) {
            $categoryIds = [(int) $request->event_category_id];
        }

        $firstCatId  = $categoryIds[0] ?? $request->event_category_id;
        $firstCatObj = $firstCatId ? EventCategory::find($firstCatId) : null;
        $eventType   = $firstCatObj ? $firstCatObj->name : ($request->event_type ?: $event->event_type);

        $data = [
            'title'             => $request->title,
            'event_category_id' => $firstCatId,
            'event_type'        => $eventType,
            'target_audience'   => $request->target_audience,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date ?: $request->start_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'is_all_day'        => $request->boolean('is_all_day'),
            'location'          => $request->location,
            'organizer'         => $request->organizer,
            'status'            => $request->status,
            'budget_pkr'        => $request->budget_pkr,
            'description'       => $request->description,
        ];

        if ($request->hasFile('banner_image')) {
            if ($event->banner_image && Storage::disk('public')->exists($event->banner_image)) {
                Storage::disk('public')->delete($event->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('event_banners', 'public');
        }

        $event->update($data);
        $event->categories()->sync($categoryIds);

        return redirect()->route('events.index')
            ->with('success', "Event '{$event->title}' updated successfully!");
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        if ($event->banner_image && Storage::disk('public')->exists($event->banner_image)) {
            Storage::disk('public')->delete($event->banner_image);
        }
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'School event removed successfully!');
    }
}
