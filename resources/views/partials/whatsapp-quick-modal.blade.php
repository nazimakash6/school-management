<!-- Global Quick WhatsApp Send Modal -->
<div class="modal fade" id="whatsappQuickModal" tabindex="-1" aria-labelledby="whatsappQuickModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header with WhatsApp Gradient -->
            <div class="modal-header text-white px-4 py-3 border-0" style="background: linear-gradient(135deg, #128c7e 0%, #25d366 100%);">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="p-2 bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                        <svg viewBox="0 0 24 24" fill="white" width="22" height="22">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M5.507 19.481C3.043 17.95 1.5 15.32 1.5 12.5a10.5 10.5 0 1 1 18.982 6.218L22.5 22.5l-3.886-1.024a10.466 10.466 0 0 1-5.116 1.324A10.5 10.5 0 0 1 5.507 19.481z" stroke="white" stroke-width="1.5" fill="none"/>
                        </svg>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="whatsappQuickModalLabel">Quick WhatsApp Message</h5>
                        <span class="fs-7 text-white-50">Instant 1-Click Send to Parent / Staff</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <form id="waQuickSendForm">
                @csrf
                <div class="modal-body p-4 bg-light bg-opacity-50">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fs-7 fw-semibold text-muted">Recipient Name</label>
                            <input type="text" class="form-control form-control-sm border-0 shadow-sm" id="wa_recipient_name" name="recipient_name" placeholder="e.g. Muhammad Ali" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fs-7 fw-semibold text-muted">Recipient Type</label>
                            <select class="form-select form-select-sm border-0 shadow-sm" id="wa_recipient_type" name="recipient_type">
                                <option value="Parent" selected>Parent</option>
                                <option value="Student">Student</option>
                                <option value="Staff">Staff</option>
                                <option value="Principal">Principal</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-7 fw-semibold text-muted">WhatsApp Phone Number</label>
                        <div class="input-group input-group-sm shadow-sm">
                            <span class="input-group-text bg-white border-0 text-muted"><i data-lucide="phone" style="width:0.9rem;height:0.9rem;"></i></span>
                            <input type="text" class="form-control border-0" id="wa_phone_number" name="phone_number" placeholder="e.g. 03001234567 or +92300..." required>
                        </div>
                        <div class="form-text fs-8 text-muted">Pakistan numbers starting with 0 will auto-convert to +92 format.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-7 fw-semibold text-muted">Message Template (Optional)</label>
                        <select class="form-select form-select-sm border-0 shadow-sm" id="wa_template_select">
                            <option value="" selected>-- Select a Message Template --</option>
                            <option value="absence">Absence Notification Alert</option>
                            <option value="staff_attendance">Staff Attendance Alert (to Principal)</option>
                            <option value="fee">Fee Payment Reminder</option>
                            <option value="exam">Exam Result Announcement</option>
                            <option value="admission">Admission Confirmation</option>
                            <option value="custom">Custom Message</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fs-7 fw-semibold text-muted">Message Body</label>
                        <textarea class="form-control border-0 shadow-sm" id="wa_message_text" name="message" rows="4" placeholder="Type your WhatsApp message here..." required></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-white border-top-0 px-4 py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-3" id="wa_copy_btn">
                        <i data-lucide="copy" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Copy Text
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm px-3 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 rounded-3 fw-bold text-white d-inline-flex align-items-center gap-1.5 shadow-sm" style="background: linear-gradient(135deg, #128c7e 0%, #25d366 100%); border: none;">
                            <svg viewBox="0 0 24 24" fill="white" width="16" height="16">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            </svg>
                            <span>Open WhatsApp & Send</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global helper function to launch WhatsApp modal from anywhere in the app
window.openWhatsAppModal = function (opts = {}) {
    const waModalEl = document.getElementById('whatsappQuickModal');
    if (!waModalEl) return;
    
    let waModal = bootstrap.Modal.getInstance(waModalEl);
    if (!waModal) {
        waModal = new bootstrap.Modal(waModalEl);
    }
    
    const nameInput = document.getElementById('wa_recipient_name');
    const typeSelect = document.getElementById('wa_recipient_type');
    const phoneInput = document.getElementById('wa_phone_number');
    const templateSelect = document.getElementById('wa_template_select');
    const messageInput = document.getElementById('wa_message_text');

    if (nameInput) nameInput.value = opts.name || '';
    if (typeSelect) typeSelect.value = opts.type || 'Parent';
    if (phoneInput) phoneInput.value = opts.phone || '';
    if (messageInput) messageInput.value = opts.message || '';
    if (templateSelect) {
        templateSelect.value = opts.template || '';
    }
    waModal.show();
};

document.addEventListener('DOMContentLoaded', function () {
    const waModalEl = document.getElementById('whatsappQuickModal');
    if (!waModalEl) return;
    
    const form = document.getElementById('waQuickSendForm');
    const nameInput = document.getElementById('wa_recipient_name');
    const typeSelect = document.getElementById('wa_recipient_type');
    const phoneInput = document.getElementById('wa_phone_number');
    const templateSelect = document.getElementById('wa_template_select');
    const messageInput = document.getElementById('wa_message_text');
    const copyBtn = document.getElementById('wa_copy_btn');

    // Template Selector change handler
    templateSelect.addEventListener('change', function () {
        const val = this.value;
        const recipient = nameInput.value || 'Staff/Student';
        const today = new Date().toLocaleDateString();

        if (val === 'absence') {
            messageInput.value = `Respected Parent, your child ${recipient} was marked ABSENT today (${today}). Please contact school administration for clarification. Thank you.`;
        } else if (val === 'staff_attendance') {
            messageInput.value = `Respected Principal, staff member ${recipient} attendance status for today (${today}) has been recorded. Thank you.`;
        } else if (val === 'fee') {
            messageInput.value = `Assalamu Alaikum! Dear Parent, the monthly fee installment for ${recipient} is due. Kindly deposit the payment at your earliest convenience to avoid late fees. Thank you.`;
        } else if (val === 'exam') {
            messageInput.value = `Dear Parent, the Examination Result Card for ${recipient} has been published. You can review the details on the student portal or contact the class teacher.`;
        } else if (val === 'admission') {
            messageInput.value = `Warm Welcome to EduCore School! Admission for ${recipient} is confirmed. We look forward to a successful academic session ahead.`;
        }
    });

    // Copy text button
    copyBtn.addEventListener('click', function () {
        if (messageInput.value) {
            navigator.clipboard.writeText(messageInput.value).then(() => {
                const origText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i data-lucide="check" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Copied!';
                setTimeout(() => { copyBtn.innerHTML = origText; }, 2000);
            });
        }
    });

    // Form Submit: Log message via AJAX & Open WhatsApp link in new tab
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const recipientName = nameInput.value;
        const recipientType = typeSelect.value;
        const phoneNumber = phoneInput.value;
        const messageText = messageInput.value;

        if (!phoneNumber || !messageText) return;

        // Get the modal instance (it must exist since the modal is open)
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('whatsappQuickModal'));

        // Call backend API to record log & get clean wa.me URL
        fetch("{{ route('whatsapp.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                recipient_name: recipientName,
                recipient_type: recipientType,
                phone_number: phoneNumber,
                message: messageText,
                message_type: 'Direct'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.whatsapp_url) {
                window.open(data.whatsapp_url, '_blank');
                if (modalInstance) modalInstance.hide();
            } else {
                alert(data.message || 'Error sending message');
            }
        })
        .catch(err => {
            // Fallback: build WhatsApp URL directly if AJAX fails
            let clean = phoneNumber.replace(/[^\d]/g, '');
            if (clean.startsWith('0') && clean.length === 11) clean = '92' + clean.substring(1);
            const fallbackUrl = `https://api.whatsapp.com/send?phone=${clean}&text=${encodeURIComponent(messageText)}`;
            window.open(fallbackUrl, '_blank');
            if (modalInstance) modalInstance.hide();
        });
    });
});
</script>
