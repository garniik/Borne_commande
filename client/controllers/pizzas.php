<?php
require_once(dirname(__FILE__) . '/../../class/produits.class.php');

// Vérifier si les pizzas sont disponibles (Jeu, Ven, Sam après 18h)
function pizzasDisponibles() {
    $jour = (int)date('N'); // 1=lundi, 7=dimanche
    $heure = (int)date('H');
    
    // Jeudi (4), Vendredi (5), Samedi (6) après 18h
    $joursAutorises = [4, 5, 6];
    $estJourAutorise = in_array($jour, $joursAutorises);
    $estHeureAutorisee = $heure >= 18;
    
    return $estJourAutorise && $estHeureAutorisee;
}

$pizzasDispo = pizzasDisponibles();
$messageHoraire = '';

if (!$pizzasDispo) {
    $jours = ['', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    $aujourdhui = $jours[(int)date('N')];
    $heure = date('H:i');
    $messageHoraire = "Les pizzas sont disponibles uniquement le Jeudi, Vendredi et Samedi à partir de 18h00.";
}

// Récupérer les paramètres de filtre
$categorie = $_GET['categorie'] ?? '';
$filtre_actif = isset($_GET['filtrer']) && $categorie !== '';

if ($filtre_actif) {
    $filter_data = ['categorie' => $categorie];
    $donnee = Produits::find($db, $filter_data);
} else {
    $donnee = Produits::fetchAll($db);
}

// Traiter les pizzas selon les horaires
foreach ($donnee as &$produit) {
    if (strcasecmp($produit['categorie'], 'Pizza') === 0 || 
        stripos($produit['nom'], 'pizza') !== false) {
        if (!$pizzasDispo) {
            // Simuler rupture de stock pour l'affichage
            $produit['stock_affiche'] = 0;
            $produit['pizza_indispo'] = true;
        } else {
            $produit['stock_affiche'] = (int)$produit['stock'];
            $produit['pizza_indispo'] = false;
        }
    } else {
        $produit['stock_affiche'] = (int)$produit['stock'];
        $produit['pizza_indispo'] = false;
    }
}
unset($produit);

// Trier : produits disponibles en premier, rupture/pizzas indispo en bas
usort($donnee, function($a, $b) {
    $stockA = (int)($a['stock_affiche'] ?? 0);
    $stockB = (int)($b['stock_affiche'] ?? 0);
    $pizzaA = !empty($a['pizza_indispo']);
    $pizzaB = !empty($b['pizza_indispo']);
    
    // Si A disponible et B non (ou pizza indispo)
    if ($stockA > 0 && ($stockB === 0 || $pizzaB)) return -1;
    // Si B disponible et A non (ou pizza indispo)
    if ($stockB > 0 && ($stockA === 0 || $pizzaA)) return 1;
    
    return (int)$a['id'] <=> (int)$b['id'];
});

// Gérer le panier
$action_panier = GETPOST('action_panier') ?? '';
$id_produit = filter_var(GETPOST('id_produit'), FILTER_VALIDATE_INT);
$quantite = max(1, (int)(GETPOST('quantite') ?? 1));

if ($action_panier === 'add' && $id_produit) {
    $produit = Produits::findById($db, $id_produit);
    
    // Vérifier si c'est une pizza et si elle est disponible
    $estPizza = (strcasecmp($produit['categorie'], 'Pizza') === 0 || 
                stripos($produit['nom'], 'pizza') !== false);
    
    if ($estPizza && !pizzasDisponibles()) {
        $_SESSION['mesgs']['errors'][] = 'Les pizzas ne sont pas disponibles actuellement.';
        header('Location: ?element=client&action=pizzas');
        exit;
    }
    
    if ($produit && (int)$produit['stock'] >= $quantite) {
        if (!isset($_SESSION['panier'])) $_SESSION['panier'] = [];
        $_SESSION['panier'][$id_produit] = ($_SESSION['panier'][$id_produit] ?? 0) + $quantite;
    } else {
        $_SESSION['mesgs']['errors'][] = 'Produit indisponible ou stock insuffisant.';
    }
    
    header('Location: ?element=client&action=pizzas');
    exit;
}

// Supprimer du panier
if ($action_panier === 'remove' && $id_produit) {
    if (isset($_SESSION['panier'][$id_produit])) {
        unset($_SESSION['panier'][$id_produit]);
    }
    header('Location: ?element=client&action=pizzas');
    exit;
}

// Vider le panier
if ($action_panier === 'clear') {
    $_SESSION['panier'] = [];
    header('Location: ?element=client&action=pizzas');
    exit;
}

// Préparer le panier
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
