<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - V-Track</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: #f5f8fd;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .dashboard-container {
            width: 100%;
            margin: 0;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(25, 118, 210, 0.10);
            padding: 32px 24px;
        }
        .dashboard-title {
            color: #070d69ff;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 24px;
        }
        .quick-actions {
            display: flex;
            gap: 24px;
            margin-bottom: 32px;
        }
        .quick-action-btn {
            background: #1976d2;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 18px 32px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(25, 118, 210, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.2s;
        }
        .quick-action-btn:hover {
            background: #1565c0;
        }
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 32px;
        }
        .chart-card {
            background: #f5f8fd;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(25,118,210,0.08);
            padding: 24px 18px 18px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .chart-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 12px;
        }
        canvas {
            max-width: 100%;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div class="dashboard-title"><i class="fas fa-chart-pie"></i> Dashboard Overview</div>
            <div>
                <a href="<?= base_url('dashboard/report') ?>" class="quick-action-btn" style="padding:10px 16px; font-size:0.95rem;"> <i class="fas fa-file-download"></i> Generate Report</a>
            </div>
        </div>
        <!-- <div class="quick-actions">
            <button class="quick-action-btn" onclick="window.location.href='<?= base_url('add-details') ?>'">
                <i class="fas fa-user-plus"></i> Add Details
            </button>
            <button class="quick-action-btn" onclick="window.location.href='<?= base_url('view-details') ?>'">
                <i class="fas fa-list"></i> View Details
            </button>
        </div> -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-title">Job Categories</div>
                <canvas id="jobChart"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">Age Categories</div>
                <canvas id="ageChart"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">Resident Type Counts</div>
                <canvas id="residentChart"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">Waste Disposal</div>
                <canvas id="wasteChart"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">Offer Receiving</div>
                <canvas id="offerChart"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">Disabled Members</div>
                <canvas id="disabledChart"></canvas>
            </div>
        </div>
    </div>
    <script>
    // Data injected by controller. Each is { labels: [...], data: [...] }
    const jobRaw = <?= isset($jobData) ? $jobData : 'null' ?>;
    const ageRaw = <?= isset($ageData) ? $ageData : 'null' ?>;
    const residentRaw = <?= isset($residentData) ? $residentData : 'null' ?>;
    const wasteRaw = <?= isset($wasteData) ? $wasteData : 'null' ?>;
    const offerRaw = <?= isset($offerData) ? $offerData : 'null' ?>;
    const disabledRaw = <?= isset($disabledData) ? $disabledData : 'null' ?>;

    // Helper to build Chart.js dataset objects from raw data
    function buildDataset(raw, color) {
        if (!raw) return null;
        return { labels: raw.labels || [], datasets: [{ label: 'Count', data: raw.data || [], backgroundColor: color || '#1976d2' }] };
    }

    const jobData = buildDataset(jobRaw, '#1976d2');
    const ageData = buildDataset(ageRaw, ['#1976d2','#388e3c','#fbc02d','#d32f2f','#7b1fa2','#0288d1']);
    const residentData = buildDataset(residentRaw, ['#1976d2','#fbc02d']);
    const wasteData = buildDataset(wasteRaw, ['#38888eff','#2fd33dff']);
    const offerData = buildDataset(offerRaw, ['#1976d2','#fbc02d','#7b1fa2','#388e3c']);
    const disabledData = buildDataset(disabledRaw, ['#d32f2f','#388e3c']);
    // Chart rendering
    new Chart(document.getElementById('jobChart'), {
        type: 'bar',
        data: jobData,
        options: {responsive:true, plugins:{legend:{display:false}}}
    });
    new Chart(document.getElementById('ageChart'), {
        type: 'pie',
        data: ageData,
        options: {responsive:true}
    });
    new Chart(document.getElementById('residentChart'), {
        type: 'doughnut',
        data: residentData,
        options: {responsive:true}
    });
    new Chart(document.getElementById('wasteChart'), {
        type: 'pie',
        data: wasteData,
        options: {responsive:true}
    });
    new Chart(document.getElementById('offerChart'), {
        type: 'bar',
        data: offerData,
        options: {responsive:true, plugins:{legend:{display:false}}}
    });
    new Chart(document.getElementById('disabledChart'), {
        type: 'doughnut',
        data: disabledData,
        options: {responsive:true}
    });
    </script>
</body>
</html>
