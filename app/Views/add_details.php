<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Details - V-Track</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    <style>
        .details-form {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(25, 118, 210, 0.10);
            padding: 32px 40px;
        }
        .form-section {
            margin-bottom: 32px;
        }
        .form-section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1976d2;
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
            background: #1976d2;
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
    }
    </script>
</head>
<body>
    <div class="details-form">
        <form>
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
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Occupation</label>
                        <select name="occupation" onchange="toggleOccupation(this)">
                            <option value="student">Student</option>
                            <option value="farmer">Farmer</option>
                            <option value="teacher">Teacher</option>
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
                        <input type="text" name="grade">
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
            <button type="submit" class="login-btn">Submit Details</button>
        </form>
    </div>
</body>
</html>
