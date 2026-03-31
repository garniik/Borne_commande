<?php
require_once(dirname(__FILE__) . '/../../class/produits.class.php');

// Récupérer tous les produits
$produits = Produits::fetchAll($db);
$produits = $produits ?? [];

// Extraire les catégories uniques
$categories = array_unique(array_column($produits, 'categorie'));
sort($categories);

// Filtrer par catégorie si demandé
if (isset($_GET['categorie']) && in_array($_GET['categorie'], $categories)) {
    $produits = array_filter($produits, fn($p) => $p['categorie'] === $_GET['categorie']);
}
