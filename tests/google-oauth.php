<?php
// Offline verification of token validation. No real Google credentials required.
require __DIR__ . '/../app/Core/GoogleOAuth.php';
class FakeGoogle extends \App\Core\GoogleOAuth
{
    public $token;
    public $certificate;
    protected function request($url, array $post = null)
    {
        return $post ? ['id_token'=>$this->token] : ['test-key'=>$this->certificate];
    }
}
function check($value, $message) { if (!$value) throw new RuntimeException($message); }
$options = ['private_key_bits'=>2048, 'private_key_type'=>OPENSSL_KEYTYPE_RSA];
if (is_file('C:/xampp/apache/conf/openssl.cnf')) $options['config'] = 'C:/xampp/apache/conf/openssl.cnf';
$key = openssl_pkey_new($options);
check((bool)$key,'Cannot generate test key');
$client = new FakeGoogle(['client_id'=>'test-client','client_secret'=>'test-secret','redirect_uri'=>'http://localhost/callback']);
$client->certificate = openssl_pkey_get_details($key)['key'];
$encode = static function ($value) { return rtrim(strtr(base64_encode($value),'+/','-_'),'='); };
$sign = static function ($claims) use ($key,$encode) {
    $data = $encode(json_encode(['alg'=>'RS256','kid'=>'test-key'])) . '.' . $encode(json_encode($claims));
    openssl_sign($data,$signature,$key,OPENSSL_ALGO_SHA256);
    return $data . '.' . $encode($signature);
};
$attempt = ['nonce'=>'test-nonce','state'=>'test-state','verifier'=>str_repeat('a',64)];
$claims = ['iss'=>'https://accounts.google.com','aud'=>'test-client','sub'=>'123456789','email'=>'test@example.com','email_verified'=>true,'iat'=>time(),'exp'=>time()+300,'nonce'=>'test-nonce'];
$client->token = $sign($claims);
check($client->exchange('test-code',$attempt)['sub'] === '123456789','Valid identity rejected');
$tests = ['aud'=>'wrong-client','iss'=>'https://attacker.example','exp'=>time()-10,'nonce'=>'wrong-nonce','email_verified'=>false,'sub'=>'','iat'=>time()+3600];
foreach ($tests as $field=>$invalid) {
    $client->token = $sign(array_replace($claims,[$field=>$invalid]));
    try { $client->exchange('test-code',$attempt); throw new LogicException('Accepted invalid ' . $field); }
    catch (RuntimeException $expected) { /* Expected rejection. */ }
}
$client->token = $sign($claims);
$parts = explode('.',$client->token); $parts[1] = $encode(json_encode(array_replace($claims,['sub'=>'999']))); $client->token = implode('.',$parts);
try { $client->exchange('test-code',$attempt); throw new LogicException('Accepted forged signature'); } catch (RuntimeException $expected) {}
parse_str(parse_url($client->authorizationUrl($attempt),PHP_URL_QUERY),$query);
check($query['state'] === 'test-state' && $query['nonce'] === 'test-nonce' && $query['code_challenge_method'] === 'S256','Missing state, nonce or PKCE');
echo "Google OAuth: valid signature accepted; invalid signature, audience, issuer, nonce, expiration, subject, verification and issued-at rejected.\n";
