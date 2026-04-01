<?php
// ── Messages flash ───────────────────────────────────────────────
if (!empty($_SESSION['mesgs']['success'])) {
    foreach ($_SESSION['mesgs']['success'] as $msg): ?>
        <div style="background:#d4edda;color:#155724;padding:8px;margin-bottom:10px;"><?= $msg ?></div>
    <?php endforeach;
    unset($_SESSION['mesgs']['success']);
}
if (!empty($_SESSION['mesgs']['errors'])) {
    foreach ($_SESSION['mesgs']['errors'] as $err): ?>
        <div style="background:#f8d7da;color:#721c24;padding:8px;margin-bottom:10px;"><?= htmlspecialchars($err) ?></div>
    <?php endforeach;
    unset($_SESSION['mesgs']['errors']);
}
?>

<h2>Valider la commande</h2>

<form method="POST" action="?element=client&action=commande">
    <label>Téléphone *<br><input type="text" name="phone" required></label><br><br>
    <label>Borne<br><input type="number" name="Num_Borne"></label><br><br>
    <button type="submit">Valider</button>
</form>

<p><a href="?element=client&action=index">← Retour à la liste</a></p>
