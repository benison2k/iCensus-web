<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About iCensus</title>
    <?php 
        // Ensure config is loaded to get base_url
        if (file_exists(__DIR__ . '/../../../config.php')) {
            include __DIR__ . '/../../../config.php';
        } else {
            $base_url = ''; 
        }
    ?>
    <link rel="icon" type="image/png" href="<?= $base_url ?>/public/assets/img/iCensusLogoOnly2.png">
    <link rel="stylesheet" href="<?= $base_url ?>/public/assets/css/style.css">
    <link rel="stylesheet" href="<?= $base_url ?>/public/assets/css/about.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="<?= isset($theme) && $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

    <?php include __DIR__ . '/../components/header.php'; ?>

    <main class="dashboard">
        <div class="about-container">
            
            <section class="about-hero fade-in">
                <div class="hero-icon-bg">
                    <img src="<?= $base_url ?>/public/assets/img/iCensusLogoOnly.png" alt="Logo">
                </div>
                <h1>Empowering Communities with Data</h1>
                <p class="hero-subtitle">iCensus is a modern digital platform designed to streamline barangay governance through accurate resident profiling and real-time analytics.</p>
            </section>

            <section class="features-grid">
                <div class="feature-card fade-in delay-1">
                    <div class="icon-box blue">
                        <span class="material-icons">cloud_sync</span>
                    </div>
                    <h3>Centralized Data</h3>
                    <p>A unified secure database allows for instant retrieval, updating, and management of resident records, eliminating paper trails.</p>
                </div>

                <div class="feature-card fade-in delay-2">
                    <div class="icon-box green">
                        <span class="material-icons">insights</span>
                    </div>
                    <h3>Real-time Analytics</h3>
                    <p>Transform raw data into actionable insights. Visualize demographics, age groups, and population density instantly.</p>
                </div>

                <div class="feature-card fade-in delay-3">
                    <div class="icon-box orange">
                        <span class="material-icons">security</span>
                    </div>
                    <h3>Secure & Reliable</h3>
                    <p>Built with role-based access control (RBAC) and encrypted authentication to ensure sensitive data remains protected.</p>
                </div>
            </section>

            <section class="mission-section fade-in delay-4">
                <div class="mission-content">
                    <h2>Our Mission</h2>
                    <p>To empower local government units with technology that fosters better-informed, responsive, and well-organized communities. By digitizing the census process, we aim to provide barangay officials with the accurate data needed for effective resource allocation and public service.</p>
                </div>
            </section>

            <section class="developer-section fade-in delay-5">
                <div class="section-header">
                    <h2>The Developer</h2>
                    <p>iCensus is a passion project built entirely by one dedicated developer.</p>
                </div>
                <div class="developer-card-wrapper">
                    <div class="developer-card">
                        <div class="dev-avatar">
                            <span class="material-icons">code</span>
                        </div>
                        <div class="dev-info">
                            <h4>Lead Developer</h4>
                            <span class="role">Full Stack Engineer</span>
                            <hr class="dev-divider">
                            <p>Responsible for the entire system architecture, from database design to the user interface, ensuring a seamless experience for all users.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="faq-section fade-in delay-5">
                <div class="section-header">
                    <h2>Frequently Asked Questions</h2>
                </div>
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
                            <span class="question-text">How do I generate a report?</span>
                            <span class="material-icons toggle-icon">expand_more</span>
                        </summary>
                        <div class="answer">
                            <p>Go to the <strong>Analytics</strong> tab in the dashboard. Click on "Generate Report," select your filters, and download the PDF.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>
                            <span class="question-text">Who do I contact for support?</span>
                            <span class="material-icons toggle-icon">expand_more</span>
                        </summary>
                        <div class="answer">
                            <p>Please contact your System Administrator for account resets or technical issues.</p>
                        </div>
                    </details>
                </div>
            </section>

            <section class="tech-section fade-in delay-5">
                <div class="tech-header">
                    <h3>Built for Performance</h3>
                    <p>Developed using modern, reliable web technologies.</p>
                </div>
                <div class="tech-badges">
                    <span class="badge">PHP 8+</span>
                    <span class="badge">MySQL</span>
                    <span class="badge">JavaScript (ES6)</span>
                    <span class="badge">Google Charts</span>
                    <span class="badge">PHPMailer</span>
                </div>
            </section>

            <section class="contact-cta fade-in delay-5">
                <h2>Have more questions?</h2>
                <p>Support is ready to help you optimize your census management.</p>
                <button class="btn-contact" id="openContactModal">Contact Support</button>
            </section>

        </div>
    </main>

    <div id="contactModal" class="modal-overlay">
        <div class="modal-card">
            <button class="close-modal">&times;</button>
            
            <div class="modal-header-bg">
                <div class="header-icon">
                    <span class="material-icons">support_agent</span>
                </div>
                <h3>Contact Support</h3>
                <p>Need help? Send a message directly to the System Admin.</p>
            </div>
            
            <div class="modal-body">
                <form id="contactForm">
                    <?= Csrf::getField(); ?>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="modern-input" placeholder="Brief summary of issue" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="4" class="modern-input" placeholder="Describe your issue in detail..." required></textarea>
                    </div>

                    <div id="contactMsg" class="status-msg"></div>

                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
                        <button type="submit" class="btn-send">
                            <span class="material-icons">send</span> Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Scroll Animation Observer
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

            // --- Enhanced Contact Modal Logic ---
            const modal = document.getElementById('contactModal');
            const openBtn = document.getElementById('openContactModal');
            const closeBtns = modal.querySelectorAll('.close-modal, .btn-cancel');
            const form = document.getElementById('contactForm');
            const msgBox = document.getElementById('contactMsg');
            const submitBtn = form.querySelector('button[type="submit"]');

            // Open
            openBtn.addEventListener('click', () => { 
                modal.classList.add('active'); // Use CSS class for fade-in
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
                msgBox.style.display = 'none';
                msgBox.className = 'status-msg'; // Reset status classes
                form.reset();
            });
            
            // Close
            const closeModal = () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            };
            
            closeBtns.forEach(btn => btn.addEventListener('click', closeModal));
            
            // Close on backdrop click
            modal.addEventListener('click', (e) => { 
                if (e.target === modal) closeModal(); 
            });

            // AJAX Submission
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Loading State
                const originalBtnContent = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="material-icons spin">refresh</span> Sending...';
                msgBox.style.display = 'none';
                msgBox.className = 'status-msg';

                try {
                    const formData = new FormData(form);
                    const response = await fetch('<?= $base_url ?>/contact/submit', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();

                    msgBox.textContent = result.message;
                    msgBox.style.display = 'block';
                    
                    if (result.status === 'success') {
                        msgBox.classList.add('success');
                        form.reset();
                        setTimeout(() => {
                            closeModal();
                            msgBox.style.display = 'none';
                        }, 2000);
                    } else {
                        msgBox.classList.add('error');
                    }
                } catch (error) {
                    msgBox.style.display = 'block';
                    msgBox.textContent = 'Network error. Please try again.';
                    msgBox.classList.add('error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnContent;
                }
            });
        });
    </script>
</body>
</html>