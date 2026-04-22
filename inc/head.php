<?php
/**
 * inc/head.php — En-tête HTML commun à toutes les pages
 */
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Borne Commande<?php if (!empty($title_page)) echo ' — ' . htmlspecialchars($title_page); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#14532d">

    <!-- Icônes -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
          integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Styles (ordre important : css/style.css est la source principale) -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Helpers PHP (fonctions flash, formaterPrix…) -->
    <?php require_once dirname(__FILE__) . '/../lib/helpers.php'; ?>
</head>
<body>

<!-- Navbar -->
<?php include dirname(__FILE__) . '/top.php'; ?>

<!-- Contenu principal -->
<div class="page-wrapper">