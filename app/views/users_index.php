<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $escape($page_title) ?> | User Directory</title>
    <style>
        :root { --ink:#eef2ff; --muted:#9aa7bd; --panel:#111827; --line:#263247; --lava:#ff5a1f; --cyan:#22d3ee; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; color:var(--ink); font-family:Inter,Segoe UI,sans-serif; background:#070b14; }
        body::before { content:""; position:fixed; inset:0; pointer-events:none; opacity:.3; background-image:linear-gradient(rgba(34,211,238,.08) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,.08) 1px,transparent 1px); background-size:42px 42px; mask-image:linear-gradient(to bottom,#000,transparent 85%); }
        .shell { position:relative; width:min(1120px,calc(100% - 32px)); margin:auto; padding:28px 0 64px; }
        nav { display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border:1px solid var(--line); border-radius:16px; background:rgba(17,24,39,.84); }
        .brand { font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .brand span { color:var(--lava); }
        nav a { color:var(--ink); text-decoration:none; margin-left:18px; font-size:.92rem; }
        nav a:hover { color:var(--cyan); }
        h1 { margin:58px 0 30px; font-size:clamp(2.6rem,7vw,5rem); line-height:.96; letter-spacing:-.055em; }
        h1 em { color:var(--lava); font-style:normal; }
        .table-wrap { overflow-x:auto; border:1px solid var(--line); border-radius:20px; background:rgba(17,24,39,.94); box-shadow:0 28px 70px rgba(0,0,0,.35); }
        table { width:100%; min-width:780px; border-collapse:collapse; }
        th,td { padding:18px 20px; text-align:left; border-bottom:1px solid var(--line); }
        th { color:var(--cyan); background:#0b111e; font:700 .72rem ui-monospace,monospace; letter-spacing:.12em; text-transform:uppercase; }
        tr:last-child td { border-bottom:0; }
        tbody tr:hover { background:rgba(34,211,238,.045); }
        td:first-child { color:var(--lava); font-weight:900; }
        .username { color:var(--cyan); }
        .empty { padding:34px; text-align:center; color:var(--muted); }
        @media (max-width:760px) { nav { align-items:flex-start; } nav a { display:block; margin:5px 0 0 12px; } h1 { margin-top:42px; } }
    </style>
</head>
<body>
<main class="shell">
    <nav>
        <div class="brand">Student<span>Signal</span></div>
        <div>
            <a href="<?= $escape(site_url('student')) ?>">Home</a>
            <a href="<?= $escape(site_url('student/profile?access_code=ABDON-F2-2026')) ?>">Student Profile</a>
            <a href="<?= $escape(site_url('users')) ?>">User Directory</a>
        </div>
    </nav>
    <h1>User <em>Directory</em></h1>
    <section class="table-wrap">
        <?php if ($users): ?>
            <table>
                <thead><tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Username</th></tr></thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $escape($user['id']) ?></td>
                        <td><?= $escape($user['firstname']) ?></td>
                        <td><?= $escape($user['lastname']) ?></td>
                        <td><?= $escape($user['email']) ?></td>
                        <td class="username">@<?= $escape($user['username']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="empty">No users are currently stored in the database.</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
