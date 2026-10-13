<?php
$homeUrl = (string) ($homeUrl ?? site_url('NAS/home'));
$checkUrl = (string) ($checkUrl ?? site_url('NAS/storage-plans/check'));
$errorType = (string) ($errorType ?? 'Error');
$errorMessage = (string) ($errorMessage ?? 'Unknown setup error');
$showTechnicalError = (bool) ($showTechnicalError ?? false);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Storage Plans Setup - eFactor NAS</title>
<style>
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;background:#f5f7fb;color:#202431}
.card{width:min(680px,100%);padding:28px;border:1px solid #e4e8ef;border-radius:18px;background:#fff;box-shadow:0 18px 50px rgba(29,38,59,.10)}
.icon{width:48px;height:48px;display:grid;place-items:center;border-radius:14px;background:#fff6e4;color:#ad7616;font-size:22px;font-weight:800}
h1{margin:16px 0 8px;color:#161a24;font-size:24px}
p{margin:0;color:#6f7787;line-height:1.65}
.steps{margin-top:20px;padding:16px;border-radius:12px;background:#fafbfe;color:#343b49;line-height:1.8}
.technical{margin-top:16px;padding:13px;border:1px solid #f2d6da;border-radius:10px;background:#fff5f6;color:#a33b46;font-family:Consolas,"Courier New",monospace;font-size:12px;word-break:break-word}
.actions{margin-top:22px;display:flex;flex-wrap:wrap;gap:10px}
a{min-height:40px;padding:0 14px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700}
.check{background:#6257d9;color:#fff}
.home{border:1px solid #e4e8ef;background:#fff;color:#343b49}
</style>
</head>
<body>
<div class="card">
<div class="icon">!</div>
<h1>Storage plans setup is incomplete</h1>
<p>The NAS Home page is working, but one of the new purchase-plan dependencies is not ready yet.</p>
<div class="steps">
<strong>Check these items:</strong><br>
1. All purchase-plan PHP files are in the exact folders.<br>
2. The SQL migration was run against the same NAS database.<br>
3. <code>nas_storage_plans</code> exists.<br>
4. <code>nas_storage_purchases</code> exists.<br>
5. The plans table contains active plans.
</div>
<?php if ($showTechnicalError): ?>
<div class="technical"><?= esc($errorType) ?>: <?= esc($errorMessage) ?></div>
<?php endif; ?>
<div class="actions">
<a href="<?= esc($checkUrl) ?>" class="check">Run setup check</a>
<a href="<?= esc($homeUrl) ?>" class="home">Back to NAS Home</a>
</div>
</div>
</body>
</html>
