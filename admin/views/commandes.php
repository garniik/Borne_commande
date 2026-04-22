
<!-- Navigation admin -->
<div class="flex-wrap-gap mb-20">
    <a href="?element=admin&action=produits" class="btn btn-ghost">
        <i class="fa-solid fa-box"></i>
        Produits
    </a>
    <a href="?element=admin&action=commandes" class="btn btn-primary">
        <i class="fa-solid fa-clipboard-list"></i>
        Commandes
    </a>
    <button class="btn btn-ghost" onclick="window.location.reload()">
        <i class="fa-solid fa-rotate-right"></i>
        Recharger
    </button>
</div>

<!-- En-tête -->
<div class="mb-28">
    <h1 class="page-title">Commandes <span>en cours</span></h1>
    <p class="page-subtitle">
        <?= count($commandes ?? []) ?> commande<?= count($commandes ?? []) > 1 ? 's' : '' ?> —
        cliquez sur une card pour voir le détail complet
    </p>
</div>


<?php if (!empty($commandes)):
    $produitsParCommande = [];
?>

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

            // Stocker pour réutilisation dans les modals
            $produitsParCommande[$commande['id']] = $produits_cmd;

            $nb_total   = count($produits_cmd);
            $nb_preview = 3;
            $a_plus     = $nb_total > $nb_preview;
            $id_card    = 'card-' . (int)$commande['id'];
            $id_modal   = 'modal-' . (int)$commande['id'];
        ?>

        <!-- ══ CARD RÉSUMÉ ══════════════════════════════ -->
        <div class="cmd-card"
             id="<?= $id_card ?>"
             onclick="ouvrirModal('<?= $id_modal ?>')"
             role="button"
             tabindex="0"
             aria-label="Voir le détail de la commande <?= (int)$commande['id'] ?>"
             onkeydown="if(event.key==='Enter'||event.key===' ') ouvrirModal('<?= $id_modal ?>')">

            <!-- En-tête coloré -->
            <div class="cmd-card-header">
                <?php if (!empty($commande['num_borne'])): ?>
                    <div class="cmd-card-borne">
                        <i class="fa-solid fa-desktop"></i>
                        Borne <?= htmlspecialchars($commande['num_borne']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Corps : heure + articles preview -->
            <div class="cmd-card-body">

                <div class="cmd-meta-row">
                    <i class="fa-solid fa-clock cmd-meta-icon"></i>
                    <?= htmlspecialchars($commande['heure']) ?>
                </div>

                <div class="cmd-separator"></div>
                <div class="cmd-articles-label">Articles</div>

                <ul class="cmd-articles-list">
                    <?php foreach (array_slice($produits_cmd, 0, $nb_preview) as $p): ?>
                        <li class="cmd-article-item">
                            <span class="cmd-article-name"><?= htmlspecialchars($p['nom']) ?></span>
                            <span class="cmd-article-qty">×<?= (int)$p['quantite'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($a_plus): ?>
                    <div class="cmd-more-badge">
                        <i class="fa-solid fa-circle-plus"></i>
                        <?= $nb_total - $nb_preview ?> article<?= ($nb_total - $nb_preview) > 1 ? 's' : '' ?> de plus
                    </div>
                <?php endif; ?>

            </div><!-- /cmd-card-body -->

            <!-- Pied : toujours épinglé en bas -->
            <div class="cmd-card-footer">
                <span class="cmd-footer-hint">
                    <i class="fa-solid fa-expand"></i> Voir le détail
                </span>
                <span class="badge badge-green">
                    <?= $nb_total ?> article<?= $nb_total > 1 ? 's' : '' ?>
                </span>
            </div>

        </div><!-- /cmd-card -->

        <?php endforeach; ?>

    </div><!-- /commandes-grid -->


    <!-- ══ MODAL CONTAINER (lazy-loaded) ═══════════════════════════════════════ -->
    <div id="cmd-modal-container"></div>

    <!-- ══ DONNÉES JSON POUR JS ═══════════════════════════════════════ -->
    <script>
    const commandesData = <?= json_encode($produitsParCommande) ?>;
    const commandesMeta = <?= json_encode(array_map(function($c) {
        return ['id' => $c['id'], 'heure' => $c['heure'], 'num_borne' => $c['num_borne'] ?? null];
    }, $commandes)) ?>;
    </script>

    <script>
    function ouvrirModal(modalId) {
        const overlay = document.getElementById('cmdOverlay');
        const container = document.getElementById('cmd-modal-container');

        const id = parseInt(modalId.replace('modal-', ''));
        const meta = commandesMeta.find(c => c.id == id);
        const produits = commandesData[id] || [];

        if (!meta) return;

        container.innerHTML = renderModal(id, meta, produits);

        const modal = document.getElementById(modalId);
        if (modal) {
            modal.offsetHeight;
            modal.classList.add('open');
        }
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function renderModal(id, meta, produits) {
        const nbProduits = produits.length;
        let produitsHtml = '';

        if (nbProduits > 0) {
            produitsHtml = '<ul class="cmd-modal-articles">' +
                produits.map(p => `<li class="cmd-modal-article-item">
                    <span class="cmd-modal-article-name">${escapeHtml(p.nom)}</span>
                    <span class="cmd-modal-article-qty">×${p.quantite}</span>
                </li>`).join('') +
                '</ul>';
        } else {
            produitsHtml = '<p class="text-muted">Aucun produit associé.</p>';
        }

        const borneHtml = meta.num_borne ?
            `<div class="cmd-modal-info-item">
                <span class="cmd-modal-info-label"><i class="fa-solid fa-desktop"></i> Borne</span>
                <span class="cmd-modal-info-value">Borne ${escapeHtml(meta.num_borne)}</span>
            </div>` : '';

        return `<div class="cmd-modal" id="modal-${id}" role="dialog" aria-modal="true" aria-label="Détail commande ${id}" onclick="fermerModal('modal-${id}')">
            <div class="cmd-modal-box" onclick="event.stopPropagation()">
                <div class="cmd-modal-header">
                    <div class="cmd-modal-title">
                        <i class="fa-solid fa-receipt"></i> Commande #${id}
                    </div>
                    <button class="cmd-modal-close" onclick="fermerModal('modal-${id}')" aria-label="Fermer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="cmd-modal-body">
                    <div class="cmd-modal-infos">
                        <div class="cmd-modal-info-item">
                            <span class="cmd-modal-info-label"><i class="fa-solid fa-clock"></i> Heure</span>
                            <span class="cmd-modal-info-value">${escapeHtml(meta.heure)}</span>
                        </div>
                        ${borneHtml}
                    </div>
                    <div class="cmd-modal-articles-title">
                        <i class="fa-solid fa-basket-shopping"></i> Articles commandés
                        <span class="badge badge-green">${nbProduits}</span>
                    </div>
                    ${produitsHtml}
                </div>
                <div class="cmd-modal-footer">
                    <button class="btn btn-ghost" onclick="fermerModal('modal-${id}')">
                        <i class="fa-solid fa-xmark"></i> Fermer
                    </button>
                    <form method="POST" action="" class="contents">
                        <input type="hidden" name="id" value="${id}">
                        <button type="submit" name="validate_commande" class="btn btn-success" onclick="return confirm('Valider cette commande ?')">
                            <i class="fa-solid fa-check"></i> Valider la commande
                        </button>
                    </form>
                </div>
            </div>
        </div>`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function fermerModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.remove('open');
        document.getElementById('cmdOverlay').classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => {
            const container = document.getElementById('cmd-modal-container');
            if (container) container.innerHTML = '';
        }, 150);
    }

    function fermerTousLesModals() {
        document.querySelectorAll('.cmd-modal.open').forEach(m => m.classList.remove('open'));
        document.getElementById('cmdOverlay').classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => {
            const container = document.getElementById('cmd-modal-container');
            if (container) container.innerHTML = '';
        }, 150);
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') fermerTousLesModals();
    });
    </script>

<?php else: ?>
    <div class="empty-state">
        <i class="fa-solid fa-inbox"></i>
        <p>Aucune commande en cours pour le moment.</p>
    </div>
<?php endif; ?>

<!-- Overlay -->
<div id="cmdOverlay" onclick="fermerTousLesModals()"></div>
