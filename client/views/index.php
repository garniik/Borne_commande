<div class="panier">
    <h2>Panier</h2>
    <?php if (empty($details)): ?>
        <p>Panier vide.</p>
    <?php else: ?>
        <ul class="panier-liste">
            <?php foreach ($details as $ligne): ?>
                <li class="panier-item">
                    <?= htmlspecialchars($ligne['produit']['nom']) ?> x<?= (int)$ligne['quantite'] ?> = <?= formaterPrix($ligne['produit']['prix'] * $ligne['quantite']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="panier-total">Total : <?= formaterPrix($total) ?></p>
        <p><a href="?element=client&action=commande" class="btn btn-success">Valider la commande</a></p>
    <?php endif; ?>
    <?php afficherMessagesFlash(); ?>
</div>

<hr>

<div class="dtitle w3-container w3-teal">
    Liste des produits
</div>

<div class="filtre-container">
    <?php if (isset($_GET['categorie']) && $_GET['categorie'] !== ''): ?>
        <div class="filtre-actif">
            Filtre actif : 
            <?php 
            $categories = ['1' => 'Boisson', '2' => 'Snack', '3' => 'Nourriture'];
            echo htmlspecialchars($categories[$_GET['categorie']] ?? 'Catégorie inconnue'); 
            ?>
            <a href="?element=client&action=index" class="btn btn-secondary" style="margin-left: 10px;">✕ Annuler le filtre</a>
        </div>
    <?php endif; ?>
    
    <form method="get" action="">
        <input type="hidden" name="element" value="client">
        <input type="hidden" name="action" value="index">
        <input type="hidden" name="filtrer" value="1">
        <label for="categorie">Catégorie :</label>
        <select name="categorie" id="categorie">
            <option value="">Toutes les catégories</option>
            <option value="1" <?= estSelectionne('1', $_GET['categorie'] ?? '') ?>>Boisson</option>
            <option value="2" <?= estSelectionne('2', $_GET['categorie'] ?? '') ?>>Snack</option>
            <option value="3" <?= estSelectionne('3', $_GET['categorie'] ?? '') ?>>Nourriture</option>
        </select>
        <button type="submit" class="btn btn-primary">Filtrer</button>
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Catégorie</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($donnee as $produit): ?>
            <tr>
                <td><?= htmlspecialchars($produit['nom']) ?></td>
                <td><?= formaterPrix($produit['prix']) ?></td>
                <td><?= (int)$produit['stock'] ?></td>
                <td><?= htmlspecialchars($produit['categorie']) ?></td>
                <td>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="action_panier" value="add">
                        <input type="hidden" name="id_produit" value="<?= (int)$produit['id'] ?>">
                        <?php if (isset($_GET['categorie'])): ?>
                            <input type="hidden" name="categorie" value="<?= htmlspecialchars($_GET['categorie']) ?>">
                        <?php endif; ?>
                        <input type="number" name="quantite" value="1" min="1" max="<?= (int)$produit['stock'] ?>" class="quantite-input">
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


