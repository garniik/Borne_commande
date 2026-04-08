<?php afficherMessagesFlash(); ?>

<!-- Navigation admin -->
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
        — Cliquez sur une card pour voir tous les articles
    </p>
</div>


<!-- ══════════════════════════════════════════════════════
     GRILLE DE CARDS COMMANDES
     - Affiche les 3 premiers articles en vue réduite
     - Clic sur la card => agrandissement (expand) sans changer de page
══════════════════════════════════════════════════════ -->
<?php if (!empty($commandes)): ?>

    <div class="commandes-grid" id="commandesGrid">

        <?php foreach ($commandes as $commande):

            // Récupérer tous les produits de cette commande
            $stmtProd = $db->prepare("
                SELECT p.nom, cp.quantite
                FROM produit_commander cp
                JOIN produits p ON cp.id_produit = p.id
                WHERE cp.id_commande = :id_commande
            ");
            $stmtProd->bindValue(':id_commande', $commande['id']);
            $stmtProd->execute();
            $produits_cmd = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

            $nb_total    = count($produits_cmd);
            $nb_preview  = 3; // nombre d'articles visibles en vue réduite
            $a_plus      = $nb_total > $nb_preview;
            $id_card     = 'card-' . (int)$commande['id'];
        ?>

            <div class="commande-card" id="<?= $id_card ?>">

                <!-- ── En-tête ──────────────────────────────── -->
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

                <!-- ── Corps ───────────────────────────────── -->
                <div class="commande-card-body">

                    <!-- Infos principales (toujours visibles) -->
                    <div class="commande-info">
                        <i class="fa-solid fa-phone"></i>
                        <?= htmlspecialchars($commande['phone'] ?? '—') ?>
                    </div>
                    <div class="commande-info">
                        <i class="fa-solid fa-clock"></i>
                        <?= htmlspecialchars($commande['heure']) ?>
                    </div>

                    <?php if (!empty($produits_cmd)): ?>

                        <!-- Articles visibles en vue réduite (max 3) -->
                        <ul class="commande-produits">
                            <?php foreach (array_slice($produits_cmd, 0, $nb_preview) as $p): ?>
                                <li class="commande-produit-item">
                                    <span><?= htmlspecialchars($p['nom']) ?></span>
                                    <span class="qty">×<?= (int)$p['quantite'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <?php if ($a_plus): ?>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="openModal(<?= (int)$commande['id'] ?>)" style="margin-top:8px; width:100%;">
                                <i class="fa-solid fa-eye"></i>
                                Voir les <?= $nb_total - $nb_preview ?> article(s) supplémentaire(s)
                            </button>
                        <?php endif; ?>

                    <?php else: ?>
                        <p style="color:var(--text-muted); font-size:.85rem; font-style:italic;">
                            Aucun produit associé.
                        </p>
                    <?php endif; ?>

                    <!-- Bouton valider — empêche le clic de déclencher le toggle de la card -->
                    <form method="POST" action=""
                          style="margin-top: 16px;"
                          onclick="event.stopPropagation()">
                        <input type="hidden" name="id" value="<?= (int)$commande['id'] ?>">
                        <button type="submit"
                                name="validate_commande"
                                class="btn btn-success btn-sm"
                                onclick="return confirm('Valider cette commande ?')">
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


<script>
/**
 * toggleCard(id)
 * Agrandit ou réduit une card commande.
 * - Ajoute la classe CSS "expanded" sur la card ciblée
 * - Les articles supplémentaires (.commande-produits-extra) s'affichent via CSS
 * - Le badge "X articles de plus" disparaît
 * - Le chevron tourne à 180°
 */
function toggleCard(cardId) {
    const card = document.getElementById(cardId);
    if (!card) return;

    const estOuverte = card.dataset.expanded === 'true';

    // Fermer toutes les autres cards ouvertes (comportement accordéon optionnel)
    // Décommenter les lignes ci-dessous pour n'avoir qu'une card ouverte à la fois :
    // document.querySelectorAll('.commande-card[data-expanded="true"]').forEach(c => {
    //     if (c.id !== cardId) fermerCard(c);
    // });

    if (estOuverte) {
        fermerCard(card);
    } else {
        ouvrirCard(card);
    }
}

function ouvrirCard(card) {
    card.dataset.expanded = 'true';
    card.classList.add('expanded');
}

function fermerCard(card) {
    card.dataset.expanded = 'false';
    card.classList.remove('expanded');
}
</script>
