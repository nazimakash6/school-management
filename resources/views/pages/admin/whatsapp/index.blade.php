@extends('layouts.app')

@section('title', 'WhatsApp Communication Console')

@push('styles')
<style>
.wa-gradient-header {
    background: linear-gradient(135deg, #075e54 0%, #128c7e 50%, #25d366 100%);
}
.wa-stat-card {
    border-left: 4px solid #25d366;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.wa-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important;
}
.wa-iframe-wrap {
    position: relative;
    width: 100%;
    height: 600px;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 10px 30px -5px rgba(0,0,0,0.12);
    border: 2px solid #25d366;
}
.wa-iframe-wrap iframe {
    width: 100%;
    height: 100%;
    border: none;
    display: block;
}
.wa-toolbar {
    background: linear-gradient(135deg, #075e54 0%, #128c7e 100%);
    color: white;
    border-radius: 14px 14px 0 0;
    padding: 0.75rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.wa-iframe-blocked {
    display: none;
    position: absolute;
    inset: 0;
    background: #f0fdf4;
    border-radius: 14px;
    z-index: 10;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Communication</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">WhatsApp Console</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center wa-gradient-header">
                    <svg viewBox="0 0 24 24" fill="white" width="22" height="22">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M5.507 19.481C3.043 17.95 1.5 15.32 1.5 12.5a10.5 10.5 0 1 1 18.982 6.218L22.5 22.5l-3.886-1.024a10.466 10.466 0 0 1-5.116 1.324A10.5 10.5 0 0 1 5.507 19.481z" stroke="white" stroke-width="1.5" fill="none"/>
                    </svg>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">WhatsApp Notification Center</h3>
                    <p class="text-muted mb-0 fs-7">Send instant WhatsApp notices to parents, students, and staff members with 1-Click</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <button class="btn btn-success fw-bold btn-sm d-inline-flex align-items-center gap-1.5 shadow-sm px-3 text-white" 
                    onclick="openWhatsAppModal({ name: '', phone: '', type: 'Parent' })"
                    style="background: linear-gradient(135deg, #128c7e 0%, #25d366 100%); border: none;">
                <i data-lucide="plus-circle" style="width:1rem;height:1rem;"></i>
                <span>Quick WhatsApp Message</span>
            </button>
            <a href="https://web.whatsapp.com" target="_blank" class="btn btn-outline-success btn-sm fw-semibold d-inline-flex align-items-center gap-1.5 px-3">
                <i data-lucide="external-link" style="width:1rem;height:1rem;"></i>
                <span>Open WhatsApp Web</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3">
            <i data-lucide="check-circle-2" class="me-2" style="width:1.2rem;height:1.2rem;"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 wa-stat-card">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold fs-8">Sent Today</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalSentToday }}</h3>
                    </div>
                    <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i data-lucide="send" style="width: 1.3rem; height: 1.3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 wa-stat-card" style="border-left-color: #0284c7;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold fs-8">Total Messages Logged</span>
                        <h3 class="fw-bold text-primary mb-0 mt-1">{{ $messages->total() }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i data-lucide="message-square" style="width: 1.3rem; height: 1.3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 wa-stat-card" style="border-left-color: #8b5cf6;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold fs-8">Delivery Method</span>
                        <h3 class="fw-bold text-purple mb-0 mt-1" style="font-size: 1.1rem;">Click-to-Send</h3>
                    </div>
                    <div class="rounded-circle bg-purple-subtle text-purple p-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background:#f3e8ff; color:#7c3aed;">
                        <i data-lucide="zap" style="width: 1.3rem; height: 1.3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 wa-stat-card" style="border-left-color: #10b981;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold fs-8">Cost Status</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">100% Free</h3>
                    </div>
                    <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i data-lucide="check-circle-2" style="width: 1.3rem; height: 1.3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Quick Compose & Message History -->
        <div class="col-lg-7">
            <!-- Recent WhatsApp Dispatch History Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="history" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        <span>Recent Dispatched Messages Log</span>
                    </h5>
                    <span class="badge bg-light text-dark border fs-8">Showing {{ $messages->count() }} entries</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Recipient</th>
                                <th>Phone Number</th>
                                <th>Message Snippet</th>
                                <th>Sent Date</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($messages as $msg)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold text-dark">{{ $msg->recipient_name }}</div>
                                    <span class="badge bg-light text-muted border text-xs">{{ $msg->recipient_type }}</span>
                                </td>
                                <td>
                                    <span class="font-monospace text-dark fs-7 fw-semibold">{{ $msg->phone_number }}</span>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block text-muted fs-7" style="max-width: 180px;" title="{{ $msg->message }}">
                                        {{ $msg->message }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $msg->created_at ? $msg->created_at->format('M d, g:i A') : 'N/A' }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-success me-1" title="Resend Message"
                                            onclick="openWhatsAppModal({
                                                name: '{{ addslashes($msg->recipient_name) }}',
                                                phone: '{{ $msg->phone_number }}',
                                                type: '{{ $msg->recipient_type }}',
                                                message: '{{ addslashes($msg->message) }}'
                                            })">
                                        <i data-lucide="rotate-cw" style="width:0.875rem;height:0.875rem;"></i>
                                    </button>
                                    <form action="{{ route('whatsapp.messages.destroy', $msg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete message log entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Log">
                                            <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i data-lucide="message-square" style="width:2rem;height:2rem;" class="mb-2 text-muted"></i>
                                    <p class="mb-0 fs-7">No WhatsApp messages dispatched yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($messages->hasPages())
                <div class="card-footer bg-white border-0 py-2">
                    {{ $messages->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Embedded Web & Instructions -->
        <div class="col-lg-5">
            <!-- Embedded WhatsApp Web Frame -->
            <div class="wa-iframe-wrap mb-4">
                <div class="wa-toolbar">
                    <div class="d-flex align-items-center gap-2">
                        <svg viewBox="0 0 24 24" fill="white" width="20" height="20">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        </svg>
                        <span class="fw-bold fs-7">WhatsApp Web Frame</span>
                    </div>
                    <a href="https://web.whatsapp.com" target="_blank" class="btn btn-light btn-xs text-success fw-bold px-2 py-0.5 rounded">
                        Open Full Tab
                    </a>
                </div>

                <iframe
                    id="wa_iframe"
                    src="https://web.whatsapp.com"
                    title="WhatsApp Web"
                    allow="camera; microphone; notifications; clipboard-read; clipboard-write"
                    sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-modals allow-top-navigation-by-user-activation"
                ></iframe>

                <div class="wa-iframe-blocked" id="wa_fallback">
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-4">
                        <div style="background:#25d366;border-radius:50%;padding:1.2rem;margin-bottom:1rem;">
                            <svg viewBox="0 0 24 24" fill="white" width="36" height="36"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">WhatsApp Web Direct Tab</h5>
                        <p class="text-muted fs-7 mb-3">If iframe is restricted by your browser, open WhatsApp Web in a separate browser tab for multi-tasking.</p>
                        <a href="https://web.whatsapp.com" target="_blank" class="btn btn-success btn-sm fw-bold px-4 shadow d-inline-flex align-items-center gap-1.5" style="background: linear-gradient(135deg, #128c7e 0%, #25d366 100%); border: none;">
                            <i data-lucide="external-link" style="width:1rem;height:1rem;"></i>
                            <span>Open web.whatsapp.com</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- How It Works Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i data-lucide="help-circle" class="text-success" style="width:1.1rem;height:1.1rem;"></i>
                    <span>How WhatsApp Click-to-Send Works</span>
                </h6>
                <ul class="list-unstyled mb-0 text-muted fs-7 d-flex flex-column gap-2.5">
                    <li class="d-flex align-items-start gap-2">
                        <span class="badge bg-success-subtle text-success rounded-circle px-2 py-1">1</span>
                        <span>Click the WhatsApp icon anywhere in Attendance, Fee Management, Exam Results, or Student Directory.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <span class="badge bg-success-subtle text-success rounded-circle px-2 py-1">2</span>
                        <span>The pre-filled message pop-up will open with the parent's phone number and tailored text message ready.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <span class="badge bg-success-subtle text-success rounded-circle px-2 py-1">3</span>
                        <span>Click <strong>"Open WhatsApp & Send"</strong>. It will record the log in database and open WhatsApp Web/App directly!</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const iframe = document.getElementById('wa_iframe');
    const fallback = document.getElementById('wa_fallback');

    let timer = setTimeout(function() {
        try {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            if (!doc || doc.body.innerHTML === '') {
                fallback.style.display = 'flex';
            }
        } catch (e) {
            fallback.style.display = 'flex';
        }
    }, 6000);

    iframe.addEventListener('error', function() {
        clearTimeout(timer);
        fallback.style.display = 'flex';
    });
});
</script>
@endpush
