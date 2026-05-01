const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');

hamburger.addEventListener('click', (e) => {
    e.stopPropagation();
    navMenu.classList.toggle('show');
    hamburger.classList.toggle('active');
});

// Close when clicking outside
document.addEventListener('click', () => {
    navMenu.classList.remove('show');
    hamburger.classList.remove('active');
});

// Prevent menu click from closing
navMenu.addEventListener('click', (e) => {
    e.stopPropagation();
});