<?php
/**
 * Returns the filesystem path for a given cache key.
 *
 * @param string $key Cache key identifier
 * @return string Full path to the cache file
 */
function cache_path(string $key): string {
    return CACHE_DIR . $key . '.json';
}

/**
 * Reads and decodes a cache file.
 *
 * @param string $key Cache key identifier
 * @return array|null Decoded cache data, or null if missing or invalid
 */
function cache_get(string $key): array|null {
    $path = cache_path($key);
    if (!file_exists($path)) {
        return null;
    }
    $raw = file_get_contents($path);
    if ($raw === false) {
        return null;
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return null;
    }
    return $data;
}

/**
 * Writes a payload to the cache, wrapping it with a timestamp.
 *
 * @param string $key     Cache key identifier
 * @param array  $payload Data to cache
 * @return bool True on success, false on failure
 */
function cache_set(string $key, array $payload): bool {
    if (!is_dir(CACHE_DIR)) {
        mkdir(CACHE_DIR, 0755, true);
    }
    $data = [
        'fetched_at' => time(),
        'payload'    => $payload,
    ];
    return file_put_contents(cache_path($key), json_encode($data, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Checks whether a cached entry is still within the TTL window.
 *
 * @param array $cached Raw cache data including 'fetched_at'
 * @return bool True if the cache is still fresh
 */
function cache_is_fresh(array $cached): bool {
    return isset($cached['fetched_at']) && (time() - $cached['fetched_at']) < CACHE_TTL;
}

/**
 * Loads a cache entry and returns its data along with staleness metadata.
 *
 * @param string $key Cache key identifier
 * @return array{data: array|null, stale: bool, age: int|null}
 */
function cache_load(string $key): array {
    $cached = cache_get($key);
    return [
        'data'  => $cached['payload'] ?? null,
        'stale' => $cached ? !cache_is_fresh($cached) : true,
        'age'   => $cached ? (time() - ($cached['fetched_at'] ?? 0)) : null,
    ];
}

/**
 * Returns a human-readable string indicating when the cache expires.
 *
 * @param string $key Cache key identifier
 * @return string Expiry description, e.g. 'in 42min', 'expired', or 'no cache'
 */
function cache_expires_in(string $key): string {
    $cached = cache_get($key);
    if (!$cached || !isset($cached['fetched_at'])) {
        return 'no cache';
    }
    $remaining = CACHE_TTL - (time() - $cached['fetched_at']);
    if ($remaining <= 0) {
        return 'expired';
    }
    if ($remaining < 60) {
        return 'in ' . $remaining . 's';
    }
    return (int)($remaining / 60) . 'min';
}

/**
 * Performs an HTTP GET request and returns the body and status code.
 *
 * @param string   $url     URL to fetch
 * @param string[] $headers Optional HTTP headers
 * @return array{body: string, status: int}
 */
function http_get(string $url, array $headers = []): array {
    $ctx = stream_context_create([
        'http' => [
            'method'          => 'GET',
            'header'          => implode("\r\n", $headers),
            'timeout'         => 8,
            'ignore_errors'   => true,
            'follow_location' => true,
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);
    $body = @file_get_contents($url, false, $ctx);
    $status = 0;
    if (isset($http_response_header)) {
        preg_match('/HTTP\/\S+\s+(\d+)/', $http_response_header[0] ?? '', $m);
        $status = (int) ($m[1] ?? 0);
    }
    return ['body' => $body ?: '', 'status' => $status];
}

/**
 * Performs an HTTP POST request and returns the body and status code.
 *
 * @param string   $url     URL to post to
 * @param string   $body    Request body
 * @param string[] $headers Optional HTTP headers
 * @return array{body: string, status: int}
 */
function http_post(string $url, string $body, array $headers = []): array {
    $ctx = stream_context_create([
        'http' => [
            'method'        => 'POST',
            'header'        => implode("\r\n", $headers),
            'content'       => $body,
            'timeout'       => 8,
            'ignore_errors' => true,
        ],
    ]);
    $resp = @file_get_contents($url, false, $ctx);
    $status = 0;
    if (isset($http_response_header)) {
        preg_match('/HTTP\/\S+\s+(\d+)/', $http_response_header[0] ?? '', $m);
        $status = (int) ($m[1] ?? 0);
    }
    return ['body' => $resp ?: '', 'status' => $status];
}

/**
 * Fetches recent public GitHub events for the configured user.
 *
 * @return array List of event items, or an array with an 'error' key on failure
 */
function fetch_github(): array {
    $url  = 'https://api.github.com/users/' . GITHUB_USER . '/events/public?per_page=60';
    $hdrs = [
        'User-Agent: ' . GITHUB_USER . '-homepage',
        'Accept: application/vnd.github+json',
    ];
    if (defined('GITHUB_TOKEN') && GITHUB_TOKEN) {
        $hdrs[] = 'Authorization: Bearer ' . GITHUB_TOKEN;
    }

    $res  = http_get($url, $hdrs);
    if ($res['status'] !== 200) {
        return ['error' => 'GitHub API error ' . $res['status']];
    }
    $events = json_decode($res['body'], true);
    if (!is_array($events)) {
        return ['error' => 'GitHub: invalid response'];
    }

    $keep_types = ['PushEvent', 'PullRequestEvent', 'IssuesEvent', 'CreateEvent', 'WatchEvent', 'ForkEvent'];
    $items = [];
    foreach ($events as $ev) {
        if (!in_array($ev['type'], $keep_types, true)) {
            continue;
        }
        $repo    = $ev['repo']['name'] ?? '';
        $type    = $ev['type'];
        $created = $ev['created_at'] ?? '';

        $desc  = '';
        $badge = 'push';

        switch ($type) {
            case 'PushEvent':
                $commits = $ev['payload']['commits'] ?? [];
                $desc    = $commits ? ($commits[0]['message'] ?? '') : 'pushed commits';
                $desc    = strtok($desc, "\n");
                $badge   = 'push';
                break;
            case 'PullRequestEvent':
                $pr    = $ev['payload']['pull_request'] ?? [];
                $desc  = ($ev['payload']['action'] ?? '') . ': ' . ($pr['number'] ?? '');
                $badge = 'pr';
                break;
            case 'IssuesEvent':
                $issue = $ev['payload']['issue'] ?? [];
                $desc  = ($ev['payload']['action'] ?? '') . ': ' . ($issue['title'] ?? '');
                $badge = 'issue';
                break;
            case 'CreateEvent':
                $desc  = 'created ' . ($ev['payload']['ref_type'] ?? '') . ' ' . ($ev['payload']['ref'] ?? '');
                $badge = 'push';
                break;
            case 'WatchEvent':
                $desc  = 'starred';
                $badge = 'star';
                break;
            case 'ForkEvent':
                $desc  = 'forked';
                $badge = 'fork';
                break;
        }

        $items[] = [
            'repo'    => $repo,
            'badge'   => $badge,
            'desc'    => $desc,
            'created' => $created,
        ];

        if (count($items) >= 10) {
            break;
        }
    }

    return $items;
}

/**
 * Obtains a fresh Spotify access token using the configured refresh token.
 *
 * @return string|false A valid access token, or false on failure
 */
function spotify_refresh_token(): string|false {
    $creds = base64_encode(SPOTIFY_CLIENT_ID . ':' . SPOTIFY_CLIENT_SECRET);
    $res   = http_post(
        'https://accounts.spotify.com/api/token',
        'grant_type=refresh_token&refresh_token=' . urlencode(SPOTIFY_REFRESH_TOKEN),
        [
            'Authorization: Basic ' . $creds,
            'Content-Type: application/x-www-form-urlencoded',
        ]
    );
    $data = json_decode($res['body'], true);
    return $data['access_token'] ?? false;
}

/**
 * Fetches the recently played tracks for the configured Spotify account.
 *
 * @return array List of track items, or an array with an 'error' key on failure
 */
function fetch_spotify(): array {
    $token = spotify_refresh_token();
    if (!$token) {
        return ['error' => 'Spotify: could not refresh token'];
    }

    $res = http_get(
        'https://api.spotify.com/v1/me/player/recently-played?limit=10',
        ['Authorization: Bearer ' . $token]
    );

    if ($res['status'] !== 200) {
        return ['error' => 'Spotify API error ' . $res['status']];
    }

    $data = json_decode($res['body'], true);
    $items = [];
    foreach ($data['items'] ?? [] as $item) {
        $track  = $item['track'] ?? [];
        $items[] = [
            'name'    => $track['name'] ?? '',
            'artist'  => implode(', ', array_column($track['artists'] ?? [], 'name')),
            'album'   => $track['album']['name'] ?? '',
            'cover'   => $track['album']['images'][2]['url'] ?? '',
            'url'     => $track['external_urls']['spotify'] ?? '',
            'played'  => $item['played_at'] ?? '',
        ];
    }
    return $items;
}

/**
 * Fetches recent public statuses from the configured Mastodon account.
 *
 * @return array List of status items, or an array with an 'error' key on failure
 */
function fetch_mastodon(): array {
    $id_cache = cache_load('mastodon_id');
    if ($id_cache['data'] && !$id_cache['stale']) {
        $account_id = $id_cache['data']['id'];
    } else {
        $res = http_get(
            MASTODON_INSTANCE . '/api/v1/accounts/lookup?acct=' . urlencode(MASTODON_ACCT),
            ['User-Agent: planetozh-homepage']
        );
        $account = json_decode($res['body'], true);
        if (empty($account['id'])) {
            return ['error' => 'Mastodon: could not resolve account'];
        }
        $account_id = $account['id'];
        cache_set('mastodon_id', ['id' => $account_id]);
    }

    $res = http_get(
        MASTODON_INSTANCE . '/api/v1/accounts/' . $account_id . '/statuses?limit=10&exclude_replies=true&exclude_reblogs=true',
        ['User-Agent: planetozh-homepage']
    );

    if ($res['status'] !== 200) {
        return ['error' => 'Mastodon API error ' . $res['status']];
    }

    $statuses = json_decode($res['body'], true);
    if (!is_array($statuses)) {
        return ['error' => 'Mastodon: invalid response'];
    }

    $items = [];
    foreach (array_slice($statuses, 0, 5) as $s) {
        $text = html_entity_decode(strip_tags($s['content'] ?? ''), ENT_QUOTES, 'UTF-8');
        $items[] = [
            'text'       => $text,
            'created'    => $s['created_at'] ?? '',
            'url'        => $s['url'] ?? '',
            'reblogs'    => $s['reblogs_count'] ?? 0,
            'favourites' => $s['favourites_count'] ?? 0,
        ];
    }
    return $items;
}

/**
 * Fetches the latest posts from the configured WordPress RSS feed.
 *
 * @return array List of post items, or an array with an 'error' key on failure
 */
function fetch_blog(): array {
    $res = http_get('https://planetozh.com/blog/feed/');
    if ($res['status'] !== 200) {
        return ['error' => 'Blog feed error ' . $res['status']];
    }

    $xml = @simplexml_load_string($res['body']);
    if (!$xml) {
        return ['error' => 'Blog: invalid RSS'];
    }

    $items = [];
    foreach ($xml->channel->item as $item) {
        $items[] = [
            'title' => (string) $item->title,
            'url'   => (string) $item->link,
            'date'  => (string) $item->pubDate,
        ];
        if (count($items) >= 5) {
            break;
        }
    }
    return $items;
}

/**
 * Returns cached data for a section, refreshing via the fetcher if stale or missing.
 *
 * @param string   $key     Cache key identifier
 * @param callable $fetcher Callback that fetches fresh data and returns an array
 * @return array{data: array|null, stale: bool, error: string|null}
 */
function get_section(string $key, callable $fetcher): array {
    $cached = cache_load($key);

    if ($cached['data'] !== null && !$cached['stale']) {
        return ['data' => $cached['data'], 'stale' => false, 'error' => null];
    }

    $fresh = $fetcher();

    if (isset($fresh['error'])) {
        return [
            'data'  => $cached['data'],
            'stale' => true,
            'error' => $fresh['error'],
        ];
    }

    cache_set($key, $fresh);
    return ['data' => $fresh, 'stale' => false, 'error' => null];
}

/**
 * Escapes a string for safe HTML output.
 *
 * @param string $s Input string
 * @return string HTML-escaped string
 */
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Converts an ISO 8601 datetime string to a human-readable relative time.
 *
 * @param string $iso ISO 8601 datetime string
 * @return string Relative time, e.g. '3h ago', '2d ago', or 'Jan 5'
 */
function time_ago(string $iso): string {
    $ts   = strtotime($iso);
    $diff = time() - $ts;
    if ($diff < 60)          { return 'just now'; }
    if ($diff < 3600)        { return (int)($diff / 60) . 'm ago'; }
    if ($diff < 86400)       { return (int)($diff / 3600) . 'h ago'; }
    if ($diff < 86400 * 7)   { return (int)($diff / 86400) . 'd ago'; }
    if ($diff < 86400 * 30)  { return (int)($diff / 86400 / 7) . 'w ago'; }
    return date('M j', $ts);
}

/**
 * Renders a warning notice if a section has stale or errored data.
 *
 * @param array  $section Section result array with 'error' and 'stale' keys
 * @param string $label   Human-readable label for the section
 * @return string HTML string with the warning notice, or empty string if fresh
 */
function stale_notice(array $section, string $label): string {
    if (!$section['error'] && !$section['stale']) {
        return '';
    }
    $msg = $section['error'] ?? ('Stale data - could not refresh ' . $label);
    return '<div class="stale-notice" title="' . h($msg) . '">⚠ cached</div>';
}

/**
 * Truncates a string to a maximum length, appending an ellipsis if needed.
 *
 * @param string $s   Input string
 * @param int    $max Maximum character length (default 160)
 * @return string Truncated string
 */
function truncate(string $s, int $max = 160): string {
    return mb_strlen($s) > $max ? mb_substr($s, 0, $max - 1) . '…' : $s;
}

/**
 * Removes emoji characters from a string.
 *
 * @param string $s Input string
 * @return string String with emoji removed
 */
function strip_emoji(string $s): string {
    return preg_replace('/[\x{1F000}-\x{1FFFF}|\x{2600}-\x{27BF}|\x{2300}-\x{23FF}|\x{FE00}-\x{FEFF}|\x{1F900}-\x{1F9FF}]/u', '', $s);
}
