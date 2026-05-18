<?php

declare(strict_types=1);

$bloodRequest = $bloodRequest ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Request #<?= (int) ($bloodRequest['id'] ?? 0) ?></title>
    <?php require BASE_PATH . '/app/Views/partials/theme_red.php'; ?>
</head>
<body>
<h1>Blood Request #<?= (int) ($bloodRequest['id'] ?? 0) ?></h1>

<p>
    <strong>Blood type:</strong> <?= htmlspecialchars((string) ($bloodRequest['blood_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?><br>
    <strong>City:</strong> <?= htmlspecialchars((string) ($bloodRequest['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?><br>
    <strong>Units:</strong> <?= (int) ($bloodRequest['units'] ?? 0) ?><br>
    <strong>Status:</strong> <?= htmlspecialchars((string) ($bloodRequest['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?><br>
    <strong>Notes:</strong> <?= htmlspecialchars((string) ($bloodRequest['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?><br>
    <strong>Contact:</strong> <?= htmlspecialchars((string) ($bloodRequest['contact_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($bloodRequest['contact_phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)<br>
    <strong>Created:</strong> <?= htmlspecialchars((string) ($bloodRequest['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
</p>

<p>
    <a href="<?= htmlspecialchars(app_url('/requests'), ENT_QUOTES, 'UTF-8') ?>">Back to open requests</a>
</p>
</body>
</html>
