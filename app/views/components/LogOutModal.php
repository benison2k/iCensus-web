<?php
// Asset base URL not needed since we hardcode /public
?>

<link rel="stylesheet" href="/public/assets/css/LogOutModal.css">
<script src="/public/assets/js/LogOutModal.js" defer></script>

<div id="logoutModal" class="modal">
  <div class="modal-content">
    <h2>Confirm Logout</h2>
    <p>Are you sure you want to log out?</p>
    <div class="modal-actions">
      <button id="confirmLogout" class="btn confirm">Yes, Logout</button>
      <button id="cancelLogout" class="btn cancel">Cancel</button>
    </div>
  </div>
</div>