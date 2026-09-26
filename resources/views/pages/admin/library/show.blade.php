@extends('layouts.app')

@section('title', 'Book Details - ' . $book->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/library.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="book-open" class="text-primary" style="width: 1.75rem; height: 1.75rem;"></i>
                Book Detail View
            </h1>
            <p class="text-muted mb-0">SKU: <span class="fw-bold font-monospace text-dark">{{ $book->book_code }}</span> · {{ $book->title }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('library.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Back to Catalog</span>
            </a>
            @if($book->available_copies > 0)
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#issueBookModal">
                    <i data-lucide="book-up" style="width: 1rem; height: 1rem;"></i>
                    <span>Issue Book</span>
                </button>
            @endif
            <a href="{{ route('library.edit', $book->id) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i data-lucide="pencil" style="width: 1rem; height: 1rem;"></i>
                <span>Edit</span>
            </a>
            <form action="{{ route('library.destroy', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this book from catalog?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-1">
                    <i data-lucide="trash-2" style="width: 1rem; height: 1rem;"></i>
                    <span>Delete</span>
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="check-circle" class="text-success" style="width:1.25rem;height:1.25rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Column: Cover Image & Quick Placement Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden text-center p-3">
                <div class="mb-3">
                    <img src="{{ $book->cover_image_url }}" alt="Cover Art" class="book-cover-large img-fluid rounded">
                </div>

                <div class="card-body p-2">
                    <h5 class="fw-bold text-dark mb-1">{{ $book->title }}</h5>
                    <div class="text-muted small mb-3">by <span class="fw-semibold text-dark">{{ $book->author }}</span></div>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge badge-category {{ $book->category_badge_class }}">
                            {{ $book->category }}
                        </span>
                        <span class="badge {{ $book->status_badge_class }}">
                            {{ $book->status }}
                        </span>
                    </div>

                    <div class="p-3 bg-light rounded-3 text-start border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">Shelf Rack Location</span>
                            <span class="fw-bold text-dark">{{ $book->rack_location ?: 'Unassigned' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">Available Copies</span>
                            <span class="fw-bold text-success fs-5">{{ $book->available_copies }} / {{ $book->total_copies }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small fw-semibold text-uppercase">Book Unit Price</span>
                            <span class="fw-bold text-dark">Rs. {{ number_format($book->price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Detailed Specs & Loan History --}}
        <div class="col-lg-8">
            {{-- Specifications Breakdown Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="info" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                        Book Specifications &amp; Publisher Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Book Title</span>
                            <div class="fw-bold text-dark fs-5 mt-1">{{ $book->title }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Book Code / SKU</span>
                            <div class="fw-bold font-monospace text-primary fs-5 mt-1">{{ $book->book_code }}</div>
                        </div>

                        <div class="col-md-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Author</span>
                            <div class="fw-semibold text-dark mt-1">{{ $book->author }}</div>
                        </div>

                        <div class="col-md-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Publisher</span>
                            <div class="fw-semibold text-dark mt-1">{{ $book->publisher ?: 'N/A' }}</div>
                        </div>

                        <div class="col-md-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block">ISBN Number</span>
                            <div class="fw-semibold text-dark mt-1">{{ $book->isbn ?: 'N/A' }}</div>
                        </div>
                    </div>

                    @if($book->description)
                        <hr class="my-4 opacity-25">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Synopsis / Notes</span>
                            <div class="p-3 bg-light rounded-3 text-dark border">
                                {{ $book->description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Checkout Loan History Table --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="history" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                        Student Borrowing History &amp; Active Loans
                    </h6>
                    <span class="badge bg-light text-dark border">{{ $book->issues->count() }} total loans</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase fw-semibold border-bottom">
                            <tr>
                                <th class="ps-3">Issue Code</th>
                                <th>Student Borrower</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                                <th class="pe-3 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($book->issues as $issue)
                                @php
                                    $isOverdue = ($issue->status === 'Issued' && $issue->due_date && $issue->due_date->isPast());
                                @endphp
                                <tr class="{{ $isOverdue ? 'bg-danger bg-opacity-10' : '' }}">
                                    <td class="ps-3 font-monospace small fw-bold">{{ $issue->issue_code }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $issue->student->full_name ?? 'N/A' }}</div>
                                        <div class="text-muted small">Adm #: {{ $issue->student->admission_number ?? 'N/A' }}</div>
                                    </td>
                                    <td class="small">{{ $issue->issue_date ? $issue->issue_date->format('M d, Y') : 'N/A' }}</td>
                                    <td class="small fw-bold {{ $isOverdue ? 'text-danger' : 'text-dark' }}">
                                        {{ $issue->due_date ? $issue->due_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="small text-muted">{{ $issue->return_date ? $issue->return_date->format('M d, Y') : 'Not Returned' }}</td>
                                    <td>
                                        <span class="badge {{ $issue->status_badge_class }}">
                                            {{ $isOverdue ? 'Overdue' : $issue->status }}
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        @if($issue->status === 'Issued')
                                            <form action="{{ route('library.return-book', $issue->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Return book copy to library?');">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i data-lucide="rotate-ccw" style="width:0.85rem;height:0.85rem;"></i> Return
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-success small fw-semibold"><i data-lucide="check" style="width:0.85rem;height:0.85rem;"></i> Returned</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i data-lucide="info" style="width:1.5rem;height:1.5rem;" class="mb-2 opacity-50"></i>
                                        <div class="small">No borrowing logs found for this book yet.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Issue Book Modal --}}
<div class="modal fade" id="issueBookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form action="{{ route('library.issue-book') }}" method="POST">
                @csrf
                <input type="hidden" name="library_id" value="{{ $book->id }}">
                <div class="modal-header bg-primary text-white py-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i data-lucide="book-up" style="width:1.2rem;height:1.2rem;"></i>
                        Issue '{{ $book->title }}'
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Select Borrower Student <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Select Student --</option>
                            @foreach(\App\Models\Student::orderBy('first_name')->get() as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->first_name }} {{ $st->last_name }} (Adm #: {{ $st->admission_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Return Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control" value="{{ \Carbon\Carbon::now()->addDays(14)->format('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-dark small">Remarks / Loan Notes</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Standard 14-day checkout."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Confirm Issue</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
