<?php afficherMessagesFlash(); ?>

<!-- En-tête de page -->
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:28px;">
    <div>
        <h1 class="page-title" style="margin-bottom:4px;">
            <span>Gestion</span> des produits
        </h1>
        <p class="page-subtitle" style="margin-bottom:0;">
            <?= count($donnee ?? []) ?> produit<?= count($donnee ?? []) > 1 ? 's' : '' ?> enregistré<?= count($donnee ?? []) > 1 ? 's' : '' ?>
        </p>
    </div>
    <button class="btn btn-primary" onclick="toggleForm()">
        <i class="fa-solid fa-plus"></i>
        Nouveau produit
    </button>
</div>


<!-- ══════════════════════════════════════════════════════
     FORMULAIRE D'AJOUT (masqué / déplié au clic)
══════════════════════════════════════════════════════ -->
<div id="formAjout" class="card collapse-panel" style="margin-bottom: 28px;">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-box-open"></i>
            Ajouter un produit
        </div>
        <button class="btn btn-ghost btn-sm" onclick="toggleForm()">
            <i class="fa-solid fa-xmark"></i>
            Fermer
        </button>
    </div>
    <div class="card-body">
        <form method="post">
            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label" for="nom">Nom du produit</label>
                    <input type="text" id="nom" name="nom" class="form-control" placeholder="Ex. Coca-Cola" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="categorie">Catégorie</label>
                    <select id="categorie" name="categorie" class="form-control" required>
                        <option value="">— Choisir —</option>
                        <option value="Boisson">Boisson</option>
                        <option value="Snack">Snack</option>
                        <option value="Nourriture">Nourriture</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="prix">Prix (€)</label>
                    <input type="number" id="prix" name="prix" class="form-control"
                           placeholder="0.00" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="stock">Stock initial</label>
                    <input type="number" id="stock" name="stock" class="form-control"
                           placeholder="0" min="0" required>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control"
                              placeholder="Description courte du produit…"></textarea>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="image">URL de l'image</label>
                    <input type="url" id="image" name="image(URL)" class="form-control"
                           placeholder="https://exemple.com/image.jpg">
                </div>

            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-ghost" onclick="toggleForm()">Annuler</button>
                <button type="submit" name="add" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Ajouter le produit
                </button>
            </div>
        </form>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════
     TABLEAU DES PRODUITS
══════════════════════════════════════════════════════ -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-list"></i>
            Liste des produits
        </div>
    </div>

    <?php if (!empty($donnee)): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Ajouter stock</th>
                        <th>Définir stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnee as $produit):
                        $stock = (int)$produit['stock'];
                        if ($stock === 0)     { $sc = 'badge-red';    $sl = 'Rupture'; }
                        elseif ($stock <= 3)  { $sc = 'badge-yellow'; $sl = $stock . ' restant(s)'; }
                        else                 { $sc = 'badge-green';  $sl = $stock; }
                    ?>
                        <tr>
                            <!-- Nom + thumbnail -->
                            <td>
                                <div class="prod-cell">
                                    <div class="prod-thumb">
                                        <?php if (!empty($produit['image'])): ?>
                                            <img src="<?= htmlspecialchars($produit['image']) ?>"
                                                 alt="<?= htmlspecialchars($produit['nom']) ?>"
                                                 loading="lazy">
                                        <?php else: ?>
                                            <i class="fa-solid fa-image" style="color:var(--text-muted);font-size:.9rem;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="prod-cell-name"><?= htmlspecialchars($produit['nom']) ?></div>
                                        <?php if (!empty($produit['description'])): ?>
                                            <div class="prod-cell-desc"><?= htmlspecialchars(mb_strimwidth($produit['description'], 0, 50, '…')) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <!-- Catégorie -->
                            <td>
                                <span class="badge badge-orange"><?= htmlspecialchars($produit['categorie']) ?></span>
                            </td>

                            <!-- Prix -->
                            <td class="prix-cell"><?= formaterPrix($produit['prix']) ?></td>

                            <!-- Stock -->
                            <td>
                                <span class="badge <?= $sc ?>"><?= $sl ?></span>
                            </td>

                            <!-- Ajouter du stock -->
                            <td>
                                <form method="post" class="stock-form">
                                    <input type="hidden" name="id" value="<?= (int)$produit['id'] ?>">
                                    <input type="number" name="quantite"
                                           class="form-control"
                                           placeholder="Qté"
                                           style="width:75px; min-height:38px; padding:6px 10px; font-size:.85rem;">
                                    <button type="submit" name="add_stock" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-plus"></i>
                                        Ajouter
                                    </button>
                                </form>
                            </td>

                            <!-- Définir le stock -->
                            <td>
                                <form method="post" class="stock-form">
                                    <input type="hidden" name="id" value="<?= (int)$produit['id'] ?>">
                                    <input type="number" name="quantite"
                                           class="form-control"
                                           placeholder="Stock"
                                           min="0"
                                           style="width:75px; min-height:38px; padding:6px 10px; font-size:.85rem;">
                                    <button type="submit" name="set_stock" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-pen"></i>
                                        Définir
                                    </button>
                                </form>
                            </td>

                            <!-- Supprimer -->
                            <td>
                                <form method="post" onsubmit="return confirm('Supprimer ce produit ?')">
                                    <input type="hidden" name="id" value="<?= (int)$produit['id'] ?>">
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-box-open"></i>
            <p>Aucun produit enregistré. Ajoutez votre premier produit ci-dessus.</p>
        </div>
    <?php endif; ?>

</div>


<script>
function toggleForm() {
    const panel = document.getElementById('formAjout');
    panel.classList.toggle('open');
    // Scroll vers le formulaire si on l'ouvre
    if (panel.classList.contains('open')) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>
