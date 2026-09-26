@extends('layouts.app')

@section('title', 'Class Examination Results')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('examination.index') }}">Examination</a></li>
                    <li class="breadcrumb-item active">Class Results</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Class Results Matrix</h1>
            <p class="text-muted small mb-0">View comprehensive class performance, total marks summary, and student result standings.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('examination.performance') }}" class="btn btn-outline-primary btn-sm">
                <i data-lucide="trending-up" style="width:1rem;height:1rem;" class="me-1"></i> Student Performance
            </a>
            <a href="{{ route('examination.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Examinations
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('examination.index') }}"><i data-lucide="list" style="width:1rem;height:1rem;" class="me-1"></i> Examinations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('exam-types.index') }}"><i data-lucide="tag" style="width:1rem;height:1rem;" class="me-1"></i> Exam Types</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-bold" href="{{ route('examination.results') }}"><i data-lucide="bar-chart-2" style="width:1rem;height:1rem;" class="me-1"></i> Class Results</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('examination.performance') }}"><i data-lucide="trending-up" style="width:1rem;height:1rem;" class="me-1"></i> Student Performance</a>
        </li>
    </ul>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('examination.results') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Filter Class</label>
                    <select name="class_name" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all">— All Classes —</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->name }}" @selected($className == $cls->name)>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Filter Exam Type</label>
                    <select name="exam_type_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all">— All Exam Types —</option>
                        @foreach($examTypes as $type)
                            <option value="{{ $type->id }}" @selected($examTypeId == $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Academic Session</label>
                    <select name="academic_session_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all">— All Sessions —</option>
                        @foreach($academicSessions as $sess)
                            <option value="{{ $sess->id }}" @selected($sessionId == $sess->id)>Session {{ $sess->session_name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Container for Examination Cards -->
    <div id="results-cards-container">
        @include('pages.admin.examination.partials.result-cards', ['examinations' => $examinations])
    </div>

    <!-- Load More Button Section -->
    <div id="load-more-container" class="text-center my-4">
        @if($examinations->hasMorePages())
            <button type="button" 
                    id="load-more-btn" 
                    data-next-url="{{ $examinations->nextPageUrl() }}" 
                    class="btn btn-outline-primary btn-lg fw-bold px-5 py-2.5 rounded-pill shadow-sm">
                <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                <i data-lucide="chevron-down" style="width:1.2rem;height:1.2rem;" class="me-1 btn-icon"></i>
                Load More Examination Results
            </button>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreContainer = document.getElementById('load-more-container');
    const cardsContainer = document.getElementById('results-cards-container');

    if (loadMoreContainer) {
        loadMoreContainer.addEventListener('click', function(e) {
            const btn = e.target.closest('#load-more-btn');
            if (!btn) return;

            const nextUrl = btn.getAttribute('data-next-url');
            if (!nextUrl) return;

            const spinner = btn.querySelector('.spinner-border');
            const icon = btn.querySelector('.btn-icon');
            btn.disabled = true;
            if (spinner) spinner.classList.remove('d-none');
            if (icon) icon.classList.add('d-none');

            fetch(nextUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    while (tempDiv.firstChild) {
                        cardsContainer.appendChild(tempDiv.firstChild);
                    }
                    if (window.lucide) { lucide.createIcons(); }
                }

                if (data.has_more && data.next_page_url) {
                    btn.setAttribute('data-next-url', data.next_page_url);
                    btn.disabled = false;
                    if (spinner) spinner.classList.add('d-none');
                    if (icon) icon.classList.remove('d-none');
                } else {
                    loadMoreContainer.innerHTML = '<div class="text-muted small fst-italic py-3"><i data-lucide="check-circle" style="width:1rem;height:1rem;" class="me-1 text-success"></i>All examination results loaded.</div>';
                    if (window.lucide) { lucide.createIcons(); }
                }
            })
            .catch(err => {
                console.error('Error loading more examination results:', err);
                btn.disabled = false;
                if (spinner) spinner.classList.add('d-none');
                if (icon) icon.classList.remove('d-none');
            });
        });
    }
});
</script>
@endpush
@endsection
