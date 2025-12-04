<?php
// app/views/components/header.php

// 1. LOAD CONFIGURATION
// Go up 3 levels to find config.php in the root (app/views/components -> root)
if (file_exists(__DIR__ . '/../../../config.php')) {
    include __DIR__ . '/../../../config.php';
} else {
    $base_url = ''; // Fallback if config is missing
}

// 2. Check if a session is active and get user role
$isUserLoggedIn = isset($_SESSION['user']);
$isAdmin = $isUserLoggedIn && ($_SESSION['user']['role_name'] === 'System Admin');
$isEncoder = $isUserLoggedIn && ($_SESSION['user']['role_name'] === 'Encoder');

// 3. Determine the correct dashboard link based on role
$dashboardLink = $isAdmin ? $base_url . '/sysadmin/dashboard' : ($isEncoder ? $base_url . '/encoder-dashboard' : $base_url . '/dashboard');

// 4. Get current page context to decide button visibility
$requestUri = $_SERVER['REQUEST_URI'];
$isDashboardPage = (strpos($requestUri, 'dashboard') !== false);

// 5. Determine "Parent URL" for the Back button
$parentUrl = $dashboardLink; 
if (strpos($requestUri, '/sysadmin/') !== false && !$isDashboardPage) {
    $parentUrl = $base_url . '/sysadmin/dashboard';
}

// 6. Check "Pinned Sidebar" preference
if ($isUserLoggedIn && !isset($_SESSION['user']['sidebar_pinned'])) {
    try {
        $config = require __DIR__ . '/../../../config/database.php';
        $db = new Database($config);
        $conn = $db->getPdo();
        
        $stmt = $conn->prepare("SELECT sidebar_pinned FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user']['id']]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $_SESSION['user']['sidebar_pinned'] = $res['sidebar_pinned'] ?? 0;
    } catch (Exception $e) {
        $_SESSION['user']['sidebar_pinned'] = 0;
    }
}

$isSidebarPinned = isset($_SESSION['user']['sidebar_pinned']) && $_SESSION['user']['sidebar_pinned'] == 1;
?>

<head>
    <link rel="icon" type="image/png" href="<?= $base_url ?>/public/assets/img/iCensusLogoOnly2.png">
    
    <link rel="stylesheet" href="<?= $base_url ?>/public/assets/css/header2.css">
    <link rel="stylesheet" href="<?= $base_url ?>/public/assets/css/sidebar.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script>
        const appBasePath = "<?= $base_url ?>";
    </script>

    <?php if ($isUserLoggedIn): ?>
    <script>
        const SESSION_TIMEOUT_MS = 1801 * 1000; 
        setTimeout(() => {
            window.location.reload(); 
        }, SESSION_TIMEOUT_MS);
    </script>
    <?php endif; ?>

    <script src="<?= $base_url ?>/public/assets/js/sidebar.js" defer></script>
</head>

<?php if ($isSidebarPinned): ?>
<script>
    document.body.classList.add('sidebar-pinned');
</script>
<?php endif; ?>

<header class="header">
    <?php if ($isUserLoggedIn && !$isDashboardPage): ?>
        <button id="sidebarToggleBtn" class="back-button" title="Open Menu">
            <span class="material-icons">menu</span>
        </button>

    <?php elseif (!$isDashboardPage): ?>
        <a href="<?= $parentUrl ?>" class="back-button" title="Go Back">
            <span class="material-icons">arrow_back</span>
        </a>

    <?php else: ?>
        <div class="header-slot"></div>
    <?php endif; ?>

    <div class="header-logo">
        <a href="<?= $dashboardLink ?>">
            <img src="<?= $base_url ?>/public/assets/img/iCensusLogoSmaller.png" alt="iCensus Logo" class="logo">
        </a>
    </div>

    <?php if ($isUserLoggedIn): ?>
        <button id="logoutBtn" class="logout-icon" title="Logout">
            <span class="material-icons">logout</span>
        </button>
    <?php else: ?>
        <div class="header-slot"></div>
    <?php endif; ?>
</header>

<?php 
if ($isUserLoggedIn && (!$isDashboardPage || $isSidebarPinned)) {
    include __DIR__ . "/sidebar.php"; 
}
include __DIR__ . "/LogOutModal.php"; 
?>