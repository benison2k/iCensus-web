// public/assets/js/residents.js

// FIX 1: Import all necessary modules for the resident management page.
import { initializeModal, openModalForAdd, openModalForEdit } from './resident/modalManager.js';
import { initializeForm } from './resident/formHandler.js';
import { initializeTable, renderTable } from './resident/tableManager.js';
import { initializeFilters, applyFilters } from './resident/filterManager.js';


document.addEventListener('DOMContentLoaded', () => {
    // --- STATE INITIALIZATION ---
    // Safely load globals passed from the PHP view (index.php)
    let state = {
        currentPage: 1,
        pageSize: 10,
        filteredResidents: [],
        currentSort: {
            column: 'last_name',
            order: 'asc'
        },
        // All data is assumed to be loaded into the global JS variables by PHP
        allResidents: typeof allResidentsData !== 'undefined' ? allResidentsData : [],
        isPendingView: typeof isPendingView !== 'undefined' ? isPendingView : false,
        userRole: typeof userRole !== 'undefined' ? userRole : ''
    };

    // --- INITIALIZATION ---
    // These functions set up event listeners and UI elements
    initializeModal(state);
    initializeForm(state);
    initializeTable(state);
    initializeFilters(state);

    // --- INITIAL RENDER/DATA LOAD ---
    // If it's the approved view, initialize the data loading flow.
    if (!state.isPendingView) {
        if (state.allResidents.length >= 0) {
            state.filteredResidents = state.allResidents;
            // This call starts the filtering and rendering process
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