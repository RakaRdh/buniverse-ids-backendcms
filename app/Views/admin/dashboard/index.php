<div class="page-header">
    <div>
        <h1 class="page-title">Executive Dashboard</h1>
        <p class="page-subtitle">Live telemetry and operational metrics for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? 'Annual Summit') ?> (<?= esc($activeEvent['year'] ?? date('Y')) ?>)</p>
    </div>
    <div style="display:flex; gap:12px;">
        <a href="<?= base_url('admin/event-settings') ?>" class="btn btn-outline">Settings</a>
        <a href="<?= base_url('admin/schedules') ?>" class="btn btn-primary">Manage Schedule</a>
    </div>
</div>

<!-- ROW 1: 3 KPI Summary Cards + Registration Telemetry Summary -->
<div class="dashboard-kpi-grid metric-grid-3">
    <!-- Confirmed Speakers -->
    <div class="metric-card" style="padding: 16px;">
        <div class="metric-icon-box teal">
            <svg class="icon" viewBox="0 0 24 24"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
        </div>
        <div class="metric-info">
            <div class="metric-label">Speakers</div>
            <div class="metric-value"><?= number_format($stats['total_speakers'] ?? 0) ?></div>
            <div class="metric-sub">Confirmed Lineup</div>
        </div>
    </div>

    <!-- Moderators
    <div class="metric-card" style="padding: 16px;">
        <div class="metric-icon-box blue">
            <svg class="icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
        <div class="metric-info">
            <div class="metric-label">Moderators</div>
            <div class="metric-value"><?= number_format($stats['total_moderators'] ?? 0) ?></div>
            <div class="metric-sub">Track Facilitators</div>
        </div>
    </div> -->

    <!-- Schedule Tracks -->
    <div class="metric-card" style="padding: 16px;">
        <div class="metric-icon-box teal">
            <svg class="icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div class="metric-info">
            <div class="metric-label">Sessions</div>
            <div class="metric-value"><?= number_format($stats['total_sessions'] ?? 0) ?></div>
            <div class="metric-sub">Plenary & Rooms</div>
        </div>
    </div>

    <!-- Sponsors & Partners
    <div class="metric-card" style="padding: 16px;">
        <div class="metric-icon-box blue">
            <svg class="icon" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div class="metric-info">
            <div class="metric-label">Sponsors</div>
            <div class="metric-value"><?= number_format($stats['total_sponsors'] ?? 0) ?></div>
            <div class="metric-sub">Commercial Partners</div>
        </div>
    </div> -->

    <!-- Registration Telemetry -->
    <div class="metric-card" style="padding: 16px;">
        <div class="metric-icon-box teal">
            <svg class="icon" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
        </div>
        <div class="metric-info">
            <div class="metric-label">Ticket Button Clicks</div>
            <div class="metric-value" style="color: var(--primary-1-7);"><?= number_format($stats['ticket_clicks'] ?? 0) ?></div>
            <div class="metric-sub">Live CTA Conversions</div>
        </div>
    </div>
</div>

<!-- ROW 2: Daily Ticket Click Line Graph (ApexCharts Spline) -->
<div class="cms-card" style="margin-bottom: 24px;">
    <div class="card-title-bar">
        <div style="display:flex; align-items:center; gap:8px;">
            <svg class="icon" style="width:16px; height:16px; stroke:var(--primary-1-7);" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            <span class="card-title-text">Event Ticket Telemetry (Real-Time Click Curve)</span>
        </div>
        <span class="status-pill status-published" style="font-size:12px;">Live Telemetry</span>
    </div>
    <div class="cms-card-body" style="padding: 20px 24px;">
        <div id="ticketTelemetryChart" style="min-height: 290px; width: 100%;"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chartPayload = <?= json_encode($chartPayload ?? ['categories' => [], 'series' => []]) ?>;
    const chartEl = document.querySelector("#ticketTelemetryChart");
    
    if (chartEl && window.ApexCharts) {
        const options = {
            series: chartPayload.series.length ? chartPayload.series : [
                { name: 'Daily Ticket Clicks', data: [0, 0, 0, 0, 0, 0, 0] },
                { name: 'Cumulative Clicks', data: [0, 0, 0, 0, 0, 0, 0] }
            ],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Montserrat, sans-serif',
                animations: { enabled: true, easing: 'easeinout', speed: 500 }
            },
            colors: ['#0b516c', '#38bdf8'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: [3, 2] },
            xaxis: {
                categories: chartPayload.categories.length ? chartPayload.categories : ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '11px',
                        fontFamily: 'Montserrat, sans-serif',
                        fontWeight: 500
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '11px',
                        fontFamily: 'Montserrat, sans-serif'
                    },
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            grid: {
                borderColor: 'var(--line)',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } }
            },
            tooltip: {
                theme: 'dark',
                x: { show: true },
                y: {
                    formatter: function(val) {
                        return val + " clicks";
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontFamily: 'Montserrat, sans-serif',
                fontSize: '12px',
                markers: { radius: 12 }
            }
        };

        const chart = new ApexCharts(chartEl, options);
        chart.render();
    }
});
</script>
