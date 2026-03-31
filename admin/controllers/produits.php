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

$donnee = Produits::fetchAll($db);
