<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Details - V-Track</title>
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
                newInput.name = 'cv_files[]';
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
            if (cvInputs.length > members.length) {
                // remove trailing inputs
                for (var r = cvInputs.length - 1; r >= members.length; r--) {
                    try { cvFilesContainer.removeChild(cvInputs[r]); } catch (e) {}
                    cvInputs.splice(r, 1);
                }
            }
            // Re-index remaining inputs
            cvInputs.forEach(function(inp, i) { inp.dataset.idx = i; });
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
                // Re-index remaining hidden inputs
                cvInputs.forEach(function(inp, i) { inp.dataset.idx = i; });
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
                <div class="form-row">
                    <div class="form-group">
                        <label>Home Number</label>
                        <input type="text" name="home_number" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" required>
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
                        <input type="text" name="nic" required>
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
                <div class="form-row">
                    <div class="form-group">
                        <label>No Job? Upload CV</label>
                        <input type="file" name="cv">
                    </div>
                </div>
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
