@extends('layouts.app')

@section('title', 'Library Management - School Catalog')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/library.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Academic Resources</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Library Center</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="book-open" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">School Library & Resource Center</h3>
                    <p class="text-muted mb-0 fs-7">Manage book catalog, student checkout logs, rack locations, and overdue returns</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;" data-bs-toggle="modal" data-bs-target="#issueBookModal">
                <i data-lucide="book-up" style="width:1rem;height:1rem;"></i> Issue Book to Student
            </button>
            <a href="{{ route('library.create') }}" class="btn btn-outline-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="plus-circle" style="width:1rem;height:1rem;"></i> Add New Book
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="check-circle" class="text-success" style="width:1.25rem;height:1.25rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="alert-circle" class="text-danger" style="width:1.25rem;height:1.25rem;"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Analytics Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm library-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Total Book Titles</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $stats['total_titles'] }}</h3>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                        <i data-lucide="book" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm library-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Physical Copies</span>
                        <h3 class="fw-bold text-info mb-0 mt-1">{{ $stats['total_copies'] }}</h3>
                    </div>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-3">
                        <i data-lucide="layers" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm library-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Currently Issued</span>
                        <h3 class="fw-bold text-purple mb-0 mt-1">{{ $stats['issued_count'] }}</h3>
                    </div>
                    <div class="p-3 bg-purple-subtle text-purple rounded-3">
                        <i data-lucide="user-check" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm library-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Overdue Returns</span>
                        <h3 class="fw-bold text-danger mb-0 mt-1">{{ $stats['overdue_count'] }}</h3>
                    </div>
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-3">
                        <i data-lucide="alert-triangle" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Container with Tabs --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-tabs-library border-0" id="libraryTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab === 'catalog' ? 'active' : '' }}" href="{{ route('library.index', ['tab' => 'catalog']) }}">
                        <i data-lucide="library" style="width:1.1rem;height:1.1rem;" class="me-2"></i>
                        Book Catalog Listing ({{ $stats['total_titles'] }})
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab === 'issues' ? 'active' : '' }}" href="{{ route('library.index', ['tab' => 'issues']) }}">
                        <i data-lucide="repeat" style="width:1.1rem;height:1.1rem;" class="me-2"></i>
                        Book Loans &amp; Return Tracker
                        @if($stats['overdue_count'] > 0)
                            <span class="badge bg-danger rounded-pill ms-2">{{ $stats['overdue_count'] }} Overdue</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            @if($activeTab === 'catalog')
                {{-- TAB 1: BOOK CATALOG --}}
                <div class="p-3 bg-light border-bottom">
                    <form action="{{ route('library.index') }}" method="GET" class="row g-2 align-items-center">
                        <input type="hidden" name="tab" value="catalog">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0"><i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search book title, author, ISBN, code..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <select name="category" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="Available" {{ request('status') === 'Available' ? 'selected' : '' }}>Available</option>
                                <option value="Fully Issued" {{ request('status') === 'Fully Issued' ? 'selected' : '' }}>Fully Issued</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-1">
                                <i data-lucide="filter" style="width:0.85rem;height:0.85rem;"></i> Filter Catalog
                            </button>
                            <a href="{{ route('library.index', ['tab' => 'catalog']) }}" class="btn btn-outline-secondary btn-sm px-3">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase fw-semibold border-bottom">
                            <tr>
                                <th class="ps-3" style="width: 60px;">Cover</th>
                                <th style="min-width: 240px;">Title &amp; Book Code</th>
                                <th style="min-width: 160px;">Author &amp; Publisher</th>
                                <th style="min-width: 140px;">Category</th>
                                <th style="min-width: 120px;">Rack Location</th>
                                <th style="min-width: 120px;">Stock Copies</th>
                                <th style="min-width: 110px;">Status</th>
                                <th class="pe-3 text-end" style="min-width: 130px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($books as $book)
                                <tr>
                                    {{-- Cover Image --}}
                                    <td class="ps-3">
                                        <img src="{{ $book->cover_image_url }}" alt="Cover" class="book-cover-thumb">
                                    </td>

                                    {{-- Title & Code --}}
                                    <td>
                                        <a href="{{ route('library.show', $book->id) }}" class="fw-bold text-dark text-decoration-none hover-primary mb-0 d-block">
                                            {{ $book->title }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge bg-dark bg-opacity-10 text-dark font-monospace text-uppercase" style="font-size:0.7rem;">
                                                {{ $book->book_code }}
                                            </span>
                                            @if($book->isbn)
                                                <span class="text-muted small" style="font-size:0.72rem;">ISBN: {{ $book->isbn }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Author --}}
                                    <td>
                                        <div class="fw-semibold text-dark small">{{ $book->author }}</div>
                                        <div class="text-muted small" style="font-size:0.75rem;">{{ $book->publisher ?: 'N/A' }}</div>
                                    </td>

                                    {{-- Category --}}
                                    <td>
                                        <span class="badge badge-category {{ $book->category_badge_class }}">
                                            {{ $book->category }}
                                        </span>
                                    </td>

                                    {{-- Rack Location --}}
                                    <td>
                                        <div class="small fw-semibold text-dark">
                                            <i data-lucide="map-pin" class="text-secondary me-1" style="width:0.8rem;height:0.8rem;"></i>
                                            {{ $book->rack_location ?: 'Shelf Unassigned' }}
                                        </div>
                                    </td>

                                    {{-- Stock Copies --}}
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <span class="text-success">{{ $book->available_copies }}</span> / {{ $book->total_copies }} Copies
                                        </div>
                                        <div class="text-muted" style="font-size:0.72rem;">Issued: {{ $book->issued_copies }}</div>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <span class="badge {{ $book->status_badge_class }}">
                                            {{ $book->status }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="pe-3 text-end">
                                        <div class="btn-group btn-group-sm">
                                            @if($book->available_copies > 0)
                                                <button type="button" class="btn btn-light border text-primary" title="Issue this Book" onclick="openQuickIssueModal({{ $book->id }}, '{{ addslashes($book->title) }}')">
                                                    <i data-lucide="book-up" style="width:0.9rem;height:0.9rem;"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('library.show', $book->id) }}" class="btn btn-light border" title="View Details">
                                                <i data-lucide="eye" style="width:0.9rem;height:0.9rem;"></i>
                                            </a>
                                            <a href="{{ route('library.edit', $book->id) }}" class="btn btn-light border" title="Edit Book">
                                                <i data-lucide="pencil" style="width:0.9rem;height:0.9rem;"></i>
                                            </a>
                                            <button type="button" class="btn btn-light border text-danger" onclick="confirmBookDelete({{ $book->id }})" title="Delete">
                                                <i data-lucide="trash-2" style="width:0.9rem;height:0.9rem;"></i>
                                            </button>
                                        </div>

                                        <form id="delete-book-form-{{ $book->id }}" action="{{ route('library.destroy', $book->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i data-lucide="book-open" style="width:3rem;height:3rem;" class="mb-3 text-secondary opacity-50"></i>
                                            <h5>No Library Books Found</h5>
                                            <p class="small mb-3">Add books to your catalog or change search criteria.</p>
                                            <a href="{{ route('library.create') }}" class="btn btn-primary btn-sm">
                                                <i data-lucide="plus" style="width:0.9rem;height:0.9rem;"></i> Add New Book
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($books->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Showing {{ $books->firstItem() }} to {{ $books->lastItem() }} of {{ $books->total() }} book titles</span>
                            <div>{{ $books->links() }}</div>
                        </div>
                    </div>
                @endif

            @else
                {{-- TAB 2: BOOK LOANS & RETURN TRACKER --}}
                <div class="p-3 bg-light border-bottom">
                    <form action="{{ route('library.index') }}" method="GET" class="row g-2 align-items-center">
                        <input type="hidden" name="tab" value="issues">
                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0"><i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i></span>
                                <input type="text" name="issue_search" class="form-control border-start-0" placeholder="Search issue code, student name, admission #, book..." value="{{ request('issue_search') }}">
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <select name="issue_status" class="form-select form-select-sm">
                                <option value="">All Loan Statuses</option>
                                <option value="Issued" {{ request('issue_status') === 'Issued' ? 'selected' : '' }}>Currently Issued</option>
                                <option value="Returned" {{ request('issue_status') === 'Returned' ? 'selected' : '' }}>Returned</option>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-1">
                                <i data-lucide="filter" style="width:0.85rem;height:0.85rem;"></i> Filter Loans
                            </button>
                            <a href="{{ route('library.index', ['tab' => 'issues']) }}" class="btn btn-outline-secondary btn-sm px-3">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase fw-semibold border-bottom">
                            <tr>
                                <th class="ps-3" style="min-width: 140px;">Issue Code</th>
                                <th style="min-width: 220px;">Book Title &amp; SKU</th>
                                <th style="min-width: 200px;">Borrower Student</th>
                                <th style="min-width: 110px;">Issue Date</th>
                                <th style="min-width: 110px;">Due Date</th>
                                <th style="min-width: 110px;">Return Date</th>
                                <th style="min-width: 110px;">Fine</th>
                                <th style="min-width: 110px;">Status</th>
                                <th class="pe-3 text-end" style="min-width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($issues as $issue)
                                @php
                                    $isOverdue = ($issue->status === 'Issued' && $issue->due_date && $issue->due_date->isPast());
                                @endphp
                                <tr class="{{ $isOverdue ? 'bg-danger bg-opacity-10' : '' }}">
                                    {{-- Issue Code --}}
                                    <td class="ps-3">
                                        <span class="badge bg-dark bg-opacity-10 text-dark font-monospace text-uppercase" style="font-size:0.72rem;">
                                            {{ $issue->issue_code }}
                                        </span>
                                    </td>

                                    {{-- Book Title --}}
                                    <td>
                                        <div class="fw-bold text-dark mb-0">{{ $issue->library->title ?? 'N/A' }}</div>
                                        <div class="text-muted small font-monospace" style="font-size:0.72rem;">{{ $issue->library->book_code ?? '' }}</div>
                                    </td>

                                    {{-- Student Borrower --}}
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            {{ $issue->student->full_name ?? ($issue->student->first_name . ' ' . $issue->student->last_name) }}
                                        </div>
                                        <div class="text-muted small" style="font-size:0.75rem;">
                                            Adm #: {{ $issue->student->admission_number ?? 'N/A' }}
                                        </div>
                                    </td>

                                    {{-- Issue Date --}}
                                    <td class="small fw-semibold text-dark">
                                        {{ $issue->issue_date ? $issue->issue_date->format('M d, Y') : 'N/A' }}
                                    </td>

                                    {{-- Due Date --}}
                                    <td class="small fw-bold {{ $isOverdue ? 'text-danger' : 'text-dark' }}">
                                        {{ $issue->due_date ? $issue->due_date->format('M d, Y') : 'N/A' }}
                                        @if($isOverdue)
                                            <span class="d-block text-danger fw-normal" style="font-size:0.68rem;">
                                                <i data-lucide="clock" style="width:0.7rem;height:0.7rem;"></i> Overdue
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Return Date --}}
                                    <td class="small text-muted">
                                        {{ $issue->return_date ? $issue->return_date->format('M d, Y') : 'Not Returned' }}
                                    </td>

                                    {{-- Fine Amount --}}
                                    <td>
                                        @if($issue->fine_amount > 0)
                                            <span class="fw-bold text-danger">Rs. {{ number_format($issue->fine_amount, 2) }}</span>
                                        @else
                                            <span class="text-muted small">Rs. 0.00</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <span class="badge {{ $issue->status_badge_class }}">
                                            {{ $isOverdue ? 'Overdue' : $issue->status }}
                                        </span>
                                    </td>

                                    {{-- Action --}}
                                    <td class="pe-3 text-end">
                                        @if($issue->status === 'Issued')
                                            <form action="{{ route('library.return-book', $issue->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm returning this book to library catalog?');">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1 shadow-sm">
                                                    <i data-lucide="rotate-ccw" style="width:0.85rem;height:0.85rem;"></i>
                                                    <span>Return Book</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small"><i data-lucide="check" class="text-success" style="width:0.85rem;height:0.85rem;"></i> Returned</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i data-lucide="repeat" style="width:3rem;height:3rem;" class="mb-3 text-secondary opacity-50"></i>
                                            <h5>No Book Checkout Records Found</h5>
                                            <p class="small mb-3">No active or past book loans match your current search.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($issues->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Showing {{ $issues->firstItem() }} to {{ $issues->lastItem() }} of {{ $issues->total() }} checkout records</span>
                            <div>{{ $issues->links() }}</div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Issue Book Modal --}}
<div class="modal fade" id="issueBookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form action="{{ route('library.issue-book') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i data-lucide="book-up" style="width:1.2rem;height:1.2rem;"></i>
                        Issue Book to Student
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Select Book <span class="text-danger">*</span></label>
                        <select name="library_id" id="issueBookSelect" class="form-select" required>
                            <option value="">-- Choose Book from Catalog --</option>
                            @foreach($availableBooks as $ab)
                                <option value="{{ $ab->id }}">
                                    {{ $ab->title }} ({{ $ab->book_code }}) - Available: {{ $ab->available_copies }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Select Borrower Student <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Select Student --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->first_name }} {{ $st->last_name }} (Adm #: {{ $st->admission_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Return Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control" value="{{ \Carbon\Carbon::now()->addDays(14)->format('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                        <div class="text-muted mt-1" style="font-size:0.75rem;">Standard borrowing loan period is 14 days.</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-dark small">Remarks / Loan Notes</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Clean condition, standard checkout."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2">
                        <i data-lucide="check-circle" style="width:1rem;height:1rem;"></i>
                        <span>Confirm Issue</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openQuickIssueModal(bookId, bookTitle) {
        document.getElementById('issueBookSelect').value = bookId;
        const modal = new bootstrap.Modal(document.getElementById('issueBookModal'));
        modal.show();
    }

    function confirmBookDelete(id) {
        if (confirm('Are you sure you want to remove this book from the library catalog?')) {
            document.getElementById(`delete-book-form-${id}`).submit();
        }
    }
</script>
@endpush
@endsection
