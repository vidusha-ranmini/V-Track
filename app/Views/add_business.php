<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Business - V-Track</title>
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
        .business-container {
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
        .form-section {
            margin-bottom: 24px;
        }
        .form-section h3 {
            color: #1976d2;
            font-size: 1.2rem;
            margin-bottom: 16px;
            border-bottom: 2px solid #e3f2fd;
            padding-bottom: 8px;
        }
        .form-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .form-group {
            flex: 1;
            min-width: 250px;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }
        .form-group label .required {
            color: #d32f2f;
        }
        .form-group input,
        .form-group select {
            padding: 10px 12px;
            border: 1px solid #bdbdbd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #1976d2;
        }
        .btn-container {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: #1976d2;
            color: #fff;
        }
        .btn-primary:hover {
            background: #1565c0;
        }
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #d5d5d5;
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
        
        /* Business table styles */
        .business-list-section {
            margin-top: 48px;
            padding-top: 32px;
            border-top: 3px solid #e3f2fd;
        }
        .business-list-section h3 {
            color: #070d69ff;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .business-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 8px;
            overflow: hidden;
        }
        .business-table thead {
            background: #070d69ff;
            color: #fff;
        }
        .business-table th,
        .business-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .business-table th {
            font-weight: 600;
            font-size: 0.95rem;
        }
        .business-table tbody tr:hover {
            background: #f5f8fd;
        }
        .business-table tbody tr:last-child td {
            border-bottom: none;
        }
        .business-type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            background: #e3f2fd;
            color: #1976d2;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .no-businesses {
            text-align: center;
            padding: 32px;
            color: #888;
            font-style: italic;
        }
        .deleted-row {
            background-color: #ffebee !important;
            color: #c62828;
        }
        .deleted-row:hover {
            background-color: #ffcdd2 !important;
        }
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            margin-right: 4px;
            transition: background 0.2s;
        }
        .btn-edit {
            background: #1976d2;
            color: #fff;
        }
        .btn-edit:hover {
            background: #1565c0;
        }
        .btn-delete {
            background: #d32f2f;
            color: #fff;
        }
        .btn-delete:hover {
            background: #c62828;
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
        .btn-filter {
            padding: 8px 16px;
            background: #1976d2;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            white-space: nowrap;
            height: 38px;
            align-self: flex-end;
        }
        .btn-filter:hover {
            background: #1565c0;
        }
    </style>
</head>
<body>
    <div class="business-container">
        <h2><i class="fas fa-store"></i> Add Business Place Details</h2>
        
        <form id="business-form">
            <div class="form-section">
                <h3>Business Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Business Name <span class="required">*</span></label>
                        <input type="text" name="business_name" id="business_name" required placeholder="Enter business name">
                    </div>
                    <div class="form-group">
                        <label>Business Owner <span class="required">*</span></label>
                        <input type="text" name="business_owner" id="business_owner" required placeholder="Enter owner name">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Business Type <span class="required">*</span></label>
                        <select name="business_type" id="business_type" required>
                            <option value="">Select Business Type</option>
                            <option value="retail">Retail Shop</option>
                            <option value="restaurant">Restaurant/Cafe</option>
                            <option value="grocery">Grocery Store</option>
                            <option value="pharmacy">Pharmacy</option>
                            <option value="salon">Salon</option>
                            <option value="hardware">Hardware Store</option>
                            <option value="electronics">Electronics</option>
                            <option value="clothing">Clothing/Apparel</option>
                            <option value="service">Service Center</option>
                            <option value="office">Office/Agency</option>
                            <option value="workshop">Garage</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group" id="custom-type-group" style="display: none;">
                        <label>Custom Business Type <span class="required">*</span></label>
                        <input type="text" name="custom_business_type" id="custom_business_type" placeholder="Enter custom business type">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Location Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Road</label>
                        <select name="road_id" id="road_id">
                            <option value="">Select Road</option>
                            <?php if (isset($roads) && is_array($roads)): ?>
                                <?php foreach ($roads as $road): ?>
                                    <option value="<?= $road['id'] ?>"><?= esc($road['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sub Road</label>
                        <select name="sub_road_id" id="sub_road_id">
                            <option value="">Select Sub Road</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Address</label>
                        <select name="address_id" id="address_id">
                            <option value="">Select Address</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="btn-container">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Business Details
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset Form
                </button>
            </div>
        </form>
        
        <!-- Existing Businesses Section -->
        <div class="business-list-section">
            <h3><i class="fas fa-list"></i> Existing Business Places (<?= isset($businesses) ? count($businesses) : 0 ?>)</h3>
            
            <!-- Filters -->
            <div class="filters-section">
                <div class="filter-group">
                    <label>Search Business Type</label>
                    <select id="filter-type">
                        <option value="">All Types</option>
                        <option value="retail">Retail Shop</option>
                        <option value="restaurant">Restaurant/Cafe</option>
                        <option value="grocery">Grocery Store</option>
                        <option value="pharmacy">Pharmacy</option>
                        <option value="salon">Salon</option>
                        <option value="hardware">Hardware Store</option>
                        <option value="electronics">Electronics</option>
                        <option value="clothing">Clothing/Apparel</option>
                        <option value="service">Service Center</option>
                        <option value="office">Office/Agency</option>
                        <option value="workshop">Garage</option>
                        <option value="other">Other</option>
                    </select>
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
                    <label>Search Owner Name</label>
                    <input type="text" id="filter-owner" placeholder="Type to search owner name">
                </div>
            </div>
            
            <?php if (isset($businesses) && count($businesses) > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="business-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Business Name</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="business-table-body">
                            <?php foreach ($businesses as $index => $business): ?>
                                <tr class="<?= isset($business['is_deleted']) && $business['is_deleted'] == 1 ? 'deleted-row' : '' ?>" 
                                    data-id="<?= $business['id'] ?>"
                                    data-type="<?= esc($business['business_type']) ?>"
                                    data-road="<?= esc($business['road_name'] ?? '') ?>"
                                    data-subroad="<?= esc($business['sub_road_name'] ?? '') ?>"
                                    data-owner="<?= esc($business['business_owner']) ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= esc($business['business_name']) ?></strong></td>
                                    <td><?= esc($business['business_owner']) ?></td>
                                    <td>
                                        <span class="business-type-badge">
                                            <?= ucwords(str_replace('_', ' ', esc($business['business_type']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $location_parts = [];
                                        if (!empty($business['road_name'])) $location_parts[] = $business['road_name'];
                                        if (!empty($business['sub_road_name'])) $location_parts[] = $business['sub_road_name'];
                                        if (!empty($business['address_line'])) $location_parts[] = $business['address_line'];
                                        echo !empty($location_parts) ? esc(implode(' / ', $location_parts)) : '<em style="color: #888;">No location</em>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (isset($business['is_deleted']) && $business['is_deleted'] == 1): ?>
                                            <span style="color: #c62828; font-weight: 600;">Deleted</span>
                                        <?php else: ?>
                                            <button class="action-btn btn-edit" onclick="editBusiness(<?= $business['id'] ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="action-btn btn-delete" onclick="deleteBusiness(<?= $business['id'] ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-businesses">
                    <i class="fas fa-info-circle" style="font-size: 2rem; color: #bdbdbd; margin-bottom: 8px;"></i>
                    <p>No business places added yet. Add your first business above!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div id="toast" class="toast"></div>
    
    <script>
    // Data from server
    const subRoadsData = <?= $subRoadsJson ?? '{}' ?>;
    const addressesData = <?= $addressesJson ?? '{}' ?>;
    
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
    
    // Populate sub roads when road is selected
    document.getElementById('road_id').addEventListener('change', function() {
        const roadId = this.value;
        const subRoadSelect = document.getElementById('sub_road_id');
        const addressSelect = document.getElementById('address_id');
        
        // Clear sub road and address
        subRoadSelect.innerHTML = '<option value="">Select Sub Road</option>';
        addressSelect.innerHTML = '<option value="">Select Address</option>';
        
        if (roadId && subRoadsData[roadId]) {
            subRoadsData[roadId].forEach(subRoad => {
                const option = document.createElement('option');
                option.value = subRoad.id;
                option.textContent = subRoad.name;
                subRoadSelect.appendChild(option);
            });
        }
    });
    
    // Show/hide custom business type input
    document.getElementById('business_type').addEventListener('change', function() {
        const customTypeGroup = document.getElementById('custom-type-group');
        const customTypeInput = document.getElementById('custom_business_type');
        
        if (this.value === 'other') {
            customTypeGroup.style.display = 'block';
            customTypeInput.setAttribute('required', 'required');
        } else {
            customTypeGroup.style.display = 'none';
            customTypeInput.removeAttribute('required');
            customTypeInput.value = '';
        }
    });
    
    // Populate addresses when sub road is selected
    document.getElementById('sub_road_id').addEventListener('change', function() {
        const subRoadId = this.value;
        const addressSelect = document.getElementById('address_id');
        
        // Clear addresses
        addressSelect.innerHTML = '<option value="">Select Address</option>';
        
        if (subRoadId && addressesData[subRoadId]) {
            addressesData[subRoadId].forEach(address => {
                const option = document.createElement('option');
                option.value = address.id;
                option.textContent = address.address;
                addressSelect.appendChild(option);
            });
        }
    });
    
    // Handle form submission
    let editingBusinessId = null;
    
    document.getElementById('business-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // If "Other" is selected, use the custom business type value
        const businessType = document.getElementById('business_type').value;
        if (businessType === 'other') {
            const customType = document.getElementById('custom_business_type').value.trim();
            if (customType) {
                formData.set('business_type', customType);
            }
        }
        
        // Add business ID if editing
        if (editingBusinessId) {
            formData.append('business_id', editingBusinessId);
        }
        
        // Create headers object
        const headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };
        
        // Add CSRF token
        const csrfToken = getCsrfToken();
        const csrfHeaderName = getCsrfHeaderName();
        if (csrfToken) {
            headers[csrfHeaderName] = csrfToken;
        }
        
        const url = editingBusinessId ? '<?= base_url('business/update') ?>' : '<?= base_url('add-business') ?>';
        
        fetch(url, {
            method: 'POST',
            headers: headers,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Business details saved successfully!', 'success');
                document.getElementById('business-form').reset();
                editingBusinessId = null;
                // Hide custom type field after reset
                document.getElementById('custom-type-group').style.display = 'none';
                document.getElementById('custom_business_type').removeAttribute('required');
                // Change button text back
                document.querySelector('.btn-primary').innerHTML = '<i class="fas fa-save"></i> Save Business Details';
                // Reload page after 1 second to show new business in table
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast(data.message || 'Failed to save business details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while saving', 'error');
        });
    });
    
    // Edit business function
    function editBusiness(businessId) {
        // Fetch business details
        fetch('<?= base_url('business/get') ?>/' + businessId, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const business = data.business;
                editingBusinessId = businessId;
                
                // Populate form
                document.getElementById('business_name').value = business.business_name;
                document.getElementById('business_owner').value = business.business_owner;
                document.getElementById('business_type').value = business.business_type;
                
                if (business.road_id) {
                    document.getElementById('road_id').value = business.road_id;
                    document.getElementById('road_id').dispatchEvent(new Event('change'));
                    
                    setTimeout(() => {
                        if (business.sub_road_id) {
                            document.getElementById('sub_road_id').value = business.sub_road_id;
                            document.getElementById('sub_road_id').dispatchEvent(new Event('change'));
                            
                            setTimeout(() => {
                                if (business.address_id) {
                                    document.getElementById('address_id').value = business.address_id;
                                }
                            }, 100);
                        }
                    }, 100);
                }
                
                // Change button text
                document.querySelector('.btn-primary').innerHTML = '<i class="fas fa-save"></i> Update Business Details';
                
                // Scroll to form
                window.scrollTo({ top: 0, behavior: 'smooth' });
                showToast('Edit mode activated', 'success');
            } else {
                showToast('Failed to load business details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }
    
    // Delete business function (soft delete)
    function deleteBusiness(businessId) {
        if (!confirm('Are you sure you want to delete this business? It will be marked as deleted but not removed from the database.')) {
            return;
        }
        
        const headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };
        
        const csrfToken = getCsrfToken();
        const csrfHeaderName = getCsrfHeaderName();
        if (csrfToken) {
            headers[csrfHeaderName] = csrfToken;
        }
        
        fetch('<?= base_url('business/delete') ?>/' + businessId, {
            method: 'POST',
            headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Business marked as deleted', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast(data.message || 'Failed to delete business', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }
    
    // Filter functions
    function applyFilters() {
        const typeFilter = document.getElementById('filter-type').value.toLowerCase();
        const roadFilter = document.getElementById('filter-road').value.toLowerCase();
        const ownerFilter = document.getElementById('filter-owner').value.toLowerCase();
        
        const rows = document.querySelectorAll('#business-table-body tr');
        let visibleCount = 0;
        
        rows.forEach((row, index) => {
            const type = row.getAttribute('data-type').toLowerCase();
            const road = row.getAttribute('data-road').toLowerCase();
            const owner = row.getAttribute('data-owner').toLowerCase();
            
            const matchType = !typeFilter || type === typeFilter || type.includes(typeFilter);
            const matchRoad = !roadFilter || road === roadFilter;
            const matchOwner = !ownerFilter || owner.includes(ownerFilter);
            
            if (matchType && matchRoad && matchOwner) {
                row.style.display = '';
                visibleCount++;
                row.querySelector('td:first-child').textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Add event listeners for real-time filtering
    document.getElementById('filter-type').addEventListener('change', applyFilters);
    document.getElementById('filter-road').addEventListener('change', applyFilters);
    document.getElementById('filter-owner').addEventListener('input', applyFilters);
    </script>
</body>
</html>
