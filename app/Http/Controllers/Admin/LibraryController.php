<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookIssue;
use App\Models\Library;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'catalog');

        // Book Catalog Query
        $catalogQuery = Library::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $catalogQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('book_code', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $catalogQuery->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $catalogQuery->where('status', $request->status);
        }

        $books = $catalogQuery->paginate(10, ['*'], 'catalog_page')->withQueryString();

        // Book Issues Query
        $issuesQuery = BookIssue::with(['library', 'student.studentClass'])->latest();
        if ($request->filled('issue_search')) {
            $isearch = $request->issue_search;
            $issuesQuery->where(function ($q) use ($isearch) {
                $q->where('issue_code', 'like', "%{$isearch}%")
                  ->orWhereHas('library', function ($lq) use ($isearch) {
                      $lq->where('title', 'like', "%{$isearch}%")->orWhere('book_code', 'like', "%{$isearch}%");
                  })
                  ->orWhereHas('student', function ($sq) use ($isearch) {
                      $sq->where('first_name', 'like', "%{$isearch}%")
                        ->orWhere('last_name', 'like', "%{$isearch}%")
                        ->orWhere('admission_number', 'like', "%{$isearch}%");
                  });
            });
        }
        if ($request->filled('issue_status')) {
            $issuesQuery->where('status', $request->issue_status);
        }

        $issues = $issuesQuery->paginate(10, ['*'], 'issues_page')->withQueryString();

        // Statistics
        $stats = [
            'total_titles'    => Library::count(),
            'total_copies'    => Library::sum('total_copies'),
            'issued_count'    => BookIssue::where('status', 'Issued')->count(),
            'overdue_count'   => BookIssue::where('status', 'Issued')->where('due_date', '<', Carbon::today())->count(),
        ];

        $categories = Library::select('category')->distinct()->pluck('category');
        $availableBooks = Library::where('available_copies', '>', 0)->get();
        $students = Student::orderBy('first_name')->get();

        return view('pages.admin.library.index', compact('books', 'issues', 'stats', 'categories', 'availableBooks', 'students', 'activeTab'));
    }

    public function create()
    {
        $categories = ['Islamic Studies', 'Science & Tech', 'Mathematics', 'Literature & Fiction', 'History & Geography', 'Languages', 'Reference & Encyclopedias', 'General'];
        return view('pages.admin.library.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_code'     => 'nullable|string|max:100|unique:libraries,book_code',
            'title'         => 'required|string|max:255',
            'author'        => 'required|string|max:255',
            'publisher'     => 'nullable|string|max:255',
            'isbn'          => 'nullable|string|max:100',
            'category'      => 'required|string|max:100',
            'rack_location' => 'nullable|string|max:100',
            'total_copies'  => 'required|integer|min:1',
            'price'         => 'nullable|numeric|min:0',
            'cover_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description'   => 'nullable|string',
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('library_covers', 'public');
            $validated['cover_image'] = $path;
        }

        $validated['available_copies'] = $validated['total_copies'];
        $validated['issued_copies'] = 0;
        $validated['created_by'] = Auth::id();

        Library::create($validated);

        return redirect()->route('library.index')
            ->with('success', 'Book added to Library Catalog successfully!');
    }

    public function show($id)
    {
        $book = Library::with(['issues.student.studentClass', 'creator'])->findOrFail($id);
        return view('pages.admin.library.show', compact('book'));
    }

    public function edit($id)
    {
        $book = Library::findOrFail($id);
        $categories = ['Islamic Studies', 'Science & Tech', 'Mathematics', 'Literature & Fiction', 'History & Geography', 'Languages', 'Reference & Encyclopedias', 'General'];
        return view('pages.admin.library.edit', compact('book', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $book = Library::findOrFail($id);

        $validated = $request->validate([
            'book_code'     => 'nullable|string|max:100|unique:libraries,book_code,' . $id,
            'title'         => 'required|string|max:255',
            'author'        => 'required|string|max:255',
            'publisher'     => 'nullable|string|max:255',
            'isbn'          => 'nullable|string|max:100',
            'category'      => 'required|string|max:100',
            'rack_location' => 'nullable|string|max:100',
            'total_copies'  => 'required|integer|min:1',
            'price'         => 'nullable|numeric|min:0',
            'cover_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description'   => 'nullable|string',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && !str_starts_with($book->cover_image, 'http')) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $path = $request->file('cover_image')->store('library_covers', 'public');
            $validated['cover_image'] = $path;
        }

        // Adjust available copies based on change in total copies
        $diff = $validated['total_copies'] - $book->total_copies;
        $validated['available_copies'] = max(0, $book->available_copies + $diff);

        $book->update($validated);

        return redirect()->route('library.show', $book->id)
            ->with('success', 'Library book details updated successfully!');
    }

    public function destroy($id)
    {
        $book = Library::findOrFail($id);
        $book->delete();

        return redirect()->route('library.index')
            ->with('success', 'Book removed from library successfully!');
    }

    public function issueBook(Request $request)
    {
        $validated = $request->validate([
            'library_id' => 'required|exists:libraries,id',
            'student_id' => 'required|exists:students,id',
            'due_date'   => 'required|date|after_or_equal:today',
            'remarks'    => 'nullable|string',
        ]);

        $book = Library::findOrFail($validated['library_id']);

        if ($book->available_copies <= 0) {
            return redirect()->back()->with('error', 'Selected book has 0 available copies for checkout!');
        }

        // Decrement available copies & increment issued copies
        $book->decrement('available_copies');
        $book->increment('issued_copies');

        $validated['issue_date'] = Carbon::today()->format('Y-m-d');
        $validated['status'] = 'Issued';
        $validated['issued_by'] = Auth::id();

        BookIssue::create($validated);

        return redirect()->route('library.index', ['tab' => 'issues'])
            ->with('success', "Book '{$book->title}' issued successfully to student!");
    }

    public function returnBook(Request $request, $issueId)
    {
        $issue = BookIssue::with('library')->findOrFail($issueId);

        if ($issue->status === 'Returned') {
            return redirect()->back()->with('info', 'Book issue has already been marked as returned.');
        }

        $issue->status = 'Returned';
        $issue->return_date = Carbon::today()->format('Y-m-d');

        // Fine calculation if past due date
        if ($issue->due_date && Carbon::today()->greaterThan($issue->due_date)) {
            $overdueDays = Carbon::today()->diffInDays($issue->due_date);
            $issue->fine_amount = $overdueDays * 2.00; // $2.00 per overdue day fine
        }

        $issue->save();

        // Increment available copies & decrement issued copies
        if ($issue->library) {
            $issue->library->increment('available_copies');
            $issue->library->decrement('issued_copies');
        }

        return redirect()->route('library.index', ['tab' => 'issues'])
            ->with('success', "Book '{$issue->library->title}' successfully returned to library!");
    }
}
