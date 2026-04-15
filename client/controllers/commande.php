<?php
require_once dirname(__FILE__) . '/../../class/commandes.class.php';
require_once dirname(__FILE__) . '/../../class/produits.class.php';

// Récupérer les bornes déjà utilisées pour l'affichage
$commande_temp = new Commandes($db);
$bornes_utilisees = $commande_temp->getBornesUtilisees();

// ── Traitement du formulaire de validation ────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $panier = $_SESSION['panier'] ?? [];

    // Vérifier que le panier n'est pas vide
    if (empty($panier)) {
        $_SESSION['mesgs']['errors'][] = 'Votre panier est vide.';
        header('Location: ?element=client&action=index');
        exit;
    }

    // Validation du numéro de borne
    $num_borne = filter_input(INPUT_POST, 'num_borne', FILTER_VALIDATE_INT);
    
    // Vérifier que la borne est entre 1 et 24
    if (!$num_borne || $num_borne < 1 || $num_borne > 24) {
        $_SESSION['mesgs']['errors'][] = 'Veuillez sélectionner une borne valide (de 1 à 24).';
        header('Location: ?element=client&action=commande');
        exit;
    }
    
    // Vérifier que la borne n'est pas déjà utilisée
    $commande_check = new Commandes($db);
    if (!$commande_check->isBorneDisponible($num_borne)) {
        $_SESSION['mesgs']['errors'][] = "La borne $num_borne est déjà utilisée. Veuillez choisir une autre borne.";
        header('Location: ?element=client&action=commande');
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

        unset($_SESSION['panier']);
        
        // Rediriger vers la page source (index ou pizza)
        $source = GETPOST('source') ?? 'index';
        $redirect_action = in_array($source, ['index', 'pizza']) ? $source : 'index';
        
        header('Location: ?element=client&action=' . $redirect_action);
        exit;

    } else {
        $_SESSION['mesgs']['errors'][] = 'Erreur lors de la création de la commande.';
        header('Location: ?element=client&action=commande');
        exit;
    }
}
