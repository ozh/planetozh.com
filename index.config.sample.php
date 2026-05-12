<?php
// GitHub Username
define('GITHUB_USER', 'ozh');

// Optional: a personal access token (read:user scope) to raise rate limit from 60 to 5000 requests/hour.
// Generate one at https://github.com/settings/tokens
define('GITHUB_TOKEN', '');

// App credentials from https://developer.spotify.com/dashboard
define('SPOTIFY_CLIENT_ID',     'your_client_id_here');
define('SPOTIFY_CLIENT_SECRET', 'your_client_secret_here');

// Refresh token obtained via the one-time OAuth flow.
// Run spot-auth.php once to get it
define('SPOTIFY_REFRESH_TOKEN', '');

// Mastodon instance base URL (no trailing slash)
define('MASTODON_INSTANCE', 'https://fosstodon.org');

// Mastodon account handle without @ ("ozh", not "@ozh")
define('MASTODON_ACCT', 'ozh');
