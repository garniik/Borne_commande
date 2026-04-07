<?php
// Afficher les erreurs (temporaire, à retirer en prod)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__) . '/../../class/commandes.class.php';
require_once dirname(__FILE__) . '/../../class/produits.class.php';

// ── Traitement du formulaire de validation ────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $panier = $_SESSION['panier'] ?? [];

    // Vérifier que le panier n'est pas vide
    if (empty($panier)) {
        $_SESSION['mesgs']['errors'][] = 'Votre panier est vide.';
        header('Location: ?element=client&action=index');
        exit;
    }

    // ── Création de la commande ───────────────────────
    $commande = new Commandes($db);
    $commande->hydrate($_POST);

    if ($commande->create()) {
        $id_commande = $commande->getId();

        // Ajouter chaque produit et déduire le stock
        foreach ($panier as $id_produit => $quantite) {
            $commande->addProduit($id_produit, $quantite);

            $produit = new Produits($db);
            $produit->hydrate(['id' => $id_produit]);
            $produit->addStock(-$quantite); // valeur négative = déduction
        }

        $_SESSION['mesgs']['confirm'][] = 'Commande enregistrée avec succès !';
        unset($_SESSION['panier']);
        header('Location: ?element=client&action=index');
        exit;

    } else {
        $_SESSION['mesgs']['errors'][] = 'Erreur lors de la création de la commande.';
        header('Location: ?element=client&action=commande');
        exit;
    }
}
