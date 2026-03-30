<?php
require_once dirname(__FILE__) . '/../../class/produits.class.php';

$produits = [];
if (isset($db) && $db) {
    $produits = Produits::fetchAll($db);
}

