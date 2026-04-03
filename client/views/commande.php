<?php afficherMessagesFlash(); ?>

<div class="commande-wrapper">

    <!-- Retour -->
    <a href="?element=client&action=index" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i>
        Retour aux produits
    </a>

    <h1 class="page-title">Valider la <span>commande</span></h1>
    <p class="page-subtitle">Vérifiez votre panier puis renseignez vos informations.</p>

    <!-- Récap du panier (si disponible) -->
    <?php
    $panier  = $_SESSION['panier'] ?? [];
    ?>
    <?php if (!empty($panier)): ?>
        <!-- NOTE : le controller client/index calcule déjà $details et $total.
             Ici on les réaffiche en récap lecture-seule. -->
    <?php endif; ?>

    <!-- Formulaire de validation -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-pen-to-square"></i>
                Vos informations
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="?element=client&action=commande">

                <div class="form-group">
                    <label class="form-label" for="phone">
                        <i class="fa-solid fa-phone"></i>
                        Téléphone <span style="color:var(--red)">*</span>
                    </label>
                    <input type="tel"
                           id="phone"
                           name="phone"
                           class="form-control"
                           placeholder="06 12 34 56 78"
                           required>
                </div>

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
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fa-solid fa-circle-check"></i>
                        Confirmer la commande
                    </button>
                </div>

            </form>
        </div>
    </div>

</div><!-- /commande-wrapper -->
