<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roads Details - V-Track</title>
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
        .roads-container {
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
        .tabs-container {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 2px solid #e0e0e0;
        }
        .tab-btn {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            color: #666;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .tab-btn:hover {
            color: #1976d2;
        }
        .tab-btn.active {
            color: #1976d2;
            border-bottom-color: #1976d2;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
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
            padding-right: 16px;
            border: 1px solid #bdbdbd;
            border-radius: 6px;
            font-size: 0.95rem;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 8px;
            overflow: hidden;
        }
        .data-table thead {
            background: #070d69ff;
            color: #fff;
        }
        .data-table th,
        .data-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .data-table th {
            font-weight: 600;
            font-size: 0.95rem;
        }
        .data-table tbody tr:hover {
            background: #f5f8fd;
        }
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        .undeveloped-row {
            background-color: #fff3e0 !important;
        }
        .undeveloped-row:hover {
            background-color: #ffe0b2 !important;
        }
        .no-data {
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
            background-color: #ff9800;
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
            background-color: #4caf50;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .status-label {
            display: inline-block;
            min-width: 80px;
            font-weight: 600;
            margin-left: 8px;
        }
        .status-developed {
            color: #4caf50;
        }
        .status-undeveloped {
            color: #ff9800;
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
    <div class="roads-container">
        <h2><i class="fas fa-road"></i> Roads Details</h2>
        
        <!-- Tabs -->
        <div class="tabs-container">
            <button class="tab-btn active" onclick="switchTab('sub-roads')">
                <i class="fas fa-map-signs"></i> Sub Roads
            </button>
            <button class="tab-btn" onclick="switchTab('sub-sub-roads')">
                <i class="fas fa-road"></i> Sub-Sub Roads (Development)
            </button>
            <button class="tab-btn" onclick="switchTab('addresses')">
                <i class="fas fa-map-marker-alt"></i> Addresses
            </button>
        </div>
        
        <!-- Sub Roads Tab -->
        <div id="sub-roads-tab" class="tab-content active">
            <!-- Filters -->
            <div class="filters-section">
                <div class="filter-group">
                    <label>Search Sub Road Name</label>
                    <input type="text" id="filter-subroad-name" placeholder="Type to search">
                </div>
                <div class="filter-group">
                    <label>Search Road</label>
                    <select id="filter-subroad-road">
                        <option value="">All Roads</option>
                        <?php if (isset($roads) && is_array($roads)): ?>
                            <?php foreach ($roads as $road): ?>
                                <option value="<?= esc($road['name']) ?>"><?= esc($road['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <?php if (isset($parentSubRoads) && count($parentSubRoads) > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sub Road Name</th>
                                <th>Road</th>
                            </tr>
                        </thead>
                        <tbody id="subroads-table-body">
                            <?php foreach ($parentSubRoads as $index => $subRoad): ?>
                                <tr data-id="<?= $subRoad['id'] ?>"
                                    data-name="<?= esc($subRoad['name']) ?>"
                                    data-road="<?= esc($subRoad['road_name'] ?? '') ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= esc($subRoad['name']) ?></strong></td>
                                    <td><?= esc($subRoad['road_name'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-info-circle" style="font-size: 2rem; color: #bdbdbd; margin-bottom: 8px;"></i>
                    <p>No sub roads found.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sub-Sub Roads Tab (with Development Status) -->
        <div id="sub-sub-roads-tab" class="tab-content">
            <!-- Filters -->
            <div class="filters-section">
                <div class="filter-group">
                    <label>Search Sub-Sub Road Name</label>
                    <input type="text" id="filter-childroad-name" placeholder="Type to search">
                </div>
                <div class="filter-group">
                    <label>Search Road</label>
                    <select id="filter-childroad-road">
                        <option value="">All Roads</option>
                        <?php if (isset($roads) && is_array($roads)): ?>
                            <?php foreach ($roads as $road): ?>
                                <option value="<?= esc($road['name']) ?>"><?= esc($road['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Search Parent Sub Road</label>
                    <input type="text" id="filter-childroad-parent" placeholder="Type to search parent sub road">
                </div>
                <div class="filter-group">
                    <label>Development Status</label>
                    <select id="filter-childroad-status">
                        <option value="">All Status</option>
                        <option value="developed">Developed</option>
                        <option value="undeveloped">Undeveloped</option>
                    </select>
                </div>
            </div>
            
            <?php if (isset($childSubRoads) && count($childSubRoads) > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sub-Sub Road Name</th>
                                <th>Road</th>
                                <th>Parent Sub Road</th>
                                <th>Development Status</th>
                            </tr>
                        </thead>
                        <tbody id="childroads-table-body">
                            <?php foreach ($childSubRoads as $index => $childRoad): ?>
                                <tr class="<?= $childRoad['is_developed'] == 0 ? 'undeveloped-row' : '' ?>" 
                                    data-id="<?= $childRoad['id'] ?>"
                                    data-name="<?= esc($childRoad['name']) ?>"
                                    data-road="<?= esc($childRoad['road_name'] ?? '') ?>"
                                    data-parent="<?= esc($childRoad['parent_sub_road_name'] ?? '') ?>"
                                    data-status="<?= $childRoad['is_developed'] == 1 ? 'developed' : 'undeveloped' ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= esc($childRoad['name']) ?></strong></td>
                                    <td><?= esc($childRoad['road_name'] ?? '-') ?></td>
                                    <td><?= esc($childRoad['parent_sub_road_name'] ?? '-') ?></td>
                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" 
                                                   <?= $childRoad['is_developed'] == 1 ? 'checked' : '' ?> 
                                                   onchange="toggleDevelopment(<?= $childRoad['id'] ?>, this)">
                                            <span class="slider"></span>
                                        </label>
                                        <span class="status-label <?= $childRoad['is_developed'] == 1 ? 'status-developed' : 'status-undeveloped' ?>" 
                                              id="status-label-<?= $childRoad['id'] ?>">
                                            <?= $childRoad['is_developed'] == 1 ? 'Developed' : 'Undeveloped' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-info-circle" style="font-size: 2rem; color: #bdbdbd; margin-bottom: 8px;"></i>
                    <p>No sub-sub roads found.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Addresses Tab -->
        <div id="addresses-tab" class="tab-content">
            <!-- Filters -->
            <div class="filters-section">
                <div class="filter-group">
                    <label>Search Address</label>
                    <input type="text" id="filter-address-name" placeholder="Type to search">
                </div>
                <div class="filter-group">
                    <label>Search Road</label>
                    <select id="filter-address-road">
                        <option value="">All Roads</option>
                        <?php if (isset($roads) && is_array($roads)): ?>
                            <?php foreach ($roads as $road): ?>
                                <option value="<?= esc($road['name']) ?>"><?= esc($road['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Search Sub Road</label>
                    <input type="text" id="filter-address-subroad" placeholder="Type to search sub road">
                </div>
            </div>
            
            <?php if (isset($addresses) && count($addresses) > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Address</th>
                                <th>Road</th>
                                <th>Sub Road</th>
                            </tr>
                        </thead>
                        <tbody id="addresses-table-body">
                            <?php foreach ($addresses as $index => $address): ?>
                                <tr data-id="<?= $address['id'] ?>"
                                    data-address="<?= esc($address['address']) ?>"
                                    data-road="<?= esc($address['road_name'] ?? '') ?>"
                                    data-subroad="<?= esc($address['sub_road_name'] ?? '') ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= esc($address['address']) ?></strong></td>
                                    <td><?= esc($address['road_name'] ?? '-') ?></td>
                                    <td><?= esc($address['sub_road_name'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-info-circle" style="font-size: 2rem; color: #bdbdbd; margin-bottom: 8px;"></i>
                    <p>No addresses found.</p>
                </div>
            <?php endif; ?>
        </div>
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
    
    // Switch tabs
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Remove active from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Show selected tab
        if (tabName === 'sub-roads') {
            document.getElementById('sub-roads-tab').classList.add('active');
            document.querySelector('.tab-btn:nth-child(1)').classList.add('active');
        } else if (tabName === 'sub-sub-roads') {
            document.getElementById('sub-sub-roads-tab').classList.add('active');
            document.querySelector('.tab-btn:nth-child(2)').classList.add('active');
        } else if (tabName === 'addresses') {
            document.getElementById('addresses-tab').classList.add('active');
            document.querySelector('.tab-btn:nth-child(3)').classList.add('active');
        }
    }
    
    // Toggle development status
    function toggleDevelopment(subRoadId, checkbox) {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };
        
        const csrfToken = getCsrfToken();
        const csrfHeaderName = getCsrfHeaderName();
        if (csrfToken) {
            headers[csrfHeaderName] = csrfToken;
        }
        
        fetch('<?= base_url('roads/toggle') ?>/' + subRoadId, {
            method: 'POST',
            headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update status label
                const statusLabel = document.getElementById('status-label-' + subRoadId);
                const row = checkbox.closest('tr');
                
                if (data.is_developed == 1) {
                    statusLabel.textContent = 'Developed';
                    statusLabel.className = 'status-label status-developed';
                    row.classList.remove('undeveloped-row');
                    row.setAttribute('data-status', 'developed');
                } else {
                    statusLabel.textContent = 'Undeveloped';
                    statusLabel.className = 'status-label status-undeveloped';
                    row.classList.add('undeveloped-row');
                    row.setAttribute('data-status', 'undeveloped');
                }
                
                showToast(data.message, 'success');
            } else {
                // Revert checkbox if failed
                checkbox.checked = !checkbox.checked;
                showToast(data.message || 'Failed to update status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            checkbox.checked = !checkbox.checked;
            showToast('An error occurred', 'error');
        });
    }
    
    // Filter sub roads
    function filterSubRoads() {
        const nameFilter = document.getElementById('filter-subroad-name').value.toLowerCase();
        const roadFilter = document.getElementById('filter-subroad-road').value.toLowerCase();
        
        const rows = document.querySelectorAll('#subroads-table-body tr');
        let visibleCount = 0;
        
        rows.forEach((row, index) => {
            const name = row.getAttribute('data-name').toLowerCase();
            const road = row.getAttribute('data-road').toLowerCase();
            
            const matchName = !nameFilter || name.includes(nameFilter);
            const matchRoad = !roadFilter || road === roadFilter;
            
            if (matchName && matchRoad) {
                row.style.display = '';
                visibleCount++;
                row.querySelector('td:first-child').textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Filter child sub roads (sub-sub roads)
    function filterChildSubRoads() {
        const nameFilter = document.getElementById('filter-childroad-name').value.toLowerCase();
        const roadFilter = document.getElementById('filter-childroad-road').value.toLowerCase();
        const parentFilter = document.getElementById('filter-childroad-parent').value.toLowerCase();
        const statusFilter = document.getElementById('filter-childroad-status').value.toLowerCase();
        
        const rows = document.querySelectorAll('#childroads-table-body tr');
        let visibleCount = 0;
        
        rows.forEach((row, index) => {
            const name = row.getAttribute('data-name').toLowerCase();
            const road = row.getAttribute('data-road').toLowerCase();
            const parent = row.getAttribute('data-parent').toLowerCase();
            const status = row.getAttribute('data-status').toLowerCase();
            
            const matchName = !nameFilter || name.includes(nameFilter);
            const matchRoad = !roadFilter || road === roadFilter;
            const matchParent = !parentFilter || parent.includes(parentFilter);
            const matchStatus = !statusFilter || status === statusFilter;
            
            if (matchName && matchRoad && matchParent && matchStatus) {
                row.style.display = '';
                visibleCount++;
                row.querySelector('td:first-child').textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Filter addresses
    function filterAddresses() {
        const addressFilter = document.getElementById('filter-address-name').value.toLowerCase();
        const roadFilter = document.getElementById('filter-address-road').value.toLowerCase();
        const subroadFilter = document.getElementById('filter-address-subroad').value.toLowerCase();
        
        const rows = document.querySelectorAll('#addresses-table-body tr');
        let visibleCount = 0;
        
        rows.forEach((row, index) => {
            const address = row.getAttribute('data-address').toLowerCase();
            const road = row.getAttribute('data-road').toLowerCase();
            const subroad = row.getAttribute('data-subroad').toLowerCase();
            
            const matchAddress = !addressFilter || address.includes(addressFilter);
            const matchRoad = !roadFilter || road === roadFilter;
            const matchSubroad = !subroadFilter || subroad.includes(subroadFilter);
            
            if (matchAddress && matchRoad && matchSubroad) {
                row.style.display = '';
                visibleCount++;
                row.querySelector('td:first-child').textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Add event listeners for real-time filtering - Sub Roads
    document.getElementById('filter-subroad-name').addEventListener('input', filterSubRoads);
    document.getElementById('filter-subroad-road').addEventListener('change', filterSubRoads);
    
    // Add event listeners for real-time filtering - Child Sub Roads
    document.getElementById('filter-childroad-name').addEventListener('input', filterChildSubRoads);
    document.getElementById('filter-childroad-road').addEventListener('change', filterChildSubRoads);
    document.getElementById('filter-childroad-parent').addEventListener('input', filterChildSubRoads);
    document.getElementById('filter-childroad-status').addEventListener('change', filterChildSubRoads);
    
    // Add event listeners for real-time filtering - Addresses
    document.getElementById('filter-address-name').addEventListener('input', filterAddresses);
    document.getElementById('filter-address-road').addEventListener('change', filterAddresses);
    document.getElementById('filter-address-subroad').addEventListener('input', filterAddresses);
    </script>
</body>
</html>
