<?php
namespace App\Core;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', '1');
            session_set_cookie_params([
                'httponly' => true,
                'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'samesite' => 'Lax',
                'path' => '/',
            ]);
            session_start();
        }
    }

    public static function set($key, $value) { $_SESSION[$key] = $value; }
    public static function get($key, $default = null) { return isset($_SESSION[$key]) ? $_SESSION[$key] : $default; }
    public static function has($key) { return isset($_SESSION[$key]); }
    public static function remove($key) { unset($_SESSION[$key]); }
    public static function destroy() { $_SESSION = []; if (session_status() === PHP_SESSION_ACTIVE) { session_destroy(); } }
}
