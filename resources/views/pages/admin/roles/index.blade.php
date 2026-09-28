@extends('layouts.app')
@section('title', 'Roles & Permissions - EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">System Administration</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Roles & Permissions</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="shield-check" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">System Roles & Permissions</h3>
                    <p class="text-muted mb-0 fs-7">Configure role privileges and granular module permissions for system users</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions">
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                <i data-lucide="shield-plus" style="width:1rem;height:1rem;"></i>
                Create Custom Role
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i data-lucide="check-circle" style="width:1.2rem;height:1.2rem;" class="me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i data-lucide="alert-circle" style="width:1.2rem;height:1.2rem;" class="me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Roles Grid -->
    <div class="row g-4 mb-4">
        @foreach($roles as $role)
        @php
            $permCount = is_array($role->permissions) ? count($role->permissions) : 0;
            $isAll = is_array($role->permissions) && (in_array('*', $role->permissions) || $permCount >= 50);
        @endphp
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i data-lucide="shield" class="text-primary" style="width:1.25rem;height:1.25rem;"></i>
                                {{ $role->name }}
                            </h5>
                            @if($role->is_system)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border">System Role</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border">Custom Role</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-3">{{ $role->description ?: 'No description provided.' }}</p>

                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-3">
                            <div>
                                <div class="text-muted small fw-semibold">Assigned Users</div>
                                <div class="fw-bold text-dark fs-6">{{ $role->user_count }} user(s)</div>
                            </div>
                            <div class="text-end">
                                <div class="text-muted small fw-semibold">Permissions</div>
                                <div>
                                    @if($isAll)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border">Full Access</span>
                                    @else
                                        <span class="badge bg-primary bg-opacity-10 text-primary border">{{ $permCount }} permissions</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-role fw-semibold"
                            data-id="{{ $role->id }}"
                            data-name="{{ $role->name }}"
                            data-description="{{ $role->description }}"
                            data-is-system="{{ $role->is_system ? '1' : '0' }}"
                            data-permissions="{{ json_encode($role->permissions ?: []) }}"
                            data-bs-toggle="modal" data-bs-target="#editRoleModal">
                            <i data-lucide="edit-3" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Edit Permissions
                        </button>

                        @if(!$role->is_system)
                        <form method="POST" action="{{ route('roles.destroy', $role->id) }}" onsubmit="return confirm('Are you sure you want to delete this role?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Role">
                                <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i> Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal: Add Custom Role -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('roles.store') }}" class="modal-content border-0 shadow" style="max-height: 88vh; display: flex; flex-direction: column;">
            @csrf
            <div class="modal-header border-bottom flex-shrink-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="shield-plus" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                    Create Custom Role
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 flex-grow-1 overflow-auto" style="max-height: calc(88vh - 130px); overflow-y: auto !important;">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Lab Supervisor, Exam Manager" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="Describe the purpose of this role...">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 sticky-top bg-white pt-1">
                    <h6 class="fw-bold text-dark mb-0">Assign Permissions</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary global-select-all" data-target="#addRoleModal">Select All</button>
                        <button type="button" class="btn btn-outline-secondary global-unselect-all" data-target="#addRoleModal">Unselect All</button>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach($availablePermissionsGrouped as $category => $perms)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border shadow-none rounded-3 h-100">
                            <div class="card-header bg-light py-2 fw-semibold text-dark small d-flex justify-content-between align-items-center">
                                <span>{{ $category }}</span>
                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 select-all-cat fs-8">Select All</button>
                            </div>
                            <div class="card-body p-3">
                                @foreach($perms as $key => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input perm-checkbox" type="checkbox" name="permissions[]" value="{{ $key }}" id="add_perm_{{ Str::slug($key) }}">
                                    <label class="form-check-label text-dark small" for="add_perm_{{ Str::slug($key) }}">
                                        {{ $label }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer border-top bg-light flex-shrink-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary fw-semibold">Save New Role</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Role & Permissions -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form id="editRoleForm" method="POST" action="" class="modal-content border-0 shadow" style="max-height: 88vh; display: flex; flex-direction: column;">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom flex-shrink-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="edit-3" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                    Edit Role Permissions: <span id="edit_role_title" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 flex-grow-1 overflow-auto" style="max-height: calc(88vh - 130px); overflow-y: auto !important;">
                <div class="row g-3 mb-4">
                    <div class="col-md-6" id="edit_name_group">
                        <label class="form-label fw-semibold text-dark">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_role_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Description</label>
                        <input type="text" name="description" id="edit_role_desc" class="form-control">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 sticky-top bg-white pt-1">
                    <h6 class="fw-bold text-dark mb-0">Module Access Permissions</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary global-select-all" data-target="#editRoleModal">Select All</button>
                        <button type="button" class="btn btn-outline-secondary global-unselect-all" data-target="#editRoleModal">Unselect All</button>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach($availablePermissionsGrouped as $category => $perms)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border shadow-none rounded-3 h-100">
                            <div class="card-header bg-light py-2 fw-semibold text-dark small d-flex justify-content-between align-items-center">
                                <span>{{ $category }}</span>
                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 select-all-cat fs-8">Select All</button>
                            </div>
                            <div class="card-body p-3">
                                @foreach($perms as $key => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input edit-perm-cb" type="checkbox" name="permissions[]" value="{{ $key }}" id="edit_perm_{{ Str::slug($key) }}">
                                    <label class="form-check-label text-dark small" for="edit_perm_{{ Str::slug($key) }}">
                                        {{ $label }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer border-top bg-light flex-shrink-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary fw-semibold">Save Permissions</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Category Select All
    document.querySelectorAll('.select-all-cat').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('.card');
            const checkboxes = card.querySelectorAll('input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            this.textContent = allChecked ? 'Select All' : 'Deselect All';
        });
    });

    // Global Select All
    document.querySelectorAll('.global-select-all').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = document.querySelector(this.dataset.target);
            modal.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);
        });
    });

    // Global Unselect All
    document.querySelectorAll('.global-unselect-all').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = document.querySelector(this.dataset.target);
            modal.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        });
    });

    // Populate Edit Role Modal
    document.querySelectorAll('.btn-edit-role').forEach(btn => {
        btn.addEventListener('click', function () {
            const id          = this.dataset.id;
            const name        = this.dataset.name;
            const desc        = this.dataset.description;
            const isSystem    = this.dataset.isSystem === '1';
            const permissions = JSON.parse(this.dataset.permissions || '[]');

            document.getElementById('editRoleForm').action = `{{ url('roles') }}/${id}`;
            document.getElementById('edit_role_title').textContent = name;
            document.getElementById('edit_role_name').value = name;
            document.getElementById('edit_role_desc').value = desc || '';

            if (isSystem) {
                document.getElementById('edit_role_name').readOnly = true;
            } else {
                document.getElementById('edit_role_name').readOnly = false;
            }

            // Uncheck all edit checkboxes first
            document.querySelectorAll('.edit-perm-cb').forEach(cb => cb.checked = false);

            // Check assigned permissions
            permissions.forEach(perm => {
                const cb = document.querySelector(`.edit-perm-cb[value="${perm}"]`);
                if (cb) cb.checked = true;
            });
        });
    });
});
</script>
@endpush
