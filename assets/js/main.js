// Mobile nav toggle
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
      "Thank you! You’re on the list. Check your inbox shortly.";
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
