<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Core\GoogleOAuth;
use App\Models\AuthAccount;

class AuthController extends Controller
{
    private function go($page)
    {
        header('Location: ' . BASE_URL . '/?page=' . $page, true, 303);
        exit;
    }

    private function input($key) { return is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : ''; }
    private function csrf()
    {
        if (!CSRF::valid($_POST['csrf'] ?? null)) throw new \DomainException('Votre formulaire a expiré. Rechargez la page et réessayez.');
    }
    private function establish(array $account)
    {
        session_regenerate_id(true);
        unset($account['password_hash']);
        $_SESSION['account'] = $account;
        $_SESSION['authenticated_at'] = time();
        unset($_SESSION['csrf'], $_SESSION['google_pending'], $_SESSION['google_attempt']);
        $this->go('dashboard');
    }
    private function errorMessage(\Throwable $error)
    {
        if ($error instanceof \DomainException) return $error->getMessage();
        error_log('Authentication failure: ' . get_class($error));
        return 'Le service est momentanément indisponible. Réessayez dans quelques instants.';
    }
    private function pendingGoogle()
    {
        $pending = $_SESSION['google_pending'] ?? null;
        if (!$pending || ($pending['expires'] ?? 0) < time()) { unset($_SESSION['google_pending']); return null; }
        return $pending;
    }

    public function register()
    {
        header('Cache-Control: no-store');
        $google = $this->pendingGoogle();
        $accountType = $google['type'] ?? (($_GET['type'] ?? '') === 'company' ? 'company' : 'candidat');
        $error = null;
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            try {
                $this->csrf();
                if (isset($_POST['google_completion']) && !$google) throw new \DomainException('La vérification Google a expiré. Recommencez avec Google.');
                $email = $google['claims']['email'] ?? $this->input('email');
                $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) throw new \DomainException('Saisissez une adresse e-mail valide (150 caractères maximum).');
                if (!$google && (strlen($password) < 8 || strlen($password) > 72 || $password !== ($_POST['password_confirmation'] ?? null))) throw new \DomainException('Utilisez un mot de passe de 8 à 72 caractères et confirmez-le à l’identique.');
                $data = ['type'=>$accountType,'email'=>$email,'password'=>$password,'telephone'=>$this->input('telephone'),
                    'nom'=>$this->input($accountType === 'company' ? 'raison_sociale' : 'nom'),
                    'prenom'=>$this->input('prenom'),'ville'=>$this->input($accountType === 'company' ? 'ville_entreprise' : 'ville_candidat'),
                    'secteur'=>$this->input('secteur'),'rccm'=>$this->input('rccm')];
                if (!$data['nom'] || mb_strlen($data['nom']) > ($accountType === 'company' ? 150 : 100) || !$data['ville'] || mb_strlen($data['ville']) > 100 || strlen($data['telephone']) > 20) throw new \DomainException('Vérifiez le nom, la ville et le téléphone.');
                if ($accountType === 'candidat' && (!$data['prenom'] || mb_strlen($data['prenom']) > 100)) throw new \DomainException('Renseignez votre prénom (100 caractères maximum).');
                if ($accountType === 'company' && (!$data['secteur'] || mb_strlen($data['secteur']) > 100 || mb_strlen($data['rccm']) > 100)) throw new \DomainException('Vérifiez le secteur et le numéro RCCM.');
                $accounts = new AuthAccount();
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                if ($accounts->rateLimited($email,$ip)) throw new \DomainException('Trop de tentatives. Réessayez dans 15 minutes.');
                $accounts->attempt($email,$ip,false);
                $this->establish($accounts->create($data, $google['claims'] ?? null));
            } catch (\Throwable $e) { $error = $this->errorMessage($e); }
        }
        $this->view('auth/register', compact('accountType', 'error', 'google'));
    }

    public function login()
    {
        header('Cache-Control: no-store');
        $error = $_SESSION['auth_message'] ?? null; unset($_SESSION['auth_message']);
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            try {
                $this->csrf();
                $email = $this->input('email'); $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
                if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email) > 150 || !$password || strlen($password) > 4096) throw new \DomainException('Adresse e-mail ou mot de passe incorrect.');
                $accounts = new AuthAccount(); $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                if ($accounts->rateLimited($email,$ip)) throw new \DomainException('Trop de tentatives. Réessayez dans 15 minutes.');
                $account = $accounts->passwordLogin($email,$password);
                $accounts->attempt($email,$ip,(bool)$account);
                if (!$account) throw new \DomainException('Adresse e-mail ou mot de passe incorrect, ou compte indisponible.');
                $this->establish($account);
            } catch (\Throwable $e) { $error = $this->errorMessage($e); }
        }
        $this->view('auth/login', compact('error'));
    }

    public function googleStart()
    {
        header('Cache-Control: no-store');
        try {
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); header('Allow: POST'); return; }
            $this->csrf();
            $client = new GoogleOAuth(require ROOT_PATH . '/config/google.php');
            if (!$client->configured()) throw new \DomainException('La connexion Google attend sa configuration par l’administrateur. Vous pouvez utiliser votre e-mail et votre mot de passe.');
            $link = null;
            if ($this->input('intent') === 'link') {
                $current = $_SESSION['account'] ?? null;
                if (!$current || time() - ($_SESSION['authenticated_at'] ?? 0) > 600) throw new \DomainException('Reconnectez-vous avec votre mot de passe avant d’associer Google.');
                $link = ['id'=>$current['id'],'type'=>$current['type']];
            }
            unset($_SESSION['google_pending']);
            $attempt = ['state'=>bin2hex(random_bytes(32)), 'nonce'=>bin2hex(random_bytes(32)), 'verifier'=>bin2hex(random_bytes(32)),
                'expires'=>time()+600, 'type'=>$this->input('account_type') === 'company' ? 'company' : 'candidat', 'link'=>$link];
            $_SESSION['google_attempt'] = $attempt;
            header('Location: ' . $client->authorizationUrl($attempt), true, 303); exit;
        } catch (\Throwable $e) { $_SESSION['auth_message'] = $this->errorMessage($e); $this->go('connexion'); }
    }

    public function googleCallback()
    {
        header('Cache-Control: no-store'); header('Referrer-Policy: no-referrer');
        $attempt = $_SESSION['google_attempt'] ?? null; unset($_SESSION['google_attempt']);
        try {
            if (!$attempt || $attempt['expires'] < time() || !is_string($_GET['state'] ?? null) || !hash_equals($attempt['state'], $_GET['state'])) throw new \DomainException('La demande Google a expiré ou est invalide. Recommencez la connexion.');
            if (isset($_GET['error'])) throw new \DomainException('La connexion Google a été annulée.');
            if (!is_string($_GET['code'] ?? null) || strlen($_GET['code']) > 4096) throw new \DomainException('Réponse Google invalide.');
            $client = new GoogleOAuth(require ROOT_PATH . '/config/google.php');
            $claims = $client->exchange($_GET['code'], $attempt);
            $accounts = new AuthAccount();
            $account = $accounts->googleAccount($claims['sub']);
            if ($attempt['link']) {
                $current = $_SESSION['account'] ?? null;
                if (!$current || $current['id'] != $attempt['link']['id'] || $current['type'] !== $attempt['link']['type']) throw new \DomainException('Votre session a changé. Recommencez l’association.');
                $current = $accounts->load($current['type'],$current['id']);
                if (!$current) throw new \DomainException('Ce compte est indisponible.');
                if ($account && ($account['id'] != $current['id'] || $account['type'] !== $current['type'])) throw new \DomainException('Ce compte Google est déjà associé à un autre compte.');
                if (!$account) $accounts->linkGoogle($current,$claims);
                $this->establish($current);
            }
            if ($account) $this->establish($account);
            if ($accounts->emailExists($claims['email'])) throw new \DomainException('Un compte utilise déjà cette adresse. Connectez-vous avec votre mot de passe puis cliquez sur « Associer Google » dans votre compte.');
            session_regenerate_id(true);
            $_SESSION['google_pending'] = ['claims'=>['sub'=>$claims['sub'],'email'=>$claims['email']], 'type'=>$attempt['type'], 'expires'=>time()+600];
            $this->go('inscription&type=' . $attempt['type']);
        } catch (\Throwable $e) { $_SESSION['auth_message'] = $this->errorMessage($e); $this->go('connexion'); }
    }

    public function dashboard()
    {
        $this->account(true);
    }

    public function workspace($route)
    {
        $this->account(true, $route);
    }

    public function account($dashboard = false, $route = null)
    {
        header('Cache-Control: no-store');
        $saved = $_SESSION['account'] ?? null;
        if (!$saved || time() - ($_SESSION['authenticated_at'] ?? 0) > 28800) { unset($_SESSION['account']); $this->go('connexion'); }
        try { $account = (new AuthAccount())->load($saved['type'],$saved['id']); }
        catch (\Throwable $e) { http_response_code(503); echo 'Service momentanément indisponible.'; return; }
        if (!$account) { unset($_SESSION['account']); $this->go('connexion'); }
        if ($dashboard) {
            $menus = require ROOT_PATH . '/config/dashboard-menu.php';
            $menu = $menus[$account['type']] ?? [];
            $routes = require ROOT_PATH . '/config/dashboard-pages.php';
            $menuLinks = [];
            foreach ($routes as $url=>$destination) {
                if ($destination['role'] === $account['type']) $menuLinks[$destination['section']] = $url;
            }
            $section = $_GET['section'] ?? 'dashboard';
            $contentView = null;
            if ($route !== null) {
                $destination = $routes[$route] ?? null;
                if (!$destination || $destination['role'] !== $account['type']) {
                    http_response_code(403);
                    $this->view('errors/403');
                    return;
                }
                $section = $destination['section'];
                $contentView = $destination['view'];
            }
            if (!is_string($section) || ($section !== 'dashboard' && !isset($menu[$section]))) {
                http_response_code(403);
                $this->view('errors/403');
                return;
            }
            if ($route === null && $section !== 'dashboard') {
                $this->go(rawurlencode($menuLinks[$section]));
            }
            try { $stats = $section === 'dashboard' ? (new \App\Models\Dashboard())->stats($account) : null; }
            catch (\Throwable $e) { $stats = null; }
            $this->view('dashboard/index', compact('account', 'stats', 'menu', 'section', 'menuLinks', 'contentView'));
        } else {
            $this->view('auth/account', compact('account'));
        }
    }

    public function logout()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !CSRF::valid($_POST['csrf'] ?? null)) { http_response_code(403); return; }
        $_SESSION = []; session_regenerate_id(true); $this->go('connexion');
    }
}
