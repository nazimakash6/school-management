@extends('layouts.app')

@section('title', '')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/academic-sessions.css') }}">
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const selectItems = document.querySelectorAll('.select-item');

            selectAll.addEventListener('change', function() {
                selectItems.forEach(item => {
                    item.checked = selectAll.checked;
                });
            });
        });
    </script>
@endpush

@section('content')



    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('academic-sessions.index') }}" class="text-decoration-none text-muted">Academic Setup</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Session Trash</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                    <i data-lucide="trash-2" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Academic Session Trash</h3>
                    <p class="text-muted mb-0 fs-7">Restore or permanently delete archived academic session records</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('academic-sessions.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to Sessions
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @endif

    <div class="filter-bar">
        <form action="{{ route('academic-sessions.trash') }}" method="get" class="d-flex align-items-center gap-2">
            <select class="form-select" name="per_page">
                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
            </select>
            <button class="btn btn-secondary btn-sm ms-auto"><i data-lucide="filter" style="width:1rem;height:1rem;"></i>
                Filter</button>
        </form>
    </div>

    <form id="bulk-action-form" action="{{ route('academic-sessions.bulk-action') }}" method="POST">
        <div class="d-flex gap-2 mb-3">
            <div>
                <a href="{{ route('academic-sessions.index') }}" class="btn btn-primary btn-sm"><i data-lucide="list"
                        style="width:1rem;height:1rem;"></i> All</a>
            </div>
            <div>
                <button class="btn btn-success btn-sm" name="action" value="restore"><i data-lucide="refresh-ccw"
                        style="width:1rem;height:1rem;"></i>
                    Bulk Restore</button>
            </div>

            <div>
                <button class="btn btn-danger btn-sm" name="action" value="delete"><i data-lucide="trash"
                        style="width:1rem;height:1rem;"></i> Bulk
                    Delete</button>
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" /></th>
                        <th>Session</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($academicSessions as $academicSession)
                        <tr>
                            <td><input type="checkbox" name="academic_sessions[]" value="{{ $academicSession->id }}"
                                    class="select-item" /></td>
                            <td>{{ $academicSession->session_name }}</td>
                            <td>{{ $academicSession->start_date->format('d-m-y') }}</td>
                            <td>{{ $academicSession->end_date->format('d-m-y') }}</td>
                            <td><span
                                    class="badge bg-{{ $academicSession->status->value == 'Active' ? 'success' : ($academicSession->status == 'Completed' ? 'secondary' : 'info') }}">{{ $academicSession->status }}</span>
                            </td>
                            <td class="actions">

                                <button type="button" class="btn btn-ghost btn-icon-sm" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Restore"
                                    onclick="submitAction('{{ route('academic-sessions.restore', $academicSession->id) }}')">
                                    <i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;"></i>
                                </button>

                                <button type="button" class="btn btn-ghost btn-icon-sm text-danger"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"
                                    onclick="submitAction('{{ route('academic-sessions.force-delete', $academicSession->id) }}')"><i
                                        data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button>

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No academic sessions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <nav class="mt-3 d-flex justify-content-between align-items-center">
        <span class="text-sm text-tertiary">
            Showing {{ $academicSessions->firstItem() ?? 0 }}
            to {{ $academicSessions->lastItem() ?? 0 }}
            of {{ $academicSessions->total() }} entries
        </span>

        <ul class="pagination mb-0">

            {{-- Previous --}}
            <li class="page-item {{ $academicSessions->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $academicSessions->previousPageUrl() }}">
                    <i data-lucide="chevron-left" style="width:1rem;height:1rem;"></i>
                </a>
            </li>

            {{-- Page Numbers --}}
            @foreach ($academicSessions->getUrlRange(1, $academicSessions->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $academicSessions->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach

            {{-- Next --}}
            <li class="page-item {{ $academicSessions->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $academicSessions->nextPageUrl() }}">
                    <i data-lucide="chevron-right" style="width:1rem;height:1rem;"></i>
                </a>
            </li>

        </ul>
    </nav>

    <form id="bulk-btn-action-form" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="_method" id="form-method" value="POST">
    </form>

    <script>
        function submitAction(url, method = 'POST') {
            const form = document.getElementById('bulk-btn-action-form');

            form.action = url;

            document.getElementById('form-method').value = method;

            form.submit();
        }
    </script>

@endsection
