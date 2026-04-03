<?php afficherMessagesFlash(); ?>

<div class="table-container">
    <h2>Commandes en cours</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Téléphone</th>
                <th>Heure</th>
                <th>Borne</th>
                <th>Produits</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($commandes)): ?>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['id']) ?></td>
                        <td><?= htmlspecialchars($commande['phone']) ?></td>
                        <td><?= htmlspecialchars($commande['heure']) ?></td>
                        <td><?= htmlspecialchars($commande['num_borne']) ?></td>
                        <td>
                            <?php
                            $produits = $db->prepare("SELECT p.nom, cp.quantite
                                FROM produit_commander cp
                                JOIN produits p ON cp.id_produit = p.id
                                WHERE cp.id_commande = :id_commande");
                            $produits->bindValue(':id_commande', $commande['id']);
                            $produits->execute();
                            $liste = $produits->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (!empty($liste)) {
                                foreach ($liste as $produit) {
                                    echo htmlspecialchars($produit['nom']) . ' x' . $produit['quantite'] . '<br>';
                                }
                            } else {
                                echo '<em>Aucun produit</em>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">
                        Aucune commande
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<a href="?element=admin&action=index" class="retour">← Retour admin</a>