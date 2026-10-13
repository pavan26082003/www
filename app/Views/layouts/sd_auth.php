<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, interactive-widget=overlays-content">
    <title><?= esc($title ?? 'Efactor') ?></title>
</head>
<body>
    <?= $this->renderSection('content') ?>
</body>
</html>
