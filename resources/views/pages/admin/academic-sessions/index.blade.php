@extends ('layouts.app')

@section ('title', '')

@push ('styles')
    <link rel="stylesheet" href="{{ asset('css/academic-sessions.css') }}" />
@endpush

@push ('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('select-all');
            const recordCheckboxes = document.querySelectorAll('.record-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    recordCheckboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                });
            }
        });
    </script>
@endpush

@section ('content')
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Academic Setup</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Academic Sessions</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="calendar-range" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Academic Sessions & Terms</h3>
                    <p class="text-muted mb-0 fs-7">Manage school academic years, term dates, active sessions, and archival records</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2 flex-wrap align-items-center">
            <a href="{{ route('academic-sessions.trash') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="archive" style="width:1rem;height:1rem;"></i> Trash
            </a>
            <a href="{{ route('academic-sessions.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="plus" style="width:1rem;height:1rem;"></i> Add Session
            </a>
        </div>
    </div>

    @include ('partials.alert')

    <div class="filter-bar">
        <form action="{{ route('academic-sessions.index') }}" method="get" class="d-flex align-items-center gap-2">
            <select class="form-select" name="status">
                <option value="">All Status</option>
                <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <select class="form-select" name="per_page">
                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
            </select>
            <button class="btn btn-secondary btn-sm ms-auto">
                <i data-lucide="filter" style="width: 1rem; height: 1rem"></i> Filter
            </button>
        </form>
    </div>

    <form action="{{ route('academic-sessions.bulk-trash') }}" method="POST">
        @csrf
        @method ('DELETE')
        <div class="mb-3">
            <div class="d-inline">
                <a href="{{ route('academic-sessions.trash') }}" class="btn btn-danger btn-sm"
                    ><i data-lucide="trash-2" style="width: 1rem; height: 1rem"></i> Go Trash Page</a
                >
            </div>
            <button type="submit" class="btn btn-danger btn-sm">
                <i data-lucide="trash" style="width: 1rem; height: 1rem"></i>
                Bulk Trash
            </button>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="select-all" />
                        </th>
                        <th>Session</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($academicSessions as $academicSession)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    class="record-checkbox"
                                    name="academic_sessions[]"
                                    value="{{ $academicSession->id }}"
                                />
                            </td>

                            <td>{{ $academicSession->session_name }}</td>

                            <td>{{ $academicSession->start_date->format('d-m-y') }}</td>

                            <td>{{ $academicSession->end_date->format('d-m-y') }}</td>

                            <td>
                                <span
                                    class="badge bg-{{ $academicSession->status->value == 'Active' ? 'success' : 'secondary' }}"
                                >
                                    {{ $academicSession->status }}
                                </span>
                            </td>

                            <td class="actions">
                                <a
                                    href="{{ route('academic-sessions.show', $academicSession) }}"
                                    class="btn btn-ghost btn-icon-sm"
                                    data-bs-toggle="tooltip"
                                    title="View"
                                >
                                    <i data-lucide="eye" style="width: 0.875rem; height: 0.875rem"></i>
                                </a>

                                <a
                                    href="{{ route('academic-sessions.edit', $academicSession) }}"
                                    class="btn btn-ghost btn-icon-sm"
                                    data-bs-toggle="tooltip"
                                    title="Edit"
                                >
                                    <i data-lucide="pencil" style="width: 0.875rem; height: 0.875rem"></i>
                                </a>

                                <button
                                    type="button"
                                    class="btn btn-ghost btn-icon-sm text-danger"
                                    data-bs-toggle="tooltip"
                                    title="Trash"
                                    onclick="deleteRecord('{{ route('academic-sessions.destroy', $academicSession) }}')"
                                >
                                    <i data-lucide="trash-2" style="width: 0.875rem; height: 0.875rem"></i>
                                </button>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No academic sessions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
    <nav class="mt-3 d-flex justify-content-between align-items-center">
        <span class="text-sm text-tertiary">
            Showing {{ $academicSessions->firstItem() ?? 0 }} to {{ $academicSessions->lastItem() ?? 0 }} of {{ $academicSessions->total() }} entries
        </span>

        <ul class="pagination mb-0">
            {{-- Previous --}}
            <li class="page-item {{ $academicSessions->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $academicSessions->previousPageUrl() }}">
                    <i data-lucide="chevron-left" style="width: 1rem; height: 1rem"></i>
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
                    <i data-lucide="chevron-right" style="width: 1rem; height: 1rem"></i>
                </a>
            </li>
        </ul>
    </nav>

    <form id="delete-form" method="POST" style="display: none">
        @csrf
        @method ('DELETE')
    </form>

    <script>
        function deleteRecord(url) {
            const form = document.getElementById('delete-form');
            form.action = url;
            form.submit();
        }
    </script>

@endsection
