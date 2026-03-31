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

<h1>Gestion des produits</h1>

<!-- ── Formulaire d'ajout ───────────────────────── -->
<form method="POST" action="index.php?element=admin&action=produits">
    <input type="hidden" name="action_produit" value="add">
    <label>Nom *<br><input type="text" name="nom" placeholder="Nom du produit" required value="<?= htmlspecialchars(GETPOST('nom') ?? '') ?>"></label><br><br>
    <label>Catégorie *<br><input type="text" name="categorie" placeholder="Ex: Burgers, Boissons…" required value="<?= htmlspecialchars(GETPOST('categorie') ?? '') ?>"></label><br><br>
    <label>Prix (€) *<br><input type="number" name="prix" step="0.01" min="0" required value="<?= htmlspecialchars(GETPOST('prix') ?? '') ?>"></label><br><br>
    <label>Stock *<br><input type="number" name="stock" min="0" required value="<?= htmlspecialchars(GETPOST('stock') ?? '') ?>"></label><br><br>
    <label>Description<br><textarea name="description" rows="3"><?= htmlspecialchars(GETPOST('description') ?? '') ?></textarea></label><br><br>
    <label>Image (URL)<br><input type="url" name="image" placeholder="https://…" value="<?= htmlspecialchars(GETPOST('image') ?? '') ?>"></label><br><br>
    <button type="submit">Ajouter</button>
</form>

<hr>

<!-- ── Recherche / filtre ───────────────────────── -->
<form method="GET" action="index.php">
    <input type="hidden" name="element" value="admin">
    <input type="hidden" name="action" value="produits">
    <label>Nom : <input type="text" name="search_nom" value="<?= htmlspecialchars(GETPOST('search_nom') ?? '') ?>"></label>
    <label>Catégorie : <select name="search_categorie">
        <option value="">Toutes</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= (GETPOST('search_categorie') === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
    </select></label>
    <button type="submit">Filtrer</button>
    <a href="index.php?element=admin&action=produits">Réinitialiser</a>
</form>

<hr>

<!-- ── Tableau des produits ───────────────────────── -->
<?php if (empty($produits)): ?>
    <p>Aucun produit trouvé.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $p): ?>
                <tr>
                    <td><?= (int)$p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nom']) ?></td>
                    <td><?= htmlspecialchars($p['categorie']) ?></td>
                    <td><?= number_format((float)$p['prix'], 2, ',', ' ') ?> €</td>
                    <td><?= (int)$p['stock'] ?></td>
                    <td>
                        <a href="index.php?element=admin&action=produits&action_produit=delete&id=<?= (int)$p['id'] ?>"
                           onclick="return confirm('Supprimer <?= addslashes(htmlspecialchars($p['nom'])) ?> ?')">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
