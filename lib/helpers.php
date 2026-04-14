<?php

/**
 * Affiche les messages flash sous forme de toasts (popup temporaire)
 */
function afficherMessagesFlash() {
    $hasMessages = !empty($_SESSION['mesgs']['success']) || !empty($_SESSION['mesgs']['errors']);
    if (!$hasMessages) return;
    ?>
    <div id="toast-container"></div>
    <script>
    (function() {
        const messages = [];
        <?php
        if (!empty($_SESSION['mesgs']['success'])) {
            foreach ($_SESSION['mesgs']['success'] as $msg) {
                echo "messages.push({type:'success', text:" . json_encode($msg) . "});\n";
            }
            unset($_SESSION['mesgs']['success']);
        }
        if (!empty($_SESSION['mesgs']['errors'])) {
            foreach ($_SESSION['mesgs']['errors'] as $err) {
                echo "messages.push({type:'error', text:" . json_encode($err) . "});\n";
            }
            unset($_SESSION['mesgs']['errors']);
        }
        ?>

        const container = document.getElementById('toast-container');
        messages.forEach(function(msg, i) {
            setTimeout(function() {
                const toast = document.createElement('div');
                toast.className = 'toast toast-' + msg.type;
                toast.innerHTML =
                    '<i class="fa-solid ' + (msg.type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark') + '"></i>' +
                    '<span>' + msg.text + '</span>' +
                    '<button class="toast-close" onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>';
                container.appendChild(toast);

                requestAnimationFrame(function() {
                    requestAnimationFrame(function() { toast.classList.add('toast-show'); });
                });

                setTimeout(function() {
                    toast.classList.remove('toast-show');
                    setTimeout(function() { toast.remove(); }, 350);
                }, 3500);
            }, i * 200);
        });
    })();
    </script>
    <?php
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
