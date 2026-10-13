<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticker form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #202940; }
        h1 { font-size: 1.4rem; }
        dl { max-width: 720px; }
        dt { font-weight: 700; margin-top: 12px; }
        dd { margin: 4px 0 0; }
        .empty { color: #64748b; }
    </style>
</head>
<body>
    <h1>Sticker form</h1>
    <?php if (empty($form)): ?>
        <p class="empty">No sticker form found for this model.</p>
    <?php else: ?>
        <dl>
            <?php foreach ((array) $form as $key => $value): ?>
                <dt><?php echo esc((string) $key); ?></dt>
                <dd><?php echo esc(is_scalar($value) ? (string) $value : json_encode($value)); ?></dd>
            <?php endforeach; ?>
        </dl>
    <?php endif; ?>
</body>
</html>
