// public/assets/js/resident/formHandler.js

import { renderTable } from './tableManager.js';

const basePath = '';

// --- Enhanced Validation Modal (Supports Categories) ---
function showValidationModal(missingFieldsData, customTitle = "Missing Required Fields", isWarning = false) {
    const existing = document.getElementById('validationModal');
    if (existing) existing.remove();

    const color = isWarning ? '#f57c00' : '#d32f2f';
    const icon = isWarning ? 'warning' : 'error_outline';
    const btnText = isWarning ? 'Check Again' : "OK, I'll Fix It";

    let listHtml = '';
    
    // Check if input is a simple Array (for duplicates) or Object (for missing fields)
    if (Array.isArray(missingFieldsData)) {
        listHtml = `
            <ul style="color: #333; text-align: left; margin-bottom: 25px; padding-left: 20px; font-weight: 500;">
                ${missingFieldsData.map(field => `<li style="margin-bottom: 5px;">${field}</li>`).join('')}
            </ul>`;
    } else {
        // Object format: { "Personal Details": ["First Name"], ... }
        listHtml = `<div style="text-align: left; margin-bottom: 20px; max-height: 50vh; overflow-y: auto; padding-right: 5px;">`;
        for (const [category, fields] of Object.entries(missingFieldsData)) {
            listHtml += `
                <div style="margin-bottom: 12px;">
                    <h5 style="margin: 0 0 5px 0; color: #444; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #eee; padding-bottom: 2px;">
                        ${category}
                    </h5>
                    <ul style="margin: 0; padding-left: 20px; color: #333;">
                        ${fields.map(f => `<li style="margin-bottom: 3px;">${f}</li>`).join('')}
                    </ul>
                </div>`;
        }
        listHtml += `</div>`;
    }

    const modalHtml = `
        <div id="validationModal" class="modal" style="display: flex; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
            <div class="modal-content" style="background: #fff; padding: 25px; border-radius: 12px; width: 90%; max-width: 450px; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: fadeIn 0.2s ease-out;">
                <span class="close-validation" style="position: absolute; right: 15px; top: 10px; font-size: 24px; cursor: pointer; color: #666;">&times;</span>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; color: ${color};">
                    <span class="material-icons" style="font-size: 28px;">${icon}</span>
                    <h3 style="margin: 0; font-size: 1.25rem;">${customTitle}</h3>
                </div>
                <p style="margin-bottom: 15px; color: #666; font-size: 0.9rem;">Please address the following items:</p>
                
                ${listHtml}

                <div style="text-align: right; margin-top: 15px;">
                    <button id="btn-close-val" style="background: #333; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">${btnText}</button>
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

// --- REAL-TIME DUPLICATE CHECKER ---
async function checkDuplicate(form) {
    const fname = form.querySelector('[name="first_name"]').value.trim();
    const lname = form.querySelector('[name="last_name"]').value.trim();
    const dob = form.querySelector('[name="dob"]').value;
    const residentId = form.querySelector('[name="resident_id"]').value;

    if (!fname || !lname || !dob) return false;

    const formData = new FormData();
    formData.append('action', 'check_duplicate');
    formData.append('first_name', fname);
    formData.append('last_name', lname);
    formData.append('dob', dob);
    formData.append('resident_id', residentId);

    try {
        const response = await fetch(`${basePath}/residents/process`, { method: 'POST', body: formData });
        const result = await response.json();
        
        if (result.status === 'found') {
            const r = result.resident;
            showValidationModal(
                [`This resident is already in the system (Status: ${r.approval_status}).`], 
                "Possible Duplicate Found", 
                true
            );
            return true; // Duplicate found
        }
    } catch (e) {
        console.error("Duplicate check failed", e);
    }
    return false; // No duplicate
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

// --- INPUT FORMATTING HELPERS ---
function capitalizeWords(str) {
    if(!str) return '';
    return str.replace(/\b\w/g, char => char.toUpperCase());
}

function setupInputFormatters(form) {
    // 1. Consolidated Capitalization & Duplicate Triggers
    // We combine these so "Blur" happens once, assuring order of operations
    const textFields = ['first_name', 'middle_name', 'last_name', 'street', 'barangay', 'occupation', 'head_of_household', 'relationship'];
    
    textFields.forEach(fieldName => {
        const input = form.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.addEventListener('blur', function() {
                // A. Capitalize immediately
                this.value = capitalizeWords(this.value);

                // B. If it's a Name field, Trigger Duplicate Check
                if (['first_name', 'last_name'].includes(fieldName)) {
                    checkDuplicate(form);
                }
            });
        }
    });

    // 2. Phone Number Validation
    const phoneInput = form.querySelector('[name="contact_number"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 11) this.value = this.value.slice(0, 11);
        });
        
        phoneInput.addEventListener('blur', function() {
            if (this.value.length > 0 && !/^09\d{9}$/.test(this.value)) {
                showValidationModal(['Contact number must be 11 digits and start with 09.'], "Invalid Format");
                this.style.borderColor = '#d32f2f';
            }
        });
    }

    // 3. Age Logic & Civil Status
    const dobInput = form.querySelector('[name="dob"]');
    const civilStatusSelect = form.querySelector('[name="civil_status"]');
    
    if (dobInput) {
        dobInput.addEventListener('change', function() {
            const dob = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            if (age < 0) {
                showValidationModal(['Date of Birth cannot be in the future.'], "Invalid Date");
                this.value = '';
                return;
            }

            if (age < 18 && civilStatusSelect) {
                const status = civilStatusSelect.value;
                if (['Married', 'Widowed', 'Separated'].includes(status)) {
                    showValidationModal([`Resident is ${age} years old. Are you sure they are ${status}?`], "Logic Warning", true);
                }
            }
            
            checkDuplicate(form);
        });
    }
}

function initializeForm(state) {
    const form = document.getElementById('residentForm');
    if (!form) return; // Guard clause

    const deleteBtn = form.querySelector('.deleteBtn');

    setupInputFormatters(form);

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const requiredInputs = form.querySelectorAll('[required]');
        
        // Use Object to group missing fields by Category
        let missingFieldsByTab = {}; 
        let firstInvalid = null;

        requiredInputs.forEach(input => {
            input.style.borderColor = ''; 
            input.style.backgroundColor = '';

            if (!input.value.trim()) {
                input.style.borderColor = '#d32f2f';
                input.style.backgroundColor = '#fff8f8';
                
                // 1. Identify Field Name
                let fieldName = input.getAttribute('name');
                const label = input.parentElement.querySelector('label'); 
                if (label) {
                    fieldName = label.textContent.replace(/[*:]/g, '').trim();
                } else {
                    fieldName = fieldName.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                }

                // 2. Identify Category (Tab Name)
                const tabContent = input.closest('.tab-content');
                let tabName = "Other Information"; // Default
                if (tabContent) {
                    const header = tabContent.querySelector('h4');
                    if (header) {
                        tabName = header.textContent.trim();
                    } else {
                        // Fallback mapping
                        const id = tabContent.id;
                        if (id === 'tab-personal') tabName = "Personal Details";
                        else if (id === 'tab-household') tabName = "Address & Household";
                        else if (id === 'tab-contact') tabName = "Contact Info";
                    }
                }
                
                // 3. Add to grouping
                if (!missingFieldsByTab[tabName]) {
                    missingFieldsByTab[tabName] = [];
                }
                missingFieldsByTab[tabName].push(fieldName);

                if (!firstInvalid) firstInvalid = input;
                
                input.addEventListener('input', function() {
                    this.style.borderColor = '';
                    this.style.backgroundColor = '';
                }, { once: true });
            }
        });

        // If errors exist, show Categorized Modal
        if (Object.keys(missingFieldsByTab).length > 0) {
            if (firstInvalid) {
                const tabContent = firstInvalid.closest('.tab-content');
                if (tabContent && !tabContent.classList.contains('active')) {
                    const tabId = tabContent.id.replace('tab-', '');
                    const tabBtn = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
                    if (tabBtn) tabBtn.click();
                }
                setTimeout(() => firstInvalid.focus(), 100);
            }
            showValidationModal(missingFieldsByTab);
            return;
        }

        // Final Duplicate Check
        const isDuplicate = await checkDuplicate(this);
        if (isDuplicate) {
            if(!confirm("A similar resident record exists. Do you really want to save this as a NEW record?")) {
                return;
            }
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