<div id="residentModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="modalTitle">Resident Info</h3>

        <div class="progress-label">Completion: 0%</div>
        <div class="progress-container">
            <div class="progress-bar" id="residentProgressBar"></div>
        </div>

        <form id="residentForm" method="POST" action="../core/residents_process.php" novalidate>
            
            <input type="hidden" name="resident_id" id="resident_id">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <label>First Name <span style="color:red">*</span></label>
            <input type="text" name="first_name" required>
            
            <label>Middle Name</label>
            <input type="text" name="middle_name">
            
            <label>Last Name <span style="color:red">*</span></label>
            <input type="text" name="last_name" required>
            
            <label>Suffix</label>
            <input type="text" name="suffix">
            
            <label>Nickname</label>
            <input type="text" name="nickname">
            
            <label>Date of Birth <span style="color:red">*</span></label>
            <input type="date" name="dob" required>
            
            <label>Gender <span style="color:red">*</span></label>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            
            <label>Civil Status</label>
            <input type="text" name="civil_status">
            
            <label>Blood Type</label>
            <input type="text" name="blood_type">
            
            <label>Nationality</label>
            <input type="text" name="nationality">

            <label>Contact Number</label>
            <input type="text" name="contact_number">
            
            <label>Email</label>
            <input type="email" name="email">
            
            <label>Emergency Contact Name</label>
            <input type="text" name="emergency_name">
            
            <label>Emergency Relation</label>
            <input type="text" name="emergency_relation">
            
            <label>Emergency Number</label>
            <input type="text" name="emergency_number">

            <label>House Number <span style="color:red">*</span></label>
            <input type="text" name="house_no" required>
            
            <label>Street <span style="color:red">*</span></label>
            <input type="text" name="street" required>
            
            <label>Purok <span style="color:red">*</span></label>
            <input type="text" name="purok" required>
            
            <label>Barangay <span style="color:red">*</span></label>
            <input type="text" name="barangay" required>
            
            <label>Head of Household <span style="color:red">*</span></label>
            <input type="text" name="head_of_household" required>
            
            <label>Relationship to Head <span style="color:red">*</span></label>
            <input type="text" name="relationship" required>

            <label>Status</label>
            <select name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Moved">Moved</option>
                <option value="Deceased">Deceased</option>
            </select>

            <div class="modal-footer" style="margin-top:1rem; display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" class="editBtn material-icons" style="cursor:pointer;">edit</button>
                <button type="button" class="deleteBtn material-icons" style="cursor:pointer;">delete</button>
                <button type="submit" name="saveResident" style="display:none;" id="saveBtn">
                    <span class="material-icons">save</span> Save
                </button>
            </div>
        </form>
    </div>
</div>