document.addEventListener('DOMContentLoaded', function () {
  initThemeToggle();
  initBackToTop();
  initSlider();
  initProductGallery();
});

function initThemeToggle() {
  const themeKey = 'techmart_theme';
  const toggle = document.getElementById('themeToggle');
  const root = document.documentElement;
  const saved = localStorage.getItem(themeKey) || 'light';
  applyTheme(saved);

  if (!toggle) return;
  toggle.addEventListener('click', function () {
    const current = root.getAttribute('data-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    applyTheme(next);
    localStorage.setItem(themeKey, next);
  });

  function applyTheme(theme) {
    root.setAttribute('data-theme', theme);
    const icon = toggle ? toggle.querySelector('i') : null;
    if (icon) {
      icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }
  }
}

function initBackToTop() {
  const btn = document.getElementById('backToTop');
  if (!btn) return;
  const toggle = () => {
    btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
  };
  toggle();
  window.addEventListener('scroll', toggle);
  btn.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

function initSlider() {
  const slides = document.querySelectorAll('.slide');
  const dotsRoot = document.getElementById('sliderDots');
  if (!slides.length || !dotsRoot) return;
  let currentIndex = 0;
  const dots = [];

  function showSlide(index) {
    slides.forEach((slide) => slide.classList.remove('active'));
    dots.forEach((dot) => dot.classList.remove('active'));
    slides[index].classList.add('active');
    dots[index].classList.add('active');
    currentIndex = index;
  }

  slides.forEach((_, index) => {
    const dot = document.createElement('button');
    if (index === 0) dot.classList.add('active');
    dot.addEventListener('click', function () {
      showSlide(index);
    });
    dotsRoot.appendChild(dot);
    dots.push(dot);
  });

  setInterval(function () {
    showSlide((currentIndex + 1) % slides.length);
  }, 3500);
}

function initProductGallery() {
  const mainImage = document.getElementById('mainImage');
  const selectedColor = document.getElementById('selectedColor');
  const thumbs = document.querySelectorAll('.color-thumb');
  if (!mainImage || !thumbs.length) return;

  thumbs.forEach((thumb) => {
    thumb.addEventListener('click', function () {
      thumbs.forEach((item) => item.classList.remove('active'));
      thumb.classList.add('active');
      mainImage.src = thumb.dataset.image || mainImage.src;
      if (selectedColor) {
        selectedColor.textContent = thumb.dataset.color || 'Mặc định';
      }
    });
  });
}
