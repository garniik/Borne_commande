<?php afficherMessagesFlash(); ?>

<div style="display:flex; gap:10px; margin-bottom:20px;">
    <a href="?element=admin&action=produits" class="btn btn-ghost">
        <i class="fa-solid fa-box"></i>
        Produits
    </a>
    <a href="?element=admin&action=commandes" class="btn btn-primary">
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

                    <!-- Liste des produits commandés (max 3) -->
                    <?php if (!empty($produits_cmd)): 
                        $total_produits = count($produits_cmd);
                        $produits_afficher = array_slice($produits_cmd, 0, 3);
                    ?>
                        <ul class="commande-produits">
                            <?php foreach ($produits_afficher as $p): ?>
                                <li class="commande-produit-item">
                                    <span><?= htmlspecialchars($p['nom']) ?></span>
                                    <span class="qty">×<?= (int)$p['quantite'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <?php if ($total_produits > 3): ?>
                            <div style="text-align:center; margin-top:8px;">
                                <button type="button" class="btn btn-ghost btn-sm" onclick="openCommandeModal(<?= (int)$commande['id'] ?>)" style="font-size:0.8rem; padding:4px 12px;">
                                    <i class="fa-solid fa-eye"></i>
                                    Voir les <?= $total_produits - 3 ?> article(s) supplémentaire(s)
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Modal pour cette commande -->
                        <div id="modal-cmd-<?= (int)$commande['id'] ?>" class="commande-modal">
                            <div class="modal-content" style="max-width: 400px;">
                                <button class="modal-close" onclick="closeCommandeModal(<?= (int)$commande['id'] ?>)">&times;</button>
                                
                                <div class="modal-header" style="margin-bottom: 20px;">
                                    <h3 style="margin: 0; font-size: 1.2rem;">
                                        <i class="fa-solid fa-hashtag"></i>
                                        Commande <?= (int)$commande['id'] ?>
                                    </h3>
                                    <?php if (!empty($commande['num_borne'])): ?>
                                        <span class="commande-borne" style="margin-top: 8px; display: inline-block;">
                                            <i class="fa-solid fa-desktop"></i>
                                            Borne <?= htmlspecialchars($commande['num_borne']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="commande-info" style="margin-bottom: 15px;">
                                    <i class="fa-solid fa-clock"></i>
                                    <?= htmlspecialchars($commande['heure']) ?>
                                </div>
                                
                                <h4 style="margin: 15px 0 10px 0; font-size: 1rem; color: var(--text-muted);">
                                    Articles (<?= $total_produits ?>)
                                </h4>
                                
                                <ul class="commande-produits" style="max-height: 300px; overflow-y: auto;">
                                    <?php foreach ($produits_cmd as $p): ?>
                                        <li class="commande-produit-item">
                                            <span><?= htmlspecialchars($p['nom']) ?></span>
                                            <span class="qty">×<?= (int)$p['quantite'] ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <p style="color:var(--text-muted); font-size:.85rem; font-style:italic;">
                            Aucun produit associé.
                        </p>
                    <?php endif; ?>
                    
                    <!-- Bouton validation -->
                    <form method="POST" action="" style="margin-top: 15px;">
                        <input type="hidden" name="id" value="<?= (int)$commande['id'] ?>">
                        <button type="submit" name="validate_commande" class="btn btn-success btn-sm" onclick="return confirm('Valider cette commande ?')">
                            <i class="fa-solid fa-check"></i>
                            Valider la commande
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

