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
    $id_card    = 'card-' . (int)$commande['id'];
?>

<div class="commande-card"
     id="<?= $id_card ?>"
     onclick="toggleCard('<?= $id_card ?>')">

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

    <div class="commande-card-body">

        <div class="commande-info">
            <i class="fa-solid fa-phone"></i>
            <?= htmlspecialchars($commande['phone'] ?? '—') ?>
        </div>

        <div class="commande-info">
            <i class="fa-solid fa-clock"></i>
            <?= htmlspecialchars($commande['heure']) ?>
        </div>

        <?php if (!empty($produits_cmd)): ?>

            <ul class="commande-produits">
                <?php foreach (array_slice($produits_cmd, 0, $nb_preview) as $p): ?>
                    <li class="commande-produit-item">
                        <span><?= htmlspecialchars($p['nom']) ?></span>
                        <span class="qty">×<?= (int)$p['quantite'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($nb_total > $nb_preview): ?>
                <p style="margin-top:8px; font-size:.85rem; color:var(--text-muted);">
                    + <?= $nb_total - $nb_preview ?> article(s) supplémentaire(s)
                </p>
            <?php endif; ?>

        <?php else: ?>
            <p style="color:var(--text-muted); font-size:.85rem; font-style:italic;">
                Aucun produit associé.
            </p>
        <?php endif; ?>

        <form method="POST"
              style="margin-top:16px;"
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

    </div>

</div>

<?php endforeach; ?>
</div>

<?php else: ?>

<div class="empty-state">
    <i class="fa-solid fa-inbox"></i>
    <p>Aucune commande en cours pour le moment.</p>
</div>

<?php endif; ?>

<!-- Overlay sombre -->
<div id="overlay"></div>

<style>

/* Overlay */
#overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    opacity: 0;
    visibility: hidden;
    transition: 0.3s ease;
    z-index: 998;
}

#overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Card normale */
.commande-card {
    cursor: pointer;
    transition: 0.3s ease;
}

/* Card en premier plan */
.commande-card.expanded {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 600px;
    max-width: 90%;
    max-height: 85vh;
    overflow-y: auto;
    z-index: 999;
    box-shadow: 0 30px 80px rgba(0,0,0,0.35);
}

</style>

<script>

let currentCard = null;
const overlay = document.getElementById("overlay");

function toggleCard(cardId) {
    const card = document.getElementById(cardId);
    if (!card) return;

    if (currentCard === card) {
        closeCard();
    } else {
        openCard(card);
    }
}

function openCard(card) {
    if (currentCard) closeCard();

    currentCard = card;
    card.classList.add("expanded");
    overlay.classList.add("active");
}

function closeCard() {
    if (!currentCard) return;

    currentCard.classList.remove("expanded");
    overlay.classList.remove("active");
    currentCard = null;
}

overlay.addEventListener("click", closeCard);

document.addEventListener("keydown", function(e){
    if (e.key === "Escape") closeCard();
});

</script>