<?php
namespace App\Core;

abstract class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        $file = VIEW_PATH . '/' . $view . '.php';
        if (!file_exists($file)) {
            http_response_code(404);
            die('Vue introuvable : ' . htmlspecialchars($view));
        }
        require $file;
    }

    protected function redirect($path)
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }
}