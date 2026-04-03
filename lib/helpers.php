<?php

/**
 * Affiche les messages flash de succès et d'erreur
 */
function afficherMessagesFlash() {
    if (!empty($_SESSION['mesgs']['success'])) {
        foreach ($_SESSION['mesgs']['success'] as $msg): ?>
            <div class="alert success">
                <?= $msg ?>
            </div>
        <?php endforeach;
        unset($_SESSION['mesgs']['success']);
    }
    
    if (!empty($_SESSION['mesgs']['errors'])) {
        foreach ($_SESSION['mesgs']['errors'] as $err): ?>
            <div class="alert error">
                <?= htmlspecialchars($err) ?>
            </div>
        <?php endforeach;
        unset($_SESSION['mesgs']['errors']);
    }
}

/**
 * Formate un prix en euros
 */
function formaterPrix($prix) {
    return number_format((float)$prix, 2, ',', ' ') . ' €';
}

/**
 * Affiche une option de sélection de catégorie
 */
function afficherOptionCategorie($valeur, $libelle, $selectionnee = '') {
    $selected = ($selectionnee === $valeur) ? 'selected' : '';
    echo "<option value=\"$valeur\" $selected>$libelle</option>";
}

/**
 * Génère les attributs pour une option sélectionnée
 */
function estSelectionne($valeurAttendue, $valeurActuelle) {
    return ($valeurAttendue === $valeurActuelle) ? 'selected' : '';
}
