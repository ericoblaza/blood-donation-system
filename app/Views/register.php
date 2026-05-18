<?php

declare(strict_types=1);

// Registration form view only — $errors and $old come from AuthController.

$errors = $errors ?? [];
$old = $old ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <?php require BASE_PATH . '/app/Views/partials/theme_red.php'; ?>
</head>
<body>
<div class="auth-center">
    <div class="auth-card">
        <h1>Register</h1>

        <form action="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>" method="post" novalidate>
            <div class="auth-field">
                <label for="name">Full Name</label><br>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars((string)($old['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?php if (!empty($errors['name'])): ?>
                    <span class="error"><?= htmlspecialchars((string)$errors['name'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="auth-field">
                <label for="email">Email</label><br>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars((string)($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?php if (!empty($errors['email'])): ?>
                    <span class="error"><?= htmlspecialchars((string)$errors['email'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="auth-field">
                <label for="password">Password</label><br>
                <input type="password" id="password" name="password">
                <?php if (!empty($errors['password'])): ?>
                    <span class="error"><?= htmlspecialchars((string)$errors['password'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="auth-field">
                <label for="password_confirmation">Retype Password</label><br>
                <input type="password" id="password_confirmation" name="password_confirmation">
                <?php if (!empty($errors['password_confirmation'])): ?>
                    <span class="error"><?= htmlspecialchars((string)$errors['password_confirmation'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <button class="auth-submit" type="submit">Create Account</button>
            <p>
                Already have an account?
                <a href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>">Go to login</a>
            </p>
        </form>
    </div>
</div>
</body>
</html>