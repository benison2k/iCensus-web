// public/assets/js/sidebar.js

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const closeBtn = document.getElementById('closeSidebarBtn');
    const body = document.body;

    // Helper to check if pinned
    const isPinned = () => body.classList.contains('sidebar-pinned');

    function openSidebar() {
        if (isPinned()) return; // Do nothing if pinned
        
        if (sidebar) sidebar.classList.add('active');
        if (overlay) overlay.classList.add('active');
        body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeSidebar() {
        if (isPinned()) return; // Do nothing if pinned

        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        body.style.overflow = ''; // Restore scrolling
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            // Check if sidebar is currently active to determine action
            if (sidebar && sidebar.classList.contains('active')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && overlay && overlay.classList.contains('active')) {
            closeSidebar();
        }
    });
});