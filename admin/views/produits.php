<div class="w3-container">
    <h1>Gestion des produits</h1>
</div>

<form method="post" class="w3-container">
    <input type="text" name="nom" placeholder="Nom du produit">
    <input type="text" name="categorie" placeholder="Catégorie">
    <input type="number" name="prix" placeholder="Prix">
    <input type="number" name="stock" placeholder="Stock">
    <textarea name="description" placeholder="Description"></textarea>
    <input type="text" name="image(URL)" placeholder="http://...">
    <button type="submit" name="add">Ajouter</button>
</form>

<?php if ($donnee): ?>
<table border="1">
    <tr>
        <th>Nom</th>
        <th>Catégorie</th>
        <th>Prix</th>
        <th>Stock</th>
        <th>Description</th>
        <th>Image</th>
        <th>Actions</th>
        <th>Ajouter du stock</th>
    </tr>
    <?php foreach ($donnee as $produit): ?>
        <tr>
            <td><?php echo $produit['nom']; ?></td>
            <td><?php echo $produit['categorie']; ?></td>
            <td><?php echo $produit['prix']; ?></td>
            <td><?php echo $produit['stock']; ?></td>
            <td><?php echo $produit['description']; ?></td>
            <td><?php echo $produit['image']; ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo (int)$produit['id']; ?>">
                    <button class="btn btn-danger" type="submit" name="delete" onclick="return confirm('Supprimer ?')">Supprimer</button>
                </form>
            </td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo (int)$produit['id']; ?>">
                    <input type="number" name="quantite" placeholder="Quantité">
                    <button class="btn btn-success" type="submit" name="add_stock">Ajouter</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
    <p>Aucun produit trouvé</p>
<?php endif; ?>