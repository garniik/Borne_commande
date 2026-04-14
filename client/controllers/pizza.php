<?php
require_once(dirname(__FILE__) . '/../../class/produits.class.php');

// Sur cette page (pizza), toutes les pizzas sont disponibles normalement
// Pas de logique horaire - gérée par le kiosk

// Récupérer les paramètres de filtre (GET ou POST)
$categorie = $_GET['categorie'] ?? $_POST['categorie'] ?? '';
$filtre_actif = isset($_GET['filtrer']) || (isset($_POST['categorie']) && $_POST['categorie'] !== '');

if ($filtre_actif && $categorie !== '') {
    $filter_data = ['categorie' => $categorie];
    $donnee = Produits::find($db, $filter_data);
} else {
    $donnee = Produits::fetchAll($db);
}

// Trier : produits en stock en premier, rupture en bas
usort($donnee, function($a, $b) {
    $stockA = (int)($a['stock'] ?? 0);
    $stockB = (int)($b['stock'] ?? 0);
    if ($stockA > 0 && $stockB === 0) return -1;
    if ($stockB > 0 && $stockA === 0) return 1;
    return (int)$a['id'] <=> (int)$b['id'];
});

// Gérer le panier (simple session)
$action_panier = GETPOST('action_panier') ?? '';
$id_produit = filter_var(GETPOST('id_produit'), FILTER_VALIDATE_INT);
$quantite = max(1, (int)(GETPOST('quantite') ?? 1));

if ($action_panier === 'add' && $id_produit) {
    $produit = Produits::findById($db, $id_produit);
    if ($produit && (int)$produit['stock'] >= $quantite) {
        if (!isset($_SESSION['panier'])) $_SESSION['panier'] = [];
        $_SESSION['panier'][$id_produit] = ($_SESSION['panier'][$id_produit] ?? 0) + $quantite;
    } else {
        $_SESSION['mesgs']['errors'][] = 'Produit indisponible ou stock insuffisant.';
    }
    
    header('Location: ?element=client&action=pizza');
    exit;
}

// Supprimer un article du panier
if ($action_panier === 'remove' && $id_produit) {
    if (isset($_SESSION['panier'][$id_produit])) {
        unset($_SESSION['panier'][$id_produit]);
    }
    header('Location: ?element=client&action=pizza');
    exit;
}

// Vider tout le panier
if ($action_panier === 'clear') {
    $_SESSION['panier'] = [];
    header('Location: ?element=client&action=pizza');
    exit;
}

// Préparer les détails du panier pour l’affichage
$panier = $_SESSION['panier'] ?? [];
$details = [];
$total = 0;
foreach ($panier as $idp => $qte) {
    $p = Produits::findById($db, $idp);
    if ($p) {
        $details[] = ['produit' => $p, 'quantite' => $qte];
        $total += (float)$p['prix'] * $qte;
    }
}
