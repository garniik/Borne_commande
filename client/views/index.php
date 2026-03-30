<h1>Produits</h1>

<?php if (!empty($db_ok)) { ?>
    <p>DB: OK</p>
<?php } else { ?>
    <p>DB: KO</p>
    <?php if (!empty($db_error)) { ?>
        <pre><?= htmlspecialchars((string)$db_error); ?></pre>
    <?php } ?>
<?php } ?>

<p>Produits: <?= isset($produits) && is_array($produits) ? count($produits) : 0; ?></p>

<?php if (empty($produits)) { ?>
    <p>Aucun produit.</p>
<?php } else { ?>
    <ul>
        <?php foreach ($produits as $p) : ?>
            <li>
                <?= htmlspecialchars((string)($p['nom'] ?? '')); ?>
                <?= htmlspecialchars((string)($p['prix'] ?? '')); ?>
                <?= htmlspecialchars((string)($p['description'] ?? '')); ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php } ?>

