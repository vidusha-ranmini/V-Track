<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>V-Track</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .sidebar-layout {
            display: flex;
            min-height: 100vh;
            background: #f5f8fd;
            margin: 0;
            width: 100vw;
        }
        .sidebar {
            width: 220px;
            background: #070d69ff;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 32px 0;
            box-shadow: 2px 0 12px rgba(25, 118, 210, 0.08);
            margin: 0;
            min-height: 100vh;
        }
        .sidebar-bottom {
            margin-top: auto;
            width: 100%;
            display: flex;
            justify-content: center;
            padding-bottom: 24px;
        }
        .sidebar-tabs {
            list-style: none;
            padding: 0;
            width: 100%;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            width: auto;
            min-width: 160px;
            max-width: 180px;
            padding: 14px 24px;
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
            border-radius: 6px 0 0 6px;
            margin-bottom: 8px;
            transition: background 0.2s;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: #1565c0;
        }
        .main-content {
            flex: 1;
            padding: 0;
            display: flex;
            align-items: stretch;
            justify-content: flex-start;
        }
        .main-iframe {
            border: none;
            width: 100%;
            height: 100vh;
        }
        .sidebar-icon {
            font-size: 1.2em;
            width: 22px;
            text-align: center;
        }
    </style>
    <script>
        function loadContent(url, el) {
            document.getElementById('main-iframe').src = url;
            // Remove active from all links
            var links = document.querySelectorAll('.sidebar-link');
            links.forEach(function(link){ link.classList.remove('active'); });
            // Add active to clicked link
            el.classList.add('active');
        }
        function logoutAndRedirect() {
            var logoutUrl = '<?= base_url('logout') ?>';
            var landing = '<?= base_url() ?>';
            // Try to call logout endpoint then redirect to landing page
            fetch(logoutUrl, { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function() { window.top.location.href = landing; })
                .catch(function() { window.top.location.href = landing; });
        }
    </script>
</head>
<body>
    <div class="sidebar-layout">
        <nav class="sidebar">
            <div class="sidebar-title">V-Track</div>
            <ul class="sidebar-tabs">
                <li><a href="#" class="sidebar-link active" onclick="loadContent('<?= base_url('dashboard') ?>', this);return false;"><span class="sidebar-icon"><i class="fas fa-tachometer-alt"></i></span>Dashboard</a></li>
                <li><a href="#" class="sidebar-link" onclick="loadContent('<?= base_url('add-details') ?>', this);return false;"><span class="sidebar-icon"><i class="fas fa-user-plus"></i></span>Add Details</a></li>
                <li><a href="#" class="sidebar-link" onclick="loadContent('<?= base_url('view-details') ?>', this);return false;"><span class="sidebar-icon"><i class="fas fa-list"></i></span>View Details</a></li>
                <li><a href="#" class="sidebar-link" onclick="loadContent('<?= base_url('add-business') ?>', this);return false;"><span class="sidebar-icon"><i class="fas fa-store"></i></span>Add Business</a></li>
                <li><a href="#" class="sidebar-link" onclick="loadContent('<?= base_url('road-lamps') ?>', this);return false;"><span class="sidebar-icon"><i class="fas fa-lightbulb"></i></span>Road Lamps</a></li>
                <li><a href="#" class="sidebar-link" onclick="loadContent('<?= base_url('roads-details') ?>', this);return false;"><span class="sidebar-icon"><i class="fas fa-road"></i></span>Roads Details</a></li>
            </ul>

            <div class="sidebar-bottom">
                <a href="#" class="sidebar-link" onclick="logoutAndRedirect(); return false;"><span class="sidebar-icon"><i class="fas fa-sign-out-alt"></i></span>Logout</a>
            </div>
        </nav>
        <main class="main-content">
            <iframe id="main-iframe" class="main-iframe" src="<?= base_url('dashboard') ?>"></iframe>
        </main>
    </div>
</body>
</html>
