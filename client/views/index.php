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
            </tr>
        </thead>
        <tbody>
            <?php foreach($donnee as $produit): ?>
            <tr>
                <td><?php echo $produit['nom']; ?></td>
                <td><?php echo $produit['prix']; ?></td>
                <td><?php echo $produit['quantite']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

