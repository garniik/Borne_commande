
<?php
if (!isset($produits) || !is_array($produits)) {
    $produits = [];
}
?>

<h1>Produits</h1>

<?php if (count($produits) === 0) { ?>
    <p>Aucun produit.</p>
<?php } else { ?>
    <ul>
        <?php foreach ($produits as $p) { ?>
            <li>
                #<?= htmlspecialchars((string)($p['id'] ?? '')); ?>
                <?= htmlspecialchars((string)($p['nom'] ?? '')); ?>
                - <?= htmlspecialchars((string)($p['prix'] ?? '')); ?>
            </li>
        <?php } ?>
    </ul>
<?php } ?>

