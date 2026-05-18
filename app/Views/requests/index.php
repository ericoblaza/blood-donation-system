<?php

declare(strict_types=1);

$requests = $requests ?? [];
$myResponses = $myResponses ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Open Blood Requests</title>
    <?php require BASE_PATH . '/app/Views/partials/theme_red.php'; ?>
</head>
<body>
<h1>Open Blood Requests</h1>

<p>
    <a href="<?= htmlspecialchars(app_url('/requests/create'), ENT_QUOTES, 'UTF-8') ?>">Create New Request</a> |
    <a href="<?= htmlspecialchars(app_url('/dashboard'), ENT_QUOTES, 'UTF-8') ?>">Back to dashboard</a>
</p>

<?php if ($requests === []): ?>
    <p>No open requests yet.</p>
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
            <th>Action</th>
            <th>My Decision</th>
        </tr>
        </thead>
        <tbody>
        <?php $currentUserId = (int) ($_SESSION['user']['id'] ?? 0); ?>
        <?php foreach ($requests as $row): ?>
            <tr>
            <td>
                <a href="<?= htmlspecialchars(app_url('/requests/' . (int) $row['id']), ENT_QUOTES, 'UTF-8') ?>">
                    <?= (int) $row['id'] ?>
                </a>
            </td>
            <td><?= htmlspecialchars((string) $row['blood_type'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $row['city'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $row['units'] ?></td>
            <td><?= htmlspecialchars((string) $row['status'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) ($row['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) ($row['contact_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> / <?= htmlspecialchars((string) ($row['contact_phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $row['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>
                <?php if ((int) $row['requester_user_id'] === $currentUserId): ?>
                    <em>Your request</em>
                <?php else: ?>
                    <form action="<?= htmlspecialchars(app_url('/requests/accept'), ENT_QUOTES, 'UTF-8') ?>" method="post" style="display:inline;" data-confirm="Accept this blood request?">
                        <input type="hidden" name="request_id" value="<?= (int) $row['id'] ?>">
                        <button type="submit">Accept</button>
                    </form>

                    <form action="<?= htmlspecialchars(app_url('/requests/decline'), ENT_QUOTES, 'UTF-8') ?>" method="post" style="display:inline;" data-confirm="Decline this blood request?">
                        <input type="hidden" name="request_id" value="<?= (int) $row['id'] ?>">
                        <button type="submit">Decline</button>
                    </form>
                <?php endif; ?>
            
            </td>


            <?php $decision = $myResponses[(int) $row['id']] ?? ''; ?>
            <td>
                    <?php
                    if ($decision === 'accept') {
                        echo 'Accepted';
                    } elseif ($decision === 'decline') {
                        echo 'Declined';
                    } else {
                        echo 'No response yet';
                    }
                    ?>
</td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php require BASE_PATH . '/app/Views/partials/confirm_modal.php'; ?>
</body>
</html>