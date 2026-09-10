<?php $title = 'Aiven database evidence'; require APP_DIR . 'views/products/header.php'; ?>
<a class="back-link" href="<?= site_url('products') ?>">&larr; Back to products</a>
<section class="page-heading"><div><p class="eyebrow">LABORATORY EXERCISE NO. 5</p><h1>Aiven MySQL products table</h1><p class="muted">Live schema and rows queried through the deployed application.</p></div></section>
<section class="stats database-stats">
  <article class="stat card"><span>Database</span><strong><?= product_escape($database['database_name'] ?? '') ?></strong><small>Aiven service database</small></article>
  <article class="stat card"><span>Server</span><strong class="database-server"><?= product_escape($database['server_name'] ?? '') ?></strong><small>Remote MySQL host</small></article>
  <article class="stat card"><span>Engine</span><strong>MySQL</strong><small><?= product_escape($database['version'] ?? '') ?></small></article>
</section>
<section class="card evidence-card"><div class="table-heading"><h2>Table schema: products</h2><span class="badge">Connected with TLS</span></div><div class="table-scroll"><table><thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead><tbody>
<?php foreach ($columns as $column): ?><tr><td><strong><?= product_escape($column['Field']) ?></strong></td><td><?= product_escape($column['Type']) ?></td><td><?= product_escape($column['Null']) ?></td><td><?= product_escape($column['Key']) ?></td><td><?= product_escape((string) ($column['Default'] ?? 'NULL')) ?></td><td><?= product_escape($column['Extra']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></section>
<section class="card evidence-card"><div class="table-heading"><h2>SELECT * FROM products</h2><span class="muted"><?= count($products) ?> row<?= count($products) === 1 ? '' : 's' ?></span></div><div class="table-scroll"><table><thead><tr><th>ID</th><th>Product name</th><th>Description</th><th>Price</th><th>Quantity</th><th>Created at</th></tr></thead><tbody>
<?php if (!$products): ?><tr><td colspan="6" class="muted">The table is currently empty.</td></tr><?php endif; ?>
<?php foreach ($products as $product): ?><tr><td><?= (int) $product['id'] ?></td><td><strong><?= product_escape($product['product_name']) ?></strong></td><td><?= product_escape($product['description']) ?></td><td>&#8369;<?= number_format((float) $product['price'], 2) ?></td><td><?= number_format((int) $product['quantity']) ?></td><td><?= product_escape($product['created_at']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></section>
<?php require APP_DIR . 'views/products/footer.php'; ?>
