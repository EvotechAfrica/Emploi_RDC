<?php
namespace App\Core;

class GoogleOAuth
{
    private $config;

    public function __construct(array $config) { $this->config = $config; }

    public function configured() { return !empty($this->config['client_id']) && !empty($this->config['client_secret']); }

    public function authorizationUrl(array $attempt)
    {
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => $this->config['client_id'], 'redirect_uri' => $this->config['redirect_uri'],
            'response_type' => 'code', 'scope' => 'openid email profile', 'prompt' => 'select_account',
            'state' => $attempt['state'], 'nonce' => $attempt['nonce'],
            'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $attempt['verifier'], true)), '+/', '-_'), '='),
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);
    }

    protected function request($url, array $post = null)
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS, CURLOPT_FOLLOWLOCATION => false]);
        if ($post !== null) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($body === false || $status !== 200) throw new \RuntimeException('Échange Google impossible. Réessayez.');
        $data = json_decode($body, true);
        if (!is_array($data)) throw new \RuntimeException('Réponse Google invalide.');
        return $data;
    }

    public function exchange($code, array $attempt)
    {
        $tokens = $this->request('https://oauth2.googleapis.com/token', [
            'code' => $code, 'client_id' => $this->config['client_id'], 'client_secret' => $this->config['client_secret'],
            'redirect_uri' => $this->config['redirect_uri'], 'grant_type' => 'authorization_code',
            'code_verifier' => $attempt['verifier'],
        ]);
        if (!is_string($tokens['id_token'] ?? null)) throw new \RuntimeException('Identité Google absente.');
        $parts = explode('.', $tokens['id_token']);
        if (count($parts) !== 3) throw new \RuntimeException('Identité Google invalide.');
        $decode = static function ($value) { return base64_decode(strtr($value, '-_', '+/'), true); };
        $header = json_decode($decode($parts[0]), true);
        $claims = json_decode($decode($parts[1]), true);
        if (!is_array($header) || !is_array($claims) || ($header['alg'] ?? '') !== 'RS256' || !is_string($header['kid'] ?? null)) throw new \RuntimeException('Signature Google invalide.');
        $certificates = $this->request('https://www.googleapis.com/oauth2/v1/certs');
        $certificate = $certificates[$header['kid']] ?? null;
        if (!is_string($certificate) || openssl_verify($parts[0] . '.' . $parts[1], $decode($parts[2]), $certificate, OPENSSL_ALGO_SHA256) !== 1) throw new \RuntimeException('Signature Google invalide.');
        if (!in_array($claims['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)
            || ($claims['aud'] ?? '') !== $this->config['client_id']
            || (isset($claims['azp']) && $claims['azp'] !== $this->config['client_id'])
            || !is_numeric($claims['exp'] ?? null) || $claims['exp'] <= time()
            || !is_numeric($claims['iat'] ?? null) || $claims['iat'] > time() + 60
            || !is_string($claims['nonce'] ?? null) || !hash_equals($attempt['nonce'], $claims['nonce'])
            || !is_string($claims['sub'] ?? null) || !preg_match('/^[0-9]{1,255}$/D', $claims['sub'])
            || ($claims['email_verified'] ?? false) !== true
            || !is_string($claims['email'] ?? null) || !filter_var($claims['email'], FILTER_VALIDATE_EMAIL)
            || strlen($claims['email']) > 150) throw new \RuntimeException('L’identité Google n’a pas pu être vérifiée.');
        return $claims;
    }
}
