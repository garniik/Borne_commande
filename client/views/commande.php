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

<!-- ── Debug ─────────────────────────────────────────────── -->
<?php if (!empty($debug)): ?>
    <div style="background:#fff3cd;color:#856404;border:1px solid #ffeaa7;padding:8px;margin-bottom:10px;font-family:monospace;white-space:pre-wrap;">
        <?= htmlspecialchars(implode("\n", $debug)) ?>
    </div>
<?php endif; ?>

<h2>Valider la commande</h2>

<form method="POST" action="?element=client&action=commande">
    <label>Téléphone *<br><input type="text" name="phone" required></label><br><br>
    <label>Borne<br><input type="number" name="num_borne"></label><br><br>
    <button type="submit">Valider</button>
</form>

<p><a href="?element=client&action=index">← Retour à la liste</a></p>
