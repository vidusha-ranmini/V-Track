<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>V-Track</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        .details-form {
            width: 100%;
            margin: 0;
            background: #fff;
            /* border-radius: 16px; */
            box-shadow: 0 4px 24px rgba(25, 118, 210, 0.10);
            padding: 32px 40px;
        }
        .form-section {
            margin-bottom: 32px;
        }
        .form-section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #070d69ff;
            margin-bottom: 18px;
        }
        .form-row {
            display: flex;
            gap: 24px;
            margin-bottom: 18px;
        }
        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 500;
            margin-bottom: 6px;
        }
        input, select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #bdbdbd;
            font-size: 1rem;
        }
        .member-list {
            margin-top: 18px;
        }
        .add-member-btn {
            background: #070d69ff;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 12px;
        }
        .add-member-btn:hover {
            background: #1565c0;
        }
        .hidden {
            display: none;
        }
    </style>
    <script>
    function toggleAssessment(el) {
        document.getElementById('assessment-number-group').style.display = el.value === 'yes' ? 'block' : 'none';
    }
    function toggleOccupation(el) {
        document.getElementById('occupation-other-group').style.display = el.value === 'other' ? 'block' : 'none';
        document.getElementById('student-fields').style.display = el.value === 'student' ? 'block' : 'none';
        document.getElementById('university-fields').style.display = el.value === 'university_student' ? 'block' : 'none';
    }

    // Toast helper for popup messages
    function showToast(type, msg, timeout = 3500) {
        var container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.position = 'fixed';
            container.style.right = '18px';
            container.style.bottom = '18px';
            container.style.zIndex = '2000';
            container.style.pointerEvents = 'none';
            document.body.appendChild(container);
        }
        var toast = document.createElement('div');
        toast.style.pointerEvents = 'auto';
        toast.style.marginTop = '8px';
        toast.style.padding = '10px 14px';
        toast.style.borderRadius = '8px';
        toast.style.boxShadow = '0 6px 18px rgba(0,0,0,0.12)';
        toast.style.color = '#fff';
        toast.style.minWidth = '180px';
        toast.style.fontWeight = '600';
        toast.innerText = msg;
        if (type === 'success') toast.style.background = '#2e7d32'; else toast.style.background = '#d32f2f';
        container.appendChild(toast);
        setTimeout(function() { try { container.removeChild(toast); } catch(e){} }, timeout);
    }

    // Member management logic
    document.addEventListener('DOMContentLoaded', function() {
        // Get the form first, then select the add-member button of type button (not the submit button)
        var form = document.querySelector('form');
        if (!form) return;
        var addBtn = form.querySelector('button.add-member-btn[type="button"]');
        var memberList = form.querySelector('.member-list');
    var members = [];
    var cvFilesContainer = document.createElement('div');
    cvFilesContainer.style.display = 'none';
    cvFilesContainer.id = 'cv-files-container';
    form.appendChild(cvFilesContainer);
    // Keep track of hidden file inputs we create so they stay in sync with `members` array
    var cvInputs = [];

    // Ensure the hidden file inputs are ordered and named to match members[] indexes.
    // This avoids the common issue where files are submitted in DOM order and no longer
    // line up with the members JSON when members are added/removed/edited.
    function resyncCvInputs() {
        // Build a map of existing inputs by their dataset.idx (if present)
        var existing = {};
        Array.from(cvFilesContainer.querySelectorAll('input[type=file]')).forEach(function(inp) {
            var di = parseInt(inp.dataset.idx, 10);
            if (!isNaN(di)) existing[di] = inp;
        });

        // Clear container and rebuild in correct order
        cvFilesContainer.innerHTML = '';
        var newCv = [];
        for (var i = 0; i < members.length; i++) {
            if (existing.hasOwnProperty(i)) {
                var inp = existing[i];
                inp.name = 'cv_files[' + i + ']';
                inp.dataset.idx = i;
                cvFilesContainer.appendChild(inp);
                newCv.push(inp);
            } else {
                // No file was provided for this member; skip creating an empty file input.
                // Server will treat missing entries as no-file for that member.
            }
        }
        cvInputs = newCv;
    }
        var editIndex = null;
        var submitBtn = form.querySelector('button[type="submit"]');
    var messageDiv = document.createElement('div');
        messageDiv.style.color = 'red';
        messageDiv.style.marginBottom = '12px';
        form.insertBefore(messageDiv, submitBtn);

        function renderMembers() {
            memberList.innerHTML = '';
            members.forEach(function(member, idx) {
                var div = document.createElement('div');
                div.className = 'member-summary';
                div.style.marginBottom = '12px';
                div.style.padding = '10px 10px 10px 10px';
                div.style.background = '#f5f8fd';
                div.style.borderRadius = '8px';
                div.style.position = 'relative';
                div.innerHTML =
                    '<div style="position:absolute;top:10px;right:10px;display:flex;gap:8px;">' +
                    '<button type=\'button\' class=\'edit-member-btn\' data-idx=\'' + idx + '\' style=\'background:none;border:none;cursor:pointer;\'><i class=\'fas fa-pencil-alt\' style=\'color:#1976d2;font-size:1.2em;\'></i></button>' +
                    '<button type=\'button\' class=\'delete-member-btn\' data-idx=\'' + idx + '\' style=\'background:none;border:none;cursor:pointer;\'><i class=\'fas fa-trash\' style=\'color:#d32f2f;font-size:1.2em;\'></i></button>' +
                    '</div>' +
                    '<strong>' + member.full_name + '</strong> (' + member.member_type + ')<br>' +
                    'NIC: ' + member.nic + ', Gender: ' + member.gender + ', Occupation: ' + member.occupation +
                    (member.occupation === 'student' ? ', School: ' + member.school + ', Grade: ' + member.grade : '') +
                    (member.occupation === 'university_student' ? ', University: ' + member.university_name : '') +
                    (member.occupation === 'other' ? ', Other: ' + member.occupation_other : '') +
                    '<br>Offers: ' + (member.offers.length ? member.offers.join(', ') : 'None') +
                    ', Disabled: ' + member.disabled +
                    ', WhatsApp: ' + member.whatsapp +
                    ', Age: ' + (member.age ? member.age : 'N/A') +
                    ', CV: ' + (member.cv ? member.cv : 'None');
                memberList.appendChild(div);
            });
        }

        function clearMemberFields() {
            form.querySelector('[name="full_name"]').value = '';
            form.querySelector('[name="name_with_initial"]').value = '';
            form.querySelector('[name="member_type"]').value = 'permanent';
            form.querySelector('[name="nic"]') .value = '';
            form.querySelector('[name="gender"]').value = 'male';
            form.querySelector('[name="occupation"]').value = '';
            form.querySelector('[name="occupation_other"]').value = '';
            form.querySelector('[name="school"]').value = '';
            form.querySelector('[name="grade"]').value = '';
            form.querySelector('[name="university_name"]').value = '';
            form.querySelectorAll('[name="offers[]"]').forEach(x=>x.checked=false);
            form.querySelector('[name="disabled"]').value = 'no';
            form.querySelector('[name="land_house_status"]').value = 'plot';
            form.querySelector('[name="whatsapp"]').value = '';
            form.querySelector('[name="age"]').value = '';
            // Reset file input
            var cv = form.querySelector('[name="cv"]');
            if (cv) cv.value = '';
            document.getElementById('student-fields').style.display = 'none';
            document.getElementById('university-fields').style.display = 'none';
            document.getElementById('occupation-other-group').style.display = 'none';
        }

        // Home mode toggle: new vs add to existing
        var homeModeRadios = form.querySelectorAll('[name="home_mode"]');
        var existingHomeInputWrap = document.getElementById('existing-home-input');
        function updateHomeMode() {
            var mode = form.querySelector('[name="home_mode"]:checked').value;
            var isExisting = (mode === 'existing');
            // disable/enable home detail inputs when adding to existing
            ['home_number','sub_road','address','no_of_members','has_assessment','assessment_number','resident_type','waste_disposal'].forEach(function(n){
                var el = form.querySelector('[name="'+n+'"]');
                if (!el) return;
                el.disabled = isExisting;
            });
            existingHomeInputWrap.style.display = isExisting ? 'block' : 'none';
        }
        homeModeRadios.forEach(function(r){ r.addEventListener('change', updateHomeMode); });
        updateHomeMode();

        function computeBirthYearFromNIC(nic) {
            if (!nic) return null;
            var digits = nic.replace(/[^0-9]/g, '');
            var year = null;
            if (digits.length === 12) {
                year = parseInt(digits.substring(0,4), 10);
            } else if (digits.length >= 2) {
                var yy = parseInt(digits.substring(0,2), 10);
                var currentYY = new Date().getFullYear() % 100;
                if (yy <= currentYY) year = 2000 + yy; else year = 1900 + yy;
            }
            return year;
        }

        function computeAgeFromNIC(nic) {
            var year = computeBirthYearFromNIC(nic);
            if (!year) return null;
            return new Date().getFullYear() - year;
        }

        // Sub-road options mapped to parent Road selection
        var subRoadMap = {
            '979 Side road': [
                '979 1st lane','979 2nd lane','979 3rd lane','Selinco Waththa','979 4th lane','Haritha uyana','979 5th lane','Sisla uyana','Jaya mawatha','979 6th Lane','979 7th lane','Seram lane','979 8th Lane','979 9th lane','Golad lane','pragathi mawatha','Sisira mawatha','Metro Niwas road','Green Lane','Ranawiru Chrandrakumara mawatha','979 10th lane','979 11 lane'
            ],
            '223 Side road': [
                '223 1st lane','223 2nd lane','223 3rd lane','223 4th lane','Gorak gaha handiya para','Daham mawatha','suhada mawatha II','223 8th lane','223 9th lane','223 10th lane','223 11 lane'
            ],
            'Korala maima side road': [
                'Welamada Para(Alubogahawaththa)','Korala maima 1st lane','Korala maima 2nd road','Annasiwaththa para','Pokuna para','Moragahalandha para','Korala maima 3rd lane','Korala maima 4th lane','rubber waththa para','Mudhaleege para'
            ],
            'Maddegoda polhena side road': [
                'Ranawiru Kapila Bandara mawatha','Maddegoda 1st lane','Maddegoda 2nd lane','Dewala para','Alubogahawaththa para','Dunkolamaduwa Para','Maddegoda 3rd lane','Maddegoda 4th lane','Maddegoda 5th lane','Maddegoda 6th lane'
            ],
            'Praja mandala para side road': [
                'Suhada Mawatha I','Mangala mawatha','prajamadala 2nd lane'
            ],
            '327 Side road': [
                '327 1st lane','Kurudugaha waththa para'
            ]
        };

        function updateSubRoadOptions() {
            var roadEl = form.querySelector('[name="home_number"]');
            var subEl = form.querySelector('[name="sub_road"]');
            if (!subEl) return;
            var road = roadEl ? roadEl.value : '';
            // clear existing
            subEl.innerHTML = '';

            // If no road selected, instruct the user and keep disabled
            if (!road) {
                var o = document.createElement('option'); o.value = ''; o.text = 'Select Road first'; subEl.appendChild(o);
                subEl.disabled = true;
                return;
            }

            // If a Main road is selected, there are no sub-roads -> keep disabled
            if (road.toLowerCase().indexOf('main') !== -1) {
                var o2 = document.createElement('option'); o2.value = ''; o2.text = 'No sub-roads for main road'; subEl.appendChild(o2);
                subEl.disabled = true;
                return;
            }

            var list = subRoadMap[road] || null;
            if (!list) {
                var o3 = document.createElement('option'); o3.value = ''; o3.text = 'No sub-roads available'; subEl.appendChild(o3);
                subEl.disabled = true;
                return;
            }

            var first = document.createElement('option'); first.value = ''; first.text = 'Select Sub Road'; subEl.appendChild(first);
            list.forEach(function(v){ var o = document.createElement('option'); o.value = v; o.text = v; subEl.appendChild(o); });
            subEl.disabled = false;
        }

        // wire road -> sub-road
        var roadSel = form.querySelector('[name="home_number"]');
        if (roadSel) {
            roadSel.addEventListener('change', function(){ try { updateSubRoadOptions(); } catch(e){} });
            // initialize
            try { updateSubRoadOptions(); } catch(e){}
        }

        // Address mapping (sample addresses). Keys match road or sub-road option values.
        var addressMap = {
            // Main roads
            '979 Main road': ['979 Main Rd - 1','979 Main Rd - 2','979 Main Rd - 3'],
            '223 Main road': ['223 Main Rd - 5','223 Main Rd - 7'],
            'Korala maima main road': ['Korala Main No.1','Korala Main No.2'],
            'Maddegoda polhena main road': ['Maddegoda Main 12','Maddegoda Main 14'],
            'Praja mandala para main road': ['Praja Main 3','Praja Main 4'],
            '327 Main road': ['327 Main 21','327 Main 22'],
            // Side roads (some examples)
            '979 Side road': ['979 Side - Block A','979 Side - Block B'],
            '223 Side road': ['223 Side - Block 1','223 Side - Block 2'],
            'Korala maima side road': ['Korala Side - A','Korala Side - B'],
            'Maddegoda polhena side road': ['Maddegoda Side - 1','Maddegoda Side - 2'],
            'Praja mandala para side road': ['Praja Side - East','Praja Side - West'],
            '327 Side road': ['327 Side - Alpha','327 Side - Beta'],

            // A few explicit sub-lane examples (others will fall back to generated samples)
            '979 1st lane': ['979 1st lane - 10','979 1st lane - 12'],
            '979 2nd lane': ['979 2nd lane - 3','979 2nd lane - 5'],
            '223 1st lane': ['223 1st lane - 1','223 1st lane - 2'],
            'Korala maima 1st lane': ['Korala 1st - 7','Korala 1st - 9'],
            'Maddegoda 1st lane': ['Maddegoda 1st - 2','Maddegoda 1st - 4']
        };

        function updateAddressOptions() {
            var addrEl = form.querySelector('[name="address"]');
            if (!addrEl) return;
            // prefer selected sub-road if enabled and chosen
            var subEl = form.querySelector('[name="sub_road"]');
            var roadEl = form.querySelector('[name="home_number"]');
            var selectedSub = (subEl && !subEl.disabled) ? (subEl.value || '') : '';
            var selectedRoad = roadEl ? (roadEl.value || '') : '';
            var key = selectedSub || selectedRoad;
            addrEl.innerHTML = '';
            if (!key) {
                var o = document.createElement('option'); o.value = ''; o.text = 'Select Road or Sub Road first'; addrEl.appendChild(o); addrEl.disabled = true; return;
            }
            // Look up addresses; if none, generate 3 sample addresses from the key
            var list = addressMap[key];
            if (!list) {
                list = [key + ' - 1','' + key + ' - 2', key + ' - 3'];
            }
            var first = document.createElement('option'); first.value = ''; first.text = 'Select Address'; addrEl.appendChild(first);
            list.forEach(function(a){ var o = document.createElement('option'); o.value = a; o.text = a; addrEl.appendChild(o); });
            addrEl.disabled = false;
        }

        // Ensure addresses update when sub-road changes as well
        var subSel = form.querySelector('[name="sub_road"]');
        if (subSel) subSel.addEventListener('change', function(){ try { updateAddressOptions(); } catch(e){} });
        // Also update addresses when road changes (in case main road has addresses)
        if (roadSel) roadSel.addEventListener('change', function(){ try { updateAddressOptions(); } catch(e){} });
        // initialize address select
        try { updateAddressOptions(); } catch(e){}

        // compute age on NIC blur
        var nicInput = form.querySelector('[name="nic"]');
        if (nicInput) {
            nicInput.addEventListener('blur', function() {
                var val = this.value;
                var age = computeAgeFromNIC(val);
                if (age !== null) {
                        var ageInput = form.querySelector('[name="age"]');
                        if (ageInput) ageInput.value = age;
                    }
            });
        }

        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Collect member fields
            var memberFields = {
                full_name: form.querySelector('[name="full_name"]').value,
                name_with_initial: form.querySelector('[name="name_with_initial"]').value,
                member_type: form.querySelector('[name="member_type"]').value,
                nic: form.querySelector('[name="nic"]').value,
                gender: form.querySelector('[name="gender"]').value,
                occupation: form.querySelector('[name="occupation"]').value,
                occupation_other: form.querySelector('[name="occupation_other"]').value,
                school: form.querySelector('[name="school"]').value,
                grade: form.querySelector('[name="grade"]').value,
                university_name: form.querySelector('[name="university_name"]').value,
                offers: Array.from(form.querySelectorAll('[name="offers[]"]:checked')).map(x=>x.value),
                disabled: form.querySelector('[name="disabled"]').value,
                land_house_status: form.querySelector('[name="land_house_status"]').value,
                whatsapp: form.querySelector('[name="whatsapp"]').value,
                age: form.querySelector('[name="age"]').value,
                // store filename for display; actual File will be appended to hidden inputs
                cv: null
            };
            // Handle file input separately so files are submitted correctly.
            // We maintain a parallel `cvInputs` array so files stay aligned with `members`.
            var originalCv = form.querySelector('[name="cv"]');
            var file = originalCv && originalCv.files && originalCv.files[0] ? originalCv.files[0] : null;
            // Decide target index for this member
            var targetIdx = (editIndex !== null) ? editIndex : members.length;

                if (file) {
                // Create or replace the hidden file input for this index
                var newInput = document.createElement('input');
                newInput.type = 'file';
                    // name will be fixed by resyncCvInputs() to cv_files[<index>]
                    newInput.name = 'cv_files[' + targetIdx + ']';
                newInput.dataset.idx = targetIdx;
                // Use DataTransfer where supported
                try {
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    newInput.files = dt.files;
                } catch (err) {
                    console.warn('Could not attach file programmatically', err);
                }

                // If editing and an existing hidden input exists for this index, replace it
                if (editIndex !== null && cvInputs[targetIdx]) {
                        try { cvFilesContainer.replaceChild(newInput, cvInputs[targetIdx]); } catch (e) { cvFilesContainer.appendChild(newInput); }
                        cvInputs[targetIdx] = newInput;
                } else {
                    // Append at the end; we'll keep arrays aligned below
                        cvFilesContainer.appendChild(newInput);
                        // ensure cvInputs contains the new input in the right place after resync
                        cvInputs.splice(targetIdx, 0, newInput);
                }

                memberFields.cv = file.name;
            } else {
                // No new file selected. If editing, preserve existing filename if present.
                if (editIndex !== null && members[editIndex] && members[editIndex].cv) {
                    memberFields.cv = members[editIndex].cv;
                } else {
                    memberFields.cv = null;
                }
            }
            if (editIndex !== null) {
                members[editIndex] = memberFields;
                editIndex = null;
            } else {
                members.push(memberFields);
            }

            // After insert/delete adjustments, ensure hidden CV inputs indexes match members array
            // Re-sync data-idx attributes and compact cvInputs if necessary
            // Remove any extra inputs (happens when editing without file selection)
            // Ensure cvInputs length equals members length
            // Re-sync hidden file inputs so DOM order and input names match members indexes
            resyncCvInputs();
            renderMembers();
            clearMemberFields();
            messageDiv.textContent = '';
            try { showToast('success', 'Member added'); } catch(e) {}
        });

        memberList.addEventListener('click', function(e) {
            // Support clicks on the button or inner icon by finding the closest button
            var btn = e.target.closest('button');
            if (!btn) return;
            if (btn.classList.contains('edit-member-btn')) {
                var idx = parseInt(btn.getAttribute('data-idx'));
                if (isNaN(idx) || !members[idx]) return;
                var member = members[idx];
                form.querySelector('[name="full_name"]').value = member.full_name;
                form.querySelector('[name="name_with_initial"]').value = member.name_with_initial;
                form.querySelector('[name="member_type"]').value = member.member_type;
                form.querySelector('[name="nic"]').value = member.nic;
                form.querySelector('[name="gender"]').value = member.gender;
                form.querySelector('[name="occupation"]').value = member.occupation;
                form.querySelector('[name="occupation_other"]').value = member.occupation_other;
                form.querySelector('[name="school"]').value = member.school;
                form.querySelector('[name="grade"]').value = member.grade;
                form.querySelector('[name="university_name"]').value = member.university_name;
                form.querySelectorAll('[name="offers[]"]').forEach(x=>x.checked = Array.isArray(member.offers) && member.offers.includes(x.value));
                form.querySelector('[name="disabled"]').value = member.disabled;
                form.querySelector('[name="land_house_status"]').value = member.land_house_status;
                form.querySelector('[name="whatsapp"]').value = member.whatsapp;
                // Note: file inputs cannot be set programmatically for security; leave cv as-is
                form.querySelector('[name="age"]').value = member.age || '';
                // Show conditional fields
                toggleOccupation(form.querySelector('[name="occupation"]'));
                editIndex = idx;
            } else if (btn.classList.contains('delete-member-btn')) {
                var idx = parseInt(btn.getAttribute('data-idx'));
                if (isNaN(idx)) return;
                members.splice(idx, 1);
                // Remove corresponding hidden CV input if present
                if (cvInputs[idx]) {
                    try { cvFilesContainer.removeChild(cvInputs[idx]); } catch (e) {}
                    cvInputs.splice(idx, 1);
                }
                // Re-sync hidden inputs after deletion so names/indexes align
                resyncCvInputs();
                renderMembers();
            }
        });

        // Hidden field to carry members data to backend
        var hiddenMembers = document.createElement('input');
        hiddenMembers.type = 'hidden';
        hiddenMembers.name = 'members_json';
        form.appendChild(hiddenMembers);

        form.addEventListener('submit', function(e) {
            // If user filled the member form but didn't click "Add Member" for the last one,
            // automatically add it so submission can proceed.
            var anyFilled = form.querySelector('[name="full_name"]').value.trim() !== '' || form.querySelector('[name="nic"]').value.trim() !== '';
            if (anyFilled) {
                // Programmatically trigger the Add Member button to save the current inputs
                try { addBtn.click(); } catch (err) { /* ignore */ }
            }

            if (members.length === 0) {
                e.preventDefault();
                messageDiv.textContent = 'Please fill out details of member details section.';
                try { showToast('error', 'Please add at least one member before submitting'); } catch(e) {}
                return;
            }

            // attach members JSON to hidden input
            hiddenMembers.value = JSON.stringify(members);
            // include home mode info so server can decide whether to create a new home or add members to an existing one
            var mode = form.querySelector('[name="home_mode"]:checked').value;
            var existingHome = form.querySelector('[name="existing_home_number"]') ? form.querySelector('[name="existing_home_number"]').value.trim() : '';
            // create or update hidden fields for server
            var modeField = form.querySelector('input[name="add_to_existing"]');
            if (!modeField) {
                modeField = document.createElement('input');
                modeField.type = 'hidden'; modeField.name = 'add_to_existing';
                form.appendChild(modeField);
            }
            modeField.value = (mode === 'existing') ? '1' : '0';
            var existingField = form.querySelector('input[name="existing_home_number_hidden"]');
            if (!existingField) {
                existingField = document.createElement('input');
                existingField.type = 'hidden'; existingField.name = 'existing_home_number_hidden';
                form.appendChild(existingField);
            }
            existingField.value = existingHome;

            // Basic client-side validation: when adding to existing, require an existing home number
            if (mode === 'existing' && !existingHome) {
                e.preventDefault();
                messageDiv.textContent = 'Please enter the existing house number to add members to.';
                try { showToast('error', 'Enter existing house number'); } catch(e) {}
                return;
            }
            messageDiv.textContent = '';
        });
    });
    </script>
</head>
<body>
    <div class="details-form">
        <form method="post" action="<?= base_url('add-details') ?>" enctype="multipart/form-data">
            <div class="form-section">
                <div class="form-section-title">Home Details</div>
                <div style="margin-bottom:12px;display:flex;gap:12px;align-items:center;">
                    <label style="font-weight:600;margin-right:8px;">Mode:</label>
                    <label style="display:inline-flex;align-items:center;gap:6px;"><input type="radio" name="home_mode" value="new" checked> New Home</label>
                    <label style="display:inline-flex;align-items:center;gap:6px;"><input type="radio" name="home_mode" value="existing"> Add to Existing Home</label>
                    <div id="existing-home-input" style="display:none;margin-left:12px;">
                        <input type="text" name="existing_home_number" placeholder="Enter existing house number">
                        <small style="color:#666;display:block">Enter the house number of the existing home to add members to.</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Road</label>
                        <select name="home_number" required>
                            <option value="">Select Road</option>
                            <option value="979 Main road">979 Main road</option>
                            <option value="979 Side road">979 Side road</option>
                            <option value="223 Main road">223 Main road</option>
                            <option value="223 Side road">223 Side road</option>
                            <option value="Korala maima main road">Korala maima main road</option>
                            <option value="Korala maima side road">Korala maima side road</option>
                            <option value="Maddegoda polhena main road">Maddegoda polhena main road</option>
                            <option value="Maddegoda polhena side road">Maddegoda polhena side road</option>
                            <option value="Praja mandala para main road">Praja mandala para main road</option>
                            <option value="Praja mandala para side road">Praja mandala para side road</option>
                            <option value="327 Main road">327 Main road</option>
                            <option value="327 Side road">327 Side road</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sub Road</label>
                        <select name="sub_road" id="sub-road-select" disabled>
                            <option value="">Select Road first</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <select name="address" id="address-select" required>
                            <option value="">Select Road or Sub Road first</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Number of Members</label>
                        <input type="number" name="no_of_members" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Has Assessment Number?</label>
                        <select name="has_assessment" onchange="toggleAssessment(this)">
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="form-group" id="assessment-number-group" style="display:none;">
                        <label>Assessment Number</label>
                        <input type="text" name="assessment_number">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Resident Type</label>
                        <select name="resident_type">
                            <option value="permanent">Permanent</option>
                            <option value="rented">Rented</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Waste Disposal</label>
                        <select name="waste_disposal">
                            <option value="council">Local Council</option>
                            <option value="home">At Home</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-section">
                <div class="form-section-title">Member Details</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Name with Initial</label>
                        <input type="text" name="name_with_initial" required>
                    </div>
                    <div class="form-group">
                        <label>Member Type</label>
                        <select name="member_type">
                            <option value="permanent">Permanent</option>
                            <option value="temporary">Temporary</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>NIC</label>
                        <input type="text" name="nic">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" min="0" placeholder="Auto from NIC">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Occupation</label>
                        <select name="occupation" onchange="toggleOccupation(this)">
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
                            <option value="no">No</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group hidden" id="occupation-other-group">
                        <label>Other Occupation</label>
                        <input type="text" name="occupation_other">
                    </div>
                </div>
                <div class="form-row hidden" id="student-fields">
                    <div class="form-group">
                        <label>School</label>
                        <input type="text" name="school">
                    </div>
                    <div class="form-group">
                        <label>Grade</label>
                        <select name="grade">
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
                </div>
                <div class="form-row hidden" id="university-fields">
                    <div class="form-group">
                        <label>University Name</label>
                        <input type="text" name="university_name">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Offers Receiving</label>
                        <div>
                            <label><input type="checkbox" name="offers[]" value="aswasuma"> Aswasuma</label>
                            <label><input type="checkbox" name="offers[]" value="adult"> Adult Offers</label>
                            <label><input type="checkbox" name="offers[]" value="mahapola"> Mahapola</label>
                            <label><input type="checkbox" name="offers[]" value="grade5"> Grade 5 Scholarship</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Disabled?</label>
                        <select name="disabled">
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Land/House Status</label>
                        <select name="land_house_status">
                            <option value="plot">Plot of Land</option>
                            <option value="no_house">No House</option>
                            <option value="no_land_house">No Land and House</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>WhatsApp Number</label>
                        <input type="text" name="whatsapp">
                    </div>
                </div>
                <!-- <div class="form-row">
                    <div class="form-group">
                        <label>No Job? Upload CV</label>
                        <input type="file" name="cv">
                    </div>
                </div> -->
                <button type="button" class="add-member-btn">Add Member</button>
                <div class="member-list">
                    <!-- Dynamically added members will appear here -->
                </div>
            </div>
            <button type="submit" class="add-member-btn">Submit Details</button>
        </form>
    </div>
</body>
</html>
