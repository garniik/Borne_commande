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

<h2>Merci !</h2>

<p>Votre commande a bien été enregistrée.</p>

<p><a href="?element=client&action=index">← Retour à la liste</a></p>
