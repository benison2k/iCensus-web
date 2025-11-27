// public/assets/js/resident/modalManager.js

// FIX: Update import to use the new file name (api_v2.js)
import { fetchData } from '../api_v2.js'; 

const basePath = '';

function setFormEditable(editable, state) {
    const form = document.getElementById('residentForm');
    const saveBtn = document.getElementById('saveBtn');
    const editBtn = form.querySelector('.editBtn');
    const deleteBtn = form.querySelector('.deleteBtn');

    form.querySelectorAll('input, select').forEach(input => input.disabled = !editable);
    if(saveBtn) saveBtn.style.display = editable ? 'inline-flex' : 'none';
    if(editBtn) editBtn.style.display = editable ? 'none' : 'inline-flex';

    if (editable || state.userRole === 'Encoder') {
        if(deleteBtn) deleteBtn.style.display = 'none';
    } else {
        if(deleteBtn) deleteBtn.style.display = 'inline-flex';
    }
};

async function openModalForEdit(id, state, startInEditMode = false) {
    const form = document.getElementById('residentForm');
    const modal = document.getElementById('residentModal');
    const modalTitle = document.getElementById('modalTitle');
    const hiddenId = document.getElementById('resident_id');
    const approveBtn = document.getElementById('approveBtn');
    const declineBtn = document.getElementById('declineBtn');
    const editBtn = modal.querySelector('.editBtn');
    const deleteBtn = modal.querySelector('.deleteBtn');

    form.reset();
    try {
        // This call will now use the new, non-cached api_v2.js
        const result = await fetchData('residents/process', { action: 'get', resident_id: id });
        
        if (result.status !== 'success' || !result.resident) {
            alert(result.message || 'Resident not found.');
            return;
        }

        const data = result.resident;
        Object.keys(data).forEach(key => {
            const el = form.elements[key];
            if (el) {
                if (el.type === 'checkbox') {
                    el.checked = (data[key] == 1);
                } else {
                    el.value = data[key];
                }
            }
        });

        setFormEditable(startInEditMode, state);
        modalTitle.textContent = startInEditMode ? `Edit Resident Info` : `View Resident Info`;
        hiddenId.value = id;

        // Show/hide buttons based on approval status (using local basePath)
        if (data.approval_status === 'pending') {
            if(approveBtn) {
                approveBtn.style.display = 'inline-flex';
                approveBtn.href = `${basePath}/residents/approve?id=${id}`;
            }
            if(declineBtn) {
                declineBtn.style.display = 'inline-flex';
                declineBtn.href = `${basePath}/residents/reject?id=${id}`;
            }
            if(editBtn) editBtn.style.display = 'none';
            if(deleteBtn) deleteBtn.style.display = 'none';
        } else {
            if(approveBtn) approveBtn.style.display = 'none';
            if(declineBtn) declineBtn.style.display = 'none';
            if(editBtn) editBtn.style.display = startInEditMode ? 'none' : 'inline-flex';
        }

        modal.style.display = 'flex';
        if (modal.updateProgress) {
            modal.updateProgress();
        }
    } catch (err) {
        console.error('Failed to fetch resident data:', err);
        alert('An unexpected error occurred.');
    }
};

function openModalForAdd(state) {
    const form = document.getElementById('residentForm');
    const modal = document.getElementById('residentModal');
    const modalTitle = document.getElementById('modalTitle');
    const hiddenId = document.getElementById('resident_id');
    const editBtn = modal.querySelector('.editBtn');
    const approveBtn = document.getElementById('approveBtn');
    const declineBtn = document.getElementById('declineBtn');
    const deleteBtn = modal.querySelector('.deleteBtn');

    form.reset();
    hiddenId.value = '';
    setFormEditable(true, state);
    if(editBtn) editBtn.style.display = 'none';
    if(approveBtn) approveBtn.style.display = 'none';
    if(declineBtn) declineBtn.style.display = 'none';
    if(deleteBtn) deleteBtn.style.display = 'none';

    modalTitle.textContent = 'Add New Resident';
    modal.style.display = 'flex';
    if (modal.updateProgress) {
        modal.updateProgress();
    }
};

function initializeModal(state) {
    const modal = document.getElementById('residentModal');
    const closeModal = modal.querySelector('.close'); 
    const editBtn = modal.querySelector('.editBtn');
    const declineBtn = document.getElementById('declineBtn');

    if(closeModal) closeModal.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });
    if(editBtn) editBtn.addEventListener('click', () => setFormEditable(true, state));
    if(declineBtn) {
        declineBtn.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to reject this entry?')) {
                e.preventDefault();
            }
        });
    }
}

export { initializeModal, openModalForEdit, openModalForAdd };