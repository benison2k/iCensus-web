// public/assets/js/resident/formHandler.js

import { renderTable } from './tableManager.js';

const basePath = '';

// --- Validation Modal Helper ---
function showValidationModal(missingFields) {
    const existing = document.getElementById('validationModal');
    if (existing) existing.remove();

    const modalHtml = `
        <div id="validationModal" class="modal" style="display: flex; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
            <div class="modal-content" style="background: #fff; padding: 25px; border-radius: 12px; width: 90%; max-width: 450px; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: fadeIn 0.2s ease-out;">
                <span class="close-validation" style="position: absolute; right: 15px; top: 10px; font-size: 24px; cursor: pointer; color: #666;">&times;</span>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; color: #d32f2f;">
                    <span class="material-icons" style="font-size: 28px;">error_outline</span>
                    <h3 style="margin: 0; font-size: 1.25rem;">Missing Required Fields</h3>
                </div>
                <p style="margin-bottom: 15px; color: #333;">You haven't filled out the following fields:</p>
                <ul style="color: #d32f2f; text-align: left; margin-bottom: 25px; padding-left: 20px; font-weight: 500;">
                    ${missingFields.map(field => `<li style="margin-bottom: 5px;">${field}</li>`).join('')}
                </ul>
                <div style="text-align: right;">
                    <button id="btn-close-val" style="background: #333; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">OK, I'll Fix It</button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = document.getElementById('validationModal');
    const closeBtn = modal.querySelector('.close-validation');
    const okBtn = document.getElementById('btn-close-val');
    const closeModal = () => modal.remove();
    
    closeBtn.onclick = closeModal;
    okBtn.onclick = closeModal;
    window.onclick = (e) => { if (e.target === modal) closeModal(); };
}

function showAjaxResult(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    const icon = type === 'success' ? 'check_circle' : 'error';
    toast.innerHTML = `<span class="material-icons">${icon}</span><p>${message}</p>`;
    document.body.appendChild(toast);

    setTimeout(() => { toast.classList.add('show'); }, 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => { toast.remove(); window.location.reload(); }, 500);
    }, 3000);
}

async function handleFormSubmit(form, state) {
    try {
        const formData = new FormData(form);
        const response = await fetch(form.action, { method: 'POST', body: formData });
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
    if (!id || !confirm('Are you sure you want to delete this resident?')) return;
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

        const requiredInputs = form.querySelectorAll('[required]');
        let missingFields = [];
        let firstInvalid = null;

        requiredInputs.forEach(input => {
            input.style.borderColor = ''; 
            input.style.backgroundColor = '';

            if (!input.value.trim()) {
                input.style.borderColor = '#d32f2f';
                input.style.backgroundColor = '#fff8f8';
                
                let fieldName = input.getAttribute('name');
                const label = input.previousElementSibling;
                if (label && label.tagName === 'LABEL') {
                    fieldName = label.textContent.replace(/[*:]/g, '').trim();
                } else {
                    fieldName = fieldName.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                }
                
                missingFields.push(fieldName);
                if (!firstInvalid) firstInvalid = input;
                
                input.addEventListener('input', function() {
                    this.style.borderColor = '';
                    this.style.backgroundColor = '';
                }, { once: true });
            }
        });

        if (missingFields.length > 0) {
            // Check if invalid field is in a hidden tab and switch to it
            if (firstInvalid) {
                const tabContent = firstInvalid.closest('.tab-content');
                if (tabContent && !tabContent.classList.contains('active')) {
                    const tabId = tabContent.id.replace('tab-', '');
                    const tabBtn = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
                    if (tabBtn) tabBtn.click();
                }
                setTimeout(() => firstInvalid.focus(), 100);
            }
            showValidationModal(missingFields);
            return;
        }

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