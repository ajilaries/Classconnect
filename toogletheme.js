function toggleTheme() {
  const themeToggle = document.getElementById('themeToggle');
  document.body.classList.toggle('dark');

  if (document.body.classList.contains('dark')) {
    localStorage.setItem('theme', 'dark');
    if(themeToggle) themeToggle.innerText = '☀️';
  } else {
    localStorage.setItem('theme', 'light');
    if(themeToggle) themeToggle.innerText = '🌙';
  }
}

// Apply saved theme on page load
window.addEventListener('DOMContentLoaded', () => {
  const savedTheme = localStorage.getItem('theme');
  const themeToggle = document.getElementById('themeToggle');

  if(savedTheme === 'dark') {
    document.body.classList.add('dark');
    if(themeToggle) themeToggle.innerText = '☀️';
  } else {
    document.body.classList.remove('dark');
    if(themeToggle) themeToggle.innerText = '🌙';
  }
});
