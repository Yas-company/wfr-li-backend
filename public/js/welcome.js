document.addEventListener("DOMContentLoaded", function () {
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
    document
        .querySelectorAll(".fade-up, .fade-in, .scale-up")
        .forEach((element) => {
            observer.observe(element);
        });
    // Language toggle functionality
    const languageToggle = document.getElementById("languageToggle");
    if (languageToggle) {
        languageToggle.addEventListener("change", function () {
            // Language toggle logic would go here
            console.log("Language toggled:", this.checked ? "AR" : "EN");
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            const targetId = this.getAttribute("href");
            if (targetId === "#") return;
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: "smooth",
                });
            }
        });
    });
}); 