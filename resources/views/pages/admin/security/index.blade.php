@extends('layouts.app')

@section('title', 'Security & Access Control — EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">System Administration</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Security & Access</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="shield-check" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Security & Access Control Center</h3>
                    <p class="text-muted mb-0 fs-7">Configure authentication rules, password strength policies, 2FA requirements, and IP firewall</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-danger btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" data-bs-toggle="modal" data-bs-target="#banIpModal">
                <i data-lucide="shield-off" style="width:1rem;height:1rem;"></i>
                <span>Block IP Address</span>
            </button>
            <button type="submit" form="securityPolicyForm" class="btn btn-primary btn-sm px-4 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="save" style="width:1rem;height:1rem;"></i>
                <span>Save Security Rules</span>
            </button>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <i data-lucide="shield-check" class="text-success icon-md"></i>
            <div>{{ session('status') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Security Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small">System Security Score</div>
                        <div class="h3 font-weight-bold text-success mb-0 mt-1">95% <span class="fs-7 fw-normal text-muted">(High)</span></div>
                    </div>
                    <div class="p-3 bg-success-subtle text-success rounded-3">
                        <i data-lucide="shield-check" class="icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small">2FA Requirement</div>
                        <div class="h3 font-weight-bold text-dark mb-0 mt-1 text-capitalize">{{ $settings['2fa_requirement'] ?? 'Optional' }}</div>
                    </div>
                    <div class="p-3 bg-primary-subtle text-primary rounded-3">
                        <i data-lucide="key" class="icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small">Blocked IP Addresses</div>
                        <div class="h3 font-weight-bold text-danger mb-0 mt-1">{{ number_format($bannedIps->count()) }}</div>
                    </div>
                    <div class="p-3 bg-danger-subtle text-danger rounded-3">
                        <i data-lucide="slash" class="icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small">Admin Accounts</div>
                        <div class="h3 font-weight-bold text-dark mb-0 mt-1">{{ number_format($adminUsers->count()) }}</div>
                    </div>
                    <div class="p-3 bg-info-subtle text-info rounded-3">
                        <i data-lucide="users" class="icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Tabs & Forms -->
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i data-lucide="sliders" class="text-danger icon-sm"></i>
                        <span>Security Panels</span>
                    </h6>
                </div>
                <div class="list-group list-group-flush p-2" id="sec-tab" role="tablist">
                    <button class="list-group-item list-group-item-action border-0 rounded-2 d-flex align-items-center gap-3 py-3 active mb-1" id="tab-auth-btn" data-bs-toggle="list" data-bs-target="#tab-auth" type="button" role="tab">
                        <i data-lucide="lock" class="icon-sm text-primary"></i>
                        <div class="text-start">
                            <div class="fw-bold text-dark fs-7">Auth &amp; 2FA Rules</div>
                            <div class="text-muted small">Authentication policies &amp; timeouts</div>
                        </div>
                    </button>

                    <button class="list-group-item list-group-item-action border-0 rounded-2 d-flex align-items-center gap-3 py-3 mb-1" id="tab-password-btn" data-bs-toggle="list" data-bs-target="#tab-password" type="button" role="tab">
                        <i data-lucide="key-round" class="icon-sm text-warning"></i>
                        <div class="text-start">
                            <div class="fw-bold text-dark fs-7">Password Policy</div>
                            <div class="text-muted small">Length, symbols &amp; expiration</div>
                        </div>
                    </button>

                    <button class="list-group-item list-group-item-action border-0 rounded-2 d-flex align-items-center gap-3 py-3 mb-1" id="tab-ip-btn" data-bs-toggle="list" data-bs-target="#tab-ip" type="button" role="tab">
                        <i data-lucide="shield-ban" class="icon-sm text-danger"></i>
                        <div class="text-start">
                            <div class="fw-bold text-dark fs-7">IP Firewall &amp; Bans</div>
                            <div class="text-muted small">Blocked IP list &amp; rules</div>
                        </div>
                    </button>

                    <button class="list-group-item list-group-item-action border-0 rounded-2 d-flex align-items-center gap-3 py-3" id="tab-logs-btn" data-bs-toggle="list" data-bs-target="#tab-logs" type="button" role="tab">
                        <i data-lucide="history" class="icon-sm text-info"></i>
                        <div class="text-start">
                            <div class="fw-bold text-dark fs-7">Security Audit Trace</div>
                            <div class="text-muted small">Recent security event logs</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="tab-content" id="sec-tabContent">

                <!-- AUTH & 2FA RULES -->
                <div class="tab-pane fade show active" id="tab-auth" role="tabpanel">
                    <form action="{{ route('security.update') }}" method="POST" id="securityPolicyForm">
                        @csrf
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i data-lucide="lock" class="text-primary icon-sm"></i>
                                    <span>Authentication Policy &amp; Session Management</span>
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Two-Factor Authentication (2FA) Requirement</label>
                                        <select class="form-select" name="2fa_requirement">
                                            <option value="disabled" {{ ($settings['2fa_requirement'] ?? '') == 'disabled' ? 'selected' : '' }}>Disabled (Standard Password)</option>
                                            <option value="optional" {{ ($settings['2fa_requirement'] ?? '') == 'optional' ? 'selected' : '' }}>Optional for Users</option>
                                            <option value="required" {{ ($settings['2fa_requirement'] ?? '') == 'required' ? 'selected' : '' }}>Mandatory for all Staff &amp; Admins</option>
                                        </select>
                                        <div class="form-text small">Requires OTP code verification on login when set to Mandatory.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Session Timeout Duration (Minutes)</label>
                                        <input type="number" class="form-control" name="session_timeout" value="{{ $settings['session_timeout'] ?? '60' }}">
                                        <div class="form-text small">Inactivity period before user is automatically signed out.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Max Invalid Login Attempts Before Lockout</label>
                                        <input type="number" class="form-control" name="max_failed_attempts" value="{{ $settings['max_failed_attempts'] ?? '5' }}">
                                        <div class="form-text small">Number of failed password attempts allowed before locking.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Lockout Duration (Minutes)</label>
                                        <input type="number" class="form-control" name="lockout_duration_minutes" value="{{ $settings['lockout_duration_minutes'] ?? '15' }}">
                                        <div class="form-text small">Time period user must wait before retrying after lockout.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PASSWORD STRENGTH PANEL INCLUDED IN FORM -->
                        <div class="card border-0 shadow-sm rounded-3 mt-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i data-lucide="key-round" class="text-warning icon-sm"></i>
                                    <span>Password Complexity &amp; Expiry Policy</span>
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Minimum Password Length</label>
                                        <input type="number" class="form-control" name="password_min_length" min="6" max="32" value="{{ $settings['password_min_length'] ?? '8' }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-secondary">Password Expiration Interval (Days)</label>
                                        <input type="number" class="form-control" name="password_expiry_days" value="{{ $settings['password_expiry_days'] ?? '90' }}">
                                        <div class="form-text small">Set 0 to disable mandatory password rotation.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch card p-3 border shadow-xs">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="req_num" name="password_require_number" value="1" {{ ($settings['password_require_number'] ?? '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark" for="req_num">Require Numbers (0-9)</label>
                                            <div class="text-muted small mt-1 ms-4">Password must contain at least one numeric character.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch card p-3 border shadow-xs">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="req_sym" name="password_require_symbol" value="1" {{ ($settings['password_require_symbol'] ?? '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark" for="req_sym">Require Special Symbols (!@#$%)</label>
                                            <div class="text-muted small mt-1 ms-4">Password must contain at least one symbol character.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- PASSWORD POLICY TAB SHORTHAND -->
                <div class="tab-pane fade" id="tab-password" role="tabpanel">
                    <div class="alert alert-info d-flex align-items-center gap-2">
                        <i data-lucide="info" class="icon-md"></i>
                        <div>Password policy settings can be configured directly in the primary Security Policies form.</div>
                    </div>
                </div>

                <!-- IP FIREWALL & BANS -->
                <div class="tab-pane fade" id="tab-ip" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i data-lucide="shield-ban" class="text-danger icon-sm"></i>
                                <span>IP Firewall &amp; Blocked List</span>
                            </h5>
                            <button type="button" class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#banIpModal">
                                <i data-lucide="plus-circle" class="icon-xs"></i> Block New IP
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Blocked IP Address</th>
                                        <th>Reason / Description</th>
                                        <th>Banned By</th>
                                        <th>Date Blocked</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bannedIps as $ip)
                                        <tr>
                                            <td class="ps-4 font-monospace fw-bold text-danger">
                                                <i data-lucide="slash" class="icon-xs me-1"></i>{{ $ip->ip_address }}
                                            </td>
                                            <td class="small text-secondary">{{ $ip->reason ?: 'Manual administrative block' }}</td>
                                            <td class="small fw-bold text-dark">{{ optional($ip->bannedBy)->name ?: 'System Admin' }}</td>
                                            <td class="small text-muted">{{ $ip->created_at->format('d M, Y h:i A') }}</td>
                                            <td class="text-end pe-4">
                                                <form action="{{ route('security.unban-ip', $ip->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                                                        <i data-lucide="check-circle" class="icon-xs"></i> Unblock IP
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i data-lucide="shield-check" class="icon-xl text-success opacity-50 mb-2"></i>
                                                <h6>No Blocked IP Addresses</h6>
                                                <p class="small mb-0">Your IP Firewall currently has no active blocks.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- SECURITY AUDIT TRACE -->
                <div class="tab-pane fade" id="tab-logs" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i data-lucide="history" class="text-info icon-sm"></i>
                                <span>Recent Security Event Logs</span>
                            </h5>
                            <a href="{{ route('audit-logs.index', ['event_type' => 'security']) }}" class="btn btn-sm btn-outline-primary">
                                View All Audit Logs &rarr;
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Timestamp</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSecurityLogs as $secLog)
                                        <tr>
                                            <td class="ps-4 small text-muted">{{ $secLog->created_at->format('d M, Y h:i:s A') }}</td>
                                            <td><span class="font-monospace small fw-bold text-danger">{{ $secLog->action }}</span></td>
                                            <td class="small text-secondary">{{ $secLog->description }}</td>
                                            <td class="font-monospace small text-muted">{{ $secLog->ip_address }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No security events recorded yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Ban IP Modal -->
<div class="modal fade" id="banIpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i data-lucide="shield-off" class="icon-md"></i> Block IP Address
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('security.ip-ban') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Target IP Address</label>
                        <input type="text" class="form-control font-monospace" name="ip_address" placeholder="e.g. 192.168.1.100" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Reason for Block</label>
                        <textarea class="form-control" name="reason" rows="2" placeholder="e.g. Repeated unauthorized login attempts"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4">Block IP Address</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
