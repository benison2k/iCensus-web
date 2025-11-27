// public/assets/js/resident_modal.js

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('residentModal');
    if (!modal) return;

    // FIX: Set to empty string for root domain
    const BASE_URL = ''; 

    const form = document.getElementById('residentForm');
    const modalTitle = document.getElementById('modalTitle');
    const closeBtn = modal.querySelector('.close');
    const cancelBtn = modal.querySelector('.btn-cancel'); // If you have one
    
    // Buttons in the footer
    const editBtn = modal.querySelector('.editBtn');
    const deleteBtn = modal.querySelector('.deleteBtn');
    const saveBtn = modal.querySelector('.btn-save');
    const approveBtn = document.getElementById('approveBtn');
    const declineBtn = document.getElementById('declineBtn');

    // Tabs
    const tabButtons = modal.querySelectorAll('.tab-button');
    const tabContents = modal.querySelectorAll('.tab-content');

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
        modal.style.display = 'flex'; // Use flex to center if your CSS supports it
        
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
            if(saveBtn) saveBtn.style.display = 'inline-block';
            // Clear hidden ID
            const idInput = form.querySelector('input[name="resident_id"]');
            if(idInput) idInput.value = '';
        } else {
            // View or Edit mode - Fetch Data
            modalTitle.textContent = 'Loading...';
            try {
                // FIX: Use correct path
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

                    // Handle Mode Specific Buttons
                    if (mode === 'view') {
                        if(editBtn) editBtn.style.display = 'inline-block';
                        if(deleteBtn) deleteBtn.style.display = 'inline-block';
                        // Add ID to buttons for later use
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
                // Add CSRF if available in a meta tag or hidden input elsewhere
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
                    location.reload(); // Refresh list
                } else {
                    alert(result.message || 'Delete failed');
                }
            } catch (e) {
                console.error(e);
                alert('Delete failed');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Basic Validation
            const required = form.querySelectorAll('[required]');
            let isValid = true;
            required.forEach(field => {
                if(!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '';
                }
            });

            if(!isValid) {
                alert('Please fill in all required fields.');
                return;
            }

            const formData = new FormData(form);
            // Ensure action is set
            formData.append('action', 'save');

            try {
                const response = await fetch(`${BASE_URL}/residents/process`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    alert(result.message || 'Saved successfully');
                    closeModal();
                    location.reload();
                } else {
                    alert(result.message || 'Save failed');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving.');
            }
        });
    }
});