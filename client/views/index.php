<h1>Produits</h1>

<?php foreach ($produits as $p) :?>
            <li>
                <?php echo $produits['nom'] ?>
                <?php echo $produits['prix'] ?>
                <?php echo $produits['description'] ?>
            </li>
<?php endforeach; ?>

