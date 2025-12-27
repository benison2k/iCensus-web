<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to iCensus</title>
    
    <link rel="icon" type="image/png" href="/public/assets/img/iCensusLogoOnly2.png">
    
    <link rel="stylesheet" href="/public/assets/css/landing-page.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* Lightbox Modal Styles */
        .image-modal {
            display: none; /* Hidden by default */
            position: fixed; 
            z-index: 2000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            overflow: hidden; 
            background-color: rgba(0,0,0,0.9); 
            backdrop-filter: blur(5px);
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .modal-content {
            margin: auto;
            display: block;
            width: auto;
            max-width: 90%;
            max-height: 85vh;
            border-radius: 4px;
            box-shadow: 0 0 25px rgba(0,0,0,0.5);
            animation: zoomIn 0.3s ease forwards;
            object-fit: contain;
        }

        #modalCaption {
            margin-top: 15px;
            color: #ccc;
            font-size: 1.1rem;
            font-weight: 500;
            text-align: center;
        }

        @keyframes zoomIn {
            from {transform: scale(0.9); opacity: 0;} 
            to {transform: scale(1); opacity: 1;}
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 30px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
            z-index: 2001;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .close-modal:hover,
        .close-modal:focus {
            color: #fff;
            background: rgba(255, 255, 255, 0.3);
            text-decoration: none;
            cursor: pointer;
        }

        /* Update Carousel Images to indicate clickability */
        .carousel-slide img {
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }
        
        .carousel-slide img:hover {
            transform: scale(1.02);
        }

        /* ====================================
           FAQ Animation Styles (Added)
        ==================================== */
        details.faq-item {
            overflow: hidden; /* Essential for height animation */
            transition: height 0.3s ease-out;
            height: auto;
            will-change: height;
        }

        /* Hide the answer initially (opacity) to fade it in */
        details.faq-item .answer {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        /* When open, fade in the answer */
        details.faq-item[open] .answer {
            opacity: 1;
        }
    </style>
</head>
<body>

    <canvas id="particleCanvas"></canvas>

    <header class="header" id="header">
        <div class="container header-container">
            <img src="/public/assets/img/iCensusLogo.png" alt="iCensus Logo" class="logo">
            <nav class="nav-menu">
                <a href="#features" class="nav-link">Features</a>
                <a href="#how-it-works" class="nav-link">How It Works</a>
                <a href="#security" class="nav-link">Security</a>
                <a href="#faq" class="nav-link">FAQ</a>
                <a href="/login" class="btn-login">
                    <span class="material-icons" style="font-size: 18px;">login</span> Member Login
                </a>
            </nav>
            <button class="mobile-toggle" id="mobileToggle">
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
                        <a href="/login" class="btn-cta">Access the Portal</a>
                    </div>
                    <div class="hero-visual-content">
                        <div class="carousel-wrapper">
                            <div class="carousel-container">
                                <div class="carousel-slides">
                                    <div class="carousel-slide" data-caption="Dashboard Overview">
                                        <img src="/public/assets/img/dashboard.png" alt="Dashboard View">
                                    </div>
                                    <div class="carousel-slide" data-caption="Residents Management">
                                        <img src="/public/assets/img/residents.png" alt="Residents Management View">
                                    </div>
                                    <div class="carousel-slide" data-caption="Data Analytics & Reports">
                                        <img src="/public/assets/img/analytics.png" alt="Analytics View">
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
                <a href="/login" class="btn-cta">Access the Portal</a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-brand">
                <img src="/public/assets/img/iCensusLogoOnly2.png" alt="iCensus" class="footer-logo">
                <p>Empowering barangays with digital census solutions.</p>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <a href="#header">Home</a>
                <a href="#features">Features</a>
                <a href="#security">Security</a>
                <a href="#faq">FAQ</a>
                <a href="/login">Login</a>
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

    <script>
        const header = document.getElementById('header');
        const backToTopBtn = document.getElementById('backToTop');

        window.addEventListener('scroll', () => {
            // Header Scroll Logic
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }

            // Back to Top Button Logic
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });

        // Click event to scroll smoothly to top
        if(backToTopBtn) {
            backToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

        // Mobile Menu Toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navMenu = document.querySelector('.nav-menu');
        
        if(mobileToggle){
            mobileToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
            });
        }

        // Particle animation script
        const canvas = document.getElementById('particleCanvas');
        const ctx = canvas.getContext('2d');
        let particlesArray = [];

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight; 
        }

        window.addEventListener('resize', () => {
            resizeCanvas();
            initParticles();
        });

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2.5 + 1;
                this.speedX = Math.random() * 0.8 - 0.4;
                this.speedY = Math.random() * 0.8 - 0.4;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
                if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
            }
            draw() {
                ctx.fillStyle = 'rgba(255, 255, 255, 0.4)';
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function initParticles() {
            particlesArray = [];
            let numberOfParticles = (canvas.width * canvas.height) / 9000;
            for (let i = 0; i < numberOfParticles; i++) {
                particlesArray.push(new Particle());
            }
        }

        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particlesArray.forEach(p => { p.update(); p.draw(); });
            requestAnimationFrame(animateParticles);
        }

        // Scroll-triggered animations
        const sections = document.querySelectorAll('.fade-in-section');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                }
            });
        }, { threshold: 0.1 });
        sections.forEach(section => observer.observe(section));

        // Carousel Logic
        const initCarousel = () => {
            const slidesContainer = document.querySelector('.carousel-slides');
            if (!slidesContainer) return;

            const slides = document.querySelectorAll('.carousel-slide');
            const prevBtn = document.querySelector('.carousel-btn.prev');
            const nextBtn = document.querySelector('.carousel-btn.next');
            const dotsContainer = document.querySelector('.carousel-dots');
            const captionEl = document.querySelector('.carousel-caption-external');
            let currentSlide = 0;
            let slideInterval;

            const showSlide = (n) => {
                currentSlide = (n + slides.length) % slides.length;
                slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
                
                document.querySelectorAll('.carousel-dot').forEach(dot => dot.classList.remove('active'));
                dotsContainer.children[currentSlide].classList.add('active');

                // Update external caption
                if (captionEl) {
                    captionEl.textContent = slides[currentSlide].dataset.caption;
                }
                
                clearInterval(slideInterval);
                slideInterval = setInterval(() => showSlide(currentSlide + 1), 5000);
            };

            slides.forEach((_, i) => {
                const dot = document.createElement('span');
                dot.classList.add('carousel-dot');
                dot.addEventListener('click', () => showSlide(i));
                dotsContainer.appendChild(dot);
            });

            prevBtn.addEventListener('click', () => showSlide(currentSlide - 1));
            nextBtn.addEventListener('click', () => showSlide(currentSlide + 1));
            
            showSlide(0);
        };

        // Initial setup
        resizeCanvas();
        initParticles();
        animateParticles();
        initCarousel();

        /* =================================
           Lightbox Logic
        ==================================== */
        const modal = document.getElementById("lightboxModal");
        const modalImg = document.getElementById("expandedImage");
        const captionText = document.getElementById("modalCaption");
        const closeBtn = document.querySelector(".close-modal");
        
        // Select all images inside carousel slides
        const carouselImages = document.querySelectorAll('.carousel-slide img');

        if (carouselImages.length > 0) {
            carouselImages.forEach(img => {
                img.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent clicking through to other elements
                    modal.style.display = "flex"; // Show modal using flex to center content
                    modalImg.src = this.src; // Set modal image source
                    
                    // Optional: Set caption from the parent slide's data attribute
                    const slideParent = this.closest('.carousel-slide');
                    if(slideParent && slideParent.dataset.caption) {
                        captionText.textContent = slideParent.dataset.caption;
                    } else {
                        captionText.textContent = "";
                    }
                });
            });
        }

        // Close Modal Function
        function closeModal() {
            modal.style.display = "none";
        }

        // Event Listeners for Closing
        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        // Close if user clicks anywhere outside the image (the background)
        if (modal) {
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });
        }
        
        // Close on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape" && modal.style.display === "flex") {
                closeModal();
            }
        });

        /* =================================
           FAQ Smooth Animation Logic (Added)
        ==================================== */
        document.querySelectorAll('details.faq-item').forEach((detail) => {
            detail.addEventListener('click', (e) => {
                // Only run logic if the click comes from the summary element
                if (e.target.closest('summary')) {
                    e.preventDefault(); // Stop default instant toggle behavior

                    const summary = detail.querySelector('summary');
                    const content = detail.querySelector('.answer');

                    // Check if closing or opening
                    if (detail.hasAttribute('open')) {
                        // --- CLOSING ---
                        // 1. Lock the current height
                        const startHeight = detail.offsetHeight;
                        detail.style.height = `${startHeight}px`;

                        // 2. Force reflow
                        void detail.offsetHeight;

                        // 3. Calculate target height (just the summary height)
                        // We use getComputedStyle to account for any padding on the details element itself
                        const style = window.getComputedStyle(detail);
                        const collapsedHeight = summary.offsetHeight + 
                                                parseFloat(style.paddingTop) + 
                                                parseFloat(style.paddingBottom);

                        // 4. Animate to collapsed height
                        detail.style.height = `${collapsedHeight}px`;

                        // 5. Remove attribute after animation ends
                        detail.addEventListener('transitionend', function onEnd() {
                            detail.removeAttribute('open');
                            detail.style.height = null; // Clean up inline style
                            detail.removeEventListener('transitionend', onEnd);
                        }, { once: true });

                    } else {
                        // --- OPENING ---
                        // 1. Set starting height explicitly
                        const startHeight = detail.offsetHeight;
                        detail.style.height = `${startHeight}px`;

                        // 2. Add 'open' attribute so content renders (hidden by overflow)
                        detail.setAttribute('open', '');
                        
                        // 3. Calculate target full height
                        const targetHeight = detail.scrollHeight;

                        // 4. Force reflow
                        void detail.offsetHeight;

                        // 5. Animate to full height
                        detail.style.height = `${targetHeight}px`;

                        // 6. Cleanup after animation
                        detail.addEventListener('transitionend', function onEnd() {
                            detail.style.height = null;
                            detail.removeEventListener('transitionend', onEnd);
                        }, { once: true });
                    }
                }
            });
        });
    </script>
</body>
</html>