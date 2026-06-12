
<div class="commande-wrapper">

    <!-- Retour -->
    <a href="?element=client&action=index" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i>
        Retour aux produits
    </a>

    <h1 class="page-title">Valider la <span>commande</span></h1>
    <p class="page-subtitle">Renseignez vos informations pour finaliser la commande.</p>

    <!-- ── Résumé du panier ─────────────────────────────── -->
    <?php
    $panier = $_SESSION['panier'] ?? [];
    $aUnProduitSeul = false;
    $detailsPanier = [];
    $total = 0;

    if (!empty($panier)) {
        foreach ($panier as $id_produit => $quantite) {
            $row = Produits::findById($db, (int)$id_produit);
            if ($row) {
                $detailsPanier[] = ['produit' => $row, 'quantite' => $quantite];
                $total += $row['prix'] * $quantite;
                if (($row['seul'] ?? 0) == 1) {
                    $aUnProduitSeul = true;
                }
            }
        }
    }
    ?>

    <?php if (!empty($detailsPanier)): ?>
    <div class="card card-compact">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-basket-shopping"></i>
                Résumé de votre panier
            </div>
        </div>
        <div class="card-body">
            <ul class="panier-liste panier-liste-compact">
                <?php foreach ($detailsPanier as $ligne): ?>
                    <li class="panier-item">
                        <span class="panier-nom"><?= htmlspecialchars($ligne['produit']['nom']) ?></span>
                        <span class="panier-qty">×<?= (int)$ligne['quantite'] ?></span>
                        <span class="panier-prix"><?= formaterPrix($ligne['produit']['prix'] * $ligne['quantite']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="panier-total-row">
                <span>Total</span>
                <span class="panier-total-montant"><?= formaterPrix($total) ?></span>
            </div>

            <!-- ── Indicateur commandabilité ───────────────── -->
            <div class="panier-validity <?= $aUnProduitSeul ? 'valid' : 'invalid' ?>">
                <i class="fa-solid <?= $aUnProduitSeul ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
                <span>
                    <?php if ($aUnProduitSeul): ?>
                        Commande possible depuis cette borne
                    <?php else: ?>
                        Veuillez passer au bar pour commander
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formulaire de validation -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-pen-to-square"></i>
                valider la commande <!-- Veuillez choisir un numero de Borne -->
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="?element=client&action=commande">
                <!-- Page source pour redirection -->
                <input type="hidden" name="source" value="<?= htmlspecialchars($_GET['source'] ?? 'index') ?>">
                
                <!-- Numéro de borne -->

                <!-- <div class="form-group">
                    <label class="form-label" for="num_borne">
                        <i class="fa-solid fa-desktop"></i>
                        Numéro de borne
                        <span class="form-hint">(1 à 24)</span>
                    </label>
                    <select id="num_borne"
                            name="num_borne"
                            class="form-control"
                            required>
                        <option value="">— Choisir une borne —</option>
                        <?php for ($i = 1; $i <= 24; $i++): ?>
                            <?php $est_utilisee = in_array($i, $bornes_utilisees); ?>
                            <option value="<?= $i ?>" <?= $est_utilisee ? 'disabled' : '' ?>>
                                Borne <?= $i ?><?= $est_utilisee ? ' (occupée)' : '' ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <?php if (!empty($bornes_utilisees)): ?>
                        <small class="form-help-text">
                            <i class="fa-solid fa-circle-info"></i>
                            Les bornes marquées "occupées" sont déjà utilisées par une commande en cours.
                        </small>
                    <?php endif; ?>
                </div> -->

                <div class="form-actions form-actions-spaced">
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
