// public/assets/js/resident/formHandler.js

import { renderTable } from './tableManager.js';

// FIX: Set to empty string
const basePath = '';

function showAjaxResult(message, type = 'success') {
// ... (rest of function remains the same)
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    const icon = type === 'success' ? 'check_circle' : 'error';
    toast.innerHTML = `<span class="material-icons">${icon}</span><p>${message}</p>`;
    document.body.appendChild(toast);

    setTimeout(() => { toast.classList.add('show'); }, 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
            window.location.reload();
        }, 500);
    }, 3000);
}

async function handleFormSubmit(form, state) {
    try {
        const formData = new FormData(form);
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (response.ok && result.status === 'success') {
            document.getElementById('residentModal').style.display = 'none';
            showAjaxResult(result.message || 'Resident saved successfully!', 'success');
        } else {
            alert(result.message || 'An error occurred.');
        }
    } catch (error) {
        alert('A network error occurred. Please try again.');
    }
}

async function handleDelete(id) {
    if (!id) return;
    if (!confirm('Are you sure you want to delete this resident?')) return;

    // FIX: Using corrected basePath
    const response = await fetch(`${basePath}/residents/process`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ action: 'delete', id: id })
    });
    const result = await response.json();
    if (result.status === 'success') {
        showAjaxResult(result.message || 'Resident deleted successfully.', 'success');
    } else {
        alert(result.message || 'Failed to delete resident.');
    }
}

function initializeForm(state) {
    const form = document.getElementById('residentForm');
    const deleteBtn = form.querySelector('.deleteBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        handleFormSubmit(this, state);
    });

    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            const id = document.getElementById('resident_id').value;
            handleDelete(id);
        });
    }
}

export { initializeForm, handleDelete };