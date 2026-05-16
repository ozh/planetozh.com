<?php
require_once __DIR__ . '/index.config.php';
require_once __DIR__ . '/index.func.php';
define('CACHE_DIR',  __DIR__ . '/cache/');
define('CACHE_TTL',  3600*3); // 3 hour

$github   = get_section('github',   'fetch_github');
$spotify  = get_section('spotify',  'fetch_spotify');
$mastodon = get_section('mastodon', 'fetch_mastodon');
$blog     = get_section('blog', 'fetch_blog');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>planetozh</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:ital,wght@0,300;0,400;0,500;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <link rel='stylesheet' id='planetozh-style-css' href='index.css?ver=1.0' media='all' />
</head>
<body>
<div class="page">
    <header class="hero">
        <div class="hero-top" id="hero-top">
            <h1 id="planet">
                <span class="planet-core">
                <span class="planet-word">planet</span>ozh
                <span class="planet-letters">
                <span class="pl" data-i="0">p</span>
                <span class="pl" data-i="1">l</span>
                <span class="pl" data-i="2">a</span>
                <span class="pl" data-i="3">n</span>
                <span class="pl" data-i="4">e</span>
                <span class="pl" data-i="5">t</span>
                </span>
                </span>
            </h1>
            <span class="hero-tagline">// ozh richard <small>(first name = 'ozh')</small></span>
        </div>
        <p class="hero-desc">Open-source ; metal head ; maker of <a href="https://yourls.org" sztyle="color:var(--accent)">YOURLS</a>.</p>
        <div class="hero-links">
            <a class="link-pill" rel="me" href="https://planetozh.com/blog/">
                <svg viewBox="0 0 24 24" width="13" height="13" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="12" fill="currentColor"/>
                    <text x="12" y="16" text-anchor="middle" font-family="monospace" font-size="13" font-weight="bold" fill="var(--bg)" letter-spacing="-1">OZH</text>
                </svg>
                blog
            </a>
            <a class="link-pill" rel="me" href="https://github.com/ozh">
                <svg viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.546-1.387-1.333-1.756-1.333-1.756-1.09-.745.083-.73.083-.73 1.205.085 1.84 1.238 1.84 1.238 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.604-2.665-.305-5.467-1.334-5.467-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.604-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 21.795 24 17.295 24 12c0-6.63-5.37-12-12-12"/></svg>
                @ozh
            </a>
            <a class="link-pill" rel="me" href="https://fosstodon.org/@ozh">
                <svg viewBox="0 0 24 24"><path d="M23.268 5.313c-.35-2.578-2.617-4.61-5.304-5.004C17.51.242 15.792 0 11.813 0h-.03c-3.98 0-4.835.242-5.288.309C3.882.692 1.496 2.518.917 5.127.64 6.412.61 7.837.661 9.143c.074 1.874.088 3.745.26 5.611.118 1.24.325 2.47.62 3.68.55 2.237 2.777 4.098 4.96 4.857 2.336.792 4.849.923 7.256.38.265-.061.527-.132.786-.213.585-.184 1.27-.39 1.774-.753a.057.057 0 0 0 .023-.043v-1.809a.052.052 0 0 0-.02-.041.053.053 0 0 0-.046-.01 20.282 20.282 0 0 1-4.709.545c-2.73 0-3.463-1.284-3.674-1.818a5.593 5.593 0 0 1-.319-1.433.053.053 0 0 1 .066-.054c1.517.363 3.072.546 4.632.546.376 0 .75 0 1.125-.01 1.57-.044 3.224-.124 4.768-.422.038-.008.077-.015.11-.024 2.435-.464 4.753-1.92 4.989-5.604.008-.145.03-1.52.03-1.67.002-.512.167-3.63-.024-5.545zm-3.748 9.195h-2.561V8.29c0-1.309-.55-1.976-1.67-1.976-1.23 0-1.846.79-1.846 2.35v3.403h-2.546V8.663c0-1.56-.617-2.35-1.848-2.35-1.112 0-1.668.668-1.67 1.977v6.218H4.822V8.102c0-1.31.337-2.35 1.011-3.12.696-.77 1.608-1.164 2.74-1.164 1.311 0 2.302.5 2.962 1.498l.638 1.06.638-1.06c.66-.999 1.65-1.498 2.96-1.498 1.13 0 2.043.395 2.74 1.164.675.77 1.012 1.81 1.012 3.12z"/></svg>
                @ozh
            </a>
            <a class="link-pill" rel="me" href="https://open.spotify.com/user/ozhy">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
                ozhy
            </a>
            <a class="link-pill" href="https://yourls.org">
                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" fill-rule="evenodd" d="M4.336 8.216h3.059c2.104 0 3.81 1.842 3.81 4.114 0 2.272-1.705 4.114-3.81 4.114H4.336c-2.104 0-3.81-1.842-3.81-4.114 0-2.272 1.706-4.114 3.81-4.114zm0 2.639a1.367 1.474 0 0 0-1.366 1.474 1.367 1.474 0 0 0 1.366 1.474h3.06a1.367 1.474 0 0 0 1.366-1.474 1.367 1.474 0 0 0-1.366-1.474z"/>
                    <path fill="currentColor" fill-rule="nonzero" d="M8.307 13.911a1.466 1.58 0 0 1-1.465-1.582 1.466 1.58 0 0 1 1.465-1.581h7.469a1.466 1.58 0 0 1 1.465 1.581 1.466 1.58 0 0 1-1.465 1.582z"/>
                    <path fill="currentColor" fill-rule="evenodd" d="M16.471 8.093h3.151c2.167 0 3.924 1.897 3.924 4.236 0 2.34-1.757 4.237-3.924 4.237h-3.151c-2.167 0-3.924-1.897-3.924-4.237 0-2.34 1.757-4.236 3.924-4.236zm0 2.717a1.408 1.518 0 0 0-1.407 1.519 1.408 1.518 0 0 0 1.407 1.519h3.151a1.408 1.518 0 0 0 1.407-1.519 1.408 1.518 0 0 0-1.407-1.519z"/>
                </svg>
                YOURLS
            </a>
        </div>
    </header>

    <div class="grid">

        <!-- Now
        <div class="card now-card">
            <div class="card-header">
                <span class="card-dot" style="background:#f5a64a"></span>
                <span class="card-label">Now</span>
            </div>
            <div class="now-grid">
                <div class="now-cell">
                    <div class="now-label">Working on</div>
                    <div class="now-value">YOURLS</div>
                </div>
                <div class="now-cell">
                    <div class="now-label">Drinking</div>
                    <div class="now-value">WINE</div>
                </div>
                <div class="now-cell">
                    <div class="now-label">Listening</div>
                    <div class="now-value">DEATH METAL</div>
                </div>
                <div class="now-cell">
                    <div class="now-label">Location</div>
                    <div class="now-value">NANTES, FR</div>
                </div>
            </div>
        </div>
        -->

        <!-- Blog -->
        <div class="card">
            <div class="card-header">
                <span class="card-dot" style="background:#4a9a4a"></span>
                <span class="card-label"><a href="https://planetozh.com/blog/">Blog</a></span>
                <?= stale_notice($blog, 'Blog') ?>
                <?php if (!$blog['error'] && !$blog['stale']): ?>
                    <span class="card-count"><a href="https://planetozh.com/blog/">planetozh.com/blog</a></span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($blog['data']): ?>
                    <?php foreach ($blog['data'] as $i => $post): ?>
                        <a href="<?= h($post['url']) ?>" target="_blank" rel="noopener" style="display:block">
                            <div class="blog-item">
                                <span class="blog-item-num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
                                <div class="blog-item-inner">
                                    <div class="blog-title"><?= h($post['title']) ?></div>
                                    <div class="blog-meta"><?= h(date('Y/m/d', strtotime($post['date']))) ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty">No data available.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mastodon -->
        <div class="card">
            <div class="card-header">
                <span class="card-dot" style="background:#7c6af5"></span>
                <span class="card-label"><a href="https://fosstodon.org/@ozh">Mastodon</a></span>
                <?= stale_notice($mastodon, 'Mastodon') ?>
                <?php if (!$mastodon['error'] && !$mastodon['stale']): ?>
                    <span class="card-count"><a href="https://fosstodon.org/@ozh">@ozh@fosstodon.org</a></span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($mastodon['data']): ?>
                    <?php foreach ($mastodon['data'] as $toot): ?>
                        <a href="<?= h($toot['url']) ?>" target="_blank" rel="noopener" style="display:block">
                            <div class="toot-item">
                                <p class="toot-text"><?= h(truncate(strip_emoji($toot['text']))) ?> <span class="toot-time"><?= h(time_ago($toot['created'])) ?></span></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty">No data available.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- GitHub -->
        <div class="card">
            <div class="card-header">
                <span class="card-dot" style="background:#888"></span>
                <span class="card-label"><a href="https://github.com/ozh">GitHub</a></span>
                <?= stale_notice($github, 'GitHub') ?>
                <?php if (!$github['error'] && !$github['stale']): ?>
                    <span class="card-count"><a href="https://github.com/ozh">github.com/ozh</a></span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($github['data']): ?>
                    <?php $previous = $current = ''; ?>
                    <?php foreach ($github['data'] as $ev): ?>
                        <?php if ($ev['repo'].$ev['badge'] !== $previous): ?>
                            <div class="gh-item">
                                <span class="gh-badge <?= h($ev['badge']) ?>"><?= h($ev['badge']) ?></span>
                                <div class="gh-info">
                                    <div class="gh-repo"><a href="https://github.com/<?= h($ev['repo']) ?>"><?= h($ev['repo']) ?></a></div>
                                    <div class="gh-desc"><?= h($ev['desc']) ?></div>
                                </div>
                                <span class="gh-time"><?= h(time_ago($ev['created'])) ?></span>
                            </div>
                            <?php $previous = $ev['repo'].$ev['badge']; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty">No data available.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Spotify -->
        <div class="card">
            <div class="card-header">
                <span class="card-dot" style="background:#1db954"></span>
                <span class="card-label"><a href="https://open.spotify.com/user/ozhy">Recently played</a></span>
                <?= stale_notice($spotify, 'Spotify') ?>
                <?php if (!$spotify['error'] && !$spotify['stale']): ?>
                    <span class="card-count"><a href="https://open.spotify.com/user/ozhy">spotify</a></span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($spotify['data']): ?>
                    <?php foreach ($spotify['data'] as $i => $track): ?>
                        <a href="<?= h($track['url']) ?>" target="_blank" rel="noopener" style="display:block">
                            <div class="track-item">
                                <span class="track-num"><?= $i + 1 ?></span>
                                <div class="track-cover">
                                    <?php if ($track['cover']): ?>
                                        <img src="<?= h($track['cover']) ?>" alt="" loading="lazy">
                                    <?php else: ?>
                                        🎵
                                    <?php endif; ?>
                                </div>
                                <div class="track-info">
                                    <div class="track-name"><?= h($track['name']) ?></div>
                                    <div class="track-artist"><?= h($track['artist']) ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty">No data available.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <footer class="footer">
        <span>planetozh.com</span>
        <span id="footer-cache">cache ttl: <?= CACHE_TTL ?>s <span class="sep">-</span> next refresh in <?= cache_expires_in('blog') ?></span>
    </footer>

</div>
<script>
( function() {
    'use strict';

    var footerPlanet = document.getElementById( 'hero-top' );

    if ( footerPlanet ) {
        var letters    = footerPlanet.querySelectorAll( '.pl' );
        var radius     = 46;
        var numLetters = letters.length;
        var angleOffset = 0;
        var animFrame;
        var isHovered  = false;
        var speed      = 0.038; //

        function drawOrbit() {
            angleOffset += speed;
            letters.forEach( function( el, i ) {
                var angle = angleOffset + ( i / numLetters ) * Math.PI * 2;
                var x = Math.cos( angle ) * radius;
                var y = Math.sin( angle ) * radius * 0.38;
                var scale = 0.75 + ( ( Math.sin( angle ) + 1 ) / 2 ) * 0.4;
                el.style.transform = 'translate( calc(-50% + ' + x + 'px), calc(-50% + ' + y + 'px) ) scale(' + scale + ')';
                el.style.zIndex    = Math.sin( angle ) > 0 ? 3 : 1;
                el.style.opacity   = isHovered ? ( 0.5 + ( ( Math.sin( angle ) + 1 ) / 2 ) * 0.5 ) : 0;
            } );
            animFrame = requestAnimationFrame( drawOrbit );
        }

        footerPlanet.addEventListener( 'mouseenter', function() {
            isHovered = true;
            if ( ! animFrame ) drawOrbit();
        } );

        footerPlanet.addEventListener( 'mouseleave', function() {
            isHovered = false;
            setTimeout( function() {
                if ( ! isHovered ) {
                    cancelAnimationFrame( animFrame );
                    animFrame = null;
                    letters.forEach( function( el ) { el.style.opacity = 0; } );
                }
            }, 400 );
        } );
    }
} )();
</script>
<!--
    Made with ❤️ by ozh - https://planetozh.com
    Source code: https://github.com/ozh/planetozh.com/
-->
</body>
</html>
