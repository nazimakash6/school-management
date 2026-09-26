/**
 * Dashboard Page Logic
 */

(function () {
  'use strict';

  const DashboardApp = {
    init() {
      this.initCharts();
      this.initCalendar();
      this.initTasks();
      this.initAttendanceRing();
    },

    getChartColors() {
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      return {
        text: isDark ? '#94a3b8' : '#64748b',
        grid: isDark ? '#334155' : '#e2e8f0',
        primary: '#6366f1',
        primaryLight: isDark ? 'rgba(99, 102, 241, 0.3)' : 'rgba(99, 102, 241, 0.15)',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        blue: '#3b82f6',
      };
    },

    initCharts() {
      this.renderFeeChart();
      this.renderAttendanceChart();
      this.renderStudentGrowthChart();
      this.renderCampusChart();

      // Re-render on theme change
      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          if (mutation.attributeName === 'data-theme') {
            this.renderFeeChart();
            this.renderAttendanceChart();
            this.renderStudentGrowthChart();
            this.renderCampusChart();
          }
        });
      });
      observer.observe(document.documentElement, { attributes: true });
    },

    renderFeeChart() {
      const ctx = document.getElementById('feeCollectionChart');
      if (!ctx) return;

      const colors = this.getChartColors();
      if (this.feeChart) this.feeChart.destroy();

      this.feeChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          datasets: [
            {
              label: 'Collected',
              data: [450000, 520000, 480000, 610000, 590000, 650000, 620000, 710000, 680000, 750000, 720000, 800000],
              borderColor: colors.primary,
              backgroundColor: colors.primaryLight,
              fill: true,
              tension: 0.4,
              borderWidth: 2,
              pointRadius: 3,
              pointHoverRadius: 5,
            },
            {
              label: 'Target',
              data: [500000, 550000, 550000, 650000, 650000, 700000, 700000, 750000, 750000, 800000, 800000, 850000],
              borderColor: colors.blue,
              backgroundColor: 'transparent',
              borderDash: [5, 5],
              tension: 0.4,
              borderWidth: 2,
              pointRadius: 0,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: colors.text === '#94a3b8' ? '#1e293b' : '#ffffff',
              titleColor: colors.text === '#94a3b8' ? '#f8fafc' : '#0f172a',
              bodyColor: colors.text === '#94a3b8' ? '#cbd5e1' : '#475569',
              borderColor: colors.grid,
              borderWidth: 1,
              padding: 10,
              callbacks: {
                label: (context) => 'Rs. ' + context.parsed.y.toLocaleString(),
              },
            },
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { color: colors.text, font: { size: 11 } },
            },
            y: {
              grid: { color: colors.grid, drawBorder: false },
              ticks: {
                color: colors.text,
                font: { size: 11 },
                callback: (value) => 'Rs.' + value / 1000 + 'k',
              },
            },
          },
        },
      });
    },

    renderAttendanceChart() {
      const ctx = document.getElementById('attendanceChart');
      if (!ctx) return;

      const colors = this.getChartColors();
      if (this.attendanceChart) this.attendanceChart.destroy();

      this.attendanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
          datasets: [
            {
              label: 'Present',
              data: [92, 94, 91, 95, 89, 85],
              backgroundColor: colors.success,
              borderRadius: 6,
            },
            {
              label: 'Absent',
              data: [5, 4, 6, 3, 7, 8],
              backgroundColor: colors.danger,
              borderRadius: 6,
            },
            {
              label: 'Leave',
              data: [3, 2, 3, 2, 4, 7],
              backgroundColor: colors.warning,
              borderRadius: 6,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { color: colors.text, usePointStyle: true, padding: 20 } },
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { color: colors.text, font: { size: 11 } },
              stacked: true,
            },
            y: {
              grid: { color: colors.grid, drawBorder: false },
              ticks: { color: colors.text, font: { size: 11 } },
              stacked: true,
              max: 100,
            },
          },
        },
      });
    },

    renderStudentGrowthChart() {
      const ctx = document.getElementById('studentGrowthChart');
      if (!ctx) return;

      const colors = this.getChartColors();
      if (this.studentGrowthChart) this.studentGrowthChart.destroy();

      this.studentGrowthChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['2019', '2020', '2021', '2022', '2023', '2024'],
          datasets: [
            {
              label: 'Students',
              data: [1200, 1350, 1480, 1620, 1850, 2100],
              borderColor: colors.success,
              backgroundColor: isDark => 'rgba(16, 185, 129, 0.1)',
              fill: true,
              tension: 0.4,
              borderWidth: 2,
              pointRadius: 4,
              pointHoverRadius: 6,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: {
              grid: { display: false },
              ticks: { color: colors.text, font: { size: 11 } },
            },
            y: {
              grid: { color: colors.grid, drawBorder: false },
              ticks: { color: colors.text, font: { size: 11 } },
            },
          },
        },
      });
    },

    renderCampusChart() {
      const ctx = document.getElementById('campusChart');
      if (!ctx) return;

      const colors = this.getChartColors();
      if (this.campusChart) this.campusChart.destroy();

      this.campusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Main Campus', 'North Campus', 'South Campus', 'City Campus'],
          datasets: [
            {
              data: [850, 520, 430, 300],
              backgroundColor: [colors.primary, colors.blue, colors.success, colors.warning],
              borderWidth: 0,
              hoverOffset: 4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '70%',
          plugins: {
            legend: { position: 'bottom', labels: { color: colors.text, usePointStyle: true, padding: 16 } },
          },
        },
      });
    },

    initCalendar() {
      const calendarGrid = document.getElementById('miniCalendar');
      if (!calendarGrid) return;

      const date = new Date();
      const year = date.getFullYear();
      const month = date.getMonth();
      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      const today = date.getDate();

      const dayNames = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
      let html = dayNames.map((d) => `<div class="calendar-day-header">${d}</div>`).join('');

      for (let i = 0; i < firstDay; i++) {
        html += `<div class="calendar-day other-month">${new Date(year, month, -firstDay + i + 1).getDate()}</div>`;
      }

      for (let i = 1; i <= daysInMonth; i++) {
        const isToday = i === today;
        const hasEvent = [3, 8, 15, 22, 28].includes(i);
        html += `<div class="calendar-day ${isToday ? 'today' : ''} ${hasEvent ? 'event' : ''}">${i}</div>`;
      }

      const remaining = (7 - ((firstDay + daysInMonth) % 7)) % 7;
      for (let i = 1; i <= remaining; i++) {
        html += `<div class="calendar-day other-month">${i}</div>`;
      }

      calendarGrid.innerHTML = html;
    },

    initTasks() {
      document.querySelectorAll('.task-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('click', () => {
          const item = checkbox.closest('.task-item');
          checkbox.classList.toggle('checked');
          item.classList.toggle('completed');

          if (checkbox.classList.contains('checked')) {
            checkbox.innerHTML = '<i data-lucide="check" style="width: 0.75rem; height: 0.75rem;"></i>';
          } else {
            checkbox.innerHTML = '';
          }
          if (window.lucide) lucide.createIcons();
        });
      });
    },

    initAttendanceRing() {
      const ring = document.getElementById('attendanceRingProgress');
      if (!ring) return;

      const radius = ring.r.baseVal.value;
      const circumference = 2 * Math.PI * radius;
      ring.style.strokeDasharray = `${circumference} ${circumference}`;
      ring.style.strokeDashoffset = circumference;

      setTimeout(() => {
        const percent = 92;
        const offset = circumference - (percent / 100) * circumference;
        ring.style.strokeDashoffset = offset;
      }, 300);
    },
  };

  document.addEventListener('DOMContentLoaded', () => {
    DashboardApp.init();
  });

  window.DashboardApp = DashboardApp;
})();
