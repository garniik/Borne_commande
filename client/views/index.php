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
                        <?php if (!empty($produit['image'])): ?>
                            <img src="<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" loading="lazy">
                        <?php else: ?>
                            <?= $emoji ?>
                        <?php endif; ?>
                    </div>

                    <!-- Infos produit -->
                    <div class="prod-card-body">
                        <div class="prod-card-cat"><?= htmlspecialchars($produit['categorie']) ?></div>
                        <div class="prod-card-name"><?= htmlspecialchars($produit['nom']) ?></div>
                        <div class="prod-card-price"><?= formaterPrix($produit['prix']) ?></div>
                    </div>

                    <!-- Actions -->
                    <div class="prod-card-footer">
                        <button type="button" class="btn btn-ghost btn-sm" onclick="openProductModal(<?= $produit['id'] ?>)" style="padding: 0 12px;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        
                        <form method="POST" action="" style="display:contents; flex: 1;">
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

                <!-- Modal pour ce produit -->
                <div id="modal-<?= $produit['id'] ?>" class="product-modal">
                    <div class="modal-content">
                        <button class="modal-close" onclick="closeProductModal(<?= $produit['id'] ?>)">&times;</button>
                        
                        <div class="modal-body">
                            <div class="modal-image">
                                <?php if (!empty($produit['image'])): ?>
                                    <img src="<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                                <?php else: ?>
                                    <div class="modal-emoji"><?= $emoji ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="modal-info">
                                <div class="modal-cat"><?= htmlspecialchars($produit['categorie']) ?></div>
                                <h2 class="modal-name"><?= htmlspecialchars($produit['nom']) ?></h2>
                                <div class="modal-price"><?= formaterPrix($produit['prix']) ?></div>
                                
                                <?php if (!empty($produit['description'])): ?>
                                    <div class="modal-description">
                                        <?= nl2br(htmlspecialchars($produit['description'])) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="modal-stock">
                                    <?php if ($stock === 0): ?>
                                        <span class="badge badge-red">Rupture de stock</span>
                                    <?php elseif ($stock <= 3): ?>
                                        <span class="badge badge-yellow"><?= $stock ?> en stock</span>
                                    <?php else: ?>
                                        <span class="badge badge-green">En stock</span>
                                    <?php endif; ?>
                                </div>
                                
                                <form method="POST" action="" class="modal-form">
                                    <input type="hidden" name="action_panier" value="add">
                                    <input type="hidden" name="id_produit" value="<?= (int)$produit['id'] ?>">
                                    <?php if (isset($_GET['categorie'])): ?>
                                        <input type="hidden" name="categorie" value="<?= htmlspecialchars($_GET['categorie']) ?>">
                                    <?php endif; ?>
                                    
                                    <div class="modal-qty">
                                        <label>Quantité:</label>
                                        <input type="number"
                                               name="quantite"
                                               value="1"
                                               min="1"
                                               max="<?= $stock ?>"
                                               class="qty-input"
                                               <?= !$dispo ? 'disabled' : '' ?>>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-full btn-lg" <?= !$dispo ? 'disabled' : '' ?>>
                                        <i class="fa-solid fa-cart-plus"></i>
                                        Ajouter au panier
                                    </button>
                                </form>
                            </div>
                        </div>
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

<script>
function openProductModal(productId) {
    const modal = document.getElementById('modal-' + productId);
    if (modal) {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}

function closeProductModal(productId) {
    const modal = document.getElementById('modal-' + productId);
    if (modal) {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }
}

// Fermer la modal en cliquant en dehors
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('product-modal') && e.target.classList.contains('open')) {
        e.target.classList.remove('open');
        document.body.style.overflow = '';
    }
});

// Fermer avec la touche Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.product-modal.open');
        openModals.forEach(function(modal) {
            modal.classList.remove('open');
        });
        document.body.style.overflow = '';
    }
});
</script>
