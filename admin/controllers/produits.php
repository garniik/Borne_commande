<?php
/**
 * Contrôleur - Gestion des produits (Admin)
 * Accessible via : index.php?element=admin&action=produits
 */

require_once dirname(__FILE__) . '/../../class/produits.class.php';

$action_produit = GETPOST('action_produit') ?? 'list';

// ─── AJOUT D'UN PRODUIT ───────────────────────────────────────────────────────
if ($action_produit === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = [];

    $nom         = trim(GETPOST('nom') ?? '');
    $categorie   = trim(GETPOST('categorie') ?? '');
    $description = trim(GETPOST('description') ?? '');
    $prix_raw    = GETPOST('prix') ?? '';
    $stock_raw   = GETPOST('stock') ?? '';
    $image       = trim(GETPOST('image') ?? '');

    if (empty($nom))       $errors[] = 'Le nom du produit est obligatoire.';
    if (empty($categorie)) $errors[] = 'La catégorie est obligatoire.';
    if (!is_numeric($prix_raw) || (float)$prix_raw < 0)
                           $errors[] = 'Le prix doit être un nombre positif.';
    if (!is_numeric($stock_raw) || (int)$stock_raw < 0)
                           $errors[] = 'Le stock doit être un entier positif.';

    if (empty($errors)) {
        $produit = new Produits($db);
        $produit->hydrate([
            'nom'         => $nom,
            'categorie'   => $categorie,
            'description' => $description,
            'prix'        => (float)$prix_raw,
            'stock'       => (int)$stock_raw,
            'image'       => $image,
        ]);
        $produit->create();

        $_SESSION['mesgs']['success'][] = 'Produit <strong>' . htmlspecialchars($nom) . '</strong> ajouté avec succès.';
        header('Location: index.php?element=admin&action=produits');
        exit;

    } else {
        foreach ($errors as $err) {
            $_SESSION['mesgs']['errors'][] = $err;
        }
    }
}

// ─── SUPPRESSION D'UN PRODUIT ─────────────────────────────────────────────────
if ($action_produit === 'delete') {
    $id = filter_var(GETPOST('id'), FILTER_VALIDATE_INT);
    if ($id) {
        $produit = new Produits($db);
        $produit->hydrate(['id' => $id]);
        $produit->delete();
        $_SESSION['mesgs']['success'][] = 'Produit supprimé avec succès.';
    }
    header('Location: index.php?element=admin&action=produits');
    exit;
}

// ─── RÉCUPÉRATION DES PRODUITS ────────────────────────────────────────────────
$search_data = [
    'nom'       => GETPOST('search_nom')       ?? '',
    'categorie' => GETPOST('search_categorie') ?? '',
];

if (!empty(array_filter($search_data))) {
    $produits = Produits::find($db, $search_data);
} else {
    $produits = Produits::fetchAll($db);
}

$produits = $produits ?? [];

// Stats rapides
$total_produits = count($produits);
$total_stock    = array_sum(array_column($produits, 'stock'));
$valeur_stock   = array_sum(array_map(fn($p) => $p['prix'] * $p['stock'], $produits));

// Catégories distinctes pour le filtre
$categories = array_unique(array_column($produits, 'categorie'));
sort($categories);