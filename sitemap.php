<?php require 'config.php';
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach (['/', '/about.php', '/contact.php', '/privacy.php', '/index.php?promo=1'] as $u): ?>
  <url><loc><?= e(SITE_URL . $u) ?></loc></url>
<?php endforeach; ?>
<?php foreach ($pdo->query("SELECT id FROM categories") as $c): ?>
  <url><loc><?= e(SITE_URL . '/index.php?cat=' . $c['id']) ?></loc></url>
<?php endforeach; ?>
<?php foreach ($pdo->query("SELECT id, created_at FROM products ORDER BY id") as $p): ?>
  <url><loc><?= e(SITE_URL . '/product.php?id=' . $p['id']) ?></loc><lastmod><?= date('Y-m-d', strtotime($p['created_at'])) ?></lastmod></url>
<?php endforeach; ?>
</urlset>