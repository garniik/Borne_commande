<?php
/**
 * inc/top.php — Barre de navigation
 * Détecte automatiquement le contexte (client / admin)
 */

$element_courant = GETPOST('element') ?? 'client';
$action_courante = GETPOST('action')  ?? 'index';
?>

<?php if ($element_courant === 'admin'): ?>
<!-- ════════════════════════════════════════════════════════
     NAVBAR ADMIN
════════════════════════════════════════════════════════ -->
<nav class="navbar navbar-admin">

    <a href="index.php?element=admin&action=commandes" class="navbar-brand">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
            <rect width="32" height="32" rx="8" fill="#22c55e"/>
            <path d="M8 10h16M8 16h10M8 22h13" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/>
            <circle cx="24" cy="22" r="4" fill="#fff"/>
            <path d="M22.5 22l1 1 2-2" stroke="#22c55e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>BorneAdmin</span>
    </a>

    <div class="navbar-links">
        <a href="index.php?element=admin&action=commandes"
           class="navbar-link <?= $action_courante === 'commandes' ? 'active' : '' ?>">
            <i class="fa-solid fa-receipt"></i>
            <span>Commandes</span>
        </a>
        <a href="index.php?element=admin&action=produits"
           class="navbar-link <?= $action_courante === 'produits' ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Produits</span>
        </a>
    </div>

    <a href="index.php?element=client&action=index" class="navbar-kiosk-btn">
        <i class="fa-solid fa-tablet-screen-button"></i>
        <span>Kiosque</span>
    </a>

</nav>

<?php else: ?>
<!-- ════════════════════════════════════════════════════════
     NAVBAR CLIENT (kiosque tablette)
════════════════════════════════════════════════════════ -->
<?php
$panier      = $_SESSION['panier'] ?? [];
$nb_articles = array_sum($panier);
?>
<nav class="navbar navbar-client">

    <a href="index.php?element=client&action=index" class="navbar-brand">
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" aria-hidden="true">
            <rect width="36" height="36" rx="10" fill="#22c55e"/>
            <ellipse cx="18" cy="21" rx="10" ry="5" fill="rgba(255,255,255,0.25)"/>
            <ellipse cx="18" cy="20" rx="9" ry="4" fill="white"/>
            <path d="M14 13 Q14.5 11 14 9" stroke="white" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
            <path d="M18 12 Q18.5 10 18 8" stroke="white" stroke-width="1.5" stroke-linecap="round" opacity="0.85"/>
            <path d="M22 13 Q22.5 11 22 9" stroke="white" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
        </svg>
        <span>Commander</span>
    </a>

    <div class="navbar-links">
        <a href="index.php?element=client&action=index"
           class="navbar-link <?= ($action_courante === 'index') ? 'active' : '' ?>">
            <i class="fa-solid fa-utensils"></i>
            <span>Menu</span>
        </a>
        <a href="index.php?element=client&action=pizza"
           class="navbar-link <?= ($action_courante === 'pizza') ? 'active' : '' ?>">
            <i class="fa-solid fa-pizza-slice"></i>
            <span>Pizzas</span>
        </a>
    </div>

    <a href="index.php?element=client&action=commande&source=<?= $action_courante ?>"
       class="navbar-cart <?= $nb_articles > 0 ? 'has-items' : '' ?>"
       aria-label="Mon panier, <?= $nb_articles ?> article<?= $nb_articles > 1 ? 's' : '' ?>">
        <i class="fa-solid fa-basket-shopping"></i>
        <?php if ($nb_articles > 0): ?>
            <span class="cart-badge"><?= $nb_articles ?></span>
        <?php endif; ?>
        <span class="cart-label">Panier</span>
    </a>

</nav>
<?php endif; ?>