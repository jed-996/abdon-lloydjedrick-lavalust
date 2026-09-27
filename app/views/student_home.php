<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $escape($page_title) ?> | Home</title>
    <style>
        :root { --ink:#eef2ff; --muted:#9aa7bd; --panel:#111827; --line:#263247; --lava:#ff5a1f; --cyan:#22d3ee; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; color:var(--ink); font-family:Inter,Segoe UI,sans-serif; background:#070b14; }
        body::before { content:""; position:fixed; inset:0; pointer-events:none; opacity:.34; background-image:linear-gradient(rgba(34,211,238,.08) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,.08) 1px,transparent 1px); background-size:42px 42px; mask-image:linear-gradient(to bottom,#000,transparent 85%); }
        .shell { position:relative; width:min(1080px,calc(100% - 32px)); margin:auto; padding:28px 0 64px; }
        nav { display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border:1px solid var(--line); border-radius:16px; background:rgba(17,24,39,.84); backdrop-filter:blur(12px); }
        .brand { font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .brand span { color:var(--lava); }
        nav a { color:var(--ink); text-decoration:none; margin-left:18px; font-size:.92rem; }
        nav a:hover { color:var(--cyan); }
        .notice { margin:24px 0 0; padding:15px 18px; border:1px solid rgba(255,90,31,.55); border-radius:14px; background:rgba(255,90,31,.1); color:#ffd7c7; }
        .hero { display:grid; grid-template-columns:1.15fr .85fr; gap:28px; align-items:center; padding:72px 0 42px; }
        .eyebrow { color:var(--cyan); font:700 .78rem ui-monospace,monospace; letter-spacing:.18em; text-transform:uppercase; }
        h1 { margin:12px 0 18px; font-size:clamp(2.7rem,7vw,5.8rem); line-height:.94; letter-spacing:-.06em; }
        h1 em { display:block; color:var(--lava); font-style:normal; }
        .lead { max-width:650px; color:var(--muted); font-size:1.08rem; line-height:1.75; }
        .actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:30px; }
        .button { display:inline-flex; padding:13px 18px; border-radius:12px; color:#081018; background:var(--cyan); font-weight:800; text-decoration:none; }
        .button.secondary { color:var(--ink); background:transparent; border:1px solid var(--line); }
        .id-card { position:relative; overflow:hidden; padding:28px; border:1px solid var(--line); border-radius:24px; background:linear-gradient(145deg,#151d2c,#0c111d); box-shadow:0 28px 70px rgba(0,0,0,.35); }
        .id-card::after { content:"F2"; position:absolute; right:-8px; bottom:-28px; color:rgba(255,90,31,.1); font-weight:900; font-size:9rem; }
        .avatar { display:grid; place-items:center; width:72px; height:72px; border-radius:20px; background:var(--lava); color:#fff; font-size:1.45rem; font-weight:900; }
        .id-card h2 { margin:22px 0 6px; font-size:1.55rem; }
        .id-card p { margin:7px 0; color:var(--muted); }
        .flow { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; padding-top:26px; }
        .flow div { padding:14px 10px; text-align:center; border:1px solid var(--line); border-radius:12px; background:var(--panel); color:var(--muted); font:600 .76rem ui-monospace,monospace; }
        @media (max-width:760px) { .hero { grid-template-columns:1fr; padding-top:48px; } .flow { grid-template-columns:1fr; } nav { align-items:flex-start; } nav a { display:block; margin:5px 0 0 12px; } }
    </style>
</head>
<body>
<main class="shell">
    <nav>
        <div class="brand">Student<span>Signal</span></div>
        <div>
            <a href="<?= $escape(site_url('student')) ?>">Home</a>
            <a href="<?= $escape(site_url('student/profile?access_code=ABDON-F2-2026')) ?>">Student Profile</a>
        </div>
    </nav>

    <?php if ($access_notice): ?>
        <div class="notice" role="alert"><strong>StudentMiddleware:</strong> Direct profile access was blocked. Use the authorized profile link below.</div>
    <?php endif; ?>

    <section class="hero">
        <div>
            <div class="eyebrow">Web Systems Laboratory / 2026</div>
            <h1>Student <em>Information</em></h1>
            <p class="lead"><?= $escape($description) ?> This page demonstrates LavaLust routing, controller data passing, views, and route middleware.</p>
            <div class="actions">
                <a class="button" href="<?= $escape(site_url('student/profile?access_code=ABDON-F2-2026')) ?>">Open protected profile</a>
                <a class="button secondary" href="<?= $escape(site_url()) ?>">LavaLust landing page</a>
            </div>
        </div>
        <aside class="id-card">
            <div class="avatar">LA</div>
            <h2><?= $escape($name) ?></h2>
            <p><?= $escape($course) ?></p>
            <p><?= $escape($year) ?> / Section <?= $escape($section) ?></p>
            <p><?= $escape($email) ?></p>
        </aside>
    </section>

    <section class="flow" aria-label="Request flow">
        <div>Browser</div><div>Route</div><div>Middleware</div><div>Controller</div><div>View</div>
    </section>
</main>
</body>
</html>
