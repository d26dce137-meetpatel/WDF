document.addEventListener('DOMContentLoaded', () => {
    
    const currentUser = localStorage.getItem('studentUser');
    const currentPage = window.location.pathname.split('/').pop() || 'home.html';

    // 0. Page Protection Logic (Login કર્યા વગર અન્ય પેજ નહિ ખૂલે)
    const publicPages = ['home.html', 'login.html', 'createaccount.html', 'forgotpassword.html', 'about.html', 'contact.html', ''];
    const isPublicPage = publicPages.includes(currentPage);

    if (!currentUser && !isPublicPage) {
        alert('⚠️ Please login first to access this page!');
        window.location.href = 'login.html';
        return;
    }

    // 1. Dynamic Navigation Menu (Login પછી જ બધા પેજ દેખાશે)
    const navMenu = document.getElementById('navMenu');
    if (navMenu) {
        if (!currentUser) {
            navMenu.innerHTML = `
                <a href="home.html">Home</a>
                <a href="about.html">About Us</a>
                <a href="contact.html">Contact Us</a>
                <a href="createaccount.html">Create Account</a>
                <a href="forgotpassword.html">Forgot Password</a>
                <a href="login.html">Login</a>
            `;
        } else {
            navMenu.innerHTML = `
                <a href="home.html">Home</a>
                <a href="dashboard.html">Dashboard</a>
                <a href="updateprofile.html">Update Profile</a>
                <a href="about.html">About Us</a>
                <a href="events.html">Events</a>
                <a href="feedback.html">Feedback</a>
                <a href="#" id="navLogoutBtn" style="color: #e63946; font-weight: bold;">Logout</a>
            `;

            const navLogoutBtn = document.getElementById('navLogoutBtn');
            if (navLogoutBtn) {
                navLogoutBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    localStorage.removeItem('studentUser');
                    window.location.href = 'home.html';
                });
            }
        }
    }

    // 2. Dark/Light Theme Switcher with LocalStorage
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const savedTheme = localStorage.getItem('portalTheme');

    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
        if (themeToggleBtn) themeToggleBtn.textContent = '☀️ Light Mode';
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const isDark = document.body.classList.toggle('dark-theme');
            if (isDark) {
                themeToggleBtn.textContent = '☀️ Light Mode';
                localStorage.setItem('portalTheme', 'dark');
            } else {
                themeToggleBtn.textContent = '🌙 Dark Mode';
                localStorage.setItem('portalTheme', 'light');
            }
        });
    }

    // 3. Persistent Hamburger Navigation
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const savedMenuState = localStorage.getItem('portalMenuState');

    if (savedMenuState === 'open' && navMenu) {
        navMenu.classList.add('open');
    }

    if (hamburgerBtn && navMenu) {
        hamburgerBtn.addEventListener('click', () => {
            const isOpen = navMenu.classList.toggle('open');
            localStorage.setItem('portalMenuState', isOpen ? 'open' : 'closed');
        });
    }

    // 4. Red 2.0 Notification Banner
    const promoBanner = document.getElementById('promoBanner');
    const closeBannerBtn = document.getElementById('closeBannerBtn');

    if (promoBanner) {
        promoBanner.style.display = 'flex';
    }

    if (promoBanner && closeBannerBtn) {
        closeBannerBtn.addEventListener('click', () => {
            promoBanner.style.display = 'none';
        });
    }

    // 5. Collapsible FAQ Accordion
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    if (accordionHeaders.length > 0) {
        accordionHeaders.forEach((header) => {
            header.addEventListener('click', () => {
                const currentContent = header.nextElementSibling;
                const currentIcon = header.querySelector('.accordion-icon');
                const parentItem = header.parentElement;
                const isAlreadyOpen = currentContent.style.maxHeight && currentContent.style.maxHeight !== '0px';

                document.querySelectorAll('.accordion-content').forEach(c => c.style.maxHeight = null);
                document.querySelectorAll('.accordion-icon').forEach(i => i.textContent = '+');
                document.querySelectorAll('.accordion-item').forEach(item => item.classList.remove('active'));

                if (!isAlreadyOpen) {
                    currentContent.style.maxHeight = currentContent.scrollHeight + "px";
                    if (currentIcon) currentIcon.textContent = '−';
                    if (parentItem) parentItem.classList.add('active');
                }
            });
        });
    }

    // 6. Create Account Submission (Submit કરતાં Modal ખૂલશે અને Login પેજ પર મોકલશે)
    const registerForm = document.getElementById('registerForm');
    const accountModal = document.getElementById('accountModal');
    const closeAccountModalBtn = document.getElementById('closeAccountModalBtn');
    const confirmAccountModalBtn = document.getElementById('confirmAccountModalBtn');
    const modalStudentDetails = document.getElementById('modalStudentDetails');

    if (registerForm && accountModal) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const name = document.getElementById('name').value.trim();
            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');
            const enrollment = document.getElementById('enrollment').value.trim();
            const confirm = document.getElementById('confirm').value;

            if (passInput && passInput.value !== confirm) {
                alert('⚠️ Passwords do not match! Please check again.');
                return;
            }

            // અહીં ઈમેઈલ અને પાસવર્ડ સિંક કરવા માટે લોકલ સ્ટોરેજમાં સેવ થાય છે
            if (emailInput && emailInput.value) {
                localStorage.setItem('registeredEmail', emailInput.value.trim());
            }
            if (passInput && passInput.value) {
                localStorage.setItem('registeredPassword', passInput.value);
            }

            if (modalStudentDetails) {
                modalStudentDetails.innerHTML = `Welcome <strong>${name}</strong> (Enrollment: <strong>${enrollment}</strong>). Your Student Portal 2.0 account has been created successfully.`;
            }
            
            accountModal.classList.add('active');
            registerForm.reset();
        });

        const closeAndRedirectToLogin = () => {
            accountModal.classList.remove('active');
            window.location.href = 'login.html';
        };

        if (closeAccountModalBtn) closeAccountModalBtn.addEventListener('click', closeAndRedirectToLogin);
        if (confirmAccountModalBtn) confirmAccountModalBtn.addEventListener('click', closeAndRedirectToLogin);
    }

    // 7. Login Form Submission (સફળતાપૂર્વક લૉગિન થયા પછી જ બધા પેજનો એક્સેસ મળશે)
    const loginForm = document.getElementById('loginForm');
    const loginModal = document.getElementById('loginModal');
    const closeLoginModalBtn = document.getElementById('closeLoginModalBtn');
    const confirmLoginModalBtn = document.getElementById('confirmLoginModalBtn');
    const modalLoginDetails = document.getElementById('modalLoginDetails');

    if (loginForm && loginModal) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            
            // સેશન સેવ કરવું
            localStorage.setItem('studentUser', email);

            if (modalLoginDetails) {
                modalLoginDetails.innerHTML = `Welcome back! Logged in as <strong>${email}</strong>.<br>Redirecting to your Student Portal 2.0 dashboard.`;
            }
            loginModal.classList.add('active');
            loginForm.reset();
        });

        const closeAndRedirectToDashboard = () => {
            loginModal.classList.remove('active');
            window.location.href = 'dashboard.html';
        };

        if (closeLoginModalBtn) closeLoginModalBtn.addEventListener('click', closeAndRedirectToDashboard);
        if (confirmLoginModalBtn) confirmLoginModalBtn.addEventListener('click', closeAndRedirectToDashboard);
    }

    // Forgot Password & Update Profile Sync Logic Addition
    const forgotForm = document.getElementById('forgotForm');
    if (forgotForm) {
        forgotForm.addEventListener('submit', () => {
            const newPass = document.getElementById('password');
            if (newPass && newPass.value) {
                localStorage.setItem('registeredPassword', newPass.value);
            }
        });
    }

    const profileEmail = document.getElementById('email');
    if (profileEmail && currentUser) {
        profileEmail.value = currentUser;
    }

    // 8. Dashboard Welcome Display & Logout
    const userDisplay = document.getElementById('userDisplay');
    const logoutBtn = document.getElementById('logoutBtn');

    if (userDisplay && currentUser) {
        userDisplay.textContent = currentUser;
    }

    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            localStorage.removeItem('studentUser');
            window.location.href = 'home.html';
        });
    }
});