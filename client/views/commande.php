<?php afficherMessagesFlash(); ?>

<div class="commande-wrapper">

    <!-- Retour -->
    <a href="?element=client&action=index" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i>
        Retour aux produits
    </a>

    <h1 class="page-title">Valider la <span>commande</span></h1>
    <p class="page-subtitle">Vérifiez votre panier puis renseignez vos informations.</p>

    <!-- Formulaire de validation -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-pen-to-square"></i>
                Vos informations
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="?element=client&action=commande" id="formCommande" novalidate>

                <!-- Champ téléphone -->
                <div class="form-group">
                    <label class="form-label" for="phone">
                        <i class="fa-solid fa-phone"></i>
                        Téléphone <span style="color:var(--red)">*</span>
                    </label>
                    <div class="phone-input-wrapper">
                        <input type="tel"
                               id="phone"
                               name="phone"
                               class="form-control"
                               placeholder="Ex. 0612345678 ou +33612345678"
                               autocomplete="tel"
                               required>
                        <!-- Icône de statut (✓ / ✗) affichée dynamiquement -->
                        <span class="phone-status" id="phoneStatus" aria-hidden="true"></span>
                    </div>
                    <!-- Message d'erreur inline -->
                    <span class="field-error" id="phoneError" role="alert"></span>
                    <span class="field-hint">
                        10 chiffres (ex. 0612345678) ou format international (ex. +33612345678)
                    </span>
                </div>

                <!-- Numéro de borne -->
                <div class="form-group">
                    <label class="form-label" for="num_borne">
                        <i class="fa-solid fa-desktop"></i>
                        Numéro de borne
                    </label>
                    <input type="number"
                           id="num_borne"
                           name="num_borne"
                           class="form-control"
                           placeholder="Ex. 3">
                </div>

                <div class="form-actions" style="margin-top: 24px;">
                    <a href="?element=client&action=index" class="btn btn-ghost">
                        Annuler
                    </a>
                    <button type="submit" id="submitBtn" class="btn btn-success btn-lg" disabled>
                        <i class="fa-solid fa-circle-check"></i>
                        Confirmer la commande
                    </button>
                </div>

            </form>
        </div>
    </div>

</div><!-- /commande-wrapper -->


<script>
/**
 * Validation du numéro de téléphone (côté client)
 *
 * Formats acceptés :
 *  - 10 chiffres locaux    : 0612345678  /  06 12 34 56 78
 *  - Format international  : +33612345678  /  +33 6 12 34 56 78
 *  - Indicatifs variés     : +1..., +44..., etc. (7–15 chiffres après le +)
 */

const phoneInput  = document.getElementById('phone');
const phoneError  = document.getElementById('phoneError');
const phoneStatus = document.getElementById('phoneStatus');
const submitBtn   = document.getElementById('submitBtn');

// Regex : numéro local 10 chiffres OU international +XX...
const PHONE_REGEX = /^(\+\d{7,15}|0\d{9})$/;

/**
 * Nettoie la saisie : supprime espaces, tirets et points
 * pour permettre "06 12 34 56 78" ou "06-12-34-56-78"
 */
function normaliserTelephone(valeur) {
    return valeur.replace(/[\s\-\.]/g, '');
}

/**
 * Valide le numéro et met à jour l'UI
 * @returns {boolean} true si valide
 */
function validerTelephone() {
    const brut      = phoneInput.value.trim();
    const normalise = normaliserTelephone(brut);
    const valide    = PHONE_REGEX.test(normalise);

    if (brut === '') {
        // Champ vide : état neutre
        setEtat('neutre', '');
    } else if (valide) {
        setEtat('ok', '');
    } else {
        setEtat('erreur', 'Numéro invalide. Exemples : 0612345678 ou +33612345678');
    }

    submitBtn.disabled = !valide;
    return valide;
}

/**
 * Met à jour les classes CSS et les messages selon l'état
 */
function setEtat(etat, message) {
    phoneInput.classList.remove('is-valid', 'is-invalid');
    phoneError.textContent = '';
    phoneStatus.textContent = '';
    phoneStatus.className = 'phone-status';

    if (etat === 'ok') {
        phoneInput.classList.add('is-valid');
        phoneStatus.textContent = '✓';
        phoneStatus.classList.add('status-ok');
    } else if (etat === 'erreur') {
        phoneInput.classList.add('is-invalid');
        phoneStatus.textContent = '✗';
        phoneStatus.classList.add('status-error');
        phoneError.textContent = message;
    }
}

// Validation en temps réel (à chaque frappe)
phoneInput.addEventListener('input', validerTelephone);

// Validation au départ du champ
phoneInput.addEventListener('blur', () => {
    if (phoneInput.value.trim() !== '') validerTelephone();
});

// Sécurité : bloquer la soumission si la validation JS échoue
document.getElementById('formCommande').addEventListener('submit', function(e) {
    if (!validerTelephone()) {
        e.preventDefault();
        phoneInput.focus();
    }
});
</script>
