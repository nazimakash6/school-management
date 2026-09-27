import * as bootstrap from 'bootstrap';
import * as lucide from 'lucide';
import Chart from 'chart.js/auto';

import './custom.js';

// Expose globals early so page-specific scripts (loaded outside Vite) can use them
window.bootstrap = bootstrap;
window.Chart = Chart;
window.lucide = lucide;

document.addEventListener('DOMContentLoaded', () => {
    // Lucide Icons
    lucide.createIcons({ icons: lucide.icons });

    // Bootstrap Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
        new bootstrap.Tooltip(element);
    });
});