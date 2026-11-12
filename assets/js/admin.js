/**
 * Admin Panel Interactivity, Live Preview & Modals
 */

function openAdminModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('open');
  }
}

function closeAdminModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('open');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  // 1. Sync Color Pickers with text input
  const colorPickers = document.querySelectorAll('.color-swatch-input');
  colorPickers.forEach(picker => {
    const textInputId = picker.getAttribute('data-sync');
    if (textInputId) {
      const textInput = document.getElementById(textInputId);
      if (textInput) {
        picker.addEventListener('input', (e) => {
          textInput.value = e.target.value.toUpperCase();
          updateLiveColorPreview();
        });
        textInput.addEventListener('input', (e) => {
          if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
            picker.value = e.target.value;
            updateLiveColorPreview();
          }
        });
      }
    }
  });

  // 2. Image File Upload Preview
  const imageInputs = document.querySelectorAll('input[type="file"][data-preview]');
  imageInputs.forEach(input => {
    const previewId = input.getAttribute('data-preview');
    const previewImg = document.getElementById(previewId);

    if (previewImg) {
      input.addEventListener('change', () => {
        const file = input.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
          };
          reader.readAsDataURL(file);
        }
      });
    }
  });

  // 3. Modal overlay click & Escape key close
  document.querySelectorAll('.admin-modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        overlay.classList.remove('open');
      }
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.admin-modal-overlay.open').forEach(m => m.classList.remove('open'));
      closeMobileSidebar();
    }
  });

  // 4. Mobile Admin Sidebar Drawer Toggle
  const adminSidebar = document.getElementById('adminSidebar');
  const adminSidebarToggle = document.getElementById('adminSidebarToggle');
  const adminSidebarClose = document.getElementById('adminSidebarClose');
  const adminBackdrop = document.getElementById('adminBackdrop');

  function openMobileSidebar() {
    if (adminSidebar) adminSidebar.classList.add('mobile-open');
    if (adminBackdrop) adminBackdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeMobileSidebar() {
    if (adminSidebar) adminSidebar.classList.remove('mobile-open');
    if (adminBackdrop) adminBackdrop.classList.remove('open');
    document.body.style.overflow = '';
  }

  if (adminSidebarToggle) adminSidebarToggle.addEventListener('click', openMobileSidebar);
  if (adminSidebarClose) adminSidebarClose.addEventListener('click', closeMobileSidebar);
  if (adminBackdrop) adminBackdrop.addEventListener('click', closeMobileSidebar);

  function updateLiveColorPreview() {
    const primaryInput = document.getElementById('primary_color');
    const accentInput = document.getElementById('accent_color');
    const bgDarkInput = document.getElementById('bg_dark');
    const bgCardInput = document.getElementById('bg_card');

    const previewBox = document.getElementById('theme-preview-box');
    if (previewBox) {
      if (primaryInput) previewBox.style.setProperty('--preview-primary', primaryInput.value);
      if (accentInput) previewBox.style.setProperty('--preview-accent', accentInput.value);
      if (bgDarkInput) previewBox.style.setProperty('--preview-bg-dark', bgDarkInput.value);
      if (bgCardInput) previewBox.style.setProperty('--preview-bg-card', bgCardInput.value);
    }
  }

  updateLiveColorPreview();
});
