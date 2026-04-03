<?php afficherMessagesFlash(); ?>

<div class="client-layout">

    <!-- ══════════════════════════════════════════════════
         COLONNE GAUCHE : filtres + grille produits
    ══════════════════════════════════════════════════ -->
    <div>

        <h1 class="page-title">Nos <span>Produits</span></h1>
        <p class="page-subtitle">Choisissez vos articles et ajoutez-les au panier.</p>

        <!-- Filtres catégorie (boutons pilule) -->
        <div class="cat-filters">
            <?php
            $cat_active = $_GET['categorie'] ?? '';
            $categories = [
                ''  => ['label' => 'Tout',      'icon' => 'fa-border-all'],
                '1' => ['label' => 'Boisson',   'icon' => 'fa-wine-glass'],
                '2' => ['label' => 'Snack',     'icon' => 'fa-cookie-bite'],
                '3' => ['label' => 'Nourriture','icon' => 'fa-utensils'],
            ];
            foreach ($categories as $id => $cat):
                $url = '?element=client&action=index' . ($id !== '' ? '&categorie=' . $id . '&filtrer=1' : '');
                $actif = ($cat_active === $id) ? 'active' : '';
            ?>
                <a href="<?= $url ?>" class="cat-btn <?= $actif ?>">
                    <i class="fa-solid <?= $cat['icon'] ?>"></i>
                    <?= $cat['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Grille de cards produits -->
        <?php if (empty($donnee)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-face-sad-tear"></i>
                <p>Aucun produit disponible dans cette catégorie.</p>
            </div>
        <?php else: ?>
            <div class="produits-grid">
                <?php foreach ($donnee as $produit):
                    $stock     = (int)$produit['stock'];
                    $dispo     = $stock > 0;
                    $stockClass = !$dispo ? 'stock-out' : '';

                    // Badge stock
                    if ($stock === 0)      { $badgeClass = 'badge-red';    $badgeLabel = 'Rupture'; }
                    elseif ($stock <= 3)   { $badgeClass = 'badge-yellow'; $badgeLabel = 'Bientôt épuisé'; }
                    else                   { $badgeClass = ''; $badgeLabel = ''; }

                    // Emoji icône si pas d'image
                    $icons = ['Boisson' => '🥤', 'Snack' => '🍪', 'Nourriture' => '🍔'];
                    $emoji = $icons[$produit['categorie']] ?? '🛒';
                ?>
                <div class="prod-card <?= $stockClass ?>">

                    <!-- Image Url -->
                    <div class="prod-card-img">
                        <img src="<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" loading="lazy">
                    </div>

                    <!-- Infos produit -->
                    <div class="prod-card-body">
                        <div class="prod-card-cat"><?= htmlspecialchars($produit['categorie']) ?></div>
                        <div class="prod-card-name"><?= htmlspecialchars($produit['nom']) ?></div>
                        <div class="prod-card-price"><?= formaterPrix($produit['prix']) ?></div>
                    </div>

                    <!-- Action ajouter au panier -->
                    <div class="prod-card-footer">
                        <form method="POST" action="" style="display:contents;">
                            <input type="hidden" name="action_panier" value="add">
                            <input type="hidden" name="id_produit"    value="<?= (int)$produit['id'] ?>">
                            <?php if (isset($_GET['categorie'])): ?>
                                <input type="hidden" name="categorie" value="<?= htmlspecialchars($_GET['categorie']) ?>">
                            <?php endif; ?>

                            <input type="number"
                                   name="quantite"
                                   value="1"
                                   min="1"
                                   max="<?= $stock ?>"
                                   class="qty-input"
                                   <?= !$dispo ? 'disabled' : '' ?>>

                            <button type="submit" class="btn btn-primary" <?= !$dispo ? 'disabled' : '' ?>>
                                <i class="fa-solid fa-cart-plus"></i>
                                Ajouter
                            </button>
                        </form>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div><!-- /colonne gauche -->


    <!-- ══════════════════════════════════════════════════
         COLONNE DROITE : panier sticky
    ══════════════════════════════════════════════════ -->
    <aside class="panier-sidebar">
        <div class="panier-card">

            <!-- En-tête orange -->
            <div class="panier-header">
                <div class="panier-header-title">
                    <i class="fa-solid fa-basket-shopping"></i>
                    Mon panier
                </div>
                <?php if (!empty($details)): ?>
                    <span class="panier-count"><?= count($details) ?></span>
                <?php endif; ?>
            </div>

            <?php if (empty($details)): ?>
                <!-- Panier vide -->
                <div class="panier-empty">
                    <i class="fa-solid fa-basket-shopping"></i>
                    Votre panier est vide
                </div>

            <?php else: ?>
                <!-- Liste des articles -->
                <ul class="panier-liste">
                    <?php foreach ($details as $ligne): ?>
                        <li class="panier-item">
                            <span class="panier-item-name">
                                <?= htmlspecialchars($ligne['produit']['nom']) ?>
                            </span>
                            <span class="panier-item-qty">×<?= (int)$ligne['quantite'] ?></span>
                            <span class="panier-item-prix">
                                <?= formaterPrix($ligne['produit']['prix'] * $ligne['quantite']) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Total + bouton valider -->
                <div class="panier-footer">
                    <div class="panier-total">
                        <span>Total</span>
                        <span class="panier-total-montant"><?= formaterPrix($total) ?></span>
                    </div>
                    <a href="?element=client&action=commande" class="btn btn-success btn-full btn-lg">
                        <i class="fa-solid fa-circle-check"></i>
                        Valider la commande
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </aside>

</div><!-- /client-layout -->
