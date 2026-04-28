<?php
require_once dirname(__FILE__) . '/../../class/commandes.class.php';
require_once dirname(__FILE__) . '/../../class/produits.class.php';

// Valider (supprimer) une commande
if (isset($_POST['validate_commande'])) {
    $commande = new Commandes($db);
    $commande->hydrate(['id' => $_POST['id']]);
    $commande->delete();
}

// Annuler une commande (restaurer le stock puis supprimer)
if (isset($_POST['cancel_commande'])) {
    $id_commande = (int)$_POST['id'];
    
    // Récupérer les produits de la commande
    $commande = new Commandes($db);
    $produits = $commande->getProduitsCommande($id_commande);
    
    // Restaurer le stock de chaque produit (sauf stock infini)
    foreach ($produits as $produit) {
        if (empty($produit['infinite_stock'])) {
            $p = new Produits($db);
            $p->hydrate(['id' => $produit['id_produit']]);
            $p->addStock((int)$produit['quantite']);
        }
    }
    
    // Supprimer la commande
    $commande->hydrate(['id' => $id_commande]);
    $commande->delete();
    
    $_SESSION['mesgs']['success'][] = 'Commande annulée et stock restauré';
    header('Location: index.php?element=admin&action=commandes');
    exit;
}

$commandes = (new Commandes($db))->findAll();
?>