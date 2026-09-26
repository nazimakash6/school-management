@extends ('layouts.app')

@section ('title', 'Student Admission')

@push ('styles')
    <link rel="stylesheet" href="{{ asset('css/admission.css') }}" />
@endpush

@section ('content')
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS BELOW HEADING -->
    <div class="content-header mb-4 flex-column align-items-start gap-3">
        <div class="w-100">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Students</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Admissions</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="user-plus" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="mb-0 fw-bold text-dark tracking-tight">Student Admission Management</h3>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 fs-8 fw-semibold">Active Session</span>
                    </div>
                    <p class="text-muted mb-0 fs-7">Track student enrollments, applications, monthly insights & CSV operations</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2 flex-wrap align-items-center pt-2 border-top w-100">
            <a href="{{ route('admission.blank-form') }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" title="Print Blank Admission Form">
                <i data-lucide="printer" style="width: 1rem; height: 1rem"></i> Print Blank Form
            </a>
            <a href="{{ route('admission.export', request()->query()) }}" class="btn btn-outline-success btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" title="Export CSV Data">
                <i data-lucide="download" style="width: 1rem; height: 1rem"></i> Export CSV
            </a>
            <button type="button" class="btn btn-outline-info btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" data-bs-toggle="modal" data-bs-target="#importAdmissionModal" title="Import CSV Batch Data">
                <i data-lucide="upload" style="width: 1rem; height: 1rem"></i> Import CSV
            </button>
            <a href="{{ route('admission.trash') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" title="View Trashed Admissions">
                <i data-lucide="archive" style="width: 1rem; height: 1rem"></i> Trash
            </a>
            <a href="{{ route('admission.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold ms-auto" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="plus" style="width: 1rem; height: 1rem"></i> New Admission
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <section class="admission-listing">
        <div class="admission-stat-grid" data-admission-stats>
            <article class="card stat-card stat-card-total">
                <div class="card-body">
                    <p class="stat-label">Total Admissions</p>
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
                    <p class="stat-label">Pending Admissions</p>
                    <h3 class="stat-value">{{ $pendingAdmissions }}</h3>
                    <p class="stat-footnote">Need review or documents</p>
                </div>
            </article>
            <article class="card stat-card stat-card-month">
                <div class="card-body">
                    <p class="stat-label">Selected Month</p>
                    <h3 class="stat-value">{{ $selectedMonthCount }}</h3>
                    <p class="stat-footnote">{{ $month === 'all' ? 'All months combined' : \Carbon\Carbon::create()->month((int) $month)->format('F') . ' admissions' }}</p>
                </div>
            </article>
        </div>

        <div class="filter-bar d-flex flex-wrap gap-2 align-items-end mt-3">
            <form
                method="GET"
                action="{{ route('admission.index') }}"
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
                        <option value="overall" @selected ($recent === 'overall')>Overall Admissions</option>
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
                    <a href="{{ route('admission.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <div class="admission-filter-summary mt-2">Showing {{ $admissions->total() }} admission(s)</div>

        <form
            id="admissionBulkActionForm"
            action="{{ route('admission.bulk-action') }}"
            method="POST"
            class="d-flex flex-wrap gap-2 align-items-end mt-2"
        >
            @csrf
            <input type="hidden" name="bulk_action" value="trash" />
            <div>
                <button
                    type="submit"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Move selected admissions to trash?');"
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
                        <th>Admission No</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Guardian</th>
                        <th>Admission Date</th>
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
                            <td>{{ $admission->admission_no }}</td>
                            <td>{{ $admission->student_name }}</td>
                            <td>{{ $admission->class_name }}</td>
                            <td>{{ $admission->guardian_name }}</td>
                            <td>{{ optional($admission->admission_date)->format('d M Y') }}</td>
                            <td>
                                <span
                                    class="status-badge {{ $admission->status }}"
                                    >{{ ucfirst($admission->status) }}</span
                                >
                            </td>
                            <td class="actions d-flex gap-2">
                                <a
                                    href="{{ route('admission.print', $admission) }}"
                                    target="_blank"
                                    class="btn btn-ghost btn-icon-sm text-success"
                                    title="Print Admission Form"
                                    ><i data-lucide="printer" style="width: 0.875rem; height: 0.875rem"></i
                                ></a>
                                <a
                                    href="{{ route('admission.show', $admission) }}"
                                    class="btn btn-ghost btn-icon-sm"
                                    title="View Profile"
                                    ><i data-lucide="eye" style="width: 0.875rem; height: 0.875rem"></i
                                ></a>
                                <a
                                    href="{{ route('admission.edit', $admission) }}"
                                    class="btn btn-ghost btn-icon-sm"
                                    title="Edit"
                                    ><i data-lucide="pencil" style="width: 0.875rem; height: 0.875rem"></i
                                ></a>
                                <form
                                    action="{{ route('admission.destroy', $admission) }}"
                                    method="POST"
                                    onsubmit="return confirm('Move this admission to trash?');"
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
                            <td colspan="10" class="admission-empty-state">No admissions found.</td>
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

    <!-- Import Admission Modal -->
    <div class="modal fade" id="importAdmissionModal" tabindex="-1" aria-labelledby="importAdmissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admission.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="importAdmissionModalLabel">Import Student Admissions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-sm text-tertiary mb-3">Upload a CSV file containing admission records to import them into the system.</p>
                        
                        <div class="mb-3 p-3 bg-light rounded border text-center">
                            <span class="d-block text-sm fw-medium mb-1">Need the correct CSV format?</span>
                            <a href="{{ route('admission.sample-csv') }}" class="btn btn-sm btn-outline-primary">
                                <i data-lucide="file-spreadsheet" style="width:1rem;height:1rem;" class="me-1"></i> Download Sample CSV Template
                            </a>
                        </div>

                        <div class="mb-3">
                            <label for="importAdmissionFile" class="form-label fw-medium">Select CSV File <span class="text-danger">*</span></label>
                            <input type="file" name="import_file" id="importAdmissionFile" class="form-control" accept=".csv, text/csv, application/csv, text/comma-separated-values" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i data-lucide="upload" style="width:1rem;height:1rem;" class="me-1"></i> Import Records
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
