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

<div style="margin-bottom: 28px;">
    <h1 class="page-title">
        Commandes <span>en cours</span>
    </h1>
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

    $nb_total = count($produits_cmd);
    $id_card  = 'card-' . (int)$commande['id'];
?>

<div class="commande-card"
     id="<?= $id_card ?>"
     onclick="toggleCard('<?= $id_card ?>')">

    <div class="commande-card-header">
        <span>Borne <?= (int)$commande['num_borne'] ?></span>
    </div>

    <div class="commande-card-body">
        <div><?= htmlspecialchars($commande['heure']) ?></div>

        <?php if (!empty($produits_cmd)): ?>

            <!-- Liste complète -->
            <ul class="commande-produits">

                <?php foreach ($produits_cmd as $index => $p): ?>
                    <li class="commande-produit-item <?= $index >= 3 ? 'extra-produit' : '' ?>">
                        <span><?= htmlspecialchars($p['nom']) ?></span>
                        <span>×<?= (int)$p['quantite'] ?></span>
                    </li>
                <?php endforeach; ?>

            </ul>

            <?php if ($nb_total > 3): ?>
                <p class="produits-more">
                    + <?= $nb_total - 3 ?> article(s) supplémentaire(s)
                </p>
            <?php endif; ?>

        <?php endif; ?>

        <form method="POST"
              style="margin-top:16px;"
              onclick="event.stopPropagation()">
            <input type="hidden" name="id" value="<?= (int)$commande['id'] ?>">
            <button type="submit"
                    name="validate_commande"
                    class="btn btn-success btn-sm">
                Valider
            </button>
        </form>

    </div>

</div>

<?php endforeach; ?>
</div>

<?php endif; ?>

<div id="overlay"></div>

<style>

/* Overlay simple sans blur */
#overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    display: none;
    z-index: 998;
}

#overlay.active {
    display: block;
}

/* Card normale */
.commande-card {
    cursor: pointer;
}

/* Card ouverte */
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
    background: white;
    padding: 20px;
}

/* Produits cachés par défaut */
.extra-produit {
    display: none;
}

/* Quand card ouverte → tout afficher */
.commande-card.expanded .extra-produit {
    display: list-item;
}

/* Cacher le "+ X articles" quand ouvert */
.commande-card.expanded .produits-more {
    display: none;
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