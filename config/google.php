<?php
$config = [
    'client_id' => getenv('GOOGLE_CLIENT_ID') ?: '',
    'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: '',
    'redirect_uri' => getenv('GOOGLE_REDIRECT_URI') ?: BASE_URL . '/?page=google-callback',
];
if (is_file(__DIR__ . '/google.local.php')) {
    $config = array_replace($config, require __DIR__ . '/google.local.php');
}
return $config;
