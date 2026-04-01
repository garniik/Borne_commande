<?php
require_once(dirname(__FILE__) . '/../../class/produits.class.php');

if(isset($_GET['filtrer'])){
    $donnee = Produits::find($db,$_GET);
} else{
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
