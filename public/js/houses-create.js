/**
 * Houses Create Page Logic
 */

(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();

    const searchInput = document.getElementById('studentSearchInput');
    const studentItems = document.querySelectorAll('.student-item');
    const selectAllBtn = document.getElementById('selectAllStudentsBtn');
    const deselectAllBtn = document.getElementById('deselectAllStudentsBtn');

    if (searchInput) {
      searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        studentItems.forEach((item) => {
          const searchText = item.getAttribute('data-search-text') || '';
          if (searchText.includes(query)) {
            item.style.display = 'flex';
          } else {
            item.style.display = 'none';
          }
        });
      });
    }

    if (selectAllBtn) {
      selectAllBtn.addEventListener('click', () => {
        studentItems.forEach((item) => {
          if (item.style.display !== 'none') {
            const checkbox = item.querySelector('.student-checkbox');
            if (checkbox) checkbox.checked = true;
          }
        });
      });
    }

    if (deselectAllBtn) {
      deselectAllBtn.addEventListener('click', () => {
        studentItems.forEach((item) => {
          if (item.style.display !== 'none') {
            const checkbox = item.querySelector('.student-checkbox');
            if (checkbox) checkbox.checked = false;
          }
        });
      });
    }
  });
})();
