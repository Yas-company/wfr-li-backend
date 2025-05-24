class Navigation {
    constructor() {
        this.sections = ['how-it-works', 'features', 'suppliers', 'products'];
        this.activeSection = '';
        this.init();
    }

    init() {
        // Initialize scroll spy
        window.addEventListener('scroll', () => this.updateActiveSection());
        this.updateActiveSection();

        // Handle smooth scrolling and cross-page navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = anchor.getAttribute('href').substring(1);
                
                // Check if we're on the home page
                if (window.location.pathname === '/' || window.location.pathname === '/home') {
                    // If on home page, just scroll to section
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        const headerOffset = 80;
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                } else {
                    // If not on home page, redirect to home page with hash
                    window.location.href = `{{ route('home') }}#${targetId}`;
                }
            });
        });
    }

    updateActiveSection() {
        let found = false;
        
        for (const id of this.sections) {
            const element = document.getElementById(id);
            if (element) {
                const rect = element.getBoundingClientRect();
                if (rect.top <= 80 && rect.bottom > 80) {
                    this.setActiveSection(id);
                    found = true;
                    break;
                }
            }
        }

        if (!found) {
            this.setActiveSection('');
        }
    }

    setActiveSection(sectionId) {
        if (this.activeSection !== sectionId) {
            this.activeSection = sectionId;
            // Dispatch custom event for Alpine.js to react to
            window.dispatchEvent(new CustomEvent('section-changed', { 
                detail: { sectionId } 
            }));
        }
    }
}

// Initialize navigation when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.navigation = new Navigation();
    
    // Handle hash in URL on page load
    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            setTimeout(() => {
                const headerOffset = 80;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }, 100); // Small delay to ensure DOM is ready
        }
    }
}); 