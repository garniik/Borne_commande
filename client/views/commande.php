<?php afficherMessagesFlash(); ?>

<div class="form-container">
    <h2>Valider la commande</h2>
    
    <form method="POST" action="?element=client&action=commande">
        <label for="phone">Téléphone *</label>
        <input type="text" id="phone" name="phone" required>
        
        <label for="num_borne">Borne</label>
        <input type="number" id="num_borne" name="num_borne">
        
        <button type="submit" class="btn btn-success">Valider</button>
    </form>
    
    <a href="?element=client&action=index" class="retour">← Retour à la liste</a>
</div>
