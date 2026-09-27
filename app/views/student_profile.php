<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $escape($page_title) ?> | Profile</title>
    <style>
        :root { --ink:#f8fafc; --muted:#94a3b8; --panel:#111827; --line:#293548; --lava:#ff5a1f; --cyan:#22d3ee; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; color:var(--ink); font-family:Inter,Segoe UI,sans-serif; background:radial-gradient(circle at 80% 10%,rgba(255,90,31,.14),transparent 30%),#070b14; }
        .shell { width:min(1050px,calc(100% - 32px)); margin:auto; padding:28px 0 64px; }
        nav { display:flex; justify-content:space-between; padding:14px 18px; border:1px solid var(--line); border-radius:16px; background:rgba(17,24,39,.88); }
        .brand { font-weight:900; text-transform:uppercase; letter-spacing:.08em; }
        .brand span { color:var(--lava); }
        nav a { color:var(--ink); margin-left:18px; text-decoration:none; }
        nav a:hover { color:var(--cyan); }
        .status { display:inline-flex; gap:9px; align-items:center; margin:44px 0 18px; padding:8px 12px; border:1px solid rgba(34,211,238,.3); border-radius:999px; color:var(--cyan); font:700 .78rem ui-monospace,monospace; }
        .status::before { content:""; width:8px; height:8px; border-radius:50%; background:var(--cyan); box-shadow:0 0 14px var(--cyan); }
        h1 { margin:0 0 12px; font-size:clamp(2.4rem,6vw,4.7rem); letter-spacing:-.055em; }
        .subtitle { color:var(--muted); line-height:1.7; max-width:760px; }
        .grid { display:grid; grid-template-columns:1.25fr .75fr; gap:22px; margin-top:34px; }
        .card { padding:26px; border:1px solid var(--line); border-radius:20px; background:linear-gradient(145deg,rgba(17,24,39,.98),rgba(10,15,26,.98)); }
        .card h2 { margin:0 0 20px; font-size:.83rem; color:var(--lava); text-transform:uppercase; letter-spacing:.16em; }
        dl { display:grid; grid-template-columns:145px 1fr; margin:0; }
        dt,dd { padding:14px 0; border-bottom:1px solid var(--line); }
        dt { color:var(--muted); } dd { margin:0; font-weight:700; }
        .tag-list { display:flex; flex-wrap:wrap; gap:9px; padding:0; list-style:none; }
        .tag-list li { padding:9px 11px; border:1px solid var(--line); border-radius:10px; color:#cbd5e1; background:#0a101c; }
        .protected { margin-top:22px; padding:17px; border-left:3px solid var(--cyan); border-radius:0 12px 12px 0; background:rgba(34,211,238,.07); color:#b8f4ff; }
        @media (max-width:760px) { .grid { grid-template-columns:1fr; } dl { grid-template-columns:1fr; } dt { padding-bottom:3px; border:0; } dd { padding-top:0; } nav a { display:block; margin:5px 0 0 12px; } }
    </style>
</head>
<body>
<main class="shell">
    <nav>
        <div class="brand">Student<span>Signal</span></div>
        <div><a href="<?= $escape(site_url('student')) ?>">Home</a><a href="<?= $escape(site_url('student/profile')) ?>">Student Profile</a></div>
    </nav>

    <div class="status">StudentMiddleware access verified</div>
    <h1><?= $escape($name) ?></h1>
    <p class="subtitle"><?= $escape($description) ?></p>

    <section class="grid">
        <article class="card">
            <h2>Academic profile</h2>
            <dl>
                <dt>Student ID</dt><dd><?= $escape($student_id) ?></dd>
                <dt>Student Name</dt><dd><?= $escape($name) ?></dd>
                <dt>Course</dt><dd><?= $escape($course) ?></dd>
                <dt>Year Level</dt><dd><?= $escape($year) ?></dd>
                <dt>Section</dt><dd><?= $escape($section) ?></dd>
                <dt>Email</dt><dd><?= $escape($email) ?></dd>
            </dl>
            <div class="protected"><strong>Protected route:</strong> This view loaded only after StudentMiddleware approved the request.</div>
        </article>
        <aside>
            <article class="card">
                <h2>Skills</h2>
                <ul class="tag-list"><?php foreach ($skills as $skill): ?><li><?= $escape($skill) ?></li><?php endforeach; ?></ul>
            </article>
            <article class="card" style="margin-top:22px">
                <h2>Hobbies</h2>
                <ul class="tag-list"><?php foreach ($hobbies as $hobby): ?><li><?= $escape($hobby) ?></li><?php endforeach; ?></ul>
            </article>
        </aside>
    </section>
</main>
</body>
</html>
