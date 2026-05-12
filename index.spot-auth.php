<?php
/**
 * ONE-TIME Spotify OAuth helper
 *
 * USAGE:
 * 1. In your Spotify app dashboard, add this URL to "Redirect URIs":
 * https://your-site/path/to/spot-auth.php (this file)
 *
 * 2. Visit https://your-site/path/to/spot-auth.php - you will be redirected to Spotify to authorize the app
 *
 * 3. After authorizing, you will be redirected back here, the page will display your refresh_token.
 *
 * 4. Copy the refresh_token into config.php and DELETE this file from your server, it is only needed once.
 */

require_once __DIR__ . '/index.config.php';

$redirect_uri = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST']
    . strtok($_SERVER['REQUEST_URI'], '?');

$scope = 'user-read-recently-played';

// Step 1 - no code yet: redirect to Spotify authorization page
if (!isset($_GET['code']) && !isset($_GET['error'])) {
    $params = http_build_query([
        'client_id'     => SPOTIFY_CLIENT_ID,
        'response_type' => 'code',
        'redirect_uri'  => $redirect_uri,
        'scope'         => $scope,
    ]);
    header('Location: https://accounts.spotify.com/authorize?' . $params);
    exit;
}

// Step 1b - user denied
if (isset($_GET['error'])) {
    echo '<p>Authorization denied: ' . htmlspecialchars($_GET['error']) . '</p>';
    exit;
}

// Step 2 - exchange code for tokens
$code  = $_GET['code'];
$creds = base64_encode(SPOTIFY_CLIENT_ID . ':' . SPOTIFY_CLIENT_SECRET);

$ctx = stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => implode("\r\n", [
            'Authorization: Basic ' . $creds,
            'Content-Type: application/x-www-form-urlencoded',
        ]),
        'content' => http_build_query([
            'grant_type'   => 'authorization_code',
            'code'         => $code,
            'redirect_uri' => $redirect_uri,
        ]),
        'ignore_errors' => true,
    ],
]);

$body = file_get_contents('https://accounts.spotify.com/api/token', false, $ctx);
$data = json_decode($body, true);

if (empty($data['refresh_token'])) {
    echo '<pre>Error: ' . htmlspecialchars($body) . '</pre>';
    exit;
}

$refresh_token = $data['refresh_token'];
$access_token  = $data['access_token'];

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spotify auth - done</title>
    <style>
        body { font-family: monospace; padding: 2rem; background: #111; color: #eee; }
        .box { background: #1a1a1a; border: 1px solid #333; border-radius: 6px; padding: 1.5rem; margin: 1rem 0; }
        .token { word-break: break-all; color: #c8f564; font-size: 13px; }
        .warn { color: #f5a64a; margin-top: 1rem; }
    </style>
</head>
<body>
<h1>✅ Spotify authorization successful</h1>

<div class="box">
    <p>Add this line to <code>config.php</code>:</p>
    <br>
    <p class="token">define('SPOTIFY_REFRESH_TOKEN', '<?= htmlspecialchars($refresh_token) ?>');</p>
</div>

<p class="warn">⚠ DELETE this file from your server once you have saved the token.</p>
</body>
</html>
