
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
                    </label>
                    <input type="number"
                           id="num_borne"
                           name="num_borne"
                           class="form-control"
                           placeholder="Ex. 3">
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
