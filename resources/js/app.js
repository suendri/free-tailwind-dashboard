import collapse from '@alpinejs/collapse';
import { Alpine, Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';

Alpine.plugin(collapse);

async function renderPostsYearChart() {
    const chartElement = document.querySelector('[data-posts-year-chart]');

    if (!chartElement || chartElement.dataset.rendered === 'true') {
        return;
    }

    chartElement.dataset.rendered = 'true';

    const { default: ApexCharts } = await import('apexcharts');

    const chart = new ApexCharts(chartElement, {
        chart: {
            type: 'bar',
            height: 320,
            toolbar: {
                show: false,
            },
            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
        },
        series: [{
            name: 'Posts',
            data: JSON.parse(chartElement.dataset.series ?? '[]'),
        }],
        xaxis: {
            categories: JSON.parse(chartElement.dataset.labels ?? '[]'),
            labels: {
                style: {
                    colors: '#6b7280',
                },
            },
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            labels: {
                style: {
                    colors: '#6b7280',
                },
            },
        },
        colors: ['#2563eb'],
        dataLabels: {
            enabled: false,
        },
        grid: {
            borderColor: '#e5e7eb',
            strokeDashArray: 4,
        },
        plotOptions: {
            bar: {
                borderRadius: 5,
                columnWidth: '44%',
            },
        },
        tooltip: {
            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
        },
    });

    chart.render();
}

document.addEventListener('livewire:navigated', renderPostsYearChart);

Livewire.start();
