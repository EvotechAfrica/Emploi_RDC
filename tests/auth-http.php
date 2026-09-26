<?php
// Integration against local XAMPP. Creates uniquely named fixtures and removes only those fixtures.
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../app/Core/Database.php';
$db = \App\Core\Database::getConnection();
$suffix = bin2hex(random_bytes(8));
$emails = ['auth-test-' . $suffix . '@example.com','company-test-' . $suffix . '@example.com'];
$cookie = tempnam(sys_get_temp_dir(),'emploi-auth-');
$request = static function ($page, $post = null) use ($cookie) {
    $curl = curl_init(BASE_URL . '/?page=' . $page);
    curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_COOKIEFILE=>$cookie,CURLOPT_TIMEOUT=>20]);
    if ($post !== null) { curl_setopt($curl,CURLOPT_POST,true); curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($post)); }
    $body = curl_exec($curl); $code = curl_getinfo($curl,CURLINFO_HTTP_CODE); $location = curl_getinfo($curl,CURLINFO_REDIRECT_URL); curl_close($curl);
    if ($body === false) throw new RuntimeException('HTTP unavailable');
    return [$code,$body,$location];
};
$token = static function ($body) { if (!preg_match('/name="csrf" value="([a-f0-9]+)"/',$body,$match)) throw new RuntimeException('Missing CSRF'); return $match[1]; };
$assert = static function ($value,$message) { if (!$value) throw new RuntimeException($message); };
try {
    [$code,,$location] = $request('dashboard'); $assert($code === 303 && $location === BASE_URL . '/?page=connexion','Guest can access dashboard');
    [$code,$body] = $request('compte'); $assert($code === 303,'Guest can access account');
    [$code,$body] = $request('inscription'); $csrf = $token($body);
    $data = ['csrf'=>$csrf,'email'=>$emails[0],'password'=>'Test-password-2026','password_confirmation'=>'Test-password-2026','nom'=>'Fixture','prenom'=>'Test','ville_candidat'=>'Kinshasa','telephone'=>''];
    [$code,$body] = $request('inscription',array_replace($data,['csrf'=>'invalid'])); $assert(strpos($body,'expiré') !== false,'Missing CSRF rejection');
    [$code,$body,$location] = $request('inscription',$data); $assert($code === 303 && $location === BASE_URL . '/?page=dashboard','Candidate not redirected to dashboard');
    [$code,$body] = $request('dashboard'); $assert($code === 200 && strpos($body,'data-account-role="candidat"') !== false && strpos($body,'momentanément indisponibles') === false,'Candidate dashboard failed');
    $assert(strpos($body,'page=candidat/cv') !== false && strpos($body,'page=admin/utilisateurs') === false && strpos($body,'page=company/paiements') === false,'Candidate menu contains wrong role links');
    [$code,$body] = $request('candidat/cv'); $assert($code === 200 && strpos($body,'<title>Mon CV') !== false && strpos($body,'data-page="candidat/cv"') !== false,'Candidate dedicated page failed');
    $routes = require ROOT_PATH . '/config/dashboard-pages.php';
    foreach ($routes as $route=>$destination) {
        $assert(is_file(ROOT_PATH . '/app/Views/' . $destination['view'] . '.php'),'Missing dedicated view');
        [$code,$body] = $request($route);
        $assert($code === ($destination['role'] === 'candidat' ? 200 : 403),'Candidate route permissions incorrect: ' . $route);
        if ($code === 200) $assert(strpos($body,'data-page="'.$destination['view'].'"') !== false,'Incorrect candidate view');
    }
    [$code] = $request('dashboard&section=utilisateurs'); $assert($code === 403,'Candidate can access admin section');
    [$code,$body] = $request('compte'); $assert($code === 200 && strpos($body,$emails[0]) !== false,'No authenticated candidate');
    $csrf = $token($body);
    [$code] = $request('deconnexion'); $assert($code === 403,'GET logout accepted');
    [$code] = $request('deconnexion',['csrf'=>$csrf]); $assert($code === 303,'Logout failed');
    [$code,$body] = $request('connexion'); $csrf = $token($body);
    [$code,$body] = $request('connexion',['csrf'=>$csrf,'email'=>$emails[0],'password'=>'wrong']); $assert($code === 200 && strpos($body,'incorrect') !== false,'Invalid password accepted');
    [$code,,$location] = $request('connexion',['csrf'=>$csrf,'email'=>$emails[0],'password'=>'Test-password-2026']); $assert($code === 303 && $location === BASE_URL . '/?page=dashboard','Password login did not redirect to dashboard');
    [$code,$body] = $request('compte'); $request('deconnexion',['csrf'=>$token($body)]);
    [$code,$body] = $request('inscription&type=company');
    $data = ['csrf'=>$token($body),'email'=>$emails[1],'password'=>'Test-password-2026','password_confirmation'=>'Test-password-2026','raison_sociale'=>'Auth Test Fixture','responsable'=>'Test','ville_entreprise'=>'Goma','secteur'=>'Tech','telephone'=>''];
    [$code,,$location] = $request('inscription&type=company',$data); $assert($code === 303 && $location === BASE_URL . '/?page=dashboard','Company not redirected to dashboard');
    [$code,$body] = $request('dashboard'); $assert($code === 200 && strpos($body,'data-account-role="company"') !== false && strpos($body,'momentanément indisponibles') === false,'Company dashboard failed');
    $assert(strpos($body,'page=company/publier') !== false && strpos($body,'page=company/paiements') !== false && strpos($body,'page=candidat/cv') === false,'Company menu contains wrong role links');
    foreach ($routes as $route=>$destination) {
        [$code,$body] = $request($route);
        $assert($code === ($destination['role'] === 'company' ? 200 : 403),'Company route permissions incorrect: ' . $route);
        if ($code === 200) $assert(strpos($body,'data-page="'.$destination['view'].'"') !== false,'Incorrect company view');
    }
    [$code,,$location] = $request('dashboard&section=paiements'); $assert($code === 303 && strpos($location,'company%2Fpaiements') !== false,'Legacy link not redirected');
    [$code] = $request('dashboard&section=utilisateurs'); $assert($code === 403,'Company can access admin section');
    $stmt = $db->prepare('SELECT role FROM utilisateur WHERE email=?'); $stmt->execute([$emails[1]]); $assert($stmt->fetchColumn() === 'company','Company received wrong privileges');
    [$code,$body] = $request('compte'); $request('deconnexion',['csrf'=>$token($body)]);
    [$code] = $request('google-callback&state=forged&code=fake'); $assert($code === 303,'Invalid callback not rejected');
    [$code,$body] = $request('connexion'); $assert(strpos($body,'invalide') !== false,'Invalid state not reported');
    [$code] = $request('compte'); $assert($code === 303,'Invalid callback authenticated visitor');
    echo "Auth HTTP: CSRF, registration candidate/company, password login, logout, role isolation and invalid OAuth state passed.\n";
} finally {
    $stmt = $db->prepare('DELETE c FROM company c JOIN utilisateur u ON c.utilisateur_id=u.id WHERE u.email=?'); $stmt->execute([$emails[1]]);
    $stmt = $db->prepare('DELETE FROM utilisateur WHERE email=?'); $stmt->execute([$emails[1]]);
    $stmt = $db->prepare('DELETE FROM candidat WHERE email=?'); $stmt->execute([$emails[0]]);
    $stmt = $db->prepare('DELETE FROM login_attempt WHERE email IN (?,?)'); $stmt->execute($emails);
    if (is_file($cookie)) unlink($cookie);
}
