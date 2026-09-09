<?php $title = 'Sign in'; require APP_DIR . 'views/products/header.php'; ?>
<div class="login-grid">
<section class="intro"><p class="eyebrow">A LITTLE ORDER. A LOT OF CLARITY.</p><h1>Your inventory.<br>All in one place.</h1><p class="lead">Keep track of your products, update stock, and make room for what comes next.</p><div class="intro-card"><span class="big-symbol">&#9638;</span><div><strong>Simple tools for everyday stock.</strong><p>Add, organize, and manage your product catalog.</p></div></div></section>
<section class="card login-card"><span class="pill">SECURE ACCESS</span><h2>Welcome back</h2><p class="muted">Sign in to manage your products.</p>
<?php if ($error): ?><div class="alert error" role="alert"><?= product_escape($error) ?></div><?php endif; ?>
<form method="post" action="<?= site_url('login') ?>"><?= product_csrf_field() ?>
<label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="username" value="<?= product_escape($email) ?>" required placeholder="you@example.com">
<label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>
<button class="button primary full" type="submit">Sign in <span aria-hidden="true">&rarr;</span></button>
</form><p class="fine">Product management is available to authorized users.</p></section>
</div>
<?php require APP_DIR . 'views/products/footer.php'; ?>
