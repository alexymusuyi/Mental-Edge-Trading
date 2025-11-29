// Mobile navigation toggle
const navToggle = document.getElementById("navToggle");
const navLinks = document.getElementById("navLinks");

if (navToggle && navLinks) {
  navToggle.addEventListener("click", () => {
    navLinks.classList.toggle("open");
  });

  // Close menu when a link is clicked (mobile)
  navLinks.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      navLinks.classList.remove("open");
    });
  });
}

// Simple fake success messages for the email forms
function handleSimpleForm(formSelector, messageSelector) {
  const form = document.querySelector(formSelector);
  const messageEl = document.querySelector(messageSelector);

  if (!form || !messageEl) return;

  form.addEventListener("submit", (e) => {
    // Remove this `preventDefault` once you connect a real email service.
    e.preventDefault();

    const emailInput = form.querySelector('input[type="email"]');
    const email = emailInput ? emailInput.value.trim() : "";

    if (!email) {
      messageEl.textContent = "Please enter a valid email.";
      messageEl.className = "form-message error";
      return;
    }

    messageEl.textContent =
      "Thank you! You're on the list. Check your inbox shortly.";
    messageEl.className = "form-message success";
    form.reset();
  });
}

handleSimpleForm(".section-email .email-form", "#emailFormMessage");
handleSimpleForm("#book-preorder .email-form", "#bookFormMessage");

// Dynamic year in the footer
const yearSpan = document.getElementById("year");
if (yearSpan) {
  yearSpan.textContent = new Date().getFullYear();
}

/* ---------------------------------------
   Scroll / fade-in animations for .reveal
---------------------------------------- */

document.addEventListener("DOMContentLoaded", () => {
  const revealEls = document.querySelectorAll(".reveal");

  if (!revealEls.length) return;

  // Fallback: make everything visible immediately (so you never get a blank page)
  revealEls.forEach((el) => {
    if (el.getBoundingClientRect().top < window.innerHeight * 0.9) {
      el.classList.add("reveal-visible");
    }
  });

  // Use IntersectionObserver for scroll-in animations
  if ("IntersectionObserver" in window) {
    const observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("reveal-visible");
            obs.unobserve(entry.target); // only animate once
          }
        });
      },
      {
        threshold: 0.15,
      }
    );

    revealEls.forEach((el) => observer.observe(el));
  } else {
    // Very old browsers: just show everything
    revealEls.forEach((el) => el.classList.add("reveal-visible"));
  }
});

// Enhanced reveal animation on scroll with stagger effect
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry, index) => {
    if (entry.isIntersecting) {
      // Add stagger delay for multiple elements
      const delay = index * 100;
      setTimeout(() => {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        entry.target.style.filter = 'blur(0px)';
      }, delay);
    }
  });
}, observerOptions);

// Observe all elements with reveal class
document.querySelectorAll('.reveal').forEach((el, index) => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(30px)';
  el.style.filter = 'blur(5px)';
  el.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out, filter 0.8s ease-out';
  observer.observe(el);
});

// Parallax effect for hero sections
window.addEventListener('scroll', () => {
  const scrolled = window.pageYOffset;
  const parallaxElements = document.querySelectorAll('.page-hero');
  
  parallaxElements.forEach(element => {
    const speed = 0.5;
    element.style.transform = `translateY(${scrolled * speed}px)`;
  });
});

// Enhanced hover effects for interactive elements
document.querySelectorAll('.btn, .nav-links a, .login-btn').forEach(element => {
  element.addEventListener('mouseenter', function() {
    if (!this.classList.contains('login-btn')) {
      this.style.transform = 'translateY(-2px)';
      this.style.boxShadow = '0 8px 25px rgba(102, 106, 16, 0.3)';
    }
  });
  
  element.addEventListener('mouseleave', function() {
    if (!this.classList.contains('login-btn')) {
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = 'none';
    }
  });
});

// Add floating animation to key elements
function addFloatingAnimation() {
  const floatingElements = document.querySelectorAll('.hero-ctas .btn, .stat-card, .credential-card');
  
  floatingElements.forEach((element, index) => {
    element.style.animationDelay = `${index * 0.2}s`;
    element.classList.add('float-animation');
  });
}

// Add CSS for floating animation
const style = document.createElement('style');
style.textContent = `
  .float-animation {
    animation: float 3s ease-in-out infinite;
  }
  
  @keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
  }
  
  .credential-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 12px 30px rgba(12, 8, 4, 0.15) !important;
  }
  
  .stat-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 12px 30px rgba(12, 8, 4, 0.15) !important;
  }
`;
document.head.appendChild(style);

// Initialize floating animations after page load
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(addFloatingAnimation, 1000);
});

// Add typing effect for hero titles
function typeWriter(element, text, speed = 50) {
  let i = 0;
  element.innerHTML = '';
  
  function type() {
    if (i < text.length) {
      element.innerHTML += text.charAt(i);
      i++;
      setTimeout(type, speed);
    }
  }
  
  type();
}

// Initialize typing effect for hero titles
document.addEventListener('DOMContentLoaded', () => {
  const heroTitles = document.querySelectorAll('.page-hero h1');
  heroTitles.forEach((title, index) => {
    const originalText = title.textContent;
    setTimeout(() => {
      typeWriter(title, originalText, 80);
    }, index * 1000);
  });
});

// Add progress indicator for long pages
function addProgressIndicator() {
  const progressBar = document.createElement('div');
  progressBar.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: var(--accent);
    z-index: 1000;
    transition: width 0.3s ease;
  `;
  document.body.appendChild(progressBar);
  
  window.addEventListener('scroll', () => {
    const scrollTop = window.pageYOffset;
    const docHeight = document.body.scrollHeight - window.innerHeight;
    const scrollPercent = (scrollTop / docHeight) * 100;
    progressBar.style.width = scrollPercent + '%';
  });
}

// Initialize progress indicator
if (document.body.scrollHeight > window.innerHeight * 2) {
  addProgressIndicator();
}

// Add smooth reveal for statistics numbers
function animateNumbers() {
  const numberElements = document.querySelectorAll('.stat-number');
  
  numberElements.forEach(element => {
    const finalNumber = element.textContent;
    const isNumber = !isNaN(finalNumber.replace(/[^0-9]/g, ''));
    
    if (isNumber) {
      element.textContent = '0';
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const target = parseInt(finalNumber.replace(/[^0-9]/g, ''));
            const suffix = finalNumber.replace(/[0-9]/g, '');
            let current = 0;
            const increment = target / 50;
            
            const timer = setInterval(() => {
              current += increment;
              if (current >= target) {
                current = target;
                clearInterval(timer);
              }
              element.textContent = Math.floor(current) + suffix;
            }, 30);
            
            observer.unobserve(entry.target);
          }
        });
      });
      
      observer.observe(element);
    }
  });
}

// Initialize number animations
document.addEventListener('DOMContentLoaded', animateNumbers);

// Add keyboard navigation support
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    // Close mobile menu if open
    if (navLinks && navLinks.classList.contains('active')) {
      navLinks.classList.remove('active');
    }
  }
});

// Add loading animation for page transitions
function showLoadingAnimation() {
  const loader = document.createElement('div');
  loader.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--bg);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.3s ease;
  `;
  loader.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--accent);"></i>';
  document.body.appendChild(loader);
  
  setTimeout(() => {
    loader.style.opacity = '1';
  }, 10);
  
  return loader;
}

// Add click handlers for links to show loading
document.querySelectorAll('a[href$=".html"], a[href^="/"]').forEach(link => {
  link.addEventListener('click', function(e) {
    if (this.hostname === window.location.hostname) {
      const loader = showLoadingAnimation();
      
      // Remove loader after navigation
      setTimeout(() => {
        document.body.removeChild(loader);
      }, 2000);
    }
  });
});