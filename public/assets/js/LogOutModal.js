document.addEventListener("DOMContentLoaded", () => {
  const logoutBtn = document.getElementById("logoutBtn");
  const logoutModal = document.getElementById("logoutModal");
  const confirmLogout = document.getElementById("confirmLogout"); 
  const cancelLogout = document.getElementById("cancelLogout");

  // Get the final confirmation button element
  const confirmBtnFinal = confirmLogout || document.getElementById("confirmLogoutBtn");

  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault();
      if(logoutModal) logoutModal.style.display = "flex";
    });
  }

  if (cancelLogout) {
    cancelLogout.addEventListener("click", () => {
      if(logoutModal) logoutModal.style.display = "none";
    });
  }

  if (confirmBtnFinal) {
    confirmBtnFinal.addEventListener("click", (e) => {
      e.preventDefault();
      
      // FIX: Use the global appBasePath variable defined in header.php
      const basePath = (typeof appBasePath !== 'undefined') ? appBasePath : '';
      
      // Redirect to the correct logout route (/logout)
      window.location.href = basePath + "/logout";
    });
  }

  window.addEventListener("click", (e) => {
    if (e.target === logoutModal) {
      logoutModal.style.display = "none";
    }
  });
});