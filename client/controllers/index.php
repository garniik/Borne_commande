<?php
require_once(dirname(__FILE__) . '/../../class/produits.class.php');

// Récupérer les paramètres de filtre (GET ou POST)
$categorie = $_GET['categorie'] ?? $_POST['categorie'] ?? '';
$filtre_actif = isset($_GET['filtrer']) || (isset($_POST['categorie']) && $_POST['categorie'] !== '');

if ($filtre_actif && $categorie !== '') {
    // Filtrer directement par nom de catégorie
    $filter_data = ['categorie' => $categorie];
    $donnee = Produits::find($db, $filter_data);
} else {
    $donnee = Produits::fetchAll($db);
}

// Marquer les pizzas comme indisponibles sur cette page
foreach ($donnee as &$produit) {
    if (strcasecmp($produit['categorie'], 'Pizza') === 0 || 
        stripos($produit['nom'], 'pizza') !== false) {
        $produit['stock_affiche'] = 0;
        $produit['pizza_indispo'] = true;
    } else {
        $produit['stock_affiche'] = (int)$produit['stock'];
        $produit['pizza_indispo'] = false;
    }
}
unset($produit);

// Trier : produits disponibles en premier, pizzas indispo en bas
usort($donnee, function($a, $b) {
    $stockA = (int)($a['stock_affiche'] ?? 0);
    $stockB = (int)($b['stock_affiche'] ?? 0);
    $pizzaA = !empty($a['pizza_indispo']);
    $pizzaB = !empty($b['pizza_indispo']);
    
    if ($stockA > 0 && ($stockB === 0 || $pizzaB)) return -1;
    if ($stockB > 0 && ($stockA === 0 || $pizzaA)) return 1;
    
    return (int)$a['id'] <=> (int)$b['id'];
});

// Gérer le panier
$action_panier = GETPOST('action_panier') ?? '';
$id_produit = filter_var(GETPOST('id_produit'), FILTER_VALIDATE_INT);
$quantite = max(1, (int)(GETPOST('quantite') ?? 1));

if ($action_panier === 'add' && $id_produit) {
    $produit = Produits::findById($db, $id_produit);
    
    // Bloquer les pizzas sur cette page
    $estPizza = (strcasecmp($produit['categorie'], 'Pizza') === 0 || 
                stripos($produit['nom'], 'pizza') !== false);
    
    if ($estPizza) {
        $_SESSION['mesgs']['errors'][] = 'Les pizzas ne sont pas disponibles actuellement.';
        header('Location: ?element=client&action=index');
        exit;
    }
    
    if ($produit && (int)$produit['stock'] >= $quantite) {
        if (!isset($_SESSION['panier'])) $_SESSION['panier'] = [];
        $_SESSION['panier'][$id_produit] = ($_SESSION['panier'][$id_produit] ?? 0) + $quantite;
    } else {
        $_SESSION['mesgs']['errors'][] = 'Produit indisponible ou stock insuffisant.';
    }
    
    header('Location: ?element=client&action=index');
    exit;
}

// Supprimer du panier
if ($action_panier === 'remove' && $id_produit) {
    if (isset($_SESSION['panier'][$id_produit])) {
        unset($_SESSION['panier'][$id_produit]);
    }
    header('Location: ?element=client&action=index');
    exit;
}

// Vider le panier
if ($action_panier === 'clear') {
    $_SESSION['panier'] = [];
    header('Location: ?element=client&action=index');
    exit;
}

// Préparer le panier
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
