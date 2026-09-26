@extends('layouts.app')

@section('title', 'Audit Log Details — EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800 mb-0">Audit Log Details #{{ $auditLog->id }}</h1>
            <p class="text-muted mb-0 small mt-1">Detailed event trace, request metadata, and JSON context payload.</p>
        </div>
        <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm">
            <i data-lucide="arrow-left" class="icon-sm"></i>
            <span>Back to Audit Logs</span>
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i data-lucide="info" class="text-primary icon-sm"></i> Event Summary
                    </h5>
                </div>
                <div class="card-body p-4">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="fw-bold text-secondary" style="width: 140px;">Log ID:</td>
                            <td class="font-monospace">#{{ $auditLog->id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-secondary">Timestamp:</td>
                            <td>{{ $auditLog->created_at->format('d M, Y — h:i:s A') }} ({{ $auditLog->created_at->diffForHumans() }})</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-secondary">User:</td>
                            <td>
                                @if($auditLog->user)
                                    <span class="fw-bold text-dark">{{ $auditLog->user->name }}</span> ({{ $auditLog->user->email }})
                                @else
                                    <span class="text-muted">System / Guest</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-secondary">Event Category:</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold text-uppercase fs-8">
                                    {{ $auditLog->event_type }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-secondary">Action Name:</td>
                            <td class="font-monospace fw-bold text-dark">{{ $auditLog->action }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-secondary">Description:</td>
                            <td>{{ $auditLog->description }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i data-lucide="globe" class="text-info icon-sm"></i> Network &amp; Client Metadata
                    </h5>
                </div>
                <div class="card-body p-4">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="fw-bold text-secondary" style="width: 140px;">IP Address:</td>
                            <td class="font-monospace fw-bold text-dark">{{ $auditLog->ip_address }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-secondary">Route Path:</td>
                            <td class="font-monospace text-primary">{{ $auditLog->route }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-secondary">HTTP Method:</td>
                            <td><span class="badge bg-dark font-monospace">{{ $auditLog->method }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-secondary">User Agent:</td>
                            <td class="small text-muted" style="word-break: break-all;">{{ $auditLog->user_agent }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i data-lucide="code" class="text-success icon-sm"></i> Context JSON Payload
                    </h5>
                </div>
                <div class="card-body p-4 bg-dark text-white rounded-bottom-3">
                    <pre class="mb-0 font-monospace text-success" style="font-size: 13px;">{{ json_encode(json_decode($auditLog->context), JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
