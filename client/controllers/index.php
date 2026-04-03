<?php
require_once(dirname(__FILE__) . '/../../class/produits.class.php');

// Récupérer les paramètres de filtre (GET ou POST)
$categorie = $_GET['categorie'] ?? $_POST['categorie'] ?? '';
$filtre_actif = isset($_GET['filtrer']) || (isset($_POST['categorie']) && $_POST['categorie'] !== '');

if ($filtre_actif && $categorie !== '') {
    $filter_data = ['categorie' => $categorie];
    // Debug pour voir les données
    $_SESSION['mesgs']['success'][] = 'Debug: Filtre catégorie = ' . $categorie;
    $donnee = Produits::find($db, $filter_data);
    // Debug pour voir le nombre de résultats
    $_SESSION['mesgs']['success'][] = 'Debug: Nombre de produits trouvés = ' . count($donnee ?? []);
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
        $redirect_url .= '&categorie=' . urlencode($_POST['categorie']) . '&filtrer=1';
    }
    header('Location: ' . $redirect_url);
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
