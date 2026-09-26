<!-- Global Quick Email Send Modal -->
<div class="modal fade" id="emailQuickModal" tabindex="-1" aria-labelledby="emailQuickModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header with Blue Gradient -->
            <div class="modal-header text-white px-4 py-3 border-0" style="background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="p-2 bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                        <i data-lucide="mail" style="width:22px;height:22px;color:white;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="emailQuickModalLabel">Quick Email Message</h5>
                        <span class="fs-7 text-white-50">Instant Email Notification to Parent / Staff / Principal / Admin</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <form id="emailQuickSendForm">
                @csrf
                <div class="modal-body p-4 bg-light bg-opacity-50">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-semibold text-muted">Recipient Name</label>
                            <input type="text" class="form-control form-control-sm border-0 shadow-sm" id="em_recipient_name" name="recipient_name" placeholder="e.g. Muhammad Ali" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-semibold text-muted">Recipient Email</label>
                            <div class="input-group input-group-sm shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i data-lucide="at-sign" style="width:0.9rem;height:0.9rem;"></i></span>
                                <input type="email" class="form-control border-0" id="em_recipient_email" name="recipient_email" placeholder="e.g. parent@example.com" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-semibold text-muted">Recipient Type</label>
                            <select class="form-select form-select-sm border-0 shadow-sm" id="em_recipient_type" name="recipient_type">
                                <option value="Parent" selected>Parent</option>
                                <option value="Student">Student</option>
                                <option value="Staff">Staff</option>
                                <option value="Principal">Principal</option>
                                <option value="Admin">Admin</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-semibold text-muted">Email Subject</label>
                            <input type="text" class="form-control form-control-sm border-0 shadow-sm" id="em_subject" name="subject" placeholder="e.g. Attendance Notification" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-7 fw-semibold text-muted">Message Template (Optional)</label>
                        <select class="form-select form-select-sm border-0 shadow-sm" id="em_template_select">
                            <option value="" selected>-- Select a Message Template --</option>
                            <option value="absence">Absence Notification Alert</option>
                            <option value="staff_attendance">Staff Attendance Alert (Principal/Admin)</option>
                            <option value="fee">Fee Payment Reminder</option>
                            <option value="exam">Exam Result Announcement</option>
                            <option value="admission">Admission Confirmation</option>
                            <option value="custom">Custom Message</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fs-7 fw-semibold text-muted">Message Body</label>
                        <textarea class="form-control border-0 shadow-sm" id="em_message_text" name="message" rows="5" placeholder="Type your email message here..." required></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-white border-top-0 px-4 py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-3" id="em_copy_btn">
                        <i data-lucide="copy" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Copy Text
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm px-3 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="em_submit_btn" class="btn btn-primary btn-sm px-4 rounded-3 fw-bold text-white d-inline-flex align-items-center gap-1.5 shadow-sm" style="background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); border: none;">
                            <i data-lucide="send" style="width:15px;height:15px;" class="me-1"></i>
                            <span>Send Email</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global helper function to launch Email modal from anywhere in the app
window.openEmailModal = function (opts = {}) {
    const modalEl = document.getElementById('emailQuickModal');
    if (!modalEl) return;

    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) {
        modal = new bootstrap.Modal(modalEl);
    }

    document.getElementById('em_recipient_name').value  = opts.name    || '';
    document.getElementById('em_recipient_email').value = opts.email   || '';
    document.getElementById('em_recipient_type').value  = opts.type    || 'Parent';
    document.getElementById('em_subject').value         = opts.subject || '';
    document.getElementById('em_message_text').value    = opts.message || '';
    document.getElementById('em_template_select').value = opts.template || '';

    modal.show();
};

document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('emailQuickModal');
    if (!modalEl) return;

    const form           = document.getElementById('emailQuickSendForm');
    const nameInput      = document.getElementById('em_recipient_name');
    const emailInput     = document.getElementById('em_recipient_email');
    const typeSelect     = document.getElementById('em_recipient_type');
    const subjectInput   = document.getElementById('em_subject');
    const templateSelect = document.getElementById('em_template_select');
    const messageInput   = document.getElementById('em_message_text');
    const copyBtn        = document.getElementById('em_copy_btn');
    const submitBtn      = document.getElementById('em_submit_btn');

    // Template Selector change handler
    templateSelect.addEventListener('change', function () {
        const val       = this.value;
        const recipient = nameInput.value || 'Student';
        const today     = new Date().toLocaleDateString('en-PK');

        if (val === 'absence') {
            subjectInput.value  = 'Attendance Notice - Student Absent Today';
            messageInput.value  = `Dear Parent,\n\nWe wish to inform you that your child ${recipient} was marked ABSENT today (${today}).\n\nPlease contact the school administration for further clarification.\n\nBest Regards,\nSchool Administration`;
        } else if (val === 'staff_attendance') {
            subjectInput.value  = 'Staff Attendance Update';
            messageInput.value  = `Dear ${recipient},\n\nThis is to inform you that a staff member's attendance for today (${today}) has been recorded.\n\nPlease review and take necessary action if required.\n\nRegards,\nSchool Administration`;
        } else if (val === 'fee') {
            subjectInput.value  = 'Fee Payment Reminder';
            messageInput.value  = `Dear Parent,\n\nThis is a reminder that the monthly fee installment for ${recipient} is due. Kindly deposit the payment at your earliest convenience to avoid late charges.\n\nThank you,\nAccounts Department`;
        } else if (val === 'exam') {
            subjectInput.value  = 'Examination Result Announcement';
            messageInput.value  = `Dear Parent,\n\nThe Examination Result Card for ${recipient} has been published.\n\nFor detailed result card, please contact the school administration.\n\nRegards,\nExamination Department`;
        } else if (val === 'admission') {
            subjectInput.value  = 'Admission Confirmation Notice';
            messageInput.value  = `Dear Parent,\n\nWe are pleased to confirm the admission of ${recipient} at our school. We look forward to a productive academic journey ahead.\n\nWarm Regards,\nAdmission Office`;
        }
    });

    // Copy text button
    copyBtn.addEventListener('click', function () {
        const fullText = `Subject: ${subjectInput.value}\n\n${messageInput.value}`;
        if (fullText.trim()) {
            navigator.clipboard.writeText(fullText).then(() => {
                const origText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i data-lucide="check" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Copied!';
                setTimeout(() => { copyBtn.innerHTML = origText; lucide.createIcons(); }, 2000);
            });
        }
    });

    // Form Submit: Send email via AJAX
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const recipientName  = nameInput.value;
        const recipientEmail = emailInput.value;
        const recipientType  = typeSelect.value;
        const subject        = subjectInput.value;
        const messageText    = messageInput.value;

        if (!recipientEmail || !subject || !messageText) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

        fetch("{{ route('email.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                recipient_name:  recipientName,
                recipient_email: recipientEmail,
                recipient_type:  recipientType,
                subject:         subject,
                message:         messageText,
                message_type:    'Direct'
            })
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="send" style="width:15px;height:15px;" class="me-1"></i><span>Send Email</span>';
            lucide.createIcons();

            if (data.success) {
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('emailQuickModal'));
                if (modalInstance) modalInstance.hide();

                // Show success toast
                const toastHtml = `<div class="position-fixed top-0 end-0 p-3" style="z-index:9999">
                    <div class="toast align-items-center text-white bg-success border-0 show" role="alert">
                        <div class="d-flex">
                            <div class="toast-body fw-semibold">
                                ✅ Email sent successfully to ${recipientEmail}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>`;
                document.body.insertAdjacentHTML('beforeend', toastHtml);
                setTimeout(() => document.querySelectorAll('.position-fixed.top-0.end-0').forEach(el => el.remove()), 4000);
            } else {
                alert('❌ Email failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="send" style="width:15px;height:15px;" class="me-1"></i><span>Send Email</span>';
            lucide.createIcons();
            alert('❌ Network error. Please try again.');
        });
    });
});
</script>
