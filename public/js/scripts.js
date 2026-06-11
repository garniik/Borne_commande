// Global custom confirm modal to avoid native dialogs showing the host
(function() {
	function buildModal() {
		const overlay = document.createElement('div');
		overlay.id = 'customConfirmOverlay';
		overlay.className = 'custom-confirm-overlay';

		const modal = document.createElement('div');
		modal.id = 'customConfirmModal';
		modal.className = 'custom-confirm-modal';

		const msg = document.createElement('div');
		msg.id = 'customConfirmMessage';
		msg.className = 'custom-confirm-message';

		const actions = document.createElement('div');
		actions.className = 'custom-confirm-actions';

		const btnCancel = document.createElement('button');
		btnCancel.className = 'btn btn-ghost';
		btnCancel.textContent = 'Annuler';

		const btnOk = document.createElement('button');
		btnOk.className = 'btn btn-primary';
		btnOk.textContent = 'Confirmer';

		actions.appendChild(btnCancel);
		actions.appendChild(btnOk);
		modal.appendChild(msg);
		modal.appendChild(actions);
		overlay.appendChild(modal);
		document.body.appendChild(overlay);

		return { overlay, modal, msg, btnCancel, btnOk };
	}

	let modalEl;
	function ensureModal() {
		if (!modalEl) modalEl = buildModal();
		return modalEl;
	}

	function showConfirm(message) {
		return new Promise(resolve => {
			const m = ensureModal();
			m.msg.textContent = message;
			m.overlay.classList.add('active');
			document.body.style.overflow = 'hidden';

			function cleanup() {
				m.overlay.classList.remove('active');
				document.body.style.overflow = '';
				m.btnCancel.removeEventListener('click', onCancel);
				m.btnOk.removeEventListener('click', onOk);
			}

			function onCancel(e) { e.preventDefault(); cleanup(); resolve(false); }
			function onOk(e) { e.preventDefault(); cleanup(); resolve(true); }

			m.btnCancel.addEventListener('click', onCancel);
			m.btnOk.addEventListener('click', onOk);
		});
	}

	function submitFormWithButton(form, button) {
		// Preserve which submit button was used by adding a hidden input
		if (button && button.name) {
			const hidden = document.createElement('input');
			hidden.type = 'hidden';
			hidden.name = button.name;
			hidden.value = button.value || '1';
			form.appendChild(hidden);
			// Submit and then remove the hidden input after short delay
			form.submit();
			setTimeout(() => hidden.remove(), 1000);
		} else {
			form.submit();
		}
	}

	// Intercept form submit when data-confirm is present on the form
	document.addEventListener('submit', function(e) {
		const form = e.target;
		if (!(form instanceof HTMLFormElement)) return;
		const msg = form.getAttribute('data-confirm');
		if (!msg) return;
		e.preventDefault();
		showConfirm(msg).then(ok => {
			if (ok) form.submit();
		});
	}, true);

	// Intercept clicks on buttons/links with data-confirm
	document.addEventListener('click', function(e) {
		const btn = e.target.closest('button[data-confirm], a[data-confirm]');
		if (!btn) return;
		e.preventDefault();
		const msg = btn.getAttribute('data-confirm');
		if (!msg) return;
		showConfirm(msg).then(ok => {
			if (!ok) return;
			// If button is inside a form, submit preserving the button name
			const form = btn.closest('form');
			if (form && btn.tagName.toLowerCase() === 'button') {
				submitFormWithButton(form, btn);
				return;
			}
			// For links, follow href
			if (btn.tagName.toLowerCase() === 'a') {
				const href = btn.getAttribute('href');
				if (href) window.location.href = href;
			}
		});
	}, false);

})();

