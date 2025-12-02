<?php
// Use the global BASE_URL constant if defined, otherwise empty string
$base_url = defined('BASE_URL') ? BASE_URL : '';
?>

<link rel="stylesheet" href="<?= $base_url ?>/public/assets/css/footer.css">

<footer class="footer">
    <p>&copy; <?= date("Y") ?> iCensus System. All rights reserved.</p>
</footer>

<button id="backToTopBtn" title="Go to top">
    <span class="material-icons">arrow_upward</span>
</button>

<script src="<?= $base_url ?>/public/assets/js/global.js"></script>

<script src="<?= $base_url ?>/public/assets/js/LogOutModal.js?v=<?= time() ?>"></script>