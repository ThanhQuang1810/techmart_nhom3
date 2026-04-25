document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-confirm]').forEach(function (button) {
    button.addEventListener('click', function (event) {
      const message = button.getAttribute('data-confirm') || 'Bạn có chắc chắn?';
      if (!confirm(message)) {
        event.preventDefault();
      }
    });
  });
});
