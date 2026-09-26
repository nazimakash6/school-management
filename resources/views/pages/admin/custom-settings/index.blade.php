@extends('layouts.app')

@section('title', 'Custom System Settings — EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">System Administration</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Custom Settings</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="settings" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Custom System Settings</h3>
                    <p class="text-muted mb-0 fs-7">Configure general school details, SMTP mail server, WhatsApp gateway & system options</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions">
            <button type="submit" form="settingsForm" class="btn btn-primary btn-sm px-4 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="save" style="width:1rem;height:1rem;"></i>
                <span>Save All Settings</span>
            </button>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <i data-lucide="check-circle" class="text-success icon-md"></i>
            <div>{{ session('status') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('custom-settings.update') }}" method="POST" id="settingsForm" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Navigation Tabs -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i data-lucide="sliders" class="text-primary icon-sm"></i>
                            <span>Settings Sections</span>
                        </h6>
                    </div>
                    <div class="list-group list-group-flush p-2" id="settings-tab" role="tablist">
                        <button class="list-group-item list-group-item-action border-0 rounded-2 d-flex align-items-center gap-3 py-3 active mb-1" id="tab-academic-btn" data-bs-toggle="list" data-bs-target="#tab-academic" type="button" role="tab">
                            <i data-lucide="book-open" class="icon-sm text-info"></i>
                            <div class="text-start">
                                <div class="fw-bold text-dark fs-7">Academic &amp; Fees</div>
                                <div class="text-muted small">Passing %, fines &amp; due dates</div>
                            </div>
                        </button>

                        <button class="list-group-item list-group-item-action border-0 rounded-2 d-flex align-items-center gap-3 py-3 mb-1" id="tab-email-btn" data-bs-toggle="list" data-bs-target="#tab-email" type="button" role="tab">
                            <i data-lucide="mail" class="icon-sm text-warning"></i>
                            <div class="text-start">
                                <div class="fw-bold text-dark fs-7">Email &amp; SMTP</div>
                                <div class="text-muted small">Mail server credentials &amp; headers</div>
                            </div>
                        </button>

                        <button class="list-group-item list-group-item-action border-0 rounded-2 d-flex align-items-center gap-3 py-3 mb-1" id="tab-whatsapp-btn" data-bs-toggle="list" data-bs-target="#tab-whatsapp" type="button" role="tab">
                            <i data-lucide="message-square" class="icon-sm text-success"></i>
                            <div class="text-start">
                                <div class="fw-bold text-dark fs-7">WhatsApp Gateway</div>
                                <div class="text-muted small">API credentials &amp; auto-alerts</div>
                            </div>
                        </button>

                        <button class="list-group-item list-group-item-action border-0 rounded-2 d-flex align-items-center gap-3 py-3" id="tab-system-btn" data-bs-toggle="list" data-bs-target="#tab-system" type="button" role="tab">
                            <i data-lucide="shield-alert" class="icon-sm text-danger"></i>
                            <div class="text-start">
                                <div class="fw-bold text-dark fs-7">System &amp; Maintenance</div>
                                <div class="text-muted small">Maintenance mode &amp; sessions</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Contents -->
            <div class="col-lg-9">
                <div class="tab-content" id="settings-tabContent">

                    <!-- ACADEMIC SETTINGS -->
                    <div class="tab-pane fade show active" id="tab-academic" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i data-lucide="book-open" class="text-info icon-sm"></i>
                                    <span>Academic &amp; Fee Rules Configuration</span>
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Late Fee Fine Amount (Rs.)</label>
                                        <input type="number" class="form-control" name="settings[late_fee_fine]" value="{{ $allSettings['late_fee_fine']->value ?? '500' }}">
                                        <div class="form-text small">Fine penalty applied after fee due date.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Default Fee Due Day of Month</label>
                                        <input type="number" class="form-control" min="1" max="28" name="settings[fee_due_day]" value="{{ $allSettings['fee_due_day']->value ?? '10' }}">
                                        <div class="form-text small">Monthly fee due day (e.g. 10th of every month).</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Minimum Passing Marks (%)</label>
                                        <input type="number" class="form-control" min="1" max="100" name="settings[pass_percentage]" value="{{ $allSettings['pass_percentage']->value ?? '40' }}">
                                        <div class="form-text small">Global threshold percentage required to pass a subject.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Required Attendance Threshold (%)</label>
                                        <input type="number" class="form-control" min="1" max="100" name="settings[attendance_threshold]" value="{{ $allSettings['attendance_threshold']->value ?? '75' }}">
                                        <div class="form-text small">Minimum attendance required for examination eligibility.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- EMAIL SETTINGS -->
                    <div class="tab-pane fade" id="tab-email" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i data-lucide="mail" class="text-warning icon-sm"></i>
                                    <span>Email / SMTP Gateway Settings</span>
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold small text-secondary">SMTP Server Host</label>
                                        <input type="text" class="form-control font-monospace" name="settings[smtp_host]" value="{{ $allSettings['smtp_host']->value ?? 'smtp.gmail.com' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-secondary">SMTP Port</label>
                                        <input type="number" class="form-control font-monospace" name="settings[smtp_port]" value="{{ $allSettings['smtp_port']->value ?? '587' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">SMTP Username / Email</label>
                                        <input type="text" class="form-control font-monospace" name="settings[smtp_username]" value="{{ $allSettings['smtp_username']->value ?? 'notifications@noorulhuda.edu.pk' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">SMTP Password</label>
                                        <input type="password" class="form-control font-monospace" name="settings[smtp_password]" value="{{ $allSettings['smtp_password']->value ?? '••••••••••••' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Encryption Type</label>
                                        <select class="form-select" name="settings[smtp_encryption]">
                                            <option value="tls" {{ ($allSettings['smtp_encryption']->value ?? '') == 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                                            <option value="ssl" {{ ($allSettings['smtp_encryption']->value ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="none" {{ ($allSettings['smtp_encryption']->value ?? '') == 'none' ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Sender Name</label>
                                        <input type="text" class="form-control" name="settings[email_from_name]" value="{{ $allSettings['email_from_name']->value ?? 'Noor Ul Huda School Admin' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WHATSAPP SETTINGS -->
                    <div class="tab-pane fade" id="tab-whatsapp" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i data-lucide="message-square" class="text-success icon-sm"></i>
                                    <span>WhatsApp Cloud API Configuration</span>
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">WhatsApp API Access Token</label>
                                        <input type="password" class="form-control font-monospace" name="settings[whatsapp_api_key]" value="{{ $allSettings['whatsapp_api_key']->value ?? 'wh_live_key_8892109841' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">WhatsApp Phone Number ID</label>
                                        <input type="text" class="form-control font-monospace" name="settings[whatsapp_phone_id]" value="{{ $allSettings['whatsapp_phone_id']->value ?? '109823471092834' }}">
                                    </div>
                                    <div class="col-12"><hr class="my-2 text-muted"></div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch card p-3 border shadow-xs">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="auto_wh_fee" name="settings[auto_whatsapp_fee_receipt]" value="1" {{ ($allSettings['auto_whatsapp_fee_receipt']->value ?? '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark" for="auto_wh_fee">Auto WhatsApp Fee Receipts</label>
                                            <div class="text-muted small mt-1 ms-4">Automatically dispatch digital payment receipts to parents via WhatsApp upon fee submission.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch card p-3 border shadow-xs">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="auto_wh_absent" name="settings[auto_whatsapp_absent_alert]" value="1" {{ ($allSettings['auto_whatsapp_absent_alert']->value ?? '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark" for="auto_wh_absent">Auto Absence Instant Alerts</label>
                                            <div class="text-muted small mt-1 ms-4">Automatically notify parents when a student is marked absent during daily attendance.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SYSTEM & MAINTENANCE SETTINGS -->
                    <div class="tab-pane fade" id="tab-system" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i data-lucide="shield-alert" class="text-danger icon-sm"></i>
                                    <span>System &amp; Maintenance Mode</span>
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch card p-3 border border-danger-subtle bg-danger-subtle bg-opacity-10 shadow-xs">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="maint_mode" name="settings[maintenance_mode]" value="1" {{ ($allSettings['maintenance_mode']->value ?? '0') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-danger fs-6" for="maint_mode">Enable System Maintenance Mode</label>
                                            <div class="text-muted small mt-1 ms-4">When enabled, non-admin users will be blocked from accessing the ERP portal with a maintenance notice.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Session Timeout (Minutes)</label>
                                        <input type="number" class="form-control" name="settings[session_timeout]" value="{{ $allSettings['session_timeout']->value ?? '60' }}">
                                        <div class="form-text small">Inactive sessions will automatically log out after this duration.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Max Login Lockout Attempts</label>
                                        <input type="number" class="form-control" name="settings[max_login_attempts]" value="{{ $allSettings['max_login_attempts']->value ?? '5' }}">
                                        <div class="form-text small">Maximum invalid password attempts before temporary IP lock.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>

</div>
@endsection
