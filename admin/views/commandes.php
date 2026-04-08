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
<div style="margin-bottom:28px;">
    <h1 class="page-title">Commandes <span>en cours</span></h1>
    <p class="page-subtitle">
        <?= count($commandes ?? []) ?> commande<?= count($commandes ?? []) > 1 ? 's' : '' ?> —
        cliquez sur une card pour voir le détail complet
    </p>
</div>


<!-- ══════════════════════════════════════════════════════
     GRILLE DE CARDS
══════════════════════════════════════════════════════ -->
<?php if (!empty($commandes)): ?>

    <div class="commandes-grid">

        <?php foreach ($commandes as $commande):

            $stmtProd = $db->prepare("
                SELECT p.nom, cp.quantite
                FROM produit_commander cp
                JOIN produits p ON cp.id_produit = p.id
                WHERE cp.id_commande = :id_commande
            ");
            $stmtProd->bindValue(':id_commande', $commande['id']);
            $stmtProd->execute();
            $produits_cmd = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

            $nb_total   = count($produits_cmd);
            $nb_preview = 3;
            $a_plus     = $nb_total > $nb_preview;
            $id_card    = 'card-' . (int)$commande['id'];
        ?>

            <!-- Card résumé — clic ouvre le modal -->
            <div class="cmd-card"
                 id="<?= $id_card ?>"
                 onclick="ouvrirModal('<?= $id_card ?>')"
                 role="button"
                 tabindex="0"
                 aria-label="Voir le détail de la commande <?= (int)$commande['id'] ?>"
                 onkeydown="if(event.key==='Enter'||event.key===' ') ouvrirModal('<?= $id_card ?>')">

                <!-- ── En-tête coloré ──────────────────────── -->
                <div class="cmd-card-header">
                    <div class="cmd-card-id">
                        <i class="fa-solid fa-receipt"></i>
                        Commande #<?= (int)$commande['id'] ?>
                    </div>
                    <?php if (!empty($commande['num_borne'])): ?>
                        <div class="cmd-card-borne">
                            <i class="fa-solid fa-desktop"></i>
                            Borne <?= htmlspecialchars($commande['num_borne']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ── Infos principales ───────────────────── -->
                <div class="cmd-card-body">

                    <div class="cmd-meta-row">
                        <span class="cmd-meta-icon"><i class="fa-solid fa-clock"></i></span>
                        <span><?= htmlspecialchars($commande['heure']) ?></span>
                    </div>

                    <!-- Séparateur -->
                    <div class="cmd-separator"></div>

                    <!-- Preview articles (3 max) -->
                    <div class="cmd-articles-label">Articles</div>
                    <ul class="cmd-articles-list">
                        <?php foreach (array_slice($produits_cmd, 0, $nb_preview) as $p): ?>
                            <li class="cmd-article-item">
                                <span class="cmd-article-name"><?= htmlspecialchars($p['nom']) ?></span>
                                <span class="cmd-article-qty">×<?= (int)$p['quantite'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Badge "X de plus" -->
                    <?php if ($a_plus): ?>
                        <div class="cmd-more-badge">
                            <i class="fa-solid fa-circle-plus"></i>
                            <?= $nb_total - $nb_preview ?> article<?= ($nb_total - $nb_preview) > 1 ? 's' : '' ?> de plus — cliquer pour voir
                        </div>
                    <?php endif; ?>

                </div>

                <!-- ── Pied de card ────────────────────────── -->
                <div class="cmd-card-footer">
                    <span class="cmd-footer-hint">
                        <i class="fa-solid fa-expand"></i> Voir le détail
                    </span>
                    <span class="cmd-article-count badge badge-green">
                        <?= $nb_total ?> article<?= $nb_total > 1 ? 's' : '' ?>
                    </span>
                </div>

            </div><!-- /cmd-card -->

        <?php endforeach; ?>

    </div><!-- /commandes-grid -->


    <!-- ══════════════════════════════════════════════
         MODALS (un par commande, caché par défaut)
    ══════════════════════════════════════════════ -->
    <?php foreach ($commandes as $commande):

        $stmtProd2 = $db->prepare("
            SELECT p.nom, cp.quantite
            FROM produit_commander cp
            JOIN produits p ON cp.id_produit = p.id
            WHERE cp.id_commande = :id_commande
        ");
        $stmtProd2->bindValue(':id_commande', $commande['id']);
        $stmtProd2->execute();
        $produits_modal = $stmtProd2->fetchAll(PDO::FETCH_ASSOC);
        $id_modal = 'modal-' . (int)$commande['id'];
    ?>

        <div class="cmd-modal" id="<?= $id_modal ?>" role="dialog" aria-modal="true" aria-label="Détail commande <?= (int)$commande['id'] ?>">
            <div class="cmd-modal-box" onclick="event.stopPropagation()">

                <!-- En-tête modal -->
                <div class="cmd-modal-header">
                    <div class="cmd-modal-title">
                        <i class="fa-solid fa-receipt"></i>
                        Commande #<?= (int)$commande['id'] ?>
                    </div>
                    <button class="cmd-modal-close"
                            onclick="fermerModal('<?= $id_modal ?>')"
                            aria-label="Fermer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Infos commande -->
                <div class="cmd-modal-body">

                    <!-- Bloc infos -->
                    <div class="cmd-modal-infos">

                        <div class="cmd-modal-info-item">
                            <span class="cmd-modal-info-label">
                                <i class="fa-solid fa-clock"></i> Heure
                            </span>
                            <span class="cmd-modal-info-value">
                                <?= htmlspecialchars($commande['heure']) ?>
                            </span>
                        </div>

                        <?php if (!empty($commande['num_borne'])): ?>
                        <div class="cmd-modal-info-item">
                            <span class="cmd-modal-info-label">
                                <i class="fa-solid fa-desktop"></i> Borne
                            </span>
                            <span class="cmd-modal-info-value">
                                Borne <?= htmlspecialchars($commande['num_borne']) ?>
                            </span>
                        </div>
                        <?php endif; ?>

                    </div>

                    <!-- Liste complète des articles -->
                    <div class="cmd-modal-articles-title">
                        <i class="fa-solid fa-basket-shopping"></i>
                        Articles commandés
                        <span class="badge badge-green"><?= count($produits_modal) ?></span>
                    </div>

                    <?php if (!empty($produits_modal)): ?>
                        <ul class="cmd-modal-articles">
                            <?php foreach ($produits_modal as $p): ?>
                                <li class="cmd-modal-article-item">
                                    <span class="cmd-modal-article-name">
                                        <?= htmlspecialchars($p['nom']) ?>
                                    </span>
                                    <span class="cmd-modal-article-qty">
                                        ×<?= (int)$p['quantite'] ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p style="color:var(--text-muted); font-style:italic; font-size:.9rem;">
                            Aucun produit associé.
                        </p>
                    <?php endif; ?>

                </div><!-- /modal-body -->

                <!-- Actions -->
                <div class="cmd-modal-footer">
                    <button class="btn btn-ghost" onclick="fermerModal('<?= $id_modal ?>')">
                        <i class="fa-solid fa-xmark"></i>
                        Fermer
                    </button>
                    <form method="POST" action="" style="display:contents;">
                        <input type="hidden" name="id" value="<?= (int)$commande['id'] ?>">
                        <button type="submit"
                                name="validate_commande"
                                class="btn btn-success"
                                onclick="return confirm('Valider cette commande ?')">
                            <i class="fa-solid fa-check"></i>
                            Valider la commande
                        </button>
                    </form>
                </div>

            </div><!-- /modal-box -->
        </div><!-- /modal -->

    <?php endforeach; ?>


<?php else: ?>

    <div class="empty-state">
        <i class="fa-solid fa-inbox"></i>
        <p>Aucune commande en cours pour le moment.</p>
    </div>

<?php endif; ?>

<!-- Overlay (fond sombre derrière le modal) -->
<div id="cmdOverlay" onclick="fermerTousLesModals()"></div>


<script>
/* ── Ouvrir un modal ─────────────────────────────── */
function ouvrirModal(cardId) {
    const modalId = cardId.replace('card-', 'modal-');
    const modal   = document.getElementById(modalId);
    const overlay = document.getElementById('cmdOverlay');
    if (!modal) return;

    modal.classList.add('open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden'; // empêche le scroll derrière
}

/* ── Fermer un modal ─────────────────────────────── */
function fermerModal(modalId) {
    const modal   = document.getElementById(modalId);
    const overlay = document.getElementById('cmdOverlay');
    if (!modal) return;

    modal.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

/* ── Fermer tous (clic overlay) ──────────────────── */
function fermerTousLesModals() {
    document.querySelectorAll('.cmd-modal.open').forEach(m => m.classList.remove('open'));
    document.getElementById('cmdOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

/* ── Touche Echap ────────────────────────────────── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') fermerTousLesModals();
});
</script>
