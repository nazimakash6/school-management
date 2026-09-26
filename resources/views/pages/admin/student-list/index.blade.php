@extends ('layouts.app')

@section ('title', 'Student List')

@push ('styles')
    <link rel="stylesheet" href="{{ asset('css/admission.css') }}" />
@endpush

@section ('content')
    {{-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS --}}
    <div class="content-header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Students</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Student Directory</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="users" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Active Student Directory</h3>
                    <p class="text-muted mb-0 fs-7">Track all registered students, enrollment details, classes & guardian contacts</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('student-list.export', request()->query()) }}" class="btn btn-outline-success btn-sm" title="Export CSV"><i data-lucide="download" style="width: 1rem; height: 1rem"></i> Export CSV</a>
            <a href="{{ route('admission.trash') }}" class="btn btn-outline-secondary btn-sm"
                ><i data-lucide="archive" style="width: 1rem; height: 1rem"></i> Trash Items</a
            >
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <section class="admission-listing">
        <div class="admission-stat-grid" data-admission-stats>
            <article class="card stat-card stat-card-total">
                <div class="card-body">
                    <p class="stat-label">Total Students</p>
                    <h3 class="stat-value">{{ $totalAdmissions }}</h3>
                    <p class="stat-footnote">All time records</p>
                </div>
            </article>
            <article class="card stat-card stat-card-active">
                <div class="card-body">
                    <p class="stat-label">Active Students</p>
                    <h3 class="stat-value">{{ $activeAdmissions }}</h3>
                    <p class="stat-footnote">Currently enrolled</p>
                </div>
            </article>
            <article class="card stat-card stat-card-pending">
                <div class="card-body">
                    <p class="stat-label">Pending Students</p>
                    <h3 class="stat-value">{{ $pendingAdmissions }}</h3>
                    <p class="stat-footnote">Need review or details</p>
                </div>
            </article>
            <article class="card stat-card stat-card-month">
                <div class="card-body">
                    <p class="stat-label">Selected Month</p>
                    <h3 class="stat-value">{{ $selectedMonthCount }}</h3>
                    <p class="stat-footnote">{{ $month === 'all' ? 'All months combined' : \Carbon\Carbon::create()->month((int) $month)->format('F') . ' students' }}</p>
                </div>
            </article>
        </div>

        <div class="filter-bar d-flex flex-wrap gap-2 align-items-end mt-3">
            <form
                method="GET"
                action="{{ route('student-list.index') }}"
                class="d-flex flex-wrap gap-2 align-items-end w-100"
            >
                <div>
                    <label for="admissionSearch" class="text-sm text-tertiary mb-1 d-block">Search</label>
                    <input
                        id="admissionSearch"
                        name="search"
                        type="text"
                        class="form-control"
                        placeholder="Search by student, guardian, class, or admission no"
                        value="{{ $search }}"
                    />
                </div>

                <div>
                    <label for="admissionStatus" class="text-sm text-tertiary mb-1 d-block">Status</label>
                    <select id="admissionStatus" name="status" class="form-select">
                        <option value="all" @selected ($status === 'all')>All Status</option>
                        <option value="active" @selected ($status === 'active')>Active</option>
                        <option value="pending" @selected ($status === 'pending')>Pending</option>
                        <option value="inactive" @selected ($status === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div>
                    <label for="admissionMonth" class="text-sm text-tertiary mb-1 d-block">Month</label>
                    <select id="admissionMonth" name="month" class="form-select">
                        <option value="all" @selected ($month === 'all')>All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected ((string) $m === $month)>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="admissionRecent" class="text-sm text-tertiary mb-1 d-block">Recent Range</label>
                    <select id="admissionRecent" name="recent" class="form-select">
                        <option value="overall" @selected ($recent === 'overall')>Overall Students</option>
                        <option value="1m" @selected ($recent === '1m')>Last 1 Month</option>
                        <option value="2m" @selected ($recent === '2m')>Last 2 Months</option>
                        <option value="3m" @selected ($recent === '3m')>Last 3 Months</option>
                        <option value="6m" @selected ($recent === '6m')>Last 6 Months</option>
                        <option value="9m" @selected ($recent === '9m')>Last 9 Months</option>
                        <option value="12m" @selected ($recent === '12m')>Last 12 Months</option>
                        <option value="2y" @selected ($recent === '2y')>Last 2 Years</option>
                        <option value="3y" @selected ($recent === '3y')>Last 3 Years</option>
                        <option value="5y" @selected ($recent === '5y')>Last 5 Years</option>
                        <option value="6y" @selected ($recent === '6y')>Last 6 Years</option>
                        <option value="7y" @selected ($recent === '7y')>Last 7 Years</option>
                        <option value="8y" @selected ($recent === '8y')>Last 8 Years</option>
                        <option value="9y" @selected ($recent === '9y')>Last 9 Years</option>
                    </select>
                </div>

                <div>
                    <label for="admissionAcademicSession" class="text-sm text-tertiary mb-1 d-block">Academic Session</label>
                    <select id="admissionAcademicSession" name="academic_session_id" class="form-select">
                        <option value="all" @selected ($academicSessionId === 'all')>All Sessions</option>
                        @foreach ($academicSessions as $session)
                            <option value="{{ $session->id }}" @selected ((string) $session->id === (string) $academicSessionId)>
                                {{ $session->session_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="admissionClass" class="text-sm text-tertiary mb-1 d-block">Class</label>
                    <select id="admissionClass" name="class_name" class="form-select">
                        <option value="all" @selected ($className === 'all')>All Classes</option>
                        @foreach ($availableClasses as $class)
                            <option value="{{ $class }}" @selected ($className === $class)>{{ $class }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="admissionPerPage" class="text-sm text-tertiary mb-1 d-block">Show records</label>
                    <select id="admissionPerPage" name="per_page" class="form-select">
                        <option value="25" @selected ($perPage === 25)>25</option>
                        <option value="50" @selected ($perPage === 50)>50</option>
                        <option value="100" @selected ($perPage === 100)>100</option>
                    </select>
                </div>

                <div class="d-flex gap-2 ms-auto">
                    <button class="btn btn-secondary btn-sm" type="submit">
                        <i data-lucide="filter" style="width: 1rem; height: 1rem"></i> Apply
                    </button>
                    <a href="{{ route('student-list.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <div class="admission-filter-summary mt-2">Showing {{ $admissions->total() }} student(s)</div>

        <form
            id="admissionBulkActionForm"
            action="{{ route('admission.bulk-action') }}"
            method="POST"
            class="d-flex flex-wrap gap-2 align-items-end mt-2"
            data-confirm="Are you sure you want to move selected students to trash?"
            data-confirm-title="Move Selected Students to Trash?"
            data-confirm-btn="Yes, Move Selected"
        >
            @csrf
            <input type="hidden" name="bulk_action" value="trash" />
            <div>
                <button
                    type="submit"
                    class="btn btn-danger btn-sm"
                >
                    Bulk Trash
                </button>
            </div>
        </form>

        <div class="table-container mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAllAdmissions" />
                        </th>
                        <th>Academic Session</th>
                        <th>Roll No</th>
                        <th>Student</th>
                        <th>Father Name</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admissions as $admission)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    name="selected_ids[]"
                                    value="{{ $admission->id }}"
                                    class="admission-row-checkbox"
                                    form="admissionBulkActionForm"
                                />
                            </td>
                            <td>{{ optional($admission->academicSession)->session_name ?: 'N/A' }}</td>
                            <td>{{ $admission->roll_no ?: 'N/A' }}</td>
                            <td class="text-capitalize">{{ $admission->first_name }} {{ $admission->last_name }}</td>
                            <td class="text-capitalize">{{ $admission->father_name ?: 'N/A' }}</td>
                            <td class="text-capitalize">{{ optional($admission->studentClass)->name ?: ($admission->class_name ?: 'N/A') }}</td>
                            <td class="text-capitalize">{{ $admission->section_name ?: 'N/A' }}</td>
                            <td>
                                @php
                                    $st = $admission->status ?: 'active';
                                @endphp
                                <span
                                    class="text-capitalize status-badge bg-{{ $st == 'active' ? 'success' : 'secondary' }} text-white"
                                    >{{ ucfirst($st) }}</span
                                >
                            </td>
                            <td class="actions d-flex gap-2 align-items-center">
                                <a
                                    href="{{ route('student-list.show', $admission->id) }}"
                                    class="btn btn-ghost btn-icon-sm"
                                    title="View"
                                    ><i data-lucide="eye" style="width: 0.875rem; height: 0.875rem"></i
                                ></a>
                                <a
                                    href="{{ route('student-list.edit', $admission->id) }}"
                                    class="btn btn-ghost btn-icon-sm"
                                    title="Edit"
                                    ><i data-lucide="pencil" style="width: 0.875rem; height: 0.875rem"></i
                                ></a>
                                <form
                                    action="{{ route('student-list.destroy', $admission->id) }}"
                                    method="POST"
                                    data-confirm="Are you sure you want to move {{ $admission->first_name }} {{ $admission->last_name }} to trash?"
                                    data-confirm-title="Move Student to Trash?"
                                    data-confirm-btn="Yes, Move to Trash"
                                >
                                    @csrf
                                    @method ('DELETE')
                                    <button
                                        type="submit"
                                        class="btn btn-ghost btn-icon-sm text-danger"
                                        title="Move to Trash"
                                    >
                                        <i data-lucide="trash-2" style="width: 0.875rem; height: 0.875rem"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="admission-empty-state">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admission-pagination">
            <p class="pagination-info">Showing {{ $admissions->firstItem() ?? 0 }} to {{ $admissions->lastItem() ?? 0 }} of {{ $admissions->total() }} entries</p>

            @if ($admissions->hasPages())
                <ul class="pagination mb-0">
                    <li class="page-item {{ $admissions->onFirstPage() ? 'disabled' : '' }}">
                        <a
                            class="page-link"
                            href="{{ $admissions->previousPageUrl() ?: '#' }}"
                            @if ($admissions->onFirstPage()) aria-disabled="true" @endif
                            ><i data-lucide="chevron-left" style="width: 1rem; height: 1rem"></i
                        ></a>
                    </li>

                    @foreach ($admissions->getUrlRange(1, $admissions->lastPage()) as $page => $url)
                        <li class="page-item {{ $page === $admissions->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    <li class="page-item {{ $admissions->hasMorePages() ? '' : 'disabled' }}">
                        <a
                            class="page-link"
                            href="{{ $admissions->nextPageUrl() ?: '#' }}"
                            @if (!$admissions->hasMorePages()) aria-disabled="true" @endif
                            ><i data-lucide="chevron-right" style="width: 1rem; height: 1rem"></i
                        ></a>
                    </li>
                </ul>
            @endif
        </div>
    </section>

    @push ('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var selectAll = document.getElementById('selectAllAdmissions');
                var rowCheckboxes = document.querySelectorAll('.admission-row-checkbox');

                if (!selectAll) return;

                selectAll.addEventListener('change', function () {
                    rowCheckboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAll.checked;
                    });
                });

                rowCheckboxes.forEach(function (checkbox) {
                    checkbox.addEventListener('change', function () {
                        if (!checkbox.checked) {
                            selectAll.checked = false;
                            return;
                        }

                        var allChecked = Array.from(rowCheckboxes).every(function (item) {
                            return item.checked;
                        });

                        selectAll.checked = allChecked;
                    });
                });
            });
        </script>
    @endpush

@endsection
