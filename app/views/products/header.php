<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= product_escape($title ?? 'Products') ?> | Stockroom</title>
<link rel="stylesheet" href="<?= product_escape(rtrim(BASE_URL, '/') . '/assets/stockroom.css') ?>">
</head>
<body>
<header class="topbar"><a class="brand" href="<?= site_url('products') ?>"><span class="brand-icon">S</span> Stockroom<span class="brand-sub">INVENTORY MANAGER</span></a>
<?php if (!empty($_SESSION['product_user'])): ?><div class="account"><span><?= product_escape($_SESSION['product_user']) ?></span><form method="post" action="<?= site_url('logout') ?>"><?= product_csrf_field() ?><button class="button subtle" type="submit">Sign out</button></form></div><?php endif; ?></header>
<main class="shell">
