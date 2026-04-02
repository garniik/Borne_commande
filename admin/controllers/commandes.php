<?php
require_once dirname(__FILE__) . '/../../class/commandes.class.php';

$commandes = (new Commandes($db))->findAll();
?>