<?php
// Afficher les erreurs PHP (temporaire)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__) . '/../../class/commandes.class.php';
require_once dirname(__FILE__) . '/../../class/produits.class.php';

$debug = []; // Pour afficher sur la page

// Traitement du formulaire de validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debug[] = "POST reçu : " . print_r($_POST, true);
    $nom = trim($_POST['nom'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $id_borne = (int)($_POST['id_borne'] ?? 0);
    $panier = $_SESSION['panier'] ?? [];
    $debug[] = "Panier : " . print_r($panier, true);

    if (empty($panier)) {
        $_SESSION['mesgs']['errors'][] = 'Votre panier est vide.';
        header('Location: ?element=client&action=index');
        exit;
    }

    // Créer la commande
    $commande = new Commandes($db);
    $commande->hydrate($_POST);
    if ($commande->create()) {
        $debug[] = "ID commande créé : " . $commande->id;
        // Ajouter chaque produit du panier à la commande
        foreach ($panier as $id_produit => $quantite) {
            $debug[] = "Ajout produit $id_produit qty $quantite à commande {$commande->id}";
            $commande->addProduit($id_produit, $quantite);
        }
        $_SESSION['mesgs']['success'][] = 'Commande enregistrée avec succès.';
        unset($_SESSION['panier']);
        header('Location: ?element=client&action=merci');
        exit;
    } else {
        $_SESSION['mesgs']['errors'][] = 'Erreur lors de la création de la commande.';
        header('Location: ?element=client&action=commande');
        exit;
    }
}
