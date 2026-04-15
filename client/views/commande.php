
<div class="commande-wrapper">

    <!-- Retour -->
    <a href="?element=client&action=index" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i>
        Retour aux produits
    </a>

    <h1 class="page-title">Valider la <span>commande</span></h1>
    <p class="page-subtitle">Renseignez vos informations pour finaliser la commande.</p>

    <!-- Formulaire de validation -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-pen-to-square"></i>
                Vos informations
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="?element=client&action=commande">
                <!-- Page source pour redirection -->
                <input type="hidden" name="source" value="<?= htmlspecialchars($_GET['source'] ?? 'index') ?>">
                
                <!-- Numéro de borne -->
                <div class="form-group">
                    <label class="form-label" for="num_borne">
                        <i class="fa-solid fa-desktop"></i>
                        Numéro de borne
                        <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 400;">(1 à 24)</span>
                    </label>
                    <select id="num_borne"
                            name="num_borne"
                            class="form-control"
                            required>
                        <option value="">— Choisir une borne —</option>
                        <?php for ($i = 1; $i <= 24; $i++): ?>
                            <?php $est_utilisee = in_array($i, $bornes_utilisees); ?>
                            <option value="<?= $i ?>" <?= $est_utilisee ? 'disabled' : '' ?>>
                                Borne <?= $i ?><?= $est_utilisee ? ' (occupée)' : '' ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <?php if (!empty($bornes_utilisees)): ?>
                        <small style="color: var(--text-muted); margin-top: 4px; display: block;">
                            <i class="fa-solid fa-circle-info"></i>
                            Les bornes marquées "occupées" sont déjà utilisées par une commande en cours.
                        </small>
                    <?php endif; ?>
                </div>

                <div class="form-actions" style="margin-top: 24px;">
                    <a href="?element=client&action=index" class="btn btn-ghost">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fa-solid fa-circle-check"></i>
                        Confirmer la commande
                    </button>
                </div>

            </form>
        </div>
    </div>

</div><!-- /commande-wrapper -->
