<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Road Lamps - V-Track</title>
    <?= csrf_meta() ?>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: #f5f8fd;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 0;
        }
        .lamps-container {
            max-width: 100%;
            width: 100%;
            margin: 0;
            background: #fff;
            border-radius: 0;
            box-shadow: none;
            padding: 32px 40px;
            min-height: 100vh;
        }
        h2 {
            color: #070d69ff;
            margin-bottom: 24px;
            font-size: 1.8rem;
        }
        .filters-section {
            margin-bottom: 24px;
            padding: 20px;
            background: #f5f8fd;
            border-radius: 8px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .filter-group {
            flex: 1;
            min-width: 200px;
            margin-right: 16px;
        }
        .filter-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
            font-size: 0.9rem;
        }
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #bdbdbd;
            border-radius: 6px;
            font-size: 0.95rem;
        }
        .lamps-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 8px;
            overflow: hidden;
        }
        .lamps-table thead {
            background: #070d69ff;
            color: #fff;
        }
        .lamps-table th,
        .lamps-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .lamps-table th {
            font-weight: 600;
            font-size: 0.95rem;
        }
        .lamps-table tbody tr:hover {
            background: #f5f8fd;
        }
        .lamps-table tbody tr:last-child td {
            border-bottom: none;
        }
        .broken-row {
            background-color: #ffebee !important;
        }
        .broken-row:hover {
            background-color: #ffcdd2 !important;
        }
        .no-lamps {
            text-align: center;
            padding: 32px;
            color: #888;
            font-style: italic;
        }
        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #4caf50;
            transition: .3s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #d32f2f;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .status-label {
            display: inline-block;
            min-width: 60px;
            font-weight: 600;
            margin-left: 8px;
        }
        .status-working {
            color: #4caf50;
        }
        .status-broken {
            color: #d32f2f;
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 20px;
            background: #4caf50;
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            display: none;
            animation: slideIn 0.3s ease;
        }
        .toast.error {
            background: #f44336;
        }
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="lamps-container">
        <h2><i class="fas fa-lightbulb"></i> Road Lamps</h2>
        
        <!-- Filters -->
        <div class="filters-section">
            <div class="filter-group">
                <label>Search Lamp Number</label>
                <input type="text" id="filter-lamp" placeholder="Type to search lamp number">
            </div>
            <div class="filter-group">
                <label>Search Road</label>
                <select id="filter-road">
                    <option value="">All Roads</option>
                    <?php if (isset($roads) && is_array($roads)): ?>
                        <?php foreach ($roads as $road): ?>
                            <option value="<?= esc($road['name']) ?>"><?= esc($road['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select id="filter-status">
                    <option value="">All Lamps</option>
                    <option value="working">Working</option>
                    <option value="broken">Broken</option>
                </select>
            </div>
        </div>
        
        <?php if (isset($lamps) && count($lamps) > 0): ?>
            <div style="overflow-x: auto;">
                <table class="lamps-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Lamp Number</th>
                            <th>Road</th>
                            <th>Sub Road</th>
                            <th>Address</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="lamps-table-body">
                        <?php foreach ($lamps as $index => $lamp): ?>
                            <tr class="<?= $lamp['is_broken'] == 1 ? 'broken-row' : '' ?>" 
                                data-id="<?= $lamp['id'] ?>"
                                data-lamp="<?= esc($lamp['lamp_number']) ?>"
                                data-road="<?= esc($lamp['road_name'] ?? '') ?>"
                                data-status="<?= $lamp['is_broken'] == 1 ? 'broken' : 'working' ?>">
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= esc($lamp['lamp_number']) ?></strong></td>
                                <td><?= esc($lamp['road_name'] ?? '-') ?></td>
                                <td><?= esc($lamp['sub_road_name'] ?? '-') ?></td>
                                <td><?= esc($lamp['address_line'] ?? '-') ?></td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" 
                                               <?= $lamp['is_broken'] == 1 ? 'checked' : '' ?> 
                                               onchange="toggleLampStatus(<?= $lamp['id'] ?>, this)">
                                        <span class="slider"></span>
                                    </label>
                                    <span class="status-label <?= $lamp['is_broken'] == 1 ? 'status-broken' : 'status-working' ?>" 
                                          id="status-label-<?= $lamp['id'] ?>">
                                        <?= $lamp['is_broken'] == 1 ? 'Broken' : 'Working' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-lamps">
                <i class="fas fa-info-circle" style="font-size: 2rem; color: #bdbdbd; margin-bottom: 8px;"></i>
                <p>No road lamps in the system yet.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div id="toast" class="toast"></div>
    
    <script>
    // Helper to get CSRF token
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
        return meta ? meta.getAttribute('content') : '';
    }
    
    // Helper to get CSRF header name
    function getCsrfHeaderName() {
        const meta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
        return meta ? meta.getAttribute('name') : 'X-CSRF-TOKEN';
    }
    
    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast ' + type;
        toast.style.display = 'block';
        
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }
    
    // Toggle lamp status
    function toggleLampStatus(lampId, checkbox) {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };
        
        const csrfToken = getCsrfToken();
        const csrfHeaderName = getCsrfHeaderName();
        if (csrfToken) {
            headers[csrfHeaderName] = csrfToken;
        }
        
        fetch('<?= base_url('lamp/toggle') ?>/' + lampId, {
            method: 'POST',
            headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update status label
                const statusLabel = document.getElementById('status-label-' + lampId);
                const row = checkbox.closest('tr');
                
                if (data.is_broken == 1) {
                    statusLabel.textContent = 'Broken';
                    statusLabel.className = 'status-label status-broken';
                    row.classList.add('broken-row');
                    row.setAttribute('data-status', 'broken');
                } else {
                    statusLabel.textContent = 'Working';
                    statusLabel.className = 'status-label status-working';
                    row.classList.remove('broken-row');
                    row.setAttribute('data-status', 'working');
                }
                
                showToast(data.message, 'success');
            } else {
                // Revert checkbox if failed
                checkbox.checked = !checkbox.checked;
                showToast(data.message || 'Failed to update lamp status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            checkbox.checked = !checkbox.checked;
            showToast('An error occurred', 'error');
        });
    }
    
    // Filter functions
    function applyFilters() {
        const lampFilter = document.getElementById('filter-lamp').value.toLowerCase();
        const roadFilter = document.getElementById('filter-road').value.toLowerCase();
        const statusFilter = document.getElementById('filter-status').value.toLowerCase();
        
        const rows = document.querySelectorAll('#lamps-table-body tr');
        let visibleCount = 0;
        
        rows.forEach((row, index) => {
            const lamp = row.getAttribute('data-lamp').toLowerCase();
            const road = row.getAttribute('data-road').toLowerCase();
            const status = row.getAttribute('data-status').toLowerCase();
            
            const matchLamp = !lampFilter || lamp.includes(lampFilter);
            const matchRoad = !roadFilter || road === roadFilter;
            const matchStatus = !statusFilter || status === statusFilter;
            
            if (matchLamp && matchRoad && matchStatus) {
                row.style.display = '';
                visibleCount++;
                row.querySelector('td:first-child').textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Add event listeners for real-time filtering
    document.getElementById('filter-lamp').addEventListener('input', applyFilters);
    document.getElementById('filter-road').addEventListener('change', applyFilters);
    document.getElementById('filter-status').addEventListener('change', applyFilters);
    </script>
</body>
</html>
