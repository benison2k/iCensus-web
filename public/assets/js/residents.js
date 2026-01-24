// public/assets/js/residents.js

import { initializeModal, openModalForAdd, openModalForEdit } from './resident/modalManager.js';
import { initializeForm } from './resident/formHandler.js';
import { initializeTable, renderTable } from './resident/tableManager.js';
import { initializeFilters, applyFilters } from './resident/filterManager.js';


document.addEventListener('DOMContentLoaded', () => {
    // --- STATE INITIALIZATION ---
    let state = {
        currentPage: 1,
        pageSize: 10,
        filteredResidents: [],
        currentSort: {
            column: 'last_name',
            order: 'asc'
        },
        allResidents: typeof allResidentsData !== 'undefined' ? allResidentsData : [],
        isPendingView: typeof isPendingView !== 'undefined' ? isPendingView : false,
        userRole: typeof userRole !== 'undefined' ? userRole : ''
    };

    // --- INITIALIZATION ---
    initializeModal(state);
    initializeForm(state);
    initializeTable(state);
    initializeFilters(state);

    // --- INITIAL RENDER/DATA LOAD ---
    if (!state.isPendingView) {
        if (state.allResidents.length >= 0) {
            state.filteredResidents = state.allResidents;
            applyFilters(state);
        } else {
            const tableBody = document.getElementById('residentsTableBody');
            if(tableBody) tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No residents found in this view.</td></tr>';
        }
    }

    // --- EVENT LISTENERS (External) ---
    const addBtn = document.getElementById('addResidentBtn');
    if (addBtn) {
        addBtn.addEventListener('click', () => openModalForAdd(state));
    }
});