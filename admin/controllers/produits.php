<?php
/**
 * admin/controllers/produits.php
 *
 * Gère : ajout produit (avec upload image), suppression,
 *        ajout de stock, définition de stock.
 */

require_once dirname(__FILE__) . '/../../class/produits.class.php';

// ── Dossier cible pour les images ─────────────────────────────────
// On remonte depuis ce fichier jusqu'à la racine du projet, puis public/images/
define('IMG_DIR', dirname(__FILE__) . '/../../public/images/');

// Types MIME autorisés pour l'upload
const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5 Mo

/**
 * Gère l'upload d'une image et retourne le nom du fichier sauvegardé,
 * ou une chaîne vide si aucun fichier n'a été envoyé.
 * En cas d'erreur, ajoute un message dans $_SESSION['mesgs']['errors'].
 */
function traiterUploadImage(string $fileField = 'image', ?string $nomProduit = null): string
{
    // Aucun fichier envoyé ou champ vide → pas d'image
    if (empty($_FILES[$fileField]) || $_FILES[$fileField]['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    $file = $_FILES[$fileField];

    // Vérification des erreurs PHP d'upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['mesgs']['errors'][] = 'Erreur lors de l\'upload (code ' . $file['error'] . ').';
        return '';
    }

    // Vérification de la taille
    if ($file['size'] > MAX_SIZE_BYTES) {
        $_SESSION['mesgs']['errors'][] = 'L\'image dépasse la taille maximale autorisée (5 Mo).';
        return '';
    }

    // Vérification du type MIME réel (pas juste l'extension déclarée)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, ALLOWED_MIME, true)) {
        $_SESSION['mesgs']['errors'][] = 'Format d\'image non supporté. Utilisez JPG, PNG, WebP ou GIF.';
        return '';
    }

    // Construction du nom de fichier : slug du nom produit + timestamp + extension
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    $ext = $extensions[$mimeType];

    // Slug à partir du nom du produit pour un nom lisible
    $nomProd  = $nomProduit ?? $_POST['nom'] ?? 'produit';
    $slug        = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($nomProd)));
    $slug        = trim($slug, '-') ?: 'produit';
    $nomFichier  = $slug . '-' . time() . '.' . $ext;
    $destination = IMG_DIR . $nomFichier;

    // Déplacement du fichier temporaire vers la destination finale
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $_SESSION['mesgs']['errors'][] = 'Impossible de sauvegarder l\'image. Vérifiez les permissions du dossier public/images/.';
        return '';
    }

    return $nomFichier; // Ce nom sera stocké en BDD
}

// ── Ajout d'un produit ────────────────────────────────────────────
if (isset($_POST['add'])) {

    $nomImage = traiterUploadImage(); // '' si pas d'image ou erreur

    // Si traiterUploadImage a ajouté une erreur, on n'insère pas
    if (empty($_SESSION['mesgs']['errors'])) {
        $produit = new Produits($db);
        $infiniteStock = $_POST['infinite_stock'] ?? 0;
        $stockValue = $infiniteStock == 1 ? 999999 : ($_POST['stock'] ?? 0);
        
        $produit->hydrate([
            'nom'            => $_POST['nom']            ?? '',
            'categorie'      => $_POST['categorie']      ?? '',
            'prix'           => $_POST['prix']           ?? 0,
            'stock'          => $stockValue,
            'description'    => $_POST['description']    ?? '',
            'image'          => $nomImage,                // nom du fichier, pas une URL
            'seul'           => $_POST['seul']           ?? 0,
            'infinite_stock' => $infiniteStock,
        ]);
        $produit->create();
    }

    header('Location: index.php?element=admin&action=produits');
    exit;
}

// ── Suppression d'un produit ──────────────────────────────────────
if (isset($_POST['delete'])) {
    // Récupérer le nom de l'image avant de supprimer le produit
    $row = Produits::findById($db, (int)$_POST['id']);
    if ($row && !empty($row['image'])) {
        $cheminImage = IMG_DIR . basename($row['image']); // basename() = sécurité
        if (is_file($cheminImage)) {
            unlink($cheminImage); // Supprime aussi le fichier physique
        }
    }

    $produit = new Produits($db);
    $produit->hydrate(['id' => (int)$_POST['id']]);
    $produit->delete();

    header('Location: index.php?element=admin&action=produits');
    exit;
}

// ── Ajout de stock ────────────────────────────────────────────────
if (isset($_POST['add_stock'])) {
    $quantite = $_POST['quantite'] ?? '';
    if ($quantite === '') {
        $_SESSION['mesgs']['errors'][] = 'Veuillez saisir une quantité pour ajouter au stock.';
    } else {
        $produit = new Produits($db);
        $produit->hydrate(['id' => (int)$_POST['id']]);
        $produit->addStock((int)$quantite);
    }
}

// ── Définition du stock ───────────────────────────────────────────
if (isset($_POST['set_stock'])) {
    $quantite = $_POST['quantite'] ?? '';
    if ($quantite === '') {
        $_SESSION['mesgs']['errors'][] = 'Veuillez saisir une quantité pour définir le stock.';
    } else {
        $produit = new Produits($db);
        $produit->hydrate(['id' => (int)$_POST['id']]);
        $produit->setStock((int)$quantite);
    }
}

// ── Mise à jour d'un produit ──────────────────────────────────────
if (isset($_POST['update'])) {
    $produit = new Produits($db);
    $id_produit = (int)$_POST['edit_id'];
    $infiniteStock = $_POST['edit_infinite_stock'] ?? 0;
    $stockValue = $infiniteStock == 1 ? 999999 : ($_POST['edit_stock'] ?? 0);
    
    // Gestion du changement d'image
    $ancienneImage = $_POST['image_current'] ?? '';
    $nouvelleImage = traiterUploadImage('edit_image', $_POST['edit_nom'] ?? '');
    
    // Si nouvelle image uploadée avec succès, supprimer l'ancienne
    if ($nouvelleImage && !empty($ancienneImage)) {
        $cheminAncienne = IMG_DIR . basename($ancienneImage);
        if (is_file($cheminAncienne)) {
            unlink($cheminAncienne);
        }
    }
    
    // Utiliser la nouvelle image si uploadée, sinon conserver l'actuelle
    $imageFinale = $nouvelleImage ?: $ancienneImage;
    
    $produit->hydrate([
        'id'             => $id_produit,
        'nom'            => $_POST['edit_nom']         ?? '',
        'categorie'      => $_POST['edit_categorie']   ?? '',
        'prix'           => $_POST['edit_prix']        ?? 0,
        'stock'          => $stockValue,
        'description'    => $_POST['edit_description'] ?? '',
        'image'          => $imageFinale,
        'seul'           => $_POST['edit_seul']        ?? 0,
        'infinite_stock' => $infiniteStock,
    ]);
    $produit->update();
    
    header('Location: index.php?element=admin&action=produits');
    exit;
}

// ── Récupération des produits (avec filtres si présents) ──────────
$search = $_GET['search'] ?? null;
$categorie = $_GET['categorie'] ?? null;

if ($search !== null || $categorie !== null) {
    $donnee = Produits::search($db, $search, $categorie);
} else {
    $donnee = Produits::fetchAll($db);
}