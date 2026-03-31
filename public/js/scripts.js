<?php if (!empty(GETPOST('action_produit')) && GETPOST('action_produit') === 'add'): ?>
  document.addEventListener('DOMContentLoaded', () => toggleForm(true));
  <?php endif; ?>
 
  function toggleForm(forceOpen) {
    const collapse = document.getElementById('form-collapse');
    const panel    = document.getElementById('form-panel');
    const isOpen   = collapse.classList.contains('open');
    if (forceOpen === true || !isOpen) {
      collapse.classList.add('open');
      panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
      collapse.classList.remove('open');
    }
  }