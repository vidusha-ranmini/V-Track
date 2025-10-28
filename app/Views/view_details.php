<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Details - V-Track</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: #f5f8fd;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .view-details-container {
            max-width: 1200px;
            margin: 32px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(25, 118, 210, 0.10);
            padding: 32px 40px;
        }
        .filter-row {
            display: flex;
            gap: 18px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 180px;
        }
        .filter-group label {
            font-weight: 500;
            margin-bottom: 6px;
        }
        .filter-group input, .filter-group select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #bdbdbd;
            font-size: 1rem;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        .details-table th, .details-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        .details-table th {
            background: #f5f8fd;
            font-weight: 600;
            color: #070d69ff;
        }
        .details-table tr:hover {
            background: #e3f2fd;
        }
        .no-data {
            text-align: center;
            color: #888;
            padding: 32px 0;
        }
        /* Action button used across views */
        .quick-action-btn {
            background: #1976d2;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.15s ease;
        }
        .quick-action-btn:hover { background: #1565c0; }
        .quick-action-btn.small { padding:6px 10px; font-size:0.9rem; border-radius:6px; }
    .quick-action-btn.danger { background:#d32f2f; }
    .quick-action-btn.danger:hover { background:#b71c1c; }
    .quick-action-btn.cancel { background:#e0e0e0; color:#000; }
    .quick-action-btn.cancel:hover { background:#d5d5d5; }

        /* Edit modal styles */
        #edit-modal { display: none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.45); align-items: center; justify-content: center; z-index: 1200; }
        #edit-modal.show { display:flex; }
        /* Details modal shares same behavior as edit modal */
        #details-modal { display: none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.45); align-items: center; justify-content: center; z-index: 1100; }
        #details-modal.show { display:flex; }
            #edit-modal .modal-content {
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                max-width: 720px;
                width: 95%;
                margin: 0 16px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.15);
                /* Allow the edit modal to scroll when content is tall */
                max-height: 90vh;
                overflow-y: auto;
                box-sizing: border-box;
                position: relative;
            }
            /* Details modal card styling: white background, rounded, smaller width */
            #details-modal .modal-content {
                background: #fff;
                padding: 20px;
                border-radius: 12px;
                max-width: 640px;
                width: 92%;
                margin: 0 16px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.15);
                overflow-y: auto;
                max-height: 90vh;
                position: relative; /* allow absolute-positioned close button */
            }
            /* Top-right close button inside modal */
            .modal-close-top {
                position: absolute;
                top: 12px;
                right: 12px;
                background: transparent;
                border: none;
                color: #333;
                font-size: 22px;
                line-height: 1;
                cursor: pointer;
                padding: 6px 8px;
                border-radius: 6px;
            }
            .modal-close-top:hover { background: rgba(0,0,0,0.06); }
            /* Delete modal content styling (white card) */
            #delete-modal .modal-content {
                background: #fff;
                padding: 18px;
                border-radius: 10px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.12);
                max-width: 420px;
                width: 92%;
                margin: 0 12px;
            }
        #edit-form label { display:block; margin-bottom:6px; font-weight:600; }
        #edit-form input[type="text"], #edit-form input[type="number"], #edit-form select {
            width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;
        }
        #details-card .quick-action-btn { margin-left:8px; }
    </style>
</head>
<body>
    <div class="view-details-container">
        <h2 style="color:#070d69ff;margin-bottom:24px;">View Family & Member Details</h2>
        <div class="filter-row">
            <div class="filter-group">
                <label for="filter-house">House Number</label>
                <input type="text" id="filter-house" placeholder="Search by house number">
            </div>
            <div class="filter-group">
                <label for="filter-address">Address</label>
                <input type="text" id="filter-address" placeholder="Search by address">
            </div>
            <div class="filter-group">
                <label for="filter-name">Name</label>
                <input type="text" id="filter-name" placeholder="Search by name">
            </div>
            <div class="filter-group">
                <label for="filter-occupation">Occupation</label>
                <select id="filter-occupation">
                    <option value="">All</option>
                    <option value="student">Student</option>
                    <option value="university_student">University Student</option>
                    <option value="business">Business</option>
                    <option value="doctor">Doctor</option>
                    <option value="teacher">Teacher</option>
                    <option value="engineer">Engineer</option>
                    <option value="accountant">Accountant</option>
                    <option value="nurse">Nurse</option>
                    <option value="farmer">Farmer</option>
                    <option value="abroad">Abroad</option>
                    <option value="self_employment">Self Employment</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="filter-offers">Offers</label>
                <select id="filter-offers">
                    <option value="">All</option>
                    <option value="aswasuma">Aswasuma</option>
                    <option value="adult">Adult Offers</option>
                   
                    <option value="grade5">Grade 5 Scholarship</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="filter-resident">Resident Type</label>
                <select id="filter-resident">
                    <option value="">All</option>
                    <option value="permanent">Permanent</option>
                    <option value="rented">Rented</option>
                </select>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="details-table" id="details-table">
                <thead>
                    <tr>
                        <th>House No</th>
                        <th>Resident Type</th>
                        <th>Member Name</th>
                        <th>Occupation</th>
                        <th>Offers</th>
                        <th>NIC</th>
                        <th>WhatsApp</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data rows will be inserted here by JS -->
                </tbody>
            </table>
            <div class="no-data" id="no-data" style="display:none;">No details found.</div>
        </div>
    <div id="details-card" style="display:none;margin-top:32px;"></div>
    <!-- Edit modal -->
    <div id="edit-modal">
            <div class="modal-content" style="margin:auto;">
                <h3>Edit Member</h3>
                <form id="edit-form" enctype="multipart/form-data">
                    <input type="hidden" name="id">
                    <input type="hidden" name="cv" value="">
                    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
                        <div style="flex:1;min-width:220px;">
                            <label>Full Name</label>
                            <input name="full_name" type="text" style="width:100%">
                        </div>
                        <div style="flex:1;min-width:220px;">
                            <label>Name with Initial</label>
                            <input name="name_with_initial" type="text" style="width:100%">
                        </div>
                        <div style="flex:1;min-width:120px;">
                            <label>Member Type</label>
                            <select name="member_type" style="width:100%">
                                <option value="permanent">Permanent</option>
                                <option value="temporary">Temporary</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <div style="flex:1;min-width:180px;">
                            <label>NIC</label>
                            <input name="nic" type="text" style="width:100%">
                        </div>
                        <div style="flex:1;min-width:140px;">
                            <label>Gender</label>
                            <select name="gender" style="width:100%"><option value="" disabled>Select</option><option value="male">Male</option><option value="female">Female</option></select>
                        </div>
                        <div style="flex:1;min-width:120px;">
                            <label>Age</label>
                            <input name="age" type="number" style="width:100%">
                        </div>
                    </div>

                    <div style="margin-top:8px;display:flex;gap:12px;flex-wrap:wrap;">
                        <div style="flex:1;min-width:220px;">
                            <label>Occupation</label>
                            <select name="occupation" style="width:100%" onchange="toggleOccupation(this)">
                                <option value="">Select</option>
                                <option value="student">Student</option>
                                <option value="university_student">University Student</option>
                                <option value="business">Business</option>
                                <option value="doctor">Doctor</option>
                                <option value="teacher">Teacher</option>
                                <option value="engineer">Engineer</option>
                                <option value="accountant">Accountant</option>
                                <option value="nurse">Nurse</option>
                                <option value="farmer">Farmer</option>
                                <option value="abroad">Abroad</option>
                                <option value="self_employment">Self Employment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div style="flex:1;min-width:220px;" id="occupation-other-group">
                            <label>Other Occupation</label>
                            <input name="occupation_other" type="text" style="width:100%">
                        </div>
                        <div style="flex:1;min-width:120px;">
                            <label>WhatsApp</label>
                            <input name="whatsapp" type="text" style="width:100%">
                        </div>
                    </div>

                    <div style="margin-top:8px;display:flex;gap:12px;flex-wrap:wrap;">
                        <div style="flex:1;min-width:220px;" id="student-fields">
                            <label>School</label>
                            <input name="school" type="text" style="width:100%">
                        </div>
                        <div style="flex:1;min-width:220px;" id="student-grade">
                            <label>Grade</label>
                            <select name="grade" style="width:100%">
                                <option value="">Select Grade</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                            </select>
                        </div>
                        <div style="flex:1;min-width:220px;" id="university-fields">
                            <label>University</label>
                            <input name="university_name" type="text" style="width:100%">
                        </div>
                    </div>

                    <div style="margin-top:8px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                        <div style="flex:1;min-width:200px;">
                            <label>Land/House Status</label>
                            <select name="land_house_status" style="width:100%">
                                <option value="plot">Plot of Land</option>
                                <option value="no_house">No House</option>
                                <option value="no_land_house">No Land and House</option>
                            </select>
                        </div>
                        <div style="flex:1;min-width:140px;">
                            <label>Disabled</label>
                            <select name="disabled" style="width:100%"><option value="no">No</option><option value="yes">Yes</option></select>
                        </div>
                        <div style="flex:1;min-width:140px;">
                            <label>Offers</label>
                            <select name="offers[]" multiple style="width:100%;height:110px;">
                                <option value="aswasuma">Aswasuma</option>
                                <option value="adult">Adult Offers</option>
                                <option value="mahapola">Mahapola</option>
                                <option value="grade5">Grade 5 Scholarship</option>
                            </select>
                            <small style="color:#666">Hold Ctrl (Windows) or Cmd (Mac) to select multiple offers.</small>
                        </div>
                    </div>

                    <div style="margin-top:8px;">
                        <label>Current CV</label>
                        <div id="current-cv" style="margin-bottom:8px;color:#444;font-size:0.95rem;"></div>
                        <label>Replace CV (optional)</label>
                        <input name="cv_file" type="file" accept="application/pdf,image/*" style="width:100%">
                    </div>

                    <div style="margin-top:12px;display:flex;gap:8px;justify-content:flex-end;">
                        <button type="button" id="edit-cancel" class="quick-action-btn cancel small">Cancel</button>
                        <button type="submit" class="quick-action-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    <!-- Details modal (full member info) -->
    <div id="details-modal">
        <div class="modal-content" style="margin:auto;max-width:800px;">
            <!-- Top-right close button -->
            <button type="button" id="details-close-top" class="modal-close-top" aria-label="Close">&times;</button>
            <div id="details-modal-body"> </div>
        </div>
    </div>
    </div>
    <!-- Delete confirmation modal -->
    <div id="delete-modal" style="display:none;position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;z-index:1250;">
        <div class="modal-content" style="max-width:420px;margin:auto;padding:18px;">
            <h3 style="margin-top:0;color:#c62828;">Confirm Delete</h3>
            <p id="delete-modal-msg" style="color:#333;margin-bottom:18px;">Are you sure you want to delete this member? This action cannot be undone.</p>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button id="delete-cancel" class="quick-action-btn cancel small">Cancel</button>
                <button id="delete-confirm" class="quick-action-btn danger small">Delete</button>
            </div>
        </div>
    </div>
    <!-- Toast container -->
    <div id="toast-container" aria-live="polite" style="position:fixed;right:18px;bottom:18px;z-index:2000;pointer-events:none;"></div>
    <script>
    // Toast / popup helper
    function showToast(type, msg, timeout = 3500) {
        var container = document.getElementById('toast-container');
        if (!container) return;
        var toast = document.createElement('div');
        toast.className = 'toast ' + (type === 'success' ? 'toast-success' : 'toast-error');
        toast.style.pointerEvents = 'auto';
        toast.style.marginTop = '8px';
        toast.style.padding = '10px 14px';
        toast.style.borderRadius = '8px';
        toast.style.boxShadow = '0 6px 18px rgba(0,0,0,0.12)';
        toast.style.color = '#fff';
        toast.style.minWidth = '180px';
        toast.style.fontWeight = '600';
        toast.innerText = msg;
        if (type === 'success') {
            toast.style.background = '#2e7d32';
        } else {
            toast.style.background = '#d32f2f';
        }
        container.appendChild(toast);
        setTimeout(function() {
            try { container.removeChild(toast); } catch (e) {}
        }, timeout);
    }
    // Data provided by controller (JSON encoded). Falls back to empty array when not provided.
    const detailsData = <?= isset($detailsData) ? $detailsData : '[]' ?>;
    function renderTable() {
        const tbody = document.querySelector('#details-table tbody');
        tbody.innerHTML = '';
        let filters = {
            house: document.getElementById('filter-house').value.toLowerCase(),
            address: document.getElementById('filter-address').value.toLowerCase(),
            name: document.getElementById('filter-name').value.toLowerCase(),
            occupation: document.getElementById('filter-occupation').value,
            offers: document.getElementById('filter-offers').value,
            resident: document.getElementById('filter-resident').value
        };
        let found = false;
        let rowData = [];
        detailsData.forEach((family, famIdx) => {
            family.members.forEach((member, memIdx) => {
                if (
                    (!filters.house || family.house_number.toLowerCase().includes(filters.house)) &&
                    (!filters.address || family.address.toLowerCase().includes(filters.address)) &&
                    (!filters.name || member.name.toLowerCase().includes(filters.name)) &&
                    (!filters.occupation || member.occupation === filters.occupation) &&
                    (!filters.offers || member.offers.includes(filters.offers)) &&
                    (!filters.resident || family.resident_type === filters.resident)
                ) {
                    found = true;
                    rowData.push({family, member, famIdx, memIdx});
                    const tr = document.createElement('tr');
                    tr.innerHTML =
                        `<td>${family.house_number}</td>` +
                        `<td>${family.resident_type.charAt(0).toUpperCase() + family.resident_type.slice(1)}</td>` +
                        `<td>${member.name}</td>` +
                        `<td>${capitalize(member.occupation)}</td>` +
                        `<td>${member.offers.map(capitalizeOffer).join(', ')}</td>` +
                        `<td>${member.nic}</td>` +
                        `<td>${member.whatsapp}</td>`;
                    tr.style.cursor = 'pointer';
                    tr.addEventListener('click', function() {
                        showDetailsCard(family, member);
                    });
                    tbody.appendChild(tr);
                }
            });
        });
        document.getElementById('no-data').style.display = found ? 'none' : 'block';
        if (!found) document.getElementById('details-card').style.display = 'none';
    }
    function showDetailsCard(family, member) {
        // Populate and show details modal
        var modal = document.getElementById('details-modal');
        var body = document.getElementById('details-modal-body');
        if (!modal || !body) return;
        body.innerHTML = `
            <h3 style="color:#070d69ff;margin-top:0;">Full Member Details</h3>
            <div style="margin-bottom:12px;"><strong>House Number:</strong> ${family.house_number}</div>
            <div style="margin-bottom:12px;"><strong>Address:</strong> ${family.address}</div>
            <div style="margin-bottom:12px;"><strong>Resident Type:</strong> ${capitalize(family.resident_type)}</div>
            <div style="margin-bottom:12px;"><strong>Member Name:</strong> ${member.name}</div>
            <div style="margin-bottom:12px;"><strong>Name with Initial:</strong> ${member.name_with_initial ? member.name_with_initial : '—'}</div>
            <div style="margin-bottom:12px;"><strong>Member Type:</strong> ${member.member_type ? member.member_type : '—'}</div>
            <div style="margin-bottom:12px;"><strong>Occupation:</strong> ${capitalize(member.occupation)}${member.occupation === 'other' && member.occupation_other ? ' (' + member.occupation_other + ')' : ''}</div>
            <div style="margin-bottom:12px;"><strong>School / Grade:</strong> ${member.school ? member.school + (member.grade ? ' / ' + member.grade : '') : (member.university_name ? member.university_name : '—')}</div>
            <div style="margin-bottom:12px;"><strong>Land/House Status:</strong> ${member.land_house_status ? capitalize(member.land_house_status) : '—'}</div>
           
            <div style="margin-bottom:12px;"><strong>Offers:</strong> ${member.offers.map(capitalizeOffer).join(', ')}</div>
            <div style="margin-bottom:12px;"><strong>NIC:</strong> ${member.nic}</div>
            <div style="margin-bottom:12px;"><strong>WhatsApp:</strong> ${member.whatsapp}</div>
            <div style="margin-bottom:12px;"><strong>Age:</strong> ${member.age ? member.age : 'N/A'}</div>
            <div style="margin-bottom:12px;"><strong>CV:</strong> ${member.cv ? `<a href="<?= base_url('writable/uploads/') ?>${member.cv}" target="_blank">Download</a>` : 'No file attached'}</div>
        `;

        // Add action buttons under the content
        var actions = document.createElement('div');
        actions.style.marginTop = '12px';
        actions.style.display = 'flex';
        actions.style.justifyContent = 'flex-end';
        actions.style.gap = '8px';

        var editBtn = document.createElement('button');
        editBtn.className = 'quick-action-btn';
        editBtn.id = 'details-edit-btn';
        editBtn.innerText = 'Edit';
        actions.appendChild(editBtn);

        var delBtn = document.createElement('button');
        delBtn.className = 'quick-action-btn danger';
        delBtn.id = 'details-delete-btn';
        delBtn.innerText = 'Delete';
        actions.appendChild(delBtn);

        body.appendChild(actions);

        // Wire buttons
        editBtn.addEventListener('click', function() {
            modal.classList.remove('show');
            openEditModal(member);
        });
        delBtn.addEventListener('click', function() {
            // Open custom delete confirmation modal
            openDeleteModal(member);
        });

        // Show modal
        modal.classList.add('show');

        // wire top-right close button
        var topClose = document.getElementById('details-close-top');
        if (topClose) {
            topClose.onclick = function() { document.getElementById('details-modal').classList.remove('show'); };
        }
    }

    // Delete modal helpers
    function openDeleteModal(member) {
        var dmodal = document.getElementById('delete-modal');
        var msg = document.getElementById('delete-modal-msg');
        var confirmBtn = document.getElementById('delete-confirm');
        var cancelBtn = document.getElementById('delete-cancel');
        if (!dmodal || !msg || !confirmBtn || !cancelBtn) return;
        msg.textContent = `Delete member "${member.name}"? This action cannot be undone.`;
        dmodal.style.display = 'flex';

        function cleanup() {
            dmodal.style.display = 'none';
            confirmBtn.onclick = null;
            cancelBtn.onclick = null;
        }

        cancelBtn.onclick = function() { cleanup(); };
        confirmBtn.onclick = function() {
            // perform deletion
            confirmBtn.disabled = true; cancelBtn.disabled = true;
            fetch('<?= base_url('member/delete') ?>/' + member.id, { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => {
                    if (!response.ok) throw response;
                    return response.json().catch(()=>({ success: true }));
                })
                .then(json => {
                    cleanup();
                    showToast('success', 'Member deleted');
                    setTimeout(()=>location.reload(),700);
                })
                .catch(err => {
                    cleanup();
                    // Prefer server-friendly message if provided, but never leak host/stack traces
                    if (err && err.json) {
                        err.json().then(j => { showToast('error', j.message || 'Delete failed'); }).catch(()=>{ showToast('error', 'Delete failed'); });
                    } else {
                        showToast('error', 'Delete failed');
                    }
                    console.error('Delete error (see server logs):', err);
                });
        };
    }

    function openEditModal(member) {
        var modal = document.getElementById('edit-modal');
        var form = document.getElementById('edit-form');
        form.id.value = member.id || '';
        form.full_name.value = member.name || '';
        form.name_with_initial.value = member.name_with_initial || '';
        form.member_type.value = member.member_type || '';
        form.nic.value = member.nic || '';
    // Ensure gender is one of allowed values ('male'|'female'). Default to 'male' when missing/invalid.
    var g = (member.gender && (member.gender === 'male' || member.gender === 'female')) ? member.gender : 'male';
    form.gender.value = g;
        form.age.value = member.age || '';
        form.occupation.value = member.occupation || '';
        form.occupation_other.value = member.occupation_other || '';
        form.whatsapp.value = member.whatsapp || '';
        form.school.value = member.school || '';
        form.grade.value = member.grade || '';
        form.university_name.value = member.university_name || '';
        form.land_house_status.value = member.land_house_status || '';
        form.disabled.value = member.disabled || 'no';
        // offers may be array — set multi-select options if present
        try {
            var offersSelect = form.querySelector('[name="offers[]"]');
            if (offersSelect) {
                // clear selections
                Array.from(offersSelect.options).forEach(function(o){ o.selected = false; });
                if (Array.isArray(member.offers)) {
                    member.offers.forEach(function(v){
                        var opt = offersSelect.querySelector('option[value="' + v + '"]');
                        if (opt) opt.selected = true;
                    });
                } else if (typeof member.offers === 'string' && member.offers.length) {
                    member.offers.split(',').map(function(s){ return s.trim(); }).forEach(function(v){
                        var opt = offersSelect.querySelector('option[value="' + v + '"]');
                        if (opt) opt.selected = true;
                    });
                }
            }
        } catch(e) {}
        // set current cv filename so backend can receive it as text (server currently reads cv from POST)
        form.cv.value = member.cv || '';
        document.getElementById('current-cv').textContent = member.cv ? member.cv : 'No file attached';
        // Ensure conditional occupation fields are shown/hidden correctly
        try { toggleOccupation(form.querySelector('[name="occupation"]')); } catch(e) {}
        modal.classList.add('show');
    }

    // Edit modal handlers
    document.getElementById('edit-cancel').addEventListener('click', function() {
        var modal = document.getElementById('edit-modal');
        modal.classList.remove('show');
    });

    function toggleOccupation(el) {
        if (!el) return;
        var val = el.value;
        var other = document.getElementById('occupation-other-group');
        var student = document.getElementById('student-fields');
        var grade = document.getElementById('student-grade');
        var uni = document.getElementById('university-fields');
        if (other) other.style.display = (val === 'other') ? 'block' : 'none';
        if (student) student.style.display = (val === 'student') ? 'block' : 'none';
        if (grade) grade.style.display = (val === 'student') ? 'block' : 'none';
        if (uni) uni.style.display = (val === 'university_student') ? 'block' : 'none';
    }

    // Wire occupation change inside modal
    (function() {
        var occ = document.querySelector('#edit-form [name="occupation"]');
        if (occ) occ.addEventListener('change', function() { toggleOccupation(this); });
    })();

    document.getElementById('edit-form').addEventListener('submit', function(evt) {
        evt.preventDefault();
    var form = evt.target;
    // Ensure gender is set and normalized before creating FormData to satisfy DB CHECK constraint
    if (!form.gender.value || (form.gender.value !== 'male' && form.gender.value !== 'female')) {
        form.gender.value = 'male';
    }
    form.gender.value = form.gender.value.toLowerCase();
    var fd = new FormData(form);
    // include file if provided (cv_file)
    // send as multipart/form-data
        fetch('<?= base_url('member/update') ?>', { method: 'POST', body: fd })
            .then(r => { if (!r.ok) throw new Error('Update failed'); return r.json(); })
            .then(j => { 
                // hide modal via class so CSS takes effect
                var modal = document.getElementById('edit-modal');
                if (modal) modal.classList.remove('show');
                showToast('success', 'Member updated');
                setTimeout(()=>location.reload(), 800);
            })
            .catch(err => { showToast('error', 'Update failed'); console.error(err); });
    });
    function capitalize(str) {
        if (!str) return '';
        return str.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
    function capitalizeOffer(str) {
        switch(str) {
            case 'aswasuma': return 'Aswasuma';
            case 'adult': return 'Adult Offers';
            case 'mahapola': return 'Mahapola';
            case 'grade5': return 'Grade 5 Scholarship';
            default: return capitalize(str);
        }
    }
    document.querySelectorAll('.filter-group input, .filter-group select').forEach(el => {
        el.addEventListener('input', renderTable);
        el.addEventListener('change', renderTable);
    });
    renderTable();
    </script>
</body>
</html>
