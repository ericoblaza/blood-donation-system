<?php

declare(strict_types=1);

$requests = $requests ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Blood Requests</title>
    <?php require BASE_PATH . '/app/Views/partials/theme_red.php'; ?>
</head>
<body>
<h1>My Blood Requests</h1>

<p>
    <a href="<?= htmlspecialchars(app_url('/requests/create'), ENT_QUOTES, 'UTF-8') ?>">Create New Request</a> |
    <a href="<?= htmlspecialchars(app_url('/requests'), ENT_QUOTES, 'UTF-8') ?>">Open Requests</a> |
    <a href="<?= htmlspecialchars(app_url('/dashboard'), ENT_QUOTES, 'UTF-8') ?>">Back to dashboard</a>
</p>

<?php if ($requests === []): ?>
    <p>You have not created any requests yet.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Blood Type</th>
            <th>City</th>
            <th>Units</th>
            <th>Status</th>
            <th>Notes</th>
            <th>Contact</th>
            <th>Created</th>
            <th>Updated</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($requests as $row): ?>
            <?php $isOpen = (string) $row['status'] === 'open'; ?>
            <tr>
                <td><?= (int) $row['id'] ?></td>
                <td><?= htmlspecialchars((string) $row['blood_type'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['city'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) $row['units'] ?></td>
                <td><?= htmlspecialchars((string) $row['status'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) ($row['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) ($row['contact_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> / <?= htmlspecialchars((string) ($row['contact_phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) ($row['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="actions-cell">
                    <?php if ($isOpen): ?>
                        <a class="btn-plain" href="<?= htmlspecialchars(app_url('/requests/' . (int) $row['id'] . '/edit'), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                        <form method="post" action="<?= htmlspecialchars(app_url('/requests/' . (int) $row['id'] . '/delete'), ENT_QUOTES, 'UTF-8') ?>" data-confirm="Delete this blood request?">
                            <button type="submit" class="btn-plain">Delete</button>
                        </form>
                    <?php else: ?>
                        <em>Locked</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php require BASE_PATH . '/app/Views/partials/confirm_modal.php'; ?>
</body>
</html>