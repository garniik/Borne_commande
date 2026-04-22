<?php
/**
 * admin/views/produits.php — Gestion des produits (admin)
 *
 * Variable disponible depuis le contrôleur : $donnee (array de produits)
 *
 * Chemin des images : public/images/{nom_fichier}
 * Le champ `image` en BDD contient uniquement le nom du fichier.
 */

// Helper : construit le src de l'image d'un produit
function srcImage(string $nomFichier): string
{
    if (empty($nomFichier)) return '';
    return 'public/images/' . htmlspecialchars(basename($nomFichier));
}
?>

<!-- En-tête de page avec navigation et bouton -->
<div class="admin-header">

    <!-- Gauche : navigation -->
    <div class="admin-header-left">
        <div class="admin-nav">
            <a href="?element=admin&action=produits" class="btn btn-primary">
                <i class="fa-solid fa-box"></i> Produits
            </a>
            <a href="?element=admin&action=commandes" class="btn btn-ghost">
                <i class="fa-solid fa-clipboard-list"></i> Commandes
            </a>
        </div>
    </div>

    <!-- Centre : titre -->
    <div class="admin-header-center">
        <h1 class="page-title">
            <span>Gestion</span> des produits
        </h1>
        <p class="page-subtitle">
            <?= count($donnee ?? []) ?> produit<?= count($donnee ?? []) > 1 ? 's' : '' ?> enregistré<?= count($donnee ?? []) > 1 ? 's' : '' ?>
        </p>
    </div>

    <!-- Droite : bouton -->
    <div class="admin-header-right">
        <button class="btn btn-primary" onclick="toggleForm()">
            <i class="fa-solid fa-plus"></i>
            Nouveau produit
        </button>
    </div>

</div>

<?php afficherMessagesFlash(); ?>


<!-- ══════════════════════════════════════════════════════
     FORMULAIRE D'AJOUT
     Important : enctype="multipart/form-data" obligatoire pour l'upload
══════════════════════════════════════════════════════ -->
<div id="formAjout" class="card collapse-panel">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-box-open"></i>
            Ajouter un produit
        </div>
        <button class="btn btn-ghost btn-sm" onclick="toggleForm()">
            <i class="fa-solid fa-xmark"></i> Fermer
        </button>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label" for="nom">Nom du produit *</label>
                    <input type="text" id="nom" name="nom" class="form-control"
                           placeholder="Ex. Coca-Cola" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="categorie">Catégorie *</label>
                    <select id="categorie" name="categorie" class="form-control" required>
                        <option value="">— Choisir —</option>
                        <option value="Soft">Soft</option>
                        <option value="Chaud">Boissons Chaudes</option>
                        <option value="Bière">Bière</option>
                        <option value="Cocktail">Cocktail</option>
                        <option value="Snack">Snack</option>
                        <option value="Pizza">Pizza</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="prix">Prix (€) *</label>
                    <input type="number" id="prix" name="prix" class="form-control"
                           placeholder="0.00" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="stock">Stock initial *</label>
                    <input type="number" id="stock" name="stock" class="form-control"
                           placeholder="0" min="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label checkbox-label">
                        <input type="checkbox" id="seul" name="seul" value="1" class="checkbox-input">
                        <span>Peut être commandé seul</span>
                    </label>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control"
                              placeholder="Description courte du produit…"></textarea>
                </div>

                <!-- ── Champ upload image ──────────────────────── -->
                <div class="form-group form-full">
                    <label class="form-label">Image du produit</label>

                    <!-- Zone de drop / clic tactile -->
                    <div class="upload-zone" id="uploadZone" onclick="document.getElementById('image').click()">
                        <!-- Prévisualisation (cachée au départ) -->
                        <img id="previewImg" src="" alt="Aperçu" class="upload-preview" style="display:none;">

                        <!-- Placeholder affiché quand pas d'image -->
                        <div class="upload-placeholder" id="uploadPlaceholder">
                            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                            <div class="upload-label">Appuyer pour choisir une image</div>
                            <div class="upload-hint">JPG, PNG, WebP ou GIF · max 5 Mo</div>
                        </div>

                        <!-- Nom du fichier sélectionné -->
                        <div class="upload-filename" id="uploadFilename" style="display:none;"></div>
                    </div>

                    <!-- Input file réel (invisible, déclenché par la zone) -->
                    <input type="file"
                           id="image"
                           name="image"
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           class="hidden-input"
                           onchange="previewImage(this)">

                    <!-- Bouton pour effacer la sélection -->
                    <button type="button"
                            id="btnClearImage"
                            class="btn btn-ghost btn-sm btn-clear-image"
                            onclick="clearImage()">
                        <i class="fa-solid fa-xmark"></i> Retirer l'image
                    </button>
                </div>
                <!-- ── Fin champ upload ────────────────────────── -->

            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-ghost" onclick="toggleForm()">Annuler</button>
                <button type="submit" name="add" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Ajouter le produit
                </button>
            </div>
        </form>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════
     TABLEAU DES PRODUITS
══════════════════════════════════════════════════════ -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-list"></i>
            Liste des produits
        </div>
    </div>

    <!-- ── Filtres de recherche ─────────────────────────────── -->
    <div class="card-body">
        <form method="GET" action="" class="filter-bar">
            <input type="hidden" name="element" value="admin">
            <input type="hidden" name="action" value="produits">

            <div class="filter-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="search" name="search" class="form-control"
                       placeholder="Rechercher un produit..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>

            <select id="filtre_categorie" name="categorie" class="form-control filter-select">
                <option value="">Toutes les catégories</option>
                <option value="Soft" <?= ($_GET['categorie'] ?? '') === 'Soft' ? 'selected' : '' ?>>Soft</option>
                <option value="Chaud" <?= ($_GET['categorie'] ?? '') === 'Chaud' ? 'selected' : '' ?>>Boissons Chaudes</option>
                <option value="Bière" <?= ($_GET['categorie'] ?? '') === 'Bière' ? 'selected' : '' ?>>Bière</option>
                <option value="Cocktail" <?= ($_GET['categorie'] ?? '') === 'Cocktail' ? 'selected' : '' ?>>Cocktail</option>
                <option value="Snack" <?= ($_GET['categorie'] ?? '') === 'Snack' ? 'selected' : '' ?>>Snack</option>
                <option value="Pizza" <?= ($_GET['categorie'] ?? '') === 'Pizza' ? 'selected' : '' ?>>Pizza</option>
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-filter"></i>
            </button>
            <a href="?element=admin&action=produits" class="btn btn-ghost" title="Réinitialiser">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        </form>
    </div>

    <style>
    .filter-bar {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    .filter-search {
        position: relative;
        flex: 1;
        min-width: 250px;
    }
    .filter-search i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }
    .filter-search input {
        padding-left: 36px;
        width: 100%;
    }
    .filter-select {
        width: auto;
        min-width: 160px;
    }
    </style>

    <?php if (!empty($donnee)): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnee as $produit):
                        $stock = (int)$produit['stock'];
                        if ($stock === 0)    { $sc = 'badge-red';    $sl = 'Rupture'; }
                        elseif ($stock <= 3) { $sc = 'badge-yellow'; $sl = $stock . ' restant(s)'; }
                        else                { $sc = 'badge-green';  $sl = $stock; }
                    ?>
                        <tr>
                            <!-- Nom + thumbnail -->
                            <td>
                                <div class="prod-cell">
                                    <div class="prod-thumb">
                                        <?php $src = srcImage($produit['image']); ?>
                                        <?php if ($src): ?>
                                            <img src="<?= $src ?>"
                                                 alt="<?= htmlspecialchars($produit['nom']) ?>"
                                                 loading="lazy"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <i class="fa-solid fa-image"
                                               style="display:none; color:var(--text-muted); font-size:.9rem;"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-image" style="color:var(--text-muted); font-size:.9rem;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="prod-cell-name"><?= htmlspecialchars($produit['nom']) ?></div>
                                        <?php if (!empty($produit['description'])): ?>
                                            <div class="prod-cell-desc">
                                                <?= htmlspecialchars(mb_strimwidth($produit['description'], 0, 50, '…')) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge badge-orange"><?= htmlspecialchars($produit['categorie']) ?></span>
                            </td>

                            <td class="prix-cell"><?= formaterPrix($produit['prix']) ?></td>

                            <td>
                                <span class="badge <?= $sc ?>"><?= $sl ?></span>
                            </td>

                            <!-- Actions -->
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" onclick="ouvrirModalEdit(<?= (int)$produit['id'] ?>, '<?= htmlspecialchars(addslashes($produit['nom'])) ?>', '<?= htmlspecialchars(addslashes($produit['categorie'])) ?>', <?= (float)$produit['prix'] ?>, <?= (int)$produit['stock'] ?>, <?= (int)($produit['seul'] ?? 0) ?>, '<?= htmlspecialchars(addslashes(str_replace(["\r\n", "\r", "\n"], "\\n", $produit['description'] ?? ''))) ?>')">
                                    <i class="fa-solid fa-pen"></i> Modifier
                                </button>
                                <form method="post" class="delete-form" onsubmit="return confirm('Supprimer ce produit et son image ?')">
                                    <input type="hidden" name="id" value="<?= (int)$produit['id'] ?>">
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i> Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-box-open"></i>
            <p>Aucun produit enregistré. Ajoutez votre premier produit ci-dessus.</p>
        </div>
    <?php endif; ?>
</div>


<style>
/* ── Zone d'upload image ─────────────────────────────────────────── */
.upload-zone {
    border: 2px dashed var(--border-color, #d1d5db);
    border-radius: 12px;
    padding: 28px 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    background: var(--surface2, #f9fafb);
    position: relative;
    min-height: 140px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}

.upload-zone:hover,
.upload-zone:focus-within {
    border-color: var(--accent, #0ea5e9);
    background: #f0f9ff;
}

.upload-zone.has-image {
    padding: 12px;
    border-style: solid;
    border-color: var(--accent, #0ea5e9);
}

.upload-icon {
    font-size: 2.2rem;
    color: var(--text-muted, #94a3b8);
    display: block;
}

.upload-label {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text, #0f172a);
}

.upload-hint {
    font-size: .8rem;
    color: var(--text-muted, #94a3b8);
}

.upload-preview {
    max-width: 100%;
    max-height: 220px;
    border-radius: 8px;
    object-fit: contain;
    box-shadow: 0 2px 12px rgba(0,0,0,.12);
}

.upload-filename {
    font-size: .82rem;
    font-weight: 600;
    color: var(--accent, #0ea5e9);
    margin-top: 6px;
    word-break: break-all;
}
</style>


<script>
/* ── Formulaire toggle ──────────────────────────────────────────── */
function toggleForm() {
    const panel = document.getElementById('formAjout');
    panel.classList.toggle('open');
    if (panel.classList.contains('open')) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/* ── Prévisualisation de l'image sélectionnée ───────────────────── */
function previewImage(input) {
    const zone        = document.getElementById('uploadZone');
    const preview     = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    const filename    = document.getElementById('uploadFilename');
    const btnClear    = document.getElementById('btnClearImage');

    if (!input.files || !input.files[0]) return;

    const file   = input.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        // Afficher la prévisualisation
        preview.src          = e.target.result;
        preview.style.display = 'block';

        // Cacher le placeholder, afficher le nom
        placeholder.style.display = 'none';
        filename.textContent       = file.name;
        filename.style.display     = 'block';

        // Passer la zone en mode "image chargée"
        zone.classList.add('has-image');

        // Afficher le bouton "retirer"
        btnClear.style.display = 'inline-flex';
    };

    reader.readAsDataURL(file);
}

/* ── Effacer la sélection ───────────────────────────────────────── */
function clearImage() {
    const input       = document.getElementById('image');
    const zone        = document.getElementById('uploadZone');
    const preview     = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    const filename    = document.getElementById('uploadFilename');
    const btnClear    = document.getElementById('btnClearImage');

    // Reset input file
    input.value = '';

    // Remettre le placeholder
    preview.style.display      = 'none';
    preview.src                = '';
    placeholder.style.display  = 'flex';
    filename.style.display     = 'none';
    btnClear.style.display     = 'none';
    zone.classList.remove('has-image');
}

/* ── Drag & drop sur la zone ────────────────────────────────────── */
(function() {
    const zone  = document.getElementById('uploadZone');
    const input = document.getElementById('image');
    if (!zone || !input) return;

    zone.addEventListener('dragover', function(e) {
        e.preventDefault();
        zone.classList.add('dragover');
    });
    zone.addEventListener('dragleave', function() {
        zone.classList.remove('dragover');
    });
    zone.addEventListener('drop', function(e) {
        e.preventDefault();
        zone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            input.files = dt.files;
            previewImage(input);
        }
    });
})();

/* ── Modal édition produit ──────────────────────────────────────── */
function ouvrirModalEdit(id, nom, categorie, prix, stock, seul, description) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nom').value = nom;
    document.getElementById('edit_categorie').value = categorie;
    document.getElementById('edit_prix').value = prix;
    document.getElementById('edit_stock').value = stock;
    document.getElementById('edit_seul').checked = (seul == 1);
    document.getElementById('edit_description').value = description;
    
    document.getElementById('modalOverlay').classList.add('active');
    document.getElementById('modalEdit').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function fermerModalEdit() {
    document.getElementById('modalOverlay').classList.remove('active');
    document.getElementById('modalEdit').classList.remove('open');
    document.body.style.overflow = '';
}

document.getElementById('modalOverlay').addEventListener('click', fermerModalEdit);
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') fermerModalEdit();
});
</script>

<!-- ═════════════════════════════════════════════════════════════════
     MODAL ÉDITION PRODUIT
     ═════════════════════════════════════════════════════════════════ -->
<div id="modalOverlay" class="modal-overlay"></div>

<div id="modalEdit" class="modal-edit" role="dialog" aria-modal="true" aria-label="Modifier le produit">
    <div class="modal-edit-box" onclick="event.stopPropagation()">
        
        <div class="modal-edit-header">
            <div class="modal-edit-title">
                <i class="fa-solid fa-pen-to-square"></i>
                Modifier le produit
            </div>
            <button class="modal-edit-close" onclick="fermerModalEdit()" aria-label="Fermer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form method="POST" action="" class="modal-edit-form">
            <input type="hidden" name="edit_id" id="edit_id">
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="edit_nom">Nom *</label>
                    <input type="text" id="edit_nom" name="edit_nom" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit_categorie">Catégorie *</label>
                    <select id="edit_categorie" name="edit_categorie" class="form-control" required>
                        <option value="Soft">Soft</option>
                        <option value="Chaud">Boissons Chaudes</option>
                        <option value="Bière">Bière</option>
                        <option value="Cocktail">Cocktail</option>
                        <option value="Snack">Snack</option>
                        <option value="Pizza">Pizza</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit_prix">Prix (€) *</label>
                    <input type="number" id="edit_prix" name="edit_prix" class="form-control" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit_stock">Stock *</label>
                    <input type="number" id="edit_stock" name="edit_stock" class="form-control" min="0" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label checkbox-label">
                        <input type="checkbox" id="edit_seul" name="edit_seul" value="1" class="checkbox-input">
                        <span>Peut être commandé seul</span>
                    </label>
                </div>
                
                <div class="form-group form-full">
                    <label class="form-label" for="edit_description">Description</label>
                    <textarea id="edit_description" name="edit_description" class="form-control" rows="3" placeholder="Description du produit..."></textarea>
                </div>
            </div>
            
            <div class="modal-edit-footer">
                <button type="button" class="btn btn-ghost" onclick="fermerModalEdit()">
                    <i class="fa-solid fa-xmark"></i> Annuler
                </button>
                <button type="submit" name="update" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> Enregistrer
                </button>
            </div>
        </form>
        
    </div>
</div>

<style>
/* ── Modal édition ──────────────────────────────────────────────── */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 900;
    display: none;
    cursor: pointer;
}
.modal-overlay.active { display: block; }

.modal-edit {
    position: fixed;
    inset: 0;
    z-index: 901;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    opacity: 0;
    transition: opacity .2s ease;
}

.modal-edit.open {
    pointer-events: auto;
    opacity: 1;
}

.modal-edit-box {
    background: var(--card);
    border-radius: var(--radius);
    border: 2px solid var(--border);
    box-shadow: 0 24px 64px rgba(0,0,0,.18);
    width: 520px;
    max-width: 92vw;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.modal-edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
}

.modal-edit-title {
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-edit-close {
    background: none;
    border: none;
    font-size: 1.4rem;
    color: var(--text-muted);
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
    transition: background .15s;
}

.modal-edit-close:hover {
    background: var(--surface2);
}

.modal-edit-form {
    padding: 20px;
    overflow-y: auto;
}

.modal-edit-footer {
    padding: 16px 20px;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    background: var(--surface);
}
</style>