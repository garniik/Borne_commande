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

    // Récupérer et nettoyer le numéro de téléphone
    $phone_brut  = trim($_POST['phone'] ?? '');
    $phone_clean = preg_replace('/[\s\-\.]/', '', $phone_brut); // supprime espaces, tirets, points

    // ── Validation PHP du téléphone ──────────────────
    // Accepte : 10 chiffres locaux (0XXXXXXXXX) ou international (+XX... entre 7 et 15 chiffres)
    $phone_regex = '/^(\+\d{7,15}|0\d{9})$/';

    if (empty($phone_brut)) {
        $_SESSION['mesgs']['errors'][] = 'Le numéro de téléphone est obligatoire.';
        header('Location: ?element=client&action=commande');
        exit;
    }

    if (!preg_match($phone_regex, $phone_clean)) {
        $_SESSION['mesgs']['errors'][] = 'Numéro de téléphone invalide. '
            . 'Formats acceptés : 0612345678 ou +33612345678.';
        header('Location: ?element=client&action=commande');
        exit;
    }

    // ── Création de la commande ───────────────────────
    // On passe le numéro nettoyé dans $_POST pour que hydrate() le récupère proprement
    $_POST['phone'] = $phone_clean;

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
