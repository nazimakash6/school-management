/**
 * Login Page Logic
 */

(function () {
  'use strict';

  const LoginApp = {
    init() {
      this.initTheme();
      this.initPasswordToggle();
      this.initForm();
    },

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

    initPasswordToggle() {
      const toggle = document.getElementById('passwordToggle');
      const input = document.getElementById('password');
      if (!toggle || !input) return;

      toggle.addEventListener('click', () => {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);

        const icon = toggle.querySelector('i');
        if (icon) {
          icon.setAttribute('data-lucide', type === 'password' ? 'eye' : 'eye-off');
          if (window.lucide) lucide.createIcons();
        }
      });
    },

    initForm() {
      const form = document.getElementById('loginForm');
      if (!form) return;

      form.addEventListener('submit', (e) => {
        e.preventDefault();

        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const submitBtn = document.getElementById('loginSubmit');
        let valid = true;

        [email, password].forEach((field) => {
          if (!field.value.trim()) {
            field.classList.add('is-invalid');
            valid = false;
          } else {
            field.classList.remove('is-invalid');
          }
        });

        if (!valid) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Signing in...';

        // Simulate login
        setTimeout(() => {
          window.location.href = 'pages/dashboard.html';
        }, 1200);
      });
    },
  };

  document.addEventListener('DOMContentLoaded', () => {
    LoginApp.init();
    if (window.lucide) lucide.createIcons();
  });
})();
