<?php
require_once dirname(__FILE__) . '/../../class/commandes.class.php';
require_once dirname(__FILE__) . '/../../class/produits.class.php';

// Traitement du formulaire de validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $id_borne = (int)($_POST['id_borne'] ?? 0);
    $panier = $_SESSION['panier'] ?? [];

    if (empty($panier)) {
        $_SESSION['mesgs']['errors'][] = 'Votre panier est vide.';
        header('Location: ?element=client&action=index');
        exit;
    }

    // Créer la commande
    $commande = new Commandes($db);
    $commande->tel = $tel;
    $commande->id_borne = $id_borne;
    if ($commande->create()) {
        // Ajouter chaque produit du panier à la commande
        foreach ($panier as $id_produit => $quantite) {
            $commande->addProduit($id_produit, $quantite);
        }
        $_SESSION['mesgs']['success'][] = 'Commande enregistrée avec succès.';
        unset($_SESSION['panier']);
        header('Location: ?element=client&action=merci');
        exit;
    } else {
        $_SESSION['mesgs']['errors'][] = 'Erreur lors de la création de la commande.';
    }
}
