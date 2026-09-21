<?php
require_once dirname(__DIR__) . '/config/config.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = APP_PATH . '/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

\App\Core\Session::start();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emploi RDC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="p-5 bg-white rounded-4 shadow-sm">
        <h1 class="fw-bold">Emploi RDC</h1>
        <p class="lead">Architecture MVC installee avec succes.</p>
        <p>La prochaine etape consiste a importer la base de donnees et developper les modules.</p>
    </div>
</div>
</body>
</html>