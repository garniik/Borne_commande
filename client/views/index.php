<?php afficherMessagesFlash(); ?>

<div class="client-layout">

    <!-- ══════════════════════════════════════════════════
         COLONNE GAUCHE : filtres + grille produits
    ══════════════════════════════════════════════════ -->
    <div>

        <h1 class="page-title">Nos <span>Produits</span></h1>
        <p class="page-subtitle">Choisissez vos articles et ajoutez-les au panier.</p>


        <!-- Filtres catégorie -->
        <div class="cat-filters">
            <?php
            $cat_active = $_GET['categorie'] ?? '';
            $categories = [
                ''           => ['label' => 'Tout',       'icon' => 'fa-border-all'],
                'Soft'       => ['label' => 'Soft',       'icon' => 'fa-glass-water'],
                'Chaud' =>['label' => 'Boissons Chaudes', 'icon'=>'fa-mug-hot'],
                'Bière'     => ['label' => 'Bière',     'icon' => 'fa-wine-bottle'],
                'cocktail'   => ['label' => 'Cocktail',   'icon' => 'fa-martini-glass-citrus'],
                'Snack'      => ['label' => 'Snack',      'icon' => 'fa-cookie-bite'],
                'Pizza' => ['label' => 'Pizza', 'icon' => 'fa-utensils'],
            ];
            foreach ($categories as $id => $cat):
                $url = '?element=client&action=index' . ($id !== '' ? '&categorie=' . urlencode($id) . '&filtrer=1' : '');
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
                <p>Aucun produit disponible.</p>
            </div>
        <?php else: ?>
            <div class="produits-grid">
                <?php foreach ($donnee as $produit):
                    $stock     = (int)($produit['stock_affiche'] ?? 0);
                    $dispo     = $stock > 0;
                    $stockClass = !$dispo ? 'stock-out' : '';
                    $isPizza   = !empty($produit['pizza_indispo']);
                    $id_card   = 'prod-card-' . (int)$produit['id'];
                    $id_modal  = 'prod-modal-' . (int)$produit['id'];

                    // Badge stock
                    if ($stock === 0) {
                        if ($isPizza && !$pizzasDispo) {
                            $badgeClass = 'badge-blue';
                            $badgeLabel = 'Disponible Jeu-Ven-Sam 18h+';
                        } else {
                            $badgeClass = 'badge-red';
                            $badgeLabel = 'Rupture';
                        }
                    } elseif ($stock <= 3) {
                        $badgeClass = 'badge-yellow';
                        $badgeLabel = 'Bientôt épuisé';
                    } else {
                        $badgeClass = '';
                        $badgeLabel = '';
                    }

                    // Emoji
                    $icons = [
                        'Pizza'    => '🍕',
                        'Soft'     => '🥤',
                        'Boissons Chaudes' => '☕︎',
                        'Bière'   => '🍺',
                        'cocktail' => '🍹',
                        'Snack'    => '🍪',
                        'Pizza'=> '🍕',
                    ];
                    $emoji = $icons[$produit['categorie']] ?? '🛒';
                ?>
                <div class="prod-card <?= $stockClass ?>"
                     id="<?= $id_card ?>"
                     onclick="ouvrirModalProduit('<?= $id_modal ?>')"
                     role="button"
                     tabindex="0"
                     aria-label="Voir le détail de <?= htmlspecialchars($produit['nom']) ?>">

                    <!-- Image -->
                    <div class="prod-card-img">
                        <?php if (!empty($badgeLabel)): ?>
                            <span class="stock-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                        <?php endif; ?>
                        <?php if (!empty($produit['image'])): ?>
                            <img src="<?= htmlspecialchars('public/images/' . basename($produit['image'])) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" loading="lazy" decoding="async">
                        <?php else: ?>
                            <?= $emoji ?>
                        <?php endif; ?>
                    </div>

                    <!-- Infos produit -->
                    <div class="prod-card-body">
                        <div class="prod-card-name"><?= htmlspecialchars($produit['nom']) ?></div>
                        <div class="prod-card-price"><?= formaterPrix($produit['prix']) ?></div>
                        <?php if ($isPizza && !$pizzasDispo): ?>
                            <div class="prod-card-note" style="font-size: 0.75rem; color: var(--text-muted);">
                                <i class="fa-solid fa-clock"></i> Jeu-Ven-Sam 18h+
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <!-- Modal container -->
            <div id="prod-modal-container"></div>

            <!-- Données JSON -->
            <script>
            const produitsData = <?= json_encode(array_map(function($p) use ($donnee, $pizzasDispo) {
                $icons = [
                    'Pizza'    => '🍕',
                    'Soft'     => '🥤',
                    'Boissons Chaudes' => '☕︎',
                    'Bière'   => '🍺',
                    'cocktail' => '🍹',
                    'Snack'    => '🍪',
                    'Pizza'=> '🍕',
                ];
                $isPizza = !empty($p['pizza_indispo']);
                $stockAffiche = $isPizza && !$pizzasDispo ? 0 : (int)$p['stock'];
                
                return [
                    'id' => $p['id'],
                    'nom' => $p['nom'],
                    'prix' => $p['prix'],
                    'categorie' => $p['categorie'],
                    'stock' => $stockAffiche,
                    'stock_reel' => (int)$p['stock'],
                    'pizza_indispo' => $isPizza,
                    'pizzas_horaire' => $pizzasDispo,
                    'image' => !empty($p['image']) ? 'public/images/' . basename($p['image']) : null,
                    'description' => $p['description'] ?? 'Aucune description disponible.',
                    'emoji' => $icons[$p['categorie']] ?? '🛒'
                ];
            }, $donnee)) ?>;
            </script>

            <script>
            function ouvrirModalProduit(modalId) {
                const overlay = document.getElementById('prodOverlay');
                const container = document.getElementById('prod-modal-container');

                const id = parseInt(modalId.replace('prod-modal-', ''));
                const produit = produitsData.find(p => p.id == id);

                if (!produit) return;

                container.innerHTML = renderModalProduit(produit);

                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.offsetHeight;
                    modal.classList.add('open');
                }
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function renderModalProduit(p) {
                const dispo = p.stock > 0;
                const isPizzaIndispo = p.pizza_indispo && !p.pizzas_horaire;
                
                let stockLabel, stockBadge;
                if (p.stock === 0) {
                    stockLabel = 'Rupture de stock';
                    stockBadge = 'badge-red';
                } else if (p.stock <= 3) {
                    stockLabel = `Plus que ${p.stock} en stock !`;
                    stockBadge = 'badge-yellow';
                } else {
                    stockLabel = `${p.stock} en stock`;
                    stockBadge = 'badge-green';
                }

                const imageHtml = p.image
                    ? `<img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.nom)}">`
                    : `<div class="prod-modal-emoji">${p.emoji}</div>`;

                const pizzaNote = isPizzaIndispo 
                    ? `<div style="background: rgba(59,130,246,0.1); padding: 10px; border-radius: 8px; margin-bottom: 12px; font-size: 0.9rem; color: #3b82f6;"><i class="fa-solid fa-clock"></i> Les pizzas sont disponibles uniquement le Jeudi, Vendredi et Samedi à partir de 18h00.</div>`
                    : '';

                return `<div class="prod-modal" id="prod-modal-${p.id}" role="dialog" aria-modal="true" aria-label="Détail produit ${escapeHtml(p.nom)}" onclick="fermerModalProduit('prod-modal-${p.id}')">
                    <div class="prod-modal-box" onclick="event.stopPropagation()">
                        <div class="prod-modal-header">
                            <div class="prod-modal-title">
                                <i class="fa-solid fa-box-open"></i>
                                ${escapeHtml(p.nom)}
                            </div>
                            <button class="prod-modal-close" onclick="fermerModalProduit('prod-modal-${p.id}')" aria-label="Fermer">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="prod-modal-body">
                            <div class="prod-modal-img">
                                ${imageHtml}
                            </div>
                            <div class="prod-modal-infos">
                                ${pizzaNote}
                                <div class="prod-modal-cat">${escapeHtml(p.categorie)}</div>
                                <div class="prod-modal-price">${formaterPrix(p.prix)}</div>
                                <div class="prod-modal-stock">
                                    <span class="badge ${stockBadge}">${stockLabel}</span>
                                </div>
                                <div class="prod-modal-desc">${escapeHtml(p.description)}</div>
                            </div>
                        </div>
                        <div class="prod-modal-footer">
                            <button class="btn btn-ghost" onclick="fermerModalProduit('prod-modal-${p.id}')">
                                <i class="fa-solid fa-xmark"></i> Fermer
                            </button>
                            <form method="POST" action="" style="display:contents;" onsubmit="fermerModalProduit('prod-modal-${p.id}')">
                                <input type="hidden" name="action_panier" value="add">
                                <input type="hidden" name="id_produit" value="${p.id}">
                                <input type="number" name="quantite" value="1" min="1" max="${dispo ? p.stock_reel : 1}" class="qty-input" ${!dispo ? 'disabled' : ''}>
                                <button type="submit" class="btn btn-primary btn-lg" ${!dispo ? 'disabled' : ''}>
                                    <i class="fa-solid fa-cart-plus"></i>
                                    Ajouter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>`;
            }

            function formaterPrix(prix) {
                return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(prix);
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function fermerModalProduit(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) return;
                modal.classList.remove('open');
                document.getElementById('prodOverlay').classList.remove('active');
                document.body.style.overflow = '';
                setTimeout(() => {
                    const container = document.getElementById('prod-modal-container');
                    if (container) container.innerHTML = '';
                }, 150);
            }

            function fermerTousLesModalsProduit() {
                document.querySelectorAll('.prod-modal.open').forEach(m => m.classList.remove('open'));
                document.getElementById('prodOverlay').classList.remove('active');
                document.body.style.overflow = '';
                setTimeout(() => {
                    const container = document.getElementById('prod-modal-container');
                    if (container) container.innerHTML = '';
                }, 150);
            }

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') fermerTousLesModalsProduit();
            });
            </script>

            <!-- Overlay -->
            <div id="prodOverlay" class="prod-overlay" onclick="fermerTousLesModalsProduit()"></div>
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
                            <!-- Bouton supprimer -->
                            <form method="POST" action="" style="display:inline; margin-left: 8px;">
                                <input type="hidden" name="action_panier" value="remove">
                                <input type="hidden" name="id_produit" value="<?= (int)$ligne['produit']['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px; min-height: auto; font-size: 0.75rem;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Total + boutons -->
                <div class="panier-footer">
                    <div class="panier-total">
                        <span>Total</span>
                        <span class="panier-total-montant"><?= formaterPrix($total) ?></span>
                    </div>
                    
                    <!-- Bouton vider le panier -->
                    <form method="POST" action="" style="margin-bottom: 10px;">
                        <input type="hidden" name="action_panier" value="clear">
                        <button type="submit" class="btn btn-ghost btn-sm btn-full" onclick="return confirm('Vider tout le panier ?')">
                            <i class="fa-solid fa-trash-can"></i>
                            Vider le panier
                        </button>
                    </form>
                    
                    <a href="?element=client&action=commande&source=index" class="btn btn-success btn-full btn-lg">
                        <i class="fa-solid fa-circle-check"></i>
                        Valider la commande
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </aside>

</div><!-- /client-layout -->