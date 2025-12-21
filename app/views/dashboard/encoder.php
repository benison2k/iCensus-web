<?php
// app/views/dashboard/encoder.php

// Define the base URL. If your site is at the root, keep empty.
$base_url = ''; 

// NOTE: The greeting_name is now passed from the controller, cleaning up the view logic.
$greetingName = $greeting_name ?? 'Encoder'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>iCensus - Encoder Dashboard</title>
<link rel="icon" type="image/png" href="<?= $base_url ?>/public/assets/img/iCensusLogoOnly2.png">
<link rel="stylesheet" href="<?= $base_url ?>/public/assets/css/style.css">
<link rel="stylesheet" href="<?= $base_url ?>/public/assets/css/dashboard_common.css">
<link rel="stylesheet" href="<?= $base_url ?>/public/assets/css/dashboard_encoder.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode'; ?>">

<?php include __DIR__ . '/../components/header.php'; ?>

<main class="dashboard encoder-dashboard">
    
    <div class="dashboard-hero">
        <div class="hero-content">
            <h1>Hello, <?= htmlspecialchars($greetingName); ?>!</h1>
            <p>Have a productive day recording census data.</p>
        </div>
        <div class="hero-date">
            <span class="material-icons">calendar_today</span>
            <span><?= date('l, F j, Y') ?></span>
        </div>
    </div>

    <div class="stats-grid">
        
        <div class="card stat-card">
            <div class="stat-icon-wrapper bg-blue">
                <span class="material-icons">today</span>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= $stats['today'] ?? 0 ?></h3>
                <p class="stat-label">Entries Today</p>
            </div>
        </div>

        <a href="<?= $base_url ?>/residents?filter_status=pending&filter_encoder=<?= $user['id'] ?>" class="card stat-card clickable-card">
            <div class="stat-icon-wrapper bg-orange">
                <span class="material-icons">hourglass_top</span>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= $stats['pending'] ?? 0 ?></h3>
                <p class="stat-label">Pending Approval</p>
            </div>
        </a>

        <div class="card stat-card">
            <div class="stat-icon-wrapper bg-green">
                <span class="material-icons">check_circle</span>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= $stats['approved'] ?? 0 ?></h3>
                <p class="stat-label">Total Approved</p>
            </div>
        </div>

    </div>
    
    <div class="content-split">
        
        <div class="card recent-activity-card">
            <div class="card-header-flex">
                <h3>Recent Submissions</h3>
                <span class="material-icons" style="opacity:0.5;">history</span>
            </div>
            
            <div class="activity-list">
                <?php if (empty($recent_activity)): ?>
                    <div class="empty-state">
                        <p>No recent activity recorded.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_activity as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon <?= $activity['approval_status'] == 'approved' ? 'success' : 'pending' ?>">
                                <span class="material-icons">person</span>
                            </div>
                            <div class="activity-info">
                                <span class="activity-name">
                                    <?= htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']) ?>
                                </span>
                                <span class="activity-time">
                                    <?= time_elapsed_string($activity['created_at']) ?>
                                </span>
                            </div>
                            <div class="activity-status">
                                <?php if($activity['approval_status'] == 'approved'): ?>
                                    <span class="status-badge success">Approved</span>
                                <?php else: ?>
                                    <span class="status-badge pending">Pending</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="actions-column">
            <a href="<?= $base_url ?>/residents" class="card action-card clickable-card">
                <div class="action-icon-box">
                    <span class="material-icons">person_add</span>
                </div>
                <div class="action-details">
                    <h3 class="action-title">Manage Residents</h3>
                    <p class="action-desc">Add, search, and update records.</p>
                </div>
                <div class="action-arrow">
                    <span class="material-icons">arrow_forward</span>
                </div>
            </a>
            
            <div class="card info-card tip-carousel">
                <div class="tip-header">
                    <span class="material-icons info-icon">lightbulb</span>
                    <span class="tip-title">Encoder Tips</span>
                </div>
                
                <div class="tip-content-wrapper" id="tipCarousel">
                    <div class="tip-slide active">
                        <p>Ensure all <strong>required fields</strong> are filled before submitting to speed up approval.</p>
                    </div>
                    <div class="tip-slide">
                        <p>Double-check the <strong>spelling of names</strong> to prevent creating duplicate records.</p>
                    </div>
                    <div class="tip-slide">
                        <p>Use the <strong>search filter</strong> to confirm a resident doesn't already exist before adding.</p>
                    </div>
                    <div class="tip-slide">
                        <p>Verify <strong>birth dates</strong> carefully to ensure accurate age demographics.</p>
                    </div>
                    <div class="tip-slide">
                        <p>Review your <strong>Pending</strong> tab regularly to check for any returned entries.</p>
                    </div>
                </div>

                <div class="tip-indicators">
                    <span class="dot active" onclick="setTip(0)"></span>
                    <span class="dot" onclick="setTip(1)"></span>
                    <span class="dot" onclick="setTip(2)"></span>
                    <span class="dot" onclick="setTip(3)"></span>
                    <span class="dot" onclick="setTip(4)"></span>
                </div>
            </div>
        </div>

    </div>

</main>

<?php include __DIR__ . '/../components/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentTip = 0;
        const slides = document.querySelectorAll('.tip-slide');
        const dots = document.querySelectorAll('.tip-indicators .dot');
        const totalTips = slides.length;
        let tipInterval;

        function showTip(index) {
            // Remove active class from all
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            // Add active class to current
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentTip = index;
        }

        function nextTip() {
            let next = (currentTip + 1) % totalTips;
            showTip(next);
        }

        // Global function for dot clicks
        window.setTip = function(index) {
            clearInterval(tipInterval); // Pause auto-play on interaction
            showTip(index);
            startCarousel(); // Restart auto-play
        };

        function startCarousel() {
            tipInterval = setInterval(nextTip, 5000); // Change every 5 seconds
        }

        // Initialize if slides exist
        if(totalTips > 0) {
            startCarousel();
        }
    });
</script>

</body>
</html>