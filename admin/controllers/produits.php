<?php
require_once(dirname(__FILE__) . '/../../class/produits.class.php');

if (isset($_POST['add'])) {
    $produit = new Produits($db);
    $produit->hydrate([
        'nom' => $_POST['nom'],
        'categorie' => $_POST['categorie'],
        'prix' => $_POST['prix'],
        'stock' => $_POST['stock'],
        'description' => $_POST['description'],
        'image' => $_POST['image'],
    ]);
    $produit->create();
}

if (isset($_POST['delete'])) {
    $produit = new Produits($db);
    $produit->hydrate(['id' => $_POST['id']]);
    $produit->delete();
}

if (isset($_POST['add_stock'])) {
    $quantite = $_POST['quantite'] ?? '';
    if ($quantite === '' || $quantite === null) {
        $_SESSION['mesgs']['errors'][] = 'Veuillez saisir une quantité pour ajouter au stock.';
    } else {
        $produit = new Produits($db);
        $produit->hydrate(['id' => $_POST['id']]);
        $produit->addStock((int)$quantite);
    }
}

if (isset($_POST['set_stock'])) {
    $quantite = $_POST['quantite'] ?? '';
    if ($quantite === '' || $quantite === null) {
        $_SESSION['mesgs']['errors'][] = 'Veuillez saisir une quantité pour définir le stock.';
    } else {
        $produit = new Produits($db);
        $produit->hydrate(['id' => $_POST['id']]);
        $produit->setStock((int)$quantite);
    }
}

$donnee = Produits::fetchAll($db);
