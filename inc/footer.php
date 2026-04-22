<?php
/**
 * inc/footer.php — Fermeture du layout + HTML
 */
if (!empty($db)) { $db = null; }
?>

</div><!-- /.page-wrapper -->

<footer class="site-footer">
    <span>Borne Commande &copy; <?= date('Y') ?></span>
</footer>

<?php afficherMessagesFlash(); ?>

</body>
</html>