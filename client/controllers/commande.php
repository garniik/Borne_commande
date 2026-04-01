<?php
// Afficher les erreurs PHP (temporaire)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Forcer l'écriture des logs dans un fichier accessible
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/commande_debug.log');

require_once dirname(__FILE__) . '/../../class/commandes.class.php';
require_once dirname(__FILE__) . '/../../class/produits.class.php';

// Traitement du formulaire de validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST reçu : " . print_r($_POST, true));
    $nom = trim($_POST['nom'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $id_borne = (int)($_POST['id_borne'] ?? 0);
    $panier = $_SESSION['panier'] ?? [];
    error_log("Panier : " . print_r($panier, true));

    if (empty($panier)) {
        $_SESSION['mesgs']['errors'][] = 'Votre panier est vide.';
        header('Location: ?element=client&action=index');
        exit;
    }

    // Créer la commande
    $commande = new Commandes($db);
    $commande->hydrate($_POST);
    if ($commande->create()) {
        // Debug : affiche l'ID de la commande
        error_log("ID commande créé : " . $commande->id);
        // Ajouter chaque produit du panier à la commande
        foreach ($panier as $id_produit => $quantite) {
            error_log("Ajout produit $id_produit qty $quantite à commande {$commande->id}");
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
