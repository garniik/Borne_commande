<?php
require_once(dirname(__FILE__) . '/../../class/produits.class.php');

// Récupérer les paramètres de filtre (GET ou POST)
$categorie = $_GET['categorie'] ?? $_POST['categorie'] ?? '';
$filtre_actif = isset($_GET['filtrer']) || (isset($_POST['categorie']) && $_POST['categorie'] !== '');

if ($filtre_actif && $categorie !== '') {
    // Convertir l'ID de catégorie en nom
    $categories = ['1' => 'Boisson', '2' => 'Snack', '3' => 'Nourriture'];
    $nom_categorie = $categories[$categorie] ?? $categorie;
    
    $filter_data = ['categorie' => $nom_categorie];
    $donnee = Produits::find($db, $filter_data);
} else {
    $donnee = Produits::fetchAll($db);
}

// Gérer le panier (simple session)
$action_panier = GETPOST('action_panier') ?? '';
$id_produit = filter_var(GETPOST('id_produit'), FILTER_VALIDATE_INT);
$quantite = max(1, (int)(GETPOST('quantite') ?? 1));

if ($action_panier === 'add' && $id_produit) {
    $produit = Produits::findById($db, $id_produit);
    if ($produit && (int)$produit['stock'] >= $quantite) {
        if (!isset($_SESSION['panier'])) $_SESSION['panier'] = [];
        $_SESSION['panier'][$id_produit] = ($_SESSION['panier'][$id_produit] ?? 0) + $quantite;
        $_SESSION['mesgs']['success'][] = 'Produit ajouté au panier.';
    } else {
        $_SESSION['mesgs']['errors'][] = 'Produit indisponible ou stock insuffisant.';
    }
    
    // Préserver les paramètres de filtre lors de la redirection
    $redirect_url = '?element=client&action=index';
    if (isset($_POST['categorie']) && $_POST['categorie'] !== '') {
        // Chercher l'ID correspondant au nom de catégorie
        $categories_noms_to_ids = ['Boisson' => '1', 'Snack' => '2', 'Nourriture' => '3'];
        $categorie_id = $categories_noms_to_ids[$_POST['categorie']] ?? $_POST['categorie'];
        $redirect_url .= '&categorie=' . urlencode($categorie_id) . '&filtrer=1';
    }
    header('Location: ' . $redirect_url);
    exit;
}

// Supprimer un article du panier
if ($action_panier === 'remove' && $id_produit) {
    if (isset($_SESSION['panier'][$id_produit])) {
        unset($_SESSION['panier'][$id_produit]);
        $_SESSION['mesgs']['success'][] = 'Produit retiré du panier.';
    }
    header('Location: ?element=client&action=index');
    exit;
}

// Vider tout le panier
if ($action_panier === 'clear') {
    $_SESSION['panier'] = [];
    $_SESSION['mesgs']['success'][] = 'Panier vidé.';
    header('Location: ?element=client&action=index');
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
