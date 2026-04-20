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

<!-- Navigation admin -->
<div style="display:flex; gap:10px; margin-bottom:20px;">
    <a href="?element=admin&action=produits" class="btn btn-primary">
        <i class="fa-solid fa-box"></i> Produits
    </a>
    <a href="?element=admin&action=commandes" class="btn btn-ghost">
        <i class="fa-solid fa-clipboard-list"></i> Commandes
    </a>
</div>

<!-- En-tête de page -->
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:28px;">
    <div>
        <h1 class="page-title" style="margin-bottom:4px;">
            <span>Gestion</span> des produits
        </h1>
        <p class="page-subtitle" style="margin-bottom:0;">
            <?= count($donnee ?? []) ?> produit<?= count($donnee ?? []) > 1 ? 's' : '' ?> enregistré<?= count($donnee ?? []) > 1 ? 's' : '' ?>
        </p>
    </div>
    <button class="btn btn-primary" onclick="toggleForm()">
        <i class="fa-solid fa-plus"></i>
        Nouveau produit
    </button>
</div>

<?php afficherMessagesFlash(); ?>


<!-- ══════════════════════════════════════════════════════
     FORMULAIRE D'AJOUT
     Important : enctype="multipart/form-data" obligatoire pour l'upload
══════════════════════════════════════════════════════ -->
<div id="formAjout" class="card collapse-panel" style="margin-bottom:28px;">
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
                        <option value="Alcool">Alcool</option>
                        <option value="Cocktail">Cocktail</option>
                        <option value="Snack">Snack</option>
                        <option value="Nourriture">Nourriture</option>
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
                           style="display:none;"
                           onchange="previewImage(this)">

                    <!-- Bouton pour effacer la sélection -->
                    <button type="button"
                            id="btnClearImage"
                            class="btn btn-ghost btn-sm"
                            style="margin-top:8px; display:none;"
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
                                <button type="button" class="btn btn-primary btn-sm" onclick="ouvrirModalEdit(<?= (int)$produit['id'] ?>, '<?= htmlspecialchars(addslashes($produit['nom'])) ?>', '<?= htmlspecialchars(addslashes($produit['categorie'])) ?>', <?= (float)$produit['prix'] ?>, <?= (int)$produit['stock'] ?>, '<?= htmlspecialchars(addslashes($produit['description'] ?? '')) ?>')">
                                    <i class="fa-solid fa-pen"></i> Modifier
                                </button>
                                <form method="post" style="display:inline; margin-left:8px;" onsubmit="return confirm('Supprimer ce produit et son image ?')">
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
            // Injecter le fichier dans l'input et déclencher la prévisualisation
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            input.files = dt.files;
            previewImage(input);
        }
    });
})();
</script>