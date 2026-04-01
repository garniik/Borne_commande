<?php
// ── Messages flash ───────────────────────────────────────────────
if (!empty($_SESSION['mesgs']['success'])) {
    foreach ($_SESSION['mesgs']['success'] as $msg): ?>
        <div style="background:#d4edda;color:#155724;padding:8px;margin-bottom:10px;"><?= $msg ?></div>
    <?php endforeach;
    unset($_SESSION['mesgs']['success']);
}
if (!empty($_SESSION['mesgs']['errors'])) {
    foreach ($_SESSION['mesgs']['errors'] as $err): ?>
        <div style="background:#f8d7da;color:#721c24;padding:8px;margin-bottom:10px;"><?= htmlspecialchars($err) ?></div>
    <?php endforeach;
    unset($_SESSION['mesgs']['errors']);
}
?>

<!-- ── Panier (toujours visible en haut) ───────────────────────── -->
<h2>Panier</h2>
<?php if (empty($details)): ?>
    <p>Panier vide.</p>
<?php else: ?>
    <ul style="list-style:none;padding:0;margin-bottom:12px;">
        <?php foreach ($details as $ligne): ?>
            <li style="border:1px solid #ddd;margin-bottom:4px;padding:6px;">
                <?= htmlspecialchars($ligne['produit']['nom']) ?> x<?= (int)$ligne['quantite'] ?> = <?= number_format((float)$ligne['produit']['prix'] * $ligne['quantite'], 2, ',', ' ') ?> €
            </li>
        <?php endforeach; ?>
    </ul>
    <p><strong>Total : <?= number_format($total, 2, ',', ' ') ?> €</strong></p>
    <p><a href="?element=client&action=commande">Valider la commande</a></p>
<?php endif; ?>

<hr>

<div class="dtitle w3-container w3-teal">
    Liste des produits
</div>

<div class="w3-container">
    <form method="get" action="">
        <!-- choix categorie -->
        <select name="categorie">
            <option value="">Toutes les catégories</option>
            <option value="1">Boisson</option>
            <option value="2">Snack</option>
            <option value="3">Nourriture</option>
        </select>
        <button type="submit">Filtrer</button>
    </form>
</div>

<div class="w3-container">
    <table class="w3-table-all">
        <thead>
            <tr class="w3-light-grey">
                <th>Nom</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Catégorie</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($donnee as $produit): ?>
            <tr>
                <td><?= htmlspecialchars($produit['nom']) ?></td>
                <td><?= number_format((float)$produit['prix'], 2, ',', ' ') ?> €</td>
                <td><?= (int)$produit['stock'] ?></td>
                <td><?= htmlspecialchars($produit['categorie']) ?></td>
                <td>
                    <form method="POST" action="?element=client&action=index" style="display:inline;">
                        <input type="hidden" name="action_panier" value="add">
                        <input type="hidden" name="id_produit" value="<?= (int)$produit['id'] ?>">
                        <input type="number" name="quantite" value="1" min="1" max="<?= (int)$produit['stock'] ?>" style="width:50px;">
                        <button type="submit">Ajouter</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


