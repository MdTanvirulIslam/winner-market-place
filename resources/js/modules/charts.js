// Dependency-free bar chart (from the winner-admin-template), fed with real
// data injected by Blade as window.__adminChart = {weekly: {labels, values,
// display}, monthly: {...}}. `display` holds preformatted tooltip strings.
const AppCharts = {
    data() {
        return window.__adminChart || null;
    },

    renderChart(range) {
        const data = this.data()?.[range];
        const chart = document.getElementById('barChart');

        if (!data || !chart) {
            return;
        }

        chart.innerHTML = '';
        const max = Math.max(...data.values, 1);

        data.values.forEach((value, index) => {
            const col = document.createElement('div');
            col.className = 'bar-col';

            const bar = document.createElement('div');
            bar.className = 'bar';
            bar.style.height = '0%';
            bar.title = data.display?.[index] ?? String(value);

            const label = document.createElement('div');
            label.className = 'bar-label';
            label.textContent = data.labels[index];

            col.appendChild(bar);
            col.appendChild(label);
            chart.appendChild(col);

            requestAnimationFrame(() => {
                window.setTimeout(() => {
                    bar.style.height = `${Math.max(4, (value / max) * 100)}%`;
                }, index * 60);
            });
        });

        document.querySelectorAll('[data-chart-range]').forEach((button) => {
            button.classList.toggle('active', button.dataset.chartRange === range);
        });
    },

    animateProgressBars() {
        document.querySelectorAll('.progress-fill[data-width]').forEach((bar, index) => {
            window.setTimeout(() => {
                bar.style.width = bar.dataset.width;
            }, 300 + index * 150);
        });
    },

    init() {
        if (document.getElementById('barChart')) {
            this.renderChart('weekly');
        }

        window.setTimeout(() => this.animateProgressBars(), 600);
    },
};

export default AppCharts;
