<?php
require_once dirname(__FILE__) . '/../../class/produits.class.php';

$db_ok = false;
$db_error = null;

if (!isset($db) || !$db) {
    $db_error = 'DB non initialisée (variable $db null).';
    if (!empty($_SESSION['mesgs']['errors'])) {
        $db_error .= "\n" . implode("\n", $_SESSION['mesgs']['errors']);
    }
} else {
    try {
        $db->query('SELECT 1');
        $db_ok = true;
    } catch (Throwable $e) {
        $db_error = $e->getMessage();
    }
}

$produits = $db_ok ? Produits::fetchAll($db) : [];


