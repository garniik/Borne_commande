<?php afficherMessagesFlash(); ?>

<div style="display:flex; gap:10px; margin-bottom:20px;">
    <a href="?element=produits&action=index" class="btn btn-ghost">
        <i class="fa-solid fa-box"></i>
        Produits
    </a>
    <a href="?element=commandes&action=index" class="btn btn-primary">
        <i class="fa-solid fa-clipboard-list"></i>
        Commandes
    </a>
</div>
<!-- En-tête -->
<div style="margin-bottom: 28px;">
    <h1 class="page-title">
        Commandes <span>en cours</span>
    </h1>
    <p class="page-subtitle">
        <?= count($commandes ?? []) ?> commande<?= count($commandes ?? []) > 1 ? 's' : '' ?> active<?= count($commandes ?? []) > 1 ? 's' : '' ?>
    </p>
</div>
<!-- Navigation admin -->



<!-- ══════════════════════════════════════════════════════
     GRILLE DE CARDS COMMANDES
══════════════════════════════════════════════════════ -->
<?php if (!empty($commandes)): ?>

    <div class="commandes-grid">
        <?php foreach ($commandes as $commande):

            // Récupérer les produits de cette commande
            $stmtProd = $db->prepare("
                SELECT p.nom, cp.quantite
                FROM produit_commander cp
                JOIN produits p ON cp.id_produit = p.id
                WHERE cp.id_commande = :id_commande
            ");
            $stmtProd->bindValue(':id_commande', $commande['id']);
            $stmtProd->execute();
            $produits_cmd = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <div class="commande-card">

                <!-- En-tête de la card -->
                <div class="commande-card-header">
                    <span class="commande-id">
                        <i class="fa-solid fa-hashtag"></i>
                        Commande <?= (int)$commande['id'] ?>
                    </span>
                    <?php if (!empty($commande['num_borne'])): ?>
                        <span class="commande-borne">
                            <i class="fa-solid fa-desktop"></i>
                            Borne <?= htmlspecialchars($commande['num_borne']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Corps de la card -->
                <div class="commande-card-body">

                    <!-- Heure -->
                    <div class="commande-info">
                        <i class="fa-solid fa-clock"></i>
                        <?= htmlspecialchars($commande['heure']) ?>
                    </div>

                    <!-- Liste des produits commandés -->
                    <?php if (!empty($produits_cmd)): ?>
                        <ul class="commande-produits">
                            <?php foreach ($produits_cmd as $p): ?>
                                <li class="commande-produit-item">
                                    <span><?= htmlspecialchars($p['nom']) ?></span>
                                    <span class="qty">×<?= (int)$p['quantite'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p style="color:var(--text-muted); font-size:.85rem; font-style:italic;">
                            Aucun produit associé.
                        </p>
                    <?php endif; ?>
                    
                    <!-- Bouton suppression -->
                    <form method="POST" action="" style="margin-top: 15px;">
                        <input type="hidden" name="id" value="<?= (int)$commande['id'] ?>">
                        <button type="submit" name="delete_commande" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette commande ?')">
                            <i class="fa-solid fa-trash"></i>
                            Supprimer
                        </button>
                    </form>

                </div><!-- /card-body -->

            </div><!-- /commande-card -->

        <?php endforeach; ?>
    </div><!-- /commandes-grid -->

<?php else: ?>

    <div class="empty-state">
        <i class="fa-solid fa-inbox"></i>
        <p>Aucune commande en cours pour le moment.</p>
    </div>

<?php endif; ?>


<!-- Lien retour admin -->
<a href="?element=admin&action=index" class="btn-back" style="margin-top: 32px; display:inline-flex;">
    <i class="fa-solid fa-arrow-left"></i>
    Retour au tableau de bord
</a>
