document.addEventListener("DOMContentLoaded", function () {
  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href");
      const targetElement = document.querySelector(targetId);
      if (targetElement) {
        targetElement.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  });

  // Scroll reveal animations
  const scrollReveal = (entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("revealed");
        observer.unobserve(entry.target);
      }
    });
  };

  const revealObserver = new IntersectionObserver(scrollReveal, {
    root: null,
    rootMargin: "0px",
    threshold: 0.1,
  });

  document.querySelectorAll(".scroll-reveal").forEach((el) => {
    revealObserver.observe(el);
  });
  
  // Mobile menu toggle
  const mobileMenuButton = document.getElementById("mobile-menu-button");
  const mobileMenu = document.getElementById("mobile-menu");
  const mobileMenuCloseButton = document.getElementById("mobile-menu-close");

  if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
    });
  }
  
  if (mobileMenuCloseButton && mobileMenu) {
    mobileMenuCloseButton.addEventListener("click", () => {
      mobileMenu.classList.add("hidden");
    });
  }

  // Close mobile menu when a link is clicked
  if (mobileMenu) {
    mobileMenu.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        mobileMenu.classList.add("hidden");
      });
    });
  }

  // FAQ accordion
  document.querySelectorAll(".faq-item details").forEach((detail) => {
    detail.addEventListener("toggle", (event) => {
      if (detail.open) {
        document.querySelectorAll(".faq-item details").forEach((otherDetail) => {
          if (otherDetail !== detail) {
            otherDetail.removeAttribute("open");
          }
        });
      }
    });
  });
  
  // Interest form submission with validation
  const interestForm = document.getElementById('interest-form');
  if(interestForm) {
    const nameInput = document.getElementById('interest-name');
    const emailInput = document.getElementById('interest-email');
    const nameError = document.getElementById('interest-name-error');
    const emailError = document.getElementById('interest-email-error');
    const submitBtn = document.getElementById('interest-submit-btn');
    const submitText = document.getElementById('interest-submit-text');
    const loadingSpinner = document.getElementById('interest-loading-spinner');
    const successMessage = document.getElementById('interest-success-message');

    const validateEmail = (email) => {
      const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(String(email).toLowerCase());
    }

    interestForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      let isValid = true;
      
      if(nameInput.value.trim() === '') {
        nameError.classList.remove('hidden');
        nameInput.classList.add('border-red-400');
        isValid = false;
      } else {
        nameError.classList.add('hidden');
        nameInput.classList.remove('border-red-400');
      }
      
      if(!validateEmail(emailInput.value)) {
        emailError.classList.remove('hidden');
        emailInput.classList.add('border-red-400');
        isValid = false;
      } else {
        emailError.classList.add('hidden');
        emailInput.classList.remove('border-red-400');
      }

      if(isValid) {
        submitText.classList.add('hidden');
        loadingSpinner.classList.remove('hidden');
        submitBtn.disabled = true;

        setTimeout(() => {
          loadingSpinner.classList.add('hidden');
          submitText.classList.remove('hidden');
          submitBtn.disabled = false;
          successMessage.classList.remove('hidden');
          interestForm.reset();

          setTimeout(() => {
            successMessage.classList.add('hidden');
          }, 5000);
        }, 1500);
      }
    });
  }
}); 