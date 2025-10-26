<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>V-Track Sidebar</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
</head>
<body>
    <div class="sidebar-layout">
        <nav class="sidebar">
            <div class="sidebar-title">V-Track</div>
            <ul class="sidebar-tabs">
                <li><a href="<?= base_url('dashboard') ?>" class="sidebar-link">Dashboard</a></li>
                <li><a href="<?= base_url('add-details') ?>" class="sidebar-link">Add Details</a></li>
                <li><a href="<?= base_url('view-details') ?>" class="sidebar-link">View Details</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <h2 class="dashboard-title">Dashboard</h2>
            <p>Welcome to your dashboard. Select a tab on the left to view or add details.</p>
        </main>
    </div>
</body>
</html>
