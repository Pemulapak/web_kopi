<script>
    // Mobile Navigation Toggle
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");

    hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        navMenu.classList.toggle("active");
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll(".nav-menu a").forEach((link) => {
        link.addEventListener("click", () => {
            hamburger.classList.remove("active");
            navMenu.classList.remove("active");
        });
    });

    // Smooth Scrolling
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            }
        });
    });

    // Scroll Animation
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
            }
        });
    }, observerOptions);

    document.querySelectorAll(".fade-in").forEach((el) => {
        observer.observe(el);
    });

    // Navbar Background on Scroll
    window.addEventListener("scroll", () => {
        const navbar = document.querySelector(".navbar");
        if (window.scrollY > 100) {
            navbar.style.background = "rgba(255, 255, 255, 0.98)";
            navbar.style.boxShadow = "0 2px 20px rgba(0,0,0,0.15)";
        } else {
            navbar.style.background = "rgba(255, 255, 255, 0.95)";
            navbar.style.boxShadow = "0 2px 20px rgba(0,0,0,0.1)";
        }
    });

    // Auto hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        });
    }, 5000);

    // Dynamic year in footer
    document.addEventListener("DOMContentLoaded", function() {
        const currentYear = new Date().getFullYear();
        const footerText = document.querySelector(".footer-bottom p");
        if (footerText) {
            footerText.innerHTML = footerText.innerHTML.replace(
                "2025",
                currentYear
            );
        }
    });

    // Add loading animation
    window.addEventListener("load", function() {
        document.body.style.opacity = "0";
        document.body.style.transition = "opacity 0.5s ease";
        setTimeout(() => {
            document.body.style.opacity = "1";
        }, 100);
    });

    // Parallax effect for hero section
    window.addEventListener("scroll", function() {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector(".hero");
        const rate = scrolled * -0.5;

        if (hero) {
            hero.style.transform = `translateY(${rate}px)`;
        }
    });

    // Counter animation for stats (if needed later)
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        const timer = setInterval(() => {
            start += increment;
            element.textContent = Math.floor(start);
            if (start >= target) {
                element.textContent = target;
                clearInterval(timer);
            }
        }, 16);
    }

    // Add click effect to buttons
    document
        .querySelectorAll(".cta-button, .submit-btn")
        .forEach((button) => {
            button.addEventListener("click", function(e) {
                let ripple = document.createElement("span");
                ripple.classList.add("ripple");
                this.appendChild(ripple);

                let x = e.clientX - e.target.offsetLeft;
                let y = e.clientY - e.target.offsetTop;

                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;

                setTimeout(() => {
                    ripple.remove();
                }, 300);
            });
        });

    // Add ripple effect CSS
    const rippleCSS = `
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255,255,255,0.6);
                transform: scale(0);
                animation: ripple-animation 0.3s ease-out;
                pointer-events: none;
            }
            
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;

    const style = document.createElement("style");
    style.textContent = rippleCSS;
    document.head.appendChild(style);

    // Menu item hover effect
    document.querySelectorAll(".menu-item").forEach((item) => {
        item.addEventListener("mouseenter", function() {
            this.style.transform = "translateY(-10px) scale(1.02)";
        });

        item.addEventListener("mouseleave", function() {
            this.style.transform = "translateY(0) scale(1)";
        });
    });

    // Typing effect for hero title
    function typeWriter(element, text, speed = 100) {
        let i = 0;
        element.innerHTML = "";

        function type() {
            if (i < text.length) {
                element.innerHTML += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }
        type();
    }

    // Initialize typing effect on page load
    setTimeout(() => {
        const heroTitle = document.querySelector(".hero h1");
        if (heroTitle) {
            const originalText = heroTitle.textContent;
            typeWriter(heroTitle, originalText, 80);
        }
    }, 1000);

    // Add entrance animations with delay
    document.addEventListener("DOMContentLoaded", function() {
        const elements = document.querySelectorAll(".fade-in");
        elements.forEach((el, index) => {
            el.style.animationDelay = `${index * 0.1}s`;
        });
    });

    // Social media links functionality
    document.querySelectorAll(".social-links a").forEach((link) => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            const platform = this.querySelector("i").classList[1].split("-")[1];

            // You can replace these with actual social media links
            const socialLinks = {
                instagram: "https://instagram.com/titikawalko",
                facebook: "https://facebook.com/titikawalko",
                twitter: "https://twitter.com/titikawalko",
                tiktok: "https://tiktok.com/@titikawalko",
                whatsapp: "https://wa.me/6281234567890",
            };

            if (socialLinks[platform]) {
                window.open(socialLinks[platform], "_blank");
            } else {
                alert(`Fitur ${platform} akan segera hadir!`);
            }
        });
    });
</script>

<!-- Untuk Confirm Password -->
<script>
    function showTab(tabName) {
        // Hide all form contents
        document.querySelectorAll('.form-content').forEach(content => {
            content.classList.remove('active');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected form and activate tab
        document.getElementById(tabName + '-form').classList.add('active');
        event.target.classList.add('active');
    }

    // Password validation
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const registerBtn = document.getElementById('registerBtn');

    passwordInput.addEventListener('input', function() {
        const password = this.value;

        // Check length
        const lengthCheck = document.getElementById('lengthCheck');
        if (password.length >= 8) {
            lengthCheck.classList.add('valid');
            lengthCheck.innerHTML = '<i class="fas fa-check"></i> Minimal 8 karakter';
        } else {
            lengthCheck.classList.remove('valid');
            lengthCheck.innerHTML = '<i class="fas fa-times"></i> Minimal 8 karakter';
        }

        // Check uppercase
        const uppercaseCheck = document.getElementById('uppercaseCheck');
        if (/[A-Z]/.test(password)) {
            uppercaseCheck.classList.add('valid');
            uppercaseCheck.innerHTML = '<i class="fas fa-check"></i> Huruf besar (A-Z)';
        } else {
            uppercaseCheck.classList.remove('valid');
            uppercaseCheck.innerHTML = '<i class="fas fa-times"></i> Huruf besar (A-Z)';
        }

        // Check lowercase
        const lowercaseCheck = document.getElementById('lowercaseCheck');
        if (/[a-z]/.test(password)) {
            lowercaseCheck.classList.add('valid');
            lowercaseCheck.innerHTML = '<i class="fas fa-check"></i> Huruf kecil (a-z)';
        } else {
            lowercaseCheck.classList.remove('valid');
            lowercaseCheck.innerHTML = '<i class="fas fa-times"></i> Huruf kecil (a-z)';
        }

        // Check number
        const numberCheck = document.getElementById('numberCheck');
        if (/\d/.test(password)) {
            numberCheck.classList.add('valid');
            numberCheck.innerHTML = '<i class="fas fa-check"></i> Angka (0-9)';
        } else {
            numberCheck.classList.remove('valid');
            numberCheck.innerHTML = '<i class="fas fa-times"></i> Angka (0-9)';
        }

        // Check special character
        const specialCheck = document.getElementById('specialCheck');
        if (/[@$!%*?&]/.test(password)) {
            specialCheck.classList.add('valid');
            specialCheck.innerHTML = '<i class="fas fa-check"></i> Karakter khusus (@$!%*?&)';
        } else {
            specialCheck.classList.remove('valid');
            specialCheck.innerHTML = '<i class="fas fa-times"></i> Karakter khusus (@$!%*?&)';
        }

        checkPasswordMatch();
        checkFormValidity();
    });

    confirmPasswordInput.addEventListener('input', function() {
        checkPasswordMatch();
        checkFormValidity();
    });

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const passwordMatch = document.getElementById('passwordMatch');

        if (confirmPassword.length > 0) {
            passwordMatch.classList.add('show');
            if (password === confirmPassword) {
                passwordMatch.classList.remove('invalid');
                passwordMatch.classList.add('valid');
                passwordMatch.innerHTML = '<i class="fas fa-check"></i> Password cocok';
            } else {
                passwordMatch.classList.remove('valid');
                passwordMatch.classList.add('invalid');
                passwordMatch.innerHTML = '<i class="fas fa-times"></i> Password tidak cocok';
            }
        } else {
            passwordMatch.classList.remove('show');
        }
    }

    function checkFormValidity() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        const isValidPassword = password.length >= 8 &&
            /[A-Z]/.test(password) &&
            /[a-z]/.test(password) &&
            /\d/.test(password) &&
            /[@$!%*?&]/.test(password);

        const isPasswordMatch = password === confirmPassword && confirmPassword.length > 0;

        if (isValidPassword && isPasswordMatch) {
            registerBtn.disabled = false;
        } else {
            registerBtn.disabled = true;
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.min.js"></script>

</body>

</html>