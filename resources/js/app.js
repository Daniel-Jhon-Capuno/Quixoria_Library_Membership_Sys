 import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Landing page animations
document.addEventListener('DOMContentLoaded', () => {
  // Hero word animation and hover flip effect
  const heroText = document.querySelector('[data-hero-text]');
  if (heroText) {
    // Split text into spans for each word while preserving spacing.
    const words = heroText.textContent.split(/\s+/);
    heroText.innerHTML = words.map((word, index) => `<span class="hero-word" style="--word-index:${index}">${word}</span>`).join(' ');

    const heroWords = heroText.querySelectorAll('.hero-word');
    const flipCooldown = new Map();

    // Initial stagger reveal so animation is visible even before hover.
    heroWords.forEach((word, index) => {
      word.style.animationDelay = `${index * 90}ms`;
      setTimeout(() => {
        word.classList.add('hero-word-visible');
      }, index * 90);
    });

    // Trigger an obvious flip when each word is directly hovered.
    heroWords.forEach((word) => {
      word.addEventListener('pointerenter', () => {
        const now = Date.now();
        const lastFlipTime = flipCooldown.get(word) || 0;
        const canFlip = now - lastFlipTime > 600;

        if (canFlip) {
          word.classList.remove('flip-trigger');
          // Force reflow to restart animation when rapidly re-hovering.
          void word.offsetWidth;
          word.classList.add('flip-trigger');
          flipCooldown.set(word, now);

          setTimeout(() => {
            word.classList.remove('flip-trigger');
          }, 650);
        }
      });
    });
  }

  // Stats count-up
  const stats = [10000, 200, 99.9];
  const statEls = document.querySelectorAll('[data-stat]');
  statEls.forEach((el, i) => {
    let count = 0;
    const target = stats[i];
    const increment = target / 100;
    const timer = setInterval(() => {
      count += increment;
      el.textContent = i < 2 ? Math.floor(count) + (i===0 ? 'k+' : '+') : count.toFixed(1) + '%';
      if (count >= target) {
        clearInterval(timer);
        el.textContent = i < 2 ? target + (i===0 ? 'k+' : '+') : target.toFixed(1) + '%';
      }
    }, 20);
  });

  // Floating particles
  const particlesContainer = document.querySelector('.particles');
  if (particlesContainer) {
    for (let i = 0; i < 25; i++) {
      const particle = document.createElement('div');
      particle.className = 'particle';
      particle.style.left = Math.random() * 100 + '%';
      particle.style.animationDelay = Math.random() * 20 + 's';
      particle.style.animationDuration = (15 + Math.random() * 10) + 's';
      particlesContainer.appendChild(particle);
    }
  }

  // Scroll reveal for landing page sections
  const revealTargets = document.querySelectorAll('.reveal-on-scroll');
  if (revealTargets.length) {
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const delay = entry.target.dataset.revealDelay ? parseInt(entry.target.dataset.revealDelay) * 200 : 0;
          setTimeout(() => {
            entry.target.classList.add('is-visible');
          }, delay);
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.18, rootMargin: '0px 0px -8% 0px' });

    revealTargets.forEach((el) => observer.observe(el));
  }

  // Cursor glow for the landing page
  const pointerGlow = document.querySelector('.pointer-glow');
  if (pointerGlow) {
    let rafId = null;
    let targetX = window.innerWidth / 2;
    let targetY = window.innerHeight / 2;
    let currentX = targetX;
    let currentY = targetY;

    const animateGlow = () => {
      currentX += (targetX - currentX) * 0.12;
      currentY += (targetY - currentY) * 0.12;
      pointerGlow.style.transform = `translate3d(${currentX - 224}px, ${currentY - 224}px, 0) scale(1)`;
      rafId = requestAnimationFrame(animateGlow);
    };

    const moveGlow = (event) => {
      targetX = event.clientX;
      targetY = event.clientY;
      pointerGlow.classList.add('is-active');
      if (!rafId) {
        animateGlow();
      }
    };

    const hideGlow = () => {
      pointerGlow.classList.remove('is-active');
      pointerGlow.style.transform = 'translate3d(-9999px, -9999px, 0) scale(0.65)';
      if (rafId) {
        cancelAnimationFrame(rafId);
        rafId = null;
      }
    };

    window.addEventListener('pointermove', moveGlow, { passive: true });
    window.addEventListener('pointerleave', hideGlow);
    window.addEventListener('blur', hideGlow);
  }
});
