<?php

declare(strict_types=1);

$responses = $responses ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Responses</title>
    <?php require BASE_PATH . '/app/Views/partials/theme_red.php'; ?>
</head>
<body>
<h1>Donor Responses</h1>

<p>
    <a href="<?= htmlspecialchars(app_url('/requests/history'), ENT_QUOTES, 'UTF-8') ?>">My Blood Requests</a> |
    <a href="<?= htmlspecialchars(app_url('/dashboard'), ENT_QUOTES, 'UTF-8') ?>">Back to dashboard</a>
</p>

<?php if ($responses === []): ?>
    <p>No donor responses yet.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
        <tr>
            <th>Request ID</th>
            <th>Blood Type</th>
            <th>City</th>
            <th>Units</th>
            <th>Request Status</th>
            <th>Donor</th>
            <th>Donor Email</th>
            <th>Decision</th>
            <th>Responded At</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($responses as $row): ?>
            <tr>
                <td><?= (int) $row['request_id'] ?></td>
                <td><?= htmlspecialchars((string) $row['blood_type'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['city'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) $row['units'] ?></td>
                <td><?= htmlspecialchars((string) $row['request_status'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) ($row['donor_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) ($row['donor_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['decision'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['responded_at'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>