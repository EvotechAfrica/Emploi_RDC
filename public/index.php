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

$page = $_GET['page'] ?? 'accueil';
$dashboardPages = require ROOT_PATH . '/config/dashboard-pages.php';
$authRoutes = ['google-start'=>'googleStart', 'google-callback'=>'googleCallback', 'dashboard'=>'dashboard', 'compte'=>'account', 'deconnexion'=>'logout'];
if (is_string($page) && isset($authRoutes[$page])) {
    (new \App\Controllers\AuthController())->{$authRoutes[$page]}();
} elseif (is_string($page) && isset($dashboardPages[$page])) {
    (new \App\Controllers\AuthController())->workspace($page);
} elseif ($page === 'admin-apercu') {
    (new \App\Controllers\AdminController())->preview();
} elseif ($page === 'connexion') {
    (new \App\Controllers\AuthController())->login();
} elseif ($page === 'inscription') {
    (new \App\Controllers\AuthController())->register();
} elseif ($page === 'accueil') {
    (new \App\Controllers\HomeController())->index();
} else {
    http_response_code(404);
    require VIEW_PATH . '/errors/404.php';
}
