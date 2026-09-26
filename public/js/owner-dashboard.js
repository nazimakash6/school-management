/**
 * Owner Dashboard Page Logic
 */

(function () {
  'use strict';

  const OwnerDashboardApp = {
    init() {
      this.initCharts();
    },

    getColors() {
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      return {
        text: isDark ? '#94a3b8' : '#64748b',
        grid: isDark ? '#334155' : '#e2e8f0',
        primary: '#6366f1',
        success: '#10b981',
        danger: '#ef4444',
        blue: '#3b82f6',
        warning: '#f59e0b',
      };
    },

    initCharts() {
      this.renderRevenueChart();
      this.renderCampusChart();
      this.renderGrowthChart();
      this.renderAttendanceChart();
      this.renderSubjectChart();

      const observer = new MutationObserver(() => {
        this.renderRevenueChart();
        this.renderCampusChart();
        this.renderGrowthChart();
        this.renderAttendanceChart();
        this.renderSubjectChart();
      });
      observer.observe(document.documentElement, { attributes: true });
    },

    renderRevenueChart() {
      const ctx = document.getElementById('ownerRevenueChart');
      if (!ctx) return;
      const c = this.getColors();
      if (this.revenueChart) this.revenueChart.destroy();
      this.revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [
            { label: 'Revenue', data: [6.5, 7.2, 6.8, 8.1, 7.9, 8.5], backgroundColor: c.primary, borderRadius: 6 },
            { label: 'Expenses', data: [4.5, 4.8, 4.6, 5.2, 5.0, 5.4], backgroundColor: c.danger, borderRadius: 6 },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom', labels: { color: c.text } } },
          scales: {
            x: { grid: { display: false }, ticks: { color: c.text } },
            y: { grid: { color: c.grid, drawBorder: false }, ticks: { color: c.text, callback: (v) => 'Rs.' + v + 'M' } },
          },
        },
      });
    },

    renderCampusChart() {
      const ctx = document.getElementById('ownerCampusChart');
      if (!ctx) return;
      const c = this.getColors();
      if (this.campusChart) this.campusChart.destroy();
      this.campusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Main', 'North', 'South', 'City'],
          datasets: [{ data: [18.5, 12.2, 9.8, 7.5], backgroundColor: [c.primary, c.blue, c.success, c.warning], borderWidth: 0 }],
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { color: c.text } } } },
      });
    },

    renderGrowthChart() {
      const ctx = document.getElementById('ownerGrowthChart');
      if (!ctx) return;
      const c = this.getColors();
      if (this.growthChart) this.growthChart.destroy();
      this.growthChart = new Chart(ctx, {
        type: 'line',
        data: { labels: ['2019', '2020', '2021', '2022', '2023', '2024'], datasets: [{ data: [1200, 1350, 1480, 1620, 1850, 2100], borderColor: c.success, backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.4 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { color: c.text } }, y: { grid: { color: c.grid, drawBorder: false }, ticks: { color: c.text } } } },
      });
    },

    renderAttendanceChart() {
      const ctx = document.getElementById('ownerAttendanceChart');
      if (!ctx) return;
      const c = this.getColors();
      if (this.attendanceChart) this.attendanceChart.destroy();
      this.attendanceChart = new Chart(ctx, {
        type: 'line',
        data: { labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], datasets: [{ data: [92, 94, 91, 95, 89], borderColor: c.blue, backgroundColor: 'rgba(59,130,246,0.1)', fill: true, tension: 0.4 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { color: c.text } }, y: { grid: { color: c.grid, drawBorder: false }, ticks: { color: c.text } } } },
      });
    },

    renderSubjectChart() {
      const ctx = document.getElementById('ownerSubjectChart');
      if (!ctx) return;
      const c = this.getColors();
      if (this.subjectChart) this.subjectChart.destroy();
      this.subjectChart = new Chart(ctx, {
        type: 'radar',
        data: {
          labels: ['Math', 'Science', 'English', 'Islamiat', 'Urdu', 'Arabic'],
          datasets: [{ label: 'Avg Score', data: [82, 78, 85, 88, 80, 75], borderColor: c.primary, backgroundColor: 'rgba(99,102,241,0.2)' }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { r: { grid: { color: c.grid }, angleLines: { color: c.grid }, pointLabels: { color: c.text } } } },
      });
    },
  };

  document.addEventListener('DOMContentLoaded', () => {
    OwnerDashboardApp.init();
  });
})();
