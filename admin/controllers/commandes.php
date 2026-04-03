<?php
require_once dirname(__FILE__) . '/../../class/commandes.class.php';

// Supprimer une commande
if (isset($_POST['delete_commande'])) {
    $commande = new Commandes($db);
    $commande->hydrate(['id' => $_POST['id']]);
    $commande->delete();
}

$commandes = (new Commandes($db))->findAll();
?>