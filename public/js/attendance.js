/**
 * Attendance Page Interactive Script
 */

(function () {
  'use strict';

  function updateStatusBadge(input) {
    if (!input || !input.checked) return;
    const row = input.closest('tr');
    if (!row) return;

    const val = input.value;
    const statusCell = row.querySelector('.final-status-cell');

    if (statusCell) {
      let badgeHtml = '';
      switch (val) {
        case 'present':
          badgeHtml = '<span class="badge bg-success-subtle text-success fw-semibold fs-7 px-2.5 py-1">Present</span>';
          break;
        case 'late':
          badgeHtml = '<span class="badge bg-warning-subtle text-warning fw-semibold fs-7 px-2.5 py-1">Late</span>';
          break;
        case 'absent':
          badgeHtml = '<span class="badge bg-danger-subtle text-danger fw-semibold fs-7 px-2.5 py-1">Absent</span>';
          break;
        case 'leave':
          badgeHtml = '<span class="badge bg-secondary-subtle text-secondary fw-semibold fs-7 px-2.5 py-1">Leave</span>';
          break;
        default:
          badgeHtml = '<span class="badge bg-light text-muted border fs-7 px-2.5 py-1">Pending</span>';
      }
      statusCell.innerHTML = badgeHtml;
    }
  }

  window.toggleSelectAll = function (selectAll) {
    const checkboxes = document.querySelectorAll('.row-select-checkbox');
    checkboxes.forEach((chk) => {
      if (!chk.disabled) {
        chk.checked = selectAll.checked;
      }
    });
    window.updateSelectedCount();
  };

  window.updateSelectedCount = function () {
    const allBoxes = document.querySelectorAll('.row-select-checkbox');
    const checkedBoxes = document.querySelectorAll('.row-select-checkbox:checked');
    const countBadge = document.getElementById('selectedCountBadge');
    const selectAllChk = document.getElementById('selectAllCheckbox');

    if (selectAllChk && allBoxes.length > 0) {
      selectAllChk.checked = checkedBoxes.length > 0 && checkedBoxes.length === allBoxes.length;
    }

    if (countBadge) {
      if (checkedBoxes.length > 0) {
        countBadge.innerHTML = `<span class="badge bg-primary px-2.5 py-1 fs-7 fw-semibold"><i data-lucide="check-square" style="width:13px;height:13px;" class="me-1"></i> ${checkedBoxes.length} Selected (Applies to checked only)</span>`;
      } else {
        countBadge.innerHTML = `<span class="badge bg-light text-dark border px-2.5 py-1 fs-7"><i data-lucide="users" style="width:13px;height:13px;" class="me-1"></i> All (${allBoxes.length})</span>`;
      }
      if (window.lucide) lucide.createIcons();
    }
  };

  window.markAttendanceBulk = function (status) {
    const checkedRowBoxes = document.querySelectorAll('.row-select-checkbox:checked');

    if (checkedRowBoxes.length > 0) {
      // Apply ONLY to checked rows
      checkedRowBoxes.forEach((chk) => {
        const row = chk.closest('tr');
        if (row) {
          const radio = row.querySelector(`input[name^="attendance"][value="${status}"]`);
          if (radio && !radio.disabled) {
            radio.checked = true;
            updateStatusBadge(radio);
          }
        }
      });
    } else {
      // Apply to ALL enabled rows in the table
      const radios = document.querySelectorAll(`input[name^="attendance"][value="${status}"]`);
      radios.forEach((radio) => {
        if (!radio.disabled) {
          radio.checked = true;
          updateStatusBadge(radio);
        }
      });
    }
  };

  function initAttendance() {
    if (window.lucide) lucide.createIcons();

    // Update status badge for all currently checked radios
    document.querySelectorAll('input[name^="attendance"]:checked').forEach(updateStatusBadge);

    // Initial update for selected count
    window.updateSelectedCount();

    // Listen for clicks/changes on any attendance radio buttons
    document.addEventListener('change', (e) => {
      if (e.target && e.target.matches('input[name^="attendance"]')) {
        updateStatusBadge(e.target);
      }
    });

    document.addEventListener('click', (e) => {
      if (e.target && e.target.matches('input[name^="attendance"]')) {
        updateStatusBadge(e.target);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAttendance);
  } else {
    initAttendance();
  }
})();
