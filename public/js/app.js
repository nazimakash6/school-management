/**
 * School Management System - Shared Application Logic
 */

(function () {
  'use strict';

  const App = {
    init() {
      this.initTheme();
      this.initSidebar();
      this.initDropdowns();
      this.initNotifications();
      this.initSearch();
      this.initTooltips();
      this.initCurrentTime();
      this.initTables();
      this.initFormValidation();
    },

    // Theme Management
    initTheme() {
      const themeToggle = document.getElementById('themeToggle');
      const storedTheme = localStorage.getItem('sms-theme') || 'light';
      document.documentElement.setAttribute('data-theme', storedTheme);
      this.updateThemeIcon(storedTheme);

      if (themeToggle) {
        themeToggle.addEventListener('click', () => {
          const currentTheme = document.documentElement.getAttribute('data-theme');
          const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
          document.documentElement.setAttribute('data-theme', newTheme);
          localStorage.setItem('sms-theme', newTheme);
          this.updateThemeIcon(newTheme);
        });
      }
    },

    updateThemeIcon(theme) {
      const lightIcon = document.getElementById('themeIconLight');
      const darkIcon = document.getElementById('themeIconDark');
      if (lightIcon && darkIcon) {
        if (theme === 'dark') {
          lightIcon.classList.add('d-none');
          darkIcon.classList.remove('d-none');
        } else {
          lightIcon.classList.remove('d-none');
          darkIcon.classList.add('d-none');
        }
      }
    },

    // Sidebar Management
    initSidebar() {
      const sidebar = document.getElementById('sidebar');
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebarClose = document.getElementById('sidebarClose');
      const sidebarOverlay = document.getElementById('sidebarOverlay');

      if (!sidebar) return;

      sidebar.querySelectorAll('a').forEach((link) => {
        link.setAttribute('target', '_self');
      });

      const storedState = localStorage.getItem('sms-sidebar-collapsed');
      if (storedState === 'true') {
        document.body.classList.add('sidebar-collapsed');
      }

      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
          if (window.innerWidth < 992) {
            document.body.classList.toggle('sidebar-mobile-open');
          } else {
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem(
              'sms-sidebar-collapsed',
              document.body.classList.contains('sidebar-collapsed')
            );
          }
        });
      }

      if (sidebarClose) {
        sidebarClose.addEventListener('click', () => {
          document.body.classList.remove('sidebar-mobile-open');
        });
      }

      if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
          document.body.classList.remove('sidebar-mobile-open');
        });
      }

      // Submenu toggles
      const submenuToggles = sidebar.querySelectorAll('.sidebar-submenu-toggle');
      submenuToggles.forEach((toggle) => {
        toggle.addEventListener('click', (e) => {
          e.preventDefault();
          const parent = toggle.closest('.sidebar-item');
          const wasOpen = parent.classList.contains('open');

          // Close siblings in same level
          const siblings = parent.parentElement.querySelectorAll(':scope > .sidebar-item.open');
          siblings.forEach((sibling) => {
            if (sibling !== parent) sibling.classList.remove('open');
          });

          parent.classList.toggle('open');

          // Store open state for desktop
          if (window.innerWidth >= 992 && !document.body.classList.contains('sidebar-collapsed')) {
            this.saveSidebarState();
          }
        });
      });

      this.restoreSidebarState();
    },

    saveSidebarState() {
      const openItems = [];
      document.querySelectorAll('#sidebar .sidebar-item.open').forEach((item) => {
        const link = item.querySelector(':scope > .sidebar-link, :scope > .sidebar-submenu-toggle');
        if (link) {
          openItems.push(link.getAttribute('href') || link.dataset.target);
        }
      });
      localStorage.setItem('sms-sidebar-open', JSON.stringify(openItems));
    },

    restoreSidebarState() {
      try {
        const openItems = JSON.parse(localStorage.getItem('sms-sidebar-open') || '[]');
        openItems.forEach((target) => {
          const link = document.querySelector(
            `#sidebar .sidebar-link[href="${target}"], #sidebar .sidebar-submenu-toggle[data-target="${target}"]`
          );
          if (link) {
            link.closest('.sidebar-item').classList.add('open');
          }
        });
      } catch (e) {
        // ignore
      }
    },

    // Dropdowns
    initDropdowns() {
      document.addEventListener('click', (e) => {
        const toggle = e.target.closest('[data-bs-toggle="dropdown"]');
        if (toggle) {
          e.preventDefault();
          const menu = toggle.nextElementSibling;
          if (menu && menu.classList.contains('dropdown-menu')) {
            const isOpen = menu.classList.contains('show');
            this.closeAllDropdowns();
            if (!isOpen) {
              menu.classList.add('show');
              toggle.setAttribute('aria-expanded', 'true');
              this.positionDropdown(menu, toggle);
            }
          }
        } else if (!e.target.closest('.dropdown-menu')) {
          this.closeAllDropdowns();
        }
      });
    },

    closeAllDropdowns() {
      document.querySelectorAll('.dropdown-menu.show').forEach((menu) => {
        menu.classList.remove('show');
        const toggle = menu.previousElementSibling;
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
      });
    },

    positionDropdown(menu, toggle) {
      const rect = toggle.getBoundingClientRect();
      const menuRect = menu.getBoundingClientRect();
      const viewportWidth = window.innerWidth;
      const viewportHeight = window.innerHeight;

      let top = rect.bottom + 8;
      let left = rect.left;

      if (left + menuRect.width > viewportWidth) {
        left = rect.right - menuRect.width;
      }

      if (top + menuRect.height > viewportHeight) {
        top = rect.top - menuRect.height - 8;
      }

      menu.style.position = 'fixed';
      menu.style.top = `${top}px`;
      menu.style.left = `${left}px`;
      menu.style.zIndex = '1050';
    },

    // Notifications Panel
    initNotifications() {
      const notificationToggle = document.getElementById('notificationToggle');
      const notificationPanel = document.getElementById('notificationPanel');
      const notificationClose = document.getElementById('notificationClose');
      const notificationOverlay = document.getElementById('notificationOverlay');

      if (notificationToggle && notificationPanel) {
        notificationToggle.addEventListener('click', () => {
          notificationPanel.classList.toggle('show');
          if (notificationOverlay) notificationOverlay.classList.toggle('show');
        });
      }

      if (notificationClose) {
        notificationClose.addEventListener('click', () => {
          notificationPanel.classList.remove('show');
          if (notificationOverlay) notificationOverlay.classList.remove('show');
        });
      }

      if (notificationOverlay) {
        notificationOverlay.addEventListener('click', () => {
          notificationPanel.classList.remove('show');
          notificationOverlay.classList.remove('show');
        });
      }

      // Mark all as read
      const markAllRead = document.getElementById('markAllRead');
      if (markAllRead) {
        markAllRead.addEventListener('click', () => {
          document.querySelectorAll('.notification-item.unread').forEach((item) => {
            item.classList.remove('unread');
          });
          const badge = document.getElementById('notificationBadge');
          if (badge) badge.remove();
        });
      }
    },

    // Global Search
    initSearch() {
      const searchToggle = document.getElementById('searchToggle');
      const searchModal = document.getElementById('searchModal');
      const searchInput = document.getElementById('globalSearchInput');

      if (searchToggle && searchModal) {
        searchToggle.addEventListener('click', () => {
          const modal = new bootstrap.Modal(searchModal);
          modal.show();
          setTimeout(() => searchInput && searchInput.focus(), 100);
        });
      }

      document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
          e.preventDefault();
          if (searchModal) {
            const modal = bootstrap.Modal.getInstance(searchModal) || new bootstrap.Modal(searchModal);
            modal.show();
            setTimeout(() => searchInput && searchInput.focus(), 100);
          }
        }
      });
    },

    // Bootstrap Tooltips & Popovers
    initTooltips() {
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map((trigger) => new bootstrap.Tooltip(trigger));

      const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
      popoverTriggerList.map((trigger) => new bootstrap.Popover(trigger));
    },

    // Current Time Display
    initCurrentTime() {
      const timeEl = document.getElementById('currentTime');
      const dateEl = document.getElementById('currentDate');

      const update = () => {
        const now = new Date();
        if (timeEl) {
          timeEl.textContent = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
          });
        }
        if (dateEl) {
          dateEl.textContent = now.toLocaleDateString('en-US', {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
          });
        }
      };

      update();
      setInterval(update, 1000);
    },

    // Table utilities
    initTables() {
      // Select all checkbox
      document.querySelectorAll('[data-select-all]').forEach((master) => {
        master.addEventListener('change', () => {
          const table = master.closest('table');
          if (table) {
            table.querySelectorAll('tbody input[type="checkbox"]').forEach((cb) => {
              cb.checked = master.checked;
            });
          }
        });
      });

      // Bulk action enable/disable
      document.querySelectorAll('tbody input[type="checkbox"]').forEach((cb) => {
        cb.addEventListener('change', () => {
          const table = cb.closest('table');
          const checked = table ? table.querySelectorAll('tbody input[type="checkbox"]:checked').length : 0;
          const bulkBar = document.getElementById('bulkActionBar');
          if (bulkBar) {
            bulkBar.classList.toggle('d-none', checked === 0);
            const countEl = document.getElementById('selectedCount');
            if (countEl) countEl.textContent = checked;
          }
        });
      });
    },

    // Basic form validation visual states
    initFormValidation() {
      document.querySelectorAll('form[data-validate]').forEach((form) => {
        form.addEventListener('submit', (e) => {
          let valid = true;
          form.querySelectorAll('[required]').forEach((field) => {
            if (!field.value.trim()) {
              valid = false;
              field.classList.add('is-invalid');
            } else {
              field.classList.remove('is-invalid');
              field.classList.add('is-valid');
            }
          });

          if (!valid) {
            e.preventDefault();
            this.showToast('Please fill in all required fields', 'danger');
          }
        });
      });
    },

    // Toast helper
    showToast(message, type = 'info') {
      const container = document.getElementById('toastContainer') || this.createToastContainer();
      const toast = document.createElement('div');
      toast.className = `toast align-items-center border-0 show`;
      toast.setAttribute('role', 'alert');
      toast.setAttribute('aria-live', 'assertive');
      toast.setAttribute('aria-atomic', 'true');

      const iconMap = {
        success: 'check-circle',
        danger: 'x-circle',
        warning: 'alert-triangle',
        info: 'info',
      };

      toast.innerHTML = `
        <div class="d-flex">
          <div class="toast-body d-flex align-items-center gap-2">
            <i data-lucide="${iconMap[type] || 'info'}" class="toast-icon text-${type === 'danger' ? 'danger' : type}"></i>
            <span>${message}</span>
          </div>
          <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      `;

      container.appendChild(toast);
      if (window.lucide) lucide.createIcons({ nodes: [toast] });

      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
      }, 4000);
    },

    createToastContainer() {
      const container = document.createElement('div');
      container.id = 'toastContainer';
      container.className = 'toast-container';
      document.body.appendChild(container);
      return container;
    },
  };

  document.addEventListener('DOMContentLoaded', () => {
    App.init();
    if (window.lucide) lucide.createIcons();
  });

  window.SMSApp = App;
})();
