// =========================================================
// 1. HAMBURGER MENU (Toggle visibility on mobile screens)
// =========================================================
const menuBtn = document.getElementById("menuBtn");
const navMenu = document.getElementById("navMenu");

if (menuBtn && navMenu) {
    menuBtn.addEventListener("click", () => {
        navMenu.classList.toggle("show");
    });
}

// =========================================================
// 2. LIGHT / DARK THEME SWITCHER (Saved via localStorage)
// =========================================================
const themeBtn = document.getElementById("themeBtn");

// Check stored theme in browser storage
if (localStorage.getItem("portalTheme") === "dark") {
    document.body.classList.add("dark");
    if (themeBtn) themeBtn.textContent = "☀️ Light Mode";
}

if (themeBtn) {
    themeBtn.addEventListener("click", (e) => {
        e.preventDefault();
        document.body.classList.toggle("dark");

        if (document.body.classList.contains("dark")) {
            localStorage.setItem("portalTheme", "dark");
            themeBtn.textContent = "☀️ Light Mode";
        } else {
            localStorage.setItem("portalTheme", "light");
            themeBtn.textContent = "🌙 Dark Mode";
        }
    });
}

// =========================================================
// 3. NOTIFICATION BANNER (Dismiss functionality)
// =========================================================
const notification = document.getElementById("notification");
const closeBanner = document.getElementById("closeNotification");

if (notification && closeBanner) {
    closeBanner.addEventListener("click", () => {
        notification.style.display = "none";
    });
}

// =========================================================
// 4. MODAL POPUP (Open, Close & Overlay dismiss)
// =========================================================
const modal = document.getElementById("modal");
const openModalBtn = document.getElementById("openModalBtn");
const closeModalBtn = document.getElementById("closeModalBtn");

if (openModalBtn && modal) {
    openModalBtn.addEventListener("click", () => {
        modal.style.display = "flex";
    });
}

if (closeModalBtn && modal) {
    closeModalBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });
}

window.addEventListener("click", (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

// =========================================================
// 5. CONTENT SLIDER (Dynamic Slide Rendering)
// =========================================================
const slides = [
    { title: "Mid-Term Exams", desc: "Mid-term exam schedule has been declared on the portal." },
    { title: "Hackathon 2026", desc: "Registrations are open for the inter-college Web Sprint." },
    { title: "Campus Placement", desc: "Tech interview preparation seminars begin this Friday." }
];

let currentSlide = 0;
const slideTitle = document.getElementById("slideTitle");
const slideDesc = document.getElementById("slideDesc");
const prevSlideBtn = document.getElementById("prevSlide");
const nextSlideBtn = document.getElementById("nextSlide");

function updateSlide(index) {
    if (slideTitle && slideDesc) {
        slideTitle.textContent = slides[index].title;
        slideDesc.textContent = slides[index].desc;
    }
}

if (nextSlideBtn && prevSlideBtn) {
    updateSlide(currentSlide);

    nextSlideBtn.addEventListener("click", () => {
        currentSlide = (currentSlide + 1) % slides.length;
        updateSlide(currentSlide);
    });

    prevSlideBtn.addEventListener("click", () => {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        updateSlide(currentSlide);
    });
}

// =========================================================
// 6. COLLAPSIBLE FAQ ACCORDION
// =========================================================
const faqQuestions = document.querySelectorAll(".faq-question");

faqQuestions.forEach((button) => {
    button.addEventListener("click", () => {
        const answer = button.nextElementSibling;
        const icon = button.querySelector("span");

        const isOpen = answer.style.display === "block";

        // Close all other open answers
        document.querySelectorAll(".faq-answer").forEach((el) => {
            el.style.display = "none";
        });
        document.querySelectorAll(".faq-question span").forEach((sp) => {
            sp.textContent = "+";
        });

        // Toggle clicked element
        if (!isOpen) {
            answer.style.display = "block";
            if (icon) icon.textContent = "−";
        }
    });
});