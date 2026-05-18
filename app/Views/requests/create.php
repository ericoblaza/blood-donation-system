<?php

declare(strict_types=1);

$errors = $errors ?? [];
$old = $old ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Blood Request</title>
    <?php require BASE_PATH . '/app/Views/partials/theme_red.php'; ?>
</head>
<body>
<h1>Create Blood Request</h1>

<form action="<?= htmlspecialchars(app_url('/requests'), ENT_QUOTES, 'UTF-8') ?>" method="post" novalidate>
    <div>
        <label for="blood_type">Blood Type</label><br>
        <select id="blood_type" name="blood_type">
            <option value="">-- Select --</option>
            <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $type): ?>
                <option value="<?= $type ?>" <?= (($old['blood_type'] ?? '') === $type) ? 'selected' : '' ?>><?= $type ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['blood_type'])): ?>
            <span class="error"><?= htmlspecialchars((string) $errors['blood_type'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
    </div>

    <br>

    <div>
        <label for="city">City</label><br>
        <input id="city" name="city" value="<?= htmlspecialchars((string) ($old['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <?php if (!empty($errors['city'])): ?>
            <span class="error"><?= htmlspecialchars((string) $errors['city'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
    </div>

    <br>

    <div>
        <label for="units">Units Needed</label><br>
        <input type="number" min="1" id="units" name="units" value="<?= htmlspecialchars((string) ($old['units'] ?? '1'), ENT_QUOTES, 'UTF-8') ?>">
        <?php if (!empty($errors['units'])): ?>
            <span class="error"><?= htmlspecialchars((string) $errors['units'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
    </div>

    <br>

    <div>
        <label for="contact_name">Contact Name</label><br>
        <input id="contact_name" name="contact_name" value="<?= htmlspecialchars((string) ($old['contact_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <?php if (!empty($errors['contact_name'])): ?>
            <span class="error"><?= htmlspecialchars((string) $errors['contact_name'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
    </div>

    <br>

    <div>
        <label for="contact_phone">Contact Phone</label><br>
        <input id="contact_phone" name="contact_phone" value="<?= htmlspecialchars((string) ($old['contact_phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <?php if (!empty($errors['contact_phone'])): ?>
            <span class="error"><?= htmlspecialchars((string) $errors['contact_phone'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
    </div>

    <br>

    <div>
        <label for="notes">Notes (optional)</label><br>
        <textarea id="notes" name="notes" rows="4" cols="40"><?= htmlspecialchars((string) ($old['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <br>
    <button type="submit">Submit Request</button>
</form>

<p>
    <a href="<?= htmlspecialchars(app_url('/requests'), ENT_QUOTES, 'UTF-8') ?>">View Open Requests</a> |
    <a href="<?= htmlspecialchars(app_url('/dashboard'), ENT_QUOTES, 'UTF-8') ?>">Back to dashboard</a>
</p>
</body>
</html>