<?php

$output = '';
$file   = '';

if (isset($_GET['file'])) {
    $file = $_GET['file'];
    $path = '/var/www/html/' . $file;

    if (file_exists($path) && is_readable($path)) {
        $output = file_get_contents($path);
    } else {
        $output = "Erreur : fichier introuvable ou non lisible.";
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin — Base Delta</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <span class="logo">▲ BASE DELTA <em>// PANNEAU ADMIN</em></span>
        <span class="status-warn">● ACCÈS RESTREINT</span>
    </header>
    <main>
        <div class="card">
            <h2>Lecteur de fichiers système</h2>
            <form method="GET" class="inline-form">
                <input type="text"
                       name="file"
                       value="<?= htmlspecialchars($file) ?>"
                       placeholder="Ex: logs/app.log"
                       class="input-text">
                <button type="submit" class="btn">Lire</button>
            </form>

            <?php if ($output !== ''): ?>
            <div class="file-output">
                <div class="file-output-header">
                    Contenu de : <code><?= htmlspecialchars($file) ?></code>
                </div>
                <pre><?= htmlspecialchars($output) ?></pre>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
