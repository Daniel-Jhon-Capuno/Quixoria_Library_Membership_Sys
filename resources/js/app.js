import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Landing page animations
document.addEventListener('DOMContentLoaded', () => {
  // Hero staggered word reveal
  const heroText = document.querySelector('[data-hero-text]');
  if (heroText) {
    heroText.innerHTML = heroText.textContent.replace(/\\S+/g, '<span class="hero-word inline-block opacity-0 -translate-y-4 transition-all duration-500">$&</span>');
    heroText.querySelectorAll('.hero-word').forEach((word, i) => {
      setTimeout(() => word.classList.add('translate-y-0', 'opacity-100'), i * 120);
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
});
