<?php
// ── Affichage des messages flash ───────────────────────────────────────────────
if (!empty($_SESSION['mesgs']['success'])) {
    foreach ($_SESSION['mesgs']['success'] as $msg): ?>
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= $msg ?></div>
    <?php endforeach;
    unset($_SESSION['mesgs']['success']);
}
if (!empty($_SESSION['mesgs']['errors'])) {
    foreach ($_SESSION['mesgs']['errors'] as $err): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($err) ?></div>
    <?php endforeach;
    unset($_SESSION['mesgs']['errors']);
}
?>
 
<div class="prod-page">
 
  <!-- ── En-tête page ───────────────────────────────────────────── -->
  <div class="prod-header">
    <div>
      <h1><i class="fa-solid fa-boxes-stacked" style="color:var(--accent);font-size:1.4rem;vertical-align:-2px;margin-right:6px;"></i>Gestion des <span>Produits</span></h1>
      <p>Visualisez et gérez l'ensemble de vos produits en stock.</p>
    </div>
    <button class="btn-add" onclick="toggleForm()">
      <i class="fa-solid fa-plus"></i> Ajouter un produit
    </button>
  </div>
 
  <!-- ── Statistiques rapides ───────────────────────────────────── -->
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="fa-solid fa-tag"></i></div>
      <div>
        <div class="stat-label">Produits</div>
        <div class="stat-value"><?= $total_produits ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green"><i class="fa-solid fa-cubes"></i></div>
      <div>
        <div class="stat-label">Total en stock</div>
        <div class="stat-value"><?= number_format($total_stock, 0, ',', ' ') ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon amber"><i class="fa-solid fa-euro-sign"></i></div>
      <div>
        <div class="stat-label">Valeur du stock</div>
        <div class="stat-value"><?= number_format($valeur_stock, 2, ',', ' ') ?> €</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#fdf2f8;color:#9333ea;"><i class="fa-solid fa-layer-group"></i></div>
      <div>
        <div class="stat-label">Catégories</div>
        <div class="stat-value"><?= count($categories) ?></div>
      </div>
    </div>
  </div>
 
  <!-- ── Formulaire d'ajout (collapsible) ───────────────────────── -->
  <div class="panel" id="form-panel">
    <div class="panel-header">
      <span class="panel-title"><i class="fa-solid fa-circle-plus"></i> Nouveau produit</span>
      <button class="btn-cancel-form" onclick="toggleForm()" style="padding:6px 12px;font-size:.8rem;">
        <i class="fa-solid fa-xmark"></i> Fermer
      </button>
    </div>
    <div class="panel-body form-collapse" id="form-collapse">
      <form method="POST" action="index.php?element=admin&action=produits">
        <input type="hidden" name="action_produit" value="add">
        <div class="form-grid">
 
          <div class="form-group">
            <label for="nom"><i class="fa-solid fa-pen"></i> Nom *</label>
            <input type="text" id="nom" name="nom" placeholder="Ex: Burger Classic" required
                   value="<?= htmlspecialchars(GETPOST('nom') ?? '') ?>">
          </div>
 
          <div class="form-group">
            <label for="categorie"><i class="fa-solid fa-folder"></i> Catégorie *</label>
            <input type="text" id="categorie" name="categorie" placeholder="Ex: Burgers, Boissons…" required
                   value="<?= htmlspecialchars(GETPOST('categorie') ?? '') ?>"
                   list="categories-list">
            <datalist id="categories-list">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>">
              <?php endforeach; ?>
            </datalist>
          </div>
 
          <div class="form-group">
            <label for="prix"><i class="fa-solid fa-euro-sign"></i> Prix (€) *</label>
            <input type="number" id="prix" name="prix" placeholder="0.00" step="0.01" min="0" required
                   value="<?= htmlspecialchars(GETPOST('prix') ?? '') ?>">
          </div>
 
          <div class="form-group">
            <label for="stock"><i class="fa-solid fa-cubes"></i> Stock *</label>
            <input type="number" id="stock" name="stock" placeholder="0" min="0" step="1" required
                   value="<?= htmlspecialchars(GETPOST('stock') ?? '') ?>">
          </div>
 
          <div class="form-group form-full">
            <label for="description"><i class="fa-solid fa-align-left"></i> Description</label>
            <textarea id="description" name="description" rows="3" placeholder="Description du produit…"><?= htmlspecialchars(GETPOST('description') ?? '') ?></textarea>
          </div>
 
          <div class="form-group form-full">
            <label for="image"><i class="fa-solid fa-image"></i> URL de l'image</label>
            <input type="url" id="image" name="image" placeholder="https://…"
                   value="<?= htmlspecialchars(GETPOST('image') ?? '') ?>">
          </div>
 
        </div>
        <div class="form-actions">
          <button type="button" class="btn-cancel-form" onclick="toggleForm()">Annuler</button>
          <button type="submit" class="btn-submit">
            <i class="fa-solid fa-floppy-disk"></i> Enregistrer le produit
          </button>
        </div>
      </form>
    </div>
  </div>
 
  <!-- ── Tableau des produits ───────────────────────────────────── -->
  <div class="panel">
    <div class="panel-header">
      <span class="panel-title"><i class="fa-solid fa-list"></i> Stock produits</span>
      <!-- Barre de recherche -->
      <form method="GET" action="index.php" class="filter-bar">
        <input type="hidden" name="element" value="admin">
        <input type="hidden" name="action"  value="produits">
        <div class="filter-group">
          <label>Recherche nom</label>
          <input type="text" name="search_nom" placeholder="Nom…" value="<?= htmlspecialchars(GETPOST('search_nom') ?? '') ?>">
        </div>
        <div class="filter-group">
          <label>Catégorie</label>
          <select name="search_categorie">
            <option value="">Toutes</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat) ?>" <?= (GETPOST('search_categorie') === $cat) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass"></i> Filtrer</button>
        <a href="index.php?element=admin&action=produits" class="btn-reset"><i class="fa-solid fa-rotate-left"></i></a>
      </form>
    </div>
 
    <div style="overflow-x:auto;">
      <?php if (empty($produits)): ?>
        <div class="empty-state">
          <i class="fa-solid fa-box-open"></i>
          <p>Aucun produit trouvé. Commencez par en ajouter un !</p>
        </div>
      <?php else: ?>
        <table class="prod-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Produit</th>
              <th>Catégorie</th>
              <th>Prix</th>
              <th>Stock</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($produits as $i => $p): ?>
              <?php
                $stock = (int)$p['stock'];
                if ($stock === 0)    { $badge = 'none'; $icon = 'fa-circle-xmark';    $label = 'Rupture'; }
                elseif ($stock <= 5) { $badge = 'low';  $icon = 'fa-triangle-exclamation'; $label = $stock . ' restant' . ($stock > 1 ? 's' : ''); }
                else                 { $badge = 'ok';   $icon = 'fa-circle-check';    $label = $stock . ' en stock'; }
              ?>
              <tr>
                <td style="color:var(--muted);font-size:.78rem;"><?= (int)$p['id'] ?></td>
                <td>
                  <div class="prod-name-cell">
                    <div class="prod-thumb">
                      <?php if (!empty($p['image'])): ?>
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="" onerror="this.parentElement.innerHTML='<i class=\'fa-solid fa-image\'></i>'">
                      <?php else: ?>
                        <i class="fa-solid fa-image"></i>
                      <?php endif; ?>
                    </div>
                    <div>
                      <div class="prod-name"><?= htmlspecialchars($p['nom']) ?></div>
                      <?php if (!empty($p['description'])): ?>
                        <div class="prod-desc" title="<?= htmlspecialchars($p['description']) ?>"><?= htmlspecialchars($p['description']) ?></div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td><span class="badge-cat"><?= htmlspecialchars($p['categorie']) ?></span></td>
                <td class="prix-cell"><?= number_format((float)$p['prix'], 2, ',', ' ') ?> €</td>
                <td>
                  <span class="badge-stock <?= $badge ?>">
                    <i class="fa-solid <?= $icon ?>"></i>
                    <?= $label ?>
                  </span>
                </td>
                <td>
                  <a href="index.php?element=admin&action=produits&action_produit=delete&id=<?= (int)$p['id'] ?>"
                     class="btn-sm btn-danger"
                     onclick="return confirm('Supprimer le produit &laquo;<?= addslashes(htmlspecialchars($p['nom'])) ?>&raquo; ?')">
                    <i class="fa-solid fa-trash"></i> Supprimer
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
 
</div>