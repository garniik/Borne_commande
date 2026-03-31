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
    <p><a href="?element=client&action=index&valider=1">Valider la commande</a></p>
<?php endif; ?>

<hr>

<?php if (GETPOST('valider')): ?>
    <h2>Commande validée</h2>
    <p>Merci ! Votre commande a été enregistrée.</p>
    <?php unset($_SESSION['panier']); ?>
    <p><a href="?element=client&action=index">← Retour à la liste</a></p>
<?php elseif ($selected): ?>
    <!-- ── Card du produit sélectionné ───────────────────────── -->
    <h2>Fiche produit</h2>
    <div style="border:1px solid #ddd;padding:12px;max-width:400px;">
        <h3><?= htmlspecialchars($selected['nom']) ?></h3>
        <p>Catégorie : <?= htmlspecialchars($selected['categorie']) ?></p>
        <p><?= htmlspecialchars($selected['description'] ?? 'Pas de description') ?></p>
        <p><strong><?= number_format((float)$selected['prix'], 2, ',', ' ') ?> €</strong></p>
        <p>Stock : <?= (int)$selected['stock'] ?></p>
        <?php if (!empty($selected['image'])): ?>
            <img src="<?= htmlspecialchars($selected['image']) ?>" style="max-width:100%;height:auto;">
        <?php endif; ?>
        <form method="POST" action="?element=client&action=index">
            <input type="hidden" name="action_panier" value="add">
            <input type="hidden" name="id_produit" value="<?= (int)$selected['id'] ?>">
            <label>Quantité : <input type="number" name="quantite" value="1" min="1" max="<?= (int)$selected['stock'] ?>"></label><br><br>
            <button type="submit">Ajouter au panier</button>
        </form>
        <br>
        <a href="?element=client&action=index">← Retour à la liste</a>
    </div>
<?php else: ?>
    <!-- ── Liste des produits ───────────────────────── -->
    <h2>Liste des produits</h2>
    <?php if (empty($produits)): ?>
        <p>Aucun produit disponible.</p>
    <?php else: ?>
        <ul style="list-style:none;padding:0;">
            <?php foreach ($produits as $p): ?>
                <li style="border:1px solid #ddd;margin-bottom:8px;padding:12px;max-width:600px;">
                    <strong><?= htmlspecialchars($p['nom']) ?></strong> — 
                    Catégorie : <?= htmlspecialchars($p['categorie']) ?> — 
                    Prix : <?= number_format((float)$p['prix'], 2, ',', ' ') ?> € — 
                    Stock : <?= (int)$p['stock'] ?>
                    <br>
                    <a href="?element=client&action=index&id=<?= (int)$p['id'] ?>">Voir la fiche</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>