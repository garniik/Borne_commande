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

<h2>Commandes en cours</h2>

<table border="1" cellpadding="8" style="border-collapse:collapse;">
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
                        // Récupérer tous les produits de cette commande
                        $produits = $pdo->prepare("SELECT p.nom, cp.quantite
                            FROM produit_commander cp
                            JOIN produits p ON cp.id_produit = p.id
                            WHERE cp.id_commande = :id_commande");
                        $produits->bindValue(':id_commande', $commande['id']);
                        $produits->execute();
                        $liste = $produits->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($liste as $p) {
                            echo htmlspecialchars($p['nom']) . ' x' . $p['quantite'] . '<br>';
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">Aucune commande</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<p><a href="?element=admin&action=index">← Retour admin</a></p>