/* ====================================
   Landing Page Logic
==================================== */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- Header & Back to Top Logic ---
    const header = document.getElementById('header');
    const backToTopBtn = document.getElementById('backToTop');

    window.addEventListener('scroll', () => {
        // Header Scroll Styles
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        // Back to Top Visibility
        if (window.scrollY > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });

    if(backToTopBtn) {
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // --- Mobile Menu Toggle & Auto-Close ---
    const mobileToggle = document.getElementById('mobileToggle');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');
    
    if(mobileToggle){
        mobileToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }

    // Close menu when a link is clicked
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
            }
        });
    });

    // --- Active Navigation State (ScrollSpy) ---
    const sections = document.querySelectorAll('section');

    const navObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting && entry.intersectionRatio >= 0.5) {
                const id = entry.target.getAttribute('id');
                if(id) {
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if(link.getAttribute('href') === `#${id}`) {
                            link.classList.add('active');
                        }
                    });
                }
            }
        });
    }, { threshold: 0.5 });

    sections.forEach(section => navObserver.observe(section));


    // --- Particle Animation (with Reduce Motion Check) ---
    const canvas = document.getElementById('particleCanvas');
    const ctx = canvas ? canvas.getContext('2d') : null;
    let particlesArray = [];
    
    // Check user preference for motion
    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (canvas && !prefersReducedMotion) {
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

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight; 
        }

        window.addEventListener('resize', () => {
            resizeCanvas();
            initParticles();
        });

        // Initialize
        resizeCanvas();
        initParticles();
        animateParticles();
    }


    // --- Scroll Triggered Animations ---
    const fadeSections = document.querySelectorAll('.fade-in-section');
    const fadeObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, { threshold: 0.1 });
    fadeSections.forEach(section => fadeObserver.observe(section));


    // --- Carousel Logic ---
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
            if(dotsContainer.children[currentSlide]) {
                dotsContainer.children[currentSlide].classList.add('active');
            }

            if (captionEl && slides[currentSlide]) {
                captionEl.textContent = slides[currentSlide].dataset.caption;
            }
            
            resetInterval();
        };

        const resetInterval = () => {
            clearInterval(slideInterval);
            slideInterval = setInterval(() => showSlide(currentSlide + 1), 5000);
        };

        // Create Dots
        slides.forEach((_, i) => {
            const dot = document.createElement('span');
            dot.classList.add('carousel-dot');
            dot.addEventListener('click', () => showSlide(i));
            dotsContainer.appendChild(dot);
        });

        // Event Listeners
        prevBtn.addEventListener('click', () => showSlide(currentSlide - 1));
        nextBtn.addEventListener('click', () => showSlide(currentSlide + 1));
        
        // Keyboard Nav
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') showSlide(currentSlide - 1);
            if (e.key === 'ArrowRight') showSlide(currentSlide + 1);
        });

        // Start
        showSlide(0);
    };

    initCarousel();


    // --- Lightbox Logic ---
    const modal = document.getElementById("lightboxModal");
    const modalImg = document.getElementById("expandedImage");
    const captionText = document.getElementById("modalCaption");
    const closeBtn = document.querySelector(".close-modal");
    const carouselImages = document.querySelectorAll('.carousel-slide img');

    if (modal && carouselImages.length > 0) {
        carouselImages.forEach(img => {
            img.addEventListener('click', function(e) {
                e.stopPropagation();
                modal.style.display = "flex";
                modalImg.src = this.src;
                
                const slideParent = this.closest('.carousel-slide');
                captionText.textContent = (slideParent && slideParent.dataset.caption) ? slideParent.dataset.caption : "";
            });
        });

        const closeModal = () => { modal.style.display = "none"; };

        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === "Escape" && modal.style.display === "flex") closeModal();
        });
    }


    // --- FAQ Logic ---
    document.querySelectorAll('details.faq-item').forEach((detail) => {
        detail.addEventListener('click', (e) => {
            if (e.target.closest('summary')) {
                e.preventDefault();
                const summary = detail.querySelector('summary');
                
                if (detail.hasAttribute('open')) {
                    // Close
                    const startHeight = detail.offsetHeight;
                    detail.style.height = `${startHeight}px`;
                    void detail.offsetHeight; // force reflow
                    
                    const style = window.getComputedStyle(detail);
                    const collapsedHeight = summary.offsetHeight + parseFloat(style.paddingTop) + parseFloat(style.paddingBottom);
                    
                    detail.style.height = `${collapsedHeight}px`;
                    
                    detail.addEventListener('transitionend', function onEnd() {
                        detail.removeAttribute('open');
                        detail.style.height = null;
                        detail.removeEventListener('transitionend', onEnd);
                    }, { once: true });
                } else {
                    // Open
                    const startHeight = detail.offsetHeight;
                    detail.style.height = `${startHeight}px`;
                    detail.setAttribute('open', '');
                    const targetHeight = detail.scrollHeight;
                    void detail.offsetHeight;
                    
                    detail.style.height = `${targetHeight}px`;
                    
                    detail.addEventListener('transitionend', function onEnd() {
                        detail.style.height = null;
                        detail.removeEventListener('transitionend', onEnd);
                    }, { once: true });
                }
            }
        });
    });
});