/**
 * Student Admission Page Logic
 */

(function () {
  'use strict';

  const AdmissionApp = {

    init() {
      this.initStepper();
      this.initAttachmentPreview();
      this.initClassSections();
    },

    initStepper() {

      const form = document.querySelector('[data-step-form]');
      if (!form) return;

      const panels = [...form.querySelectorAll('[data-step-panel]')];
      const steps = [...document.querySelectorAll('[data-step-target]')];
      const nextBtns = [...form.querySelectorAll('[data-step-next]')];
      const prevBtns = [...form.querySelectorAll('[data-step-prev]')];

      let currentStep = 0;

      function showStep(index) {

        if (index < 0 || index >= panels.length) return;

        currentStep = index;

        panels.forEach((panel, i) => {

          const active = i === index;

          panel.hidden = !active;
          panel.classList.toggle('is-active', active);

        });

        steps.forEach((step, i) => {

          step.classList.toggle('active', i === index);
          step.classList.toggle('is-completed', i < index);

          if (i === index) {
            step.setAttribute('aria-current', 'step');
          } else {
            step.removeAttribute('aria-current');
          }

        });

        const firstInput = panels[index].querySelector(
          'input, select, textarea'
        );

        if (firstInput) {
          firstInput.focus({
            preventScroll: true
          });
        }

      }

      // Next
      nextBtns.forEach(btn => {

        btn.addEventListener('click', () => {

          if (currentStep < panels.length - 1) {
            showStep(currentStep + 1);
          }

        });

      });

      // Previous
      prevBtns.forEach(btn => {

        btn.addEventListener('click', () => {

          if (currentStep > 0) {
            showStep(currentStep - 1);
          }

        });

      });

      // Step Click
      steps.forEach((step, index) => {

        step.addEventListener('click', () => {

          showStep(index);

        });

      });

      // Reset Form
      form.addEventListener('reset', () => {

        setTimeout(() => {

          showStep(0);

        }, 50);

      });

      // Initial Load
      showStep(0);

    },

    initAttachmentPreview() {
      const input = document.getElementById('attached_documents');
      const preview = document.getElementById('attachmentPreview');

      if (!input || !preview) return;

      const dt = new DataTransfer();

      input.addEventListener('change', function () {
        for (let i = 0; i < this.files.length; i++) {
          dt.items.add(this.files[i]);
        }
        input.files = dt.files;
        renderPreview();
      });

      function renderPreview() {
        preview.innerHTML = '';
        const files = dt.files;

        if (!files.length) return;

        for (let i = 0; i < files.length; i++) {
          const file = files[i];
          const size = (file.size / 1024).toFixed(1);

          const itemEl = document.createElement('div');
          itemEl.className = 'd-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 bg-white shadow-sm';
          itemEl.innerHTML = `
            <div>
              <strong class="text-primary me-1">${i + 1}.</strong>
              <span class="fw-semibold text-dark me-2">${file.name}</span>
              <small class="text-muted">(${size} KB)</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0 px-2 remove-attachment-btn" data-index="${i}">
              &times; Remove
            </button>
          `;

          preview.appendChild(itemEl);
        }

        preview.querySelectorAll('.remove-attachment-btn').forEach(btn => {
          btn.addEventListener('click', function () {
            const index = parseInt(this.getAttribute('data-index'), 10);
            dt.items.remove(index);
            input.files = dt.files;
            renderPreview();
          });
        });
      }
    },

    initClassSections() {
      const classSelect = document.getElementById('classSelect');
      const sectionSelect = document.getElementById('sectionSelect');

      if (!classSelect || !sectionSelect) return;

      function populateSections(isUserChange) {
        const pendingVal = sectionSelect.getAttribute('data-pending-value') || sectionSelect.value;

        if (!classSelect.selectedOptions || !classSelect.selectedOptions[0]) return;
        const rawSections = classSelect.selectedOptions[0].dataset.sections;
        if (!rawSections) return;

        try {
          const sections = JSON.parse(rawSections);
          if (sectionSelect.options.length <= 1 || isUserChange) {
            sectionSelect.innerHTML = '<option value="">Select section</option>';
            sections.forEach(section => {
              const opt = document.createElement('option');
              opt.value = section;
              opt.textContent = section;
              if (section === pendingVal) {
                opt.selected = true;
              }
              sectionSelect.appendChild(opt);
            });
          }
        } catch (e) {
          console.error("Error parsing sections JSON:", e);
        }
      }

      classSelect.addEventListener('change', function () {
        sectionSelect.removeAttribute('data-pending-value');
        populateSections(true);
      });

      if (classSelect.value) {
        populateSections(false);
      }
    },

    initInputFormatting() {
      const cnicFields = ['cnic_bform', 'father_cnic', 'mother_cnic', 'guardian_cnic'];
      const phoneFields = ['student_mobile_no', 'father_phone', 'mother_phone', 'guardian_primary_mobile_no', 'guardian_secondary_mobile_no', 'emergency_contact_mobile_no'];

      const sanitizeInput = (target) => {
        if (!target || !target.name) return;
        if (cnicFields.includes(target.name)) {
          target.value = target.value.replace(/[^0-9]/g, '').slice(0, 13);
        } else if (phoneFields.includes(target.name)) {
          target.value = target.value.replace(/[^0-9]/g, '').slice(0, 11);
        }
      };

      document.addEventListener('input', function (e) {
        sanitizeInput(e.target);
      });

      document.addEventListener('paste', function (e) {
        const target = e.target;
        if (!target || !target.name) return;
        setTimeout(() => sanitizeInput(target), 0);
      });
    }

  };

  document.addEventListener('DOMContentLoaded', () => {

    AdmissionApp.init();
    AdmissionApp.initInputFormatting();
    if (window.lucide) {
      lucide.createIcons();
    }

  });

})();