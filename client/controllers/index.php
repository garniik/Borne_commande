<?php

require_once(dirname(__FILE__) . '/../../class/produits.class.php');

if(isset($_GET['filtrer'])){
    $donnee = Produits::find($db,$_GET);
} else{
    $donnee = Produits::fetchAll($db);
}
