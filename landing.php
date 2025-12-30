<?php 
// Configuration for Base URL and Versioning
// Set this to your subfolder path if deployed in a subdirectory (e.g., '/icensus')
$baseUrl = ''; 

// Use time() for development to bust cache. 
// For production, change this to a static string like '1.0.0'
$ver = time(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to iCensus</title>
    <meta name="description" content="iCensus: A secure digital census management system for Barangays. Streamline resident profiling, analytics, and community reporting.">

    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="og:title" content="iCensus - Digital Barangay Management">
    <meta property="og:description" content="Streamline resident profiling, generate instant reports, and build a better-informed community.">
    <meta property="og:image" content="<?= $baseUrl ?>/public/assets/img/dashboard.png">

    <link rel="icon" type="image/png" href="<?= $baseUrl ?>/public/assets/img/iCensusLogoOnly2.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $baseUrl ?>/public/assets/img/iCensusLogoOnly2.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="<?= $baseUrl ?>/public/assets/css/landing/layout.css?v=<?= $ver ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>/public/assets/css/landing/components.css?v=<?= $ver ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>/public/assets/css/landing/sections.css?v=<?= $ver ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>/public/assets/css/landing/graphics.css?v=<?= $ver ?>">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body id="home">

    <canvas id="particleCanvas"></canvas>

    <header class="header" id="header">
        <div class="container header-container">
            <img src="<?= $baseUrl ?>/public/assets/img/iCensusLogo.png" alt="iCensus Logo" class="logo">
            <nav class="nav-menu">
                <a href="#home" class="nav-link active">Home</a>
                <a href="#features" class="nav-link">Features</a>
                <a href="#how-it-works" class="nav-link">How It Works</a>
                <a href="#security" class="nav-link">Security</a>
                <a href="#faq" class="nav-link">FAQ</a>
                <a href="<?= $baseUrl ?>/login" class="btn-login">
                    <span class="material-icons" style="font-size: 18px;">login</span> Member Login
                </a>
            </nav>
            <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Navigation">
                <span class="material-icons">menu</span>
            </button>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-grid">
                    <div class="hero-text-content">
                        <h1 class="hero-title">Empowering Your Barangay with Digital Census Management</h1>
                        <p class="hero-subtitle">
                            Welcome to iCensus. Streamline resident profiling, generate instant reports, and build a better-informed community with our secure digital platform.
                        </p>
                        <div class="hero-buttons">
                            <a href="<?= $baseUrl ?>/login" class="btn-cta">Access the Portal</a>
                            <a href="#features" class="btn-cta btn-secondary">Learn More</a>
                        </div>
                    </div>
                    <div class="hero-visual-content">
                        <div class="carousel-wrapper">
                            <div class="carousel-container">
                                <div class="carousel-slides">
                                    <div class="carousel-slide" data-caption="Dashboard Overview">
                                        <img src="<?= $baseUrl ?>/public/assets/img/dashboard.png" alt="Dashboard View">
                                    </div>
                                    <div class="carousel-slide" data-caption="Residents Management">
                                        <img src="<?= $baseUrl ?>/public/assets/img/residents.png" alt="Residents Management View">
                                    </div>
                                    <div class="carousel-slide" data-caption="Data Analytics & Reports">
                                        <img src="<?= $baseUrl ?>/public/assets/img/analytics.png" alt="Analytics View">
                                    </div>
                                </div>
                                <button class="carousel-btn prev" title="Previous">&#10094;</button>
                                <button class="carousel-btn next" title="Next">&#10095;</button>
                                <div class="carousel-dots"></div>
                            </div>
                            <div class="carousel-caption-external"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mission-section fade-in-section">
            <div class="container">
                <div class="mission-content">
                    <h2 class="section-title">Our Mission</h2>
                    <p class="mission-text">
                        To empower local government units with technology that fosters better-informed, responsive, and well-organized communities. By digitizing the census process, we aim to provide barangay officials with the accurate data needed for effective resource allocation and public service.
                    </p>
                </div>
            </div>
        </section>

        <section class="impact-section fade-in-section">
            <div class="container">
                <div class="impact-grid">
                    <div class="impact-item">
                        <span class="material-icons impact-icon">eco</span>
                        <span class="impact-label">100% Paperless</span>
                        <p>Reduce waste and save physical storage space.</p>
                    </div>
                    <div class="impact-item">
                        <span class="material-icons impact-icon">bolt</span>
                        <span class="impact-label">Instant Retrieval</span>
                        <p>Search resident records in milliseconds.</p>
                    </div>
                    <div class="impact-item">
                        <span class="material-icons impact-icon">cloud_done</span>
                        <span class="impact-label">Always On</span>
                        <p>Access data securely from anywhere, anytime.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="features fade-in-section">
            <div class="container">
                <h2 class="section-title">Everything You Need in One Platform</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                             <div class="css-icon icon-database">
                                <div class="db-layer top"></div>
                                <div class="db-layer mid"></div>
                                <div class="db-layer bot"></div>
                            </div>
                        </div>
                        <h3 class="feature-title">Centralized Resident Data</h3>
                        <p class="feature-description">Securely manage, view, and update all resident information in one organized and accessible database.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <div class="css-icon icon-analytics-pie">
                                <div class="pie-chart"></div>
                                <div class="pie-slice"></div>
                            </div>
                        </div>
                        <h3 class="feature-title">Insightful Analytics</h3>
                        <p class="feature-description">Generate real-time demographic reports and statistics with a powerful analytics dashboard.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <div class="css-icon icon-shield">
                                <div class="shield-body">
                                    <div class="shield-inner"></div>
                                </div>
                            </div>
                        </div>
                        <h3 class="feature-title">Role-Based Access</h3>
                        <p class="feature-description">Ensure data security with distinct permission levels for Admins and Encoders.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="how-it-works fade-in-section">
            <div class="container">
                <h2 class="section-title">A Simple, Streamlined Process</h2>
                <div class="process-wrapper">
                    <div class="process-step">
                        <div class="step-icon">
                            <div class="css-icon icon-lock">
                                <div class="lock-shackle"></div>
                                <div class="lock-body">
                                    <div class="keyhole"></div>
                                </div>
                            </div>
                        </div>
                        <h3 class="step-title">1. Secure Login</h3>
                        <p>Access the system using your officially provided credentials with role-based permissions.</p>
                    </div>
                    <div class="step-arrow">&rarr;</div>
                    <div class="process-step">
                        <div class="step-icon">
                            <div class="css-icon icon-files">
                                <div class="file-doc">
                                    <div class="file-lines"></div>
                                </div>
                                <div class="file-pencil"></div>
                            </div>
                        </div>
                        <h3 class="step-title">2. Manage Data</h3>
                        <p>Easily add new residents, update existing information, and search the entire database in seconds.</p>
                    </div>
                    <div class="step-arrow">&rarr;</div>
                    <div class="process-step">
                        <div class="step-icon">
                             <div class="css-icon icon-report">
                                <div class="report-doc">
                                    <div class="report-graph">
                                        <div class="r-bar b1"></div>
                                        <div class="r-bar b2"></div>
                                        <div class="r-bar b3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 class="step-title">3. Generate Insights</h3>
                        <p>Instantly create official reports and visualize demographic data through the analytics dashboard.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="role-section fade-in-section">
            <div class="container">
                 <h2 class="section-title">Designed For Your Role</h2>
                 
                 <div class="role-row">
                     <div class="role-text">
                         <h3>For Barangay Admins</h3>
                         <p>Oversee all census operations with a comprehensive dashboard. Manage user accounts for encoders, view system-wide analytics for better community planning, and ensure the integrity and security of all resident data.</p>
                     </div>
                     <div class="role-img-container">
                        <div class="css-graphic admin-graphic">
                            <div class="admin-screen">
                                <div class="screen-header"></div>
                                <div class="screen-body">
                                    <div class="screen-sidebar"></div>
                                    <div class="screen-content">
                                        <div class="graph-bar bar-1"></div>
                                        <div class="graph-bar bar-2"></div>
                                        <div class="graph-bar bar-3"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="admin-base"></div>
                        </div>
                     </div>
                 </div>

                 <div class="role-row reverse">
                      <div class="role-text">
                         <h3>For Data Encoders</h3>
                         <p>Focus on what you do best: accurate and efficient data entry. With a clean, straightforward interface, you can add and update resident profiles quickly, minimizing errors and maximizing productivity.</p>
                     </div>
                     <div class="role-img-container">
                        <div class="css-graphic encoder-graphic">
                            <div class="form-paper">
                                <div class="form-header"></div>
                                <div class="form-line full"></div>
                                <div class="form-line full"></div>
                                <div class="form-line short"></div>
                                <div class="selection-box"></div>
                            </div>
                            <div class="floating-pen"></div>
                        </div>
                     </div>
                 </div>
            </div>
        </section>

        <section id="security" class="security-section fade-in-section">
            <div class="container">
                <div class="security-grid">
                    <div class="security-content">
                        <h2 class="section-title" style="text-align: left; margin-bottom: 1.5rem;">Uncompromised Data Security</h2>
                        <p class="security-text">
                            We understand the importance of data privacy in census management. iCensus incorporates advanced security measures to protect your barangay's information.
                        </p>
                        <ul class="security-list">
                            <li>
                                <span class="material-icons sec-icon">security</span>
                                <div class="sec-detail">
                                    <strong>Two-Factor Authentication (2FA)</strong>
                                    <span>Add an extra layer of protection to user accounts to prevent unauthorized access.</span>
                                </div>
                            </li>
                            <li>
                                <span class="material-icons sec-icon">history</span>
                                <div class="sec-detail">
                                    <strong>Comprehensive Audit Logs</strong>
                                    <span>Every action is recorded. Admins can track data changes and user activities transparently.</span>
                                </div>
                            </li>
                            <li>
                                <span class="material-icons sec-icon">storage</span>
                                <div class="sec-detail">
                                    <strong>Automated Database Tools</strong>
                                    <span>Built-in tools for data integrity, ensuring records are safe and recoverable.</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="security-visual">
                         <div class="css-graphic security-graphic">
                            <div class="vault-body">
                                <div class="vault-dial">
                                    <div class="dial-center"></div>
                                    <div class="dial-handle"></div>
                                </div>
                                <div class="vault-led"></div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="faq" class="faq-section fade-in-section">
            <div class="container">
                <h2 class="section-title">Frequently Asked Questions</h2>
                <div class="faq-grid">
                    <details class="faq-item">
                        <summary>
                            <span class="question-text">Is the resident data secure?</span>
                            <span class="material-icons toggle-icon">expand_more</span>
                        </summary>
                        <div class="answer">
                            <p>Yes. The system uses encrypted passwords and strictly enforces role-based access control. Only authorized Encoders and Admins can access specific data sets.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="question-text">Can I access this on mobile?</span>
                            <span class="material-icons toggle-icon">expand_more</span>
                        </summary>
                        <div class="answer">
                            <p>Absolutely. iCensus is built with a responsive design that adapts to desktops, tablets, and mobile phones for data entry on the go.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="question-text">Who do I contact for support?</span>
                            <span class="material-icons toggle-icon">expand_more</span>
                        </summary>
                        <div class="answer">
                            <p>Please contact your System Administrator for account resets or technical issues. Once logged in, you can use the support form to reach the admin directly.</p>
                        </div>
                    </details>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="container">
                <h2>Ready to Get Started?</h2>
                <p>Access the secure portal to begin managing your community's census data.</p>
                <a href="<?= $baseUrl ?>/login" class="btn-cta">Access the Portal</a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-brand">
                <img src="<?= $baseUrl ?>/public/assets/img/iCensusLogo.png" alt="iCensus" class="footer-logo" style="width: auto; height: 50px; border-radius: 0;">
                <p>Empowering barangays with digital census solutions.</p>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <a href="#header">Home</a>
                <a href="#features">Features</a>
                <a href="#security">Security</a>
                <a href="#faq">FAQ</a>
                <a href="<?= $baseUrl ?>/login">Login</a>
            </div>
            <div class="footer-contact">
                 <h4>Contact</h4>
                 <p>Support is available for registered barangay officials.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date("Y") ?> iCensus System. All Rights Reserved.</p>
        </div>
    </footer>

    <button id="backToTop" class="back-to-top" aria-label="Back to Top">
        <span class="material-icons">arrow_upward</span>
    </button>

    <div id="lightboxModal" class="image-modal">
        <span class="close-modal">&times;</span>
        <img class="modal-content" id="expandedImage" alt="Expanded View">
        <div id="modalCaption"></div>
    </div>

    <script src="<?= $baseUrl ?>/public/assets/js/landing.js?v=<?= $ver ?>" defer></script>
</body>
</html>