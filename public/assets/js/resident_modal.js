// public/assets/js/resident_modal.js

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('residentModal');
    if (!modal) return;

    // FIX: Set to empty string for root domain
    const BASE_URL = ''; 

    const form = document.getElementById('residentForm');
    const modalTitle = document.getElementById('modalTitle');
    const closeBtn = modal.querySelector('.close');
    
    // Buttons in the footer
    const editBtn = modal.querySelector('.editBtn');
    const deleteBtn = modal.querySelector('.deleteBtn');
    const saveBtn = modal.querySelector('.btn-save');
    const approveBtn = document.getElementById('approveBtn');
    const declineBtn = document.getElementById('declineBtn');

    // Tabs
    const tabButtons = modal.querySelectorAll('.tab-button');
    const tabContents = modal.querySelectorAll('.tab-content');

    // --- NEW: Progress Bar Logic ---
    const progressBar = modal.querySelector('.progress-bar');
    const progressLabel = modal.querySelector('.progress-label');

    // Define the update function
    const updateProgress = () => {
        if (!form) return;
        
        // Select all required inputs (visible ones only if needed, but usually all required)
        const requiredInputs = form.querySelectorAll('[required]');
        let filledCount = 0;
        let total = 0;

        requiredInputs.forEach(input => {
            // Check if the input is actually part of the current view (optional, but good practice)
            // For now, we count all required fields in the form
            total++;
            if (input.value.trim() !== '') {
                filledCount++;
            }
        });

        // Avoid division by zero
        const percentage = total === 0 ? 100 : Math.round((filledCount / total) * 100);

        if (progressBar) progressBar.style.width = `${percentage}%`;
        if (progressLabel) progressLabel.textContent = `Completion: ${percentage}%`;
    };

    // Attach function to modal DOM object so external scripts (like modalManager.js) can call modal.updateProgress()
    modal.updateProgress = updateProgress;

    // Attach event listeners to form inputs to update in real-time
    if (form) {
        form.addEventListener('input', updateProgress);
        form.addEventListener('change', updateProgress);
    }
    // -------------------------------

    // --- Tab Switching Logic ---
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            button.classList.add('active');
            const targetId = `tab-${button.dataset.tab}`;
            const targetContent = document.getElementById(targetId);
            if(targetContent) targetContent.classList.add('active');
        });
    });

    // --- Close Modal Logic ---
    const closeModal = () => {
        modal.style.display = 'none';
        if(form) form.reset();
        
        // Reset progress bar
        if (progressBar) progressBar.style.width = '0%';
        if (progressLabel) progressLabel.textContent = 'Completion: 0%';

        // Reset tabs to first one
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        if(tabButtons[0]) tabButtons[0].classList.add('active');
        if(tabContents[0]) tabContents[0].classList.add('active');
    };

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    // --- Open Modal (Global Function) ---
    window.openResidentModal = async (id = null, mode = 'view') => {
        modal.style.display = 'flex'; 
        
        // Hide all footer buttons first
        [editBtn, deleteBtn, saveBtn, approveBtn, declineBtn].forEach(btn => {
            if(btn) btn.style.display = 'none';
        });

        // Enable/Disable inputs based on mode
        const inputs = form.querySelectorAll('input, select, textarea');
        const isEditable = (mode === 'add' || mode === 'edit');
        inputs.forEach(input => input.disabled = !isEditable);

        if (mode === 'add') {
            modalTitle.textContent = 'Add New Resident';
            form.reset();
            updateProgress(); // Reset bar
            if(saveBtn) saveBtn.style.display = 'inline-block';
            
            // Clear hidden ID
            const idInput = form.querySelector('input[name="resident_id"]');
            if(idInput) idInput.value = '';
        } else {
            // View or Edit mode - Fetch Data
            modalTitle.textContent = 'Loading...';
            try {
                const response = await fetch(`${BASE_URL}/residents/process?action=get&id=${id}`);
                const result = await response.json();

                if (result.status === 'success' && result.resident) {
                    const r = result.resident;
                    modalTitle.textContent = (mode === 'edit') ? 'Edit Resident' : 'Resident Details';
                    
                    // Populate form
                    Object.keys(r).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            if (input.type === 'radio') {
                                if (input.value == r[key]) input.checked = true;
                            } else {
                                input.value = r[key];
                            }
                        }
                    });

                    updateProgress(); // Update bar with loaded data

                    // Handle Mode Specific Buttons
                    if (mode === 'view') {
                        if(editBtn) editBtn.style.display = 'inline-block';
                        if(deleteBtn) deleteBtn.style.display = 'inline-block';
                        if(editBtn) editBtn.dataset.id = id;
                        if(deleteBtn) deleteBtn.dataset.id = id;
                    } else if (mode === 'edit') {
                        if(saveBtn) saveBtn.style.display = 'inline-block';
                    }
                } else {
                    alert('Error fetching resident data');
                    closeModal();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while loading data.');
                closeModal();
            }
        }
    };

    // --- Button Actions ---
    if(editBtn) {
        editBtn.addEventListener('click', () => {
            const id = editBtn.dataset.id;
            window.openResidentModal(id, 'edit');
        });
    }

    if(deleteBtn) {
        deleteBtn.addEventListener('click', async () => {
            const id = deleteBtn.dataset.id;
            if(!confirm('Are you sure you want to delete this resident?')) return;

            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                const csrfInput = document.querySelector('input[name="csrf_token"]');
                if(csrfInput) formData.append('csrf_token', csrfInput.value);

                const response = await fetch(`${BASE_URL}/residents/process`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if(result.status === 'success') {
                    alert('Resident deleted successfully'); 
                    closeModal();
                    location.reload(); 
                } else {
                    alert(result.message || 'Delete failed');
                }
            } catch (e) {
                console.error(e);
                alert('Delete failed');
            }
        });
    }

    // --- FIX: REMOVED THE DUPLICATE FORM SUBMISSION LISTENER ---
    // The 'submit' event is now handled exclusively by public/assets/js/resident/formHandler.js
});