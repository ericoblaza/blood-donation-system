<?php

declare(strict_types=1);

// Login form view only — $errors and $old are set by AuthController before this file is included.

$errors = $errors ?? [];
$old = $old ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <?php require BASE_PATH . '/app/Views/partials/theme_red.php'; ?>
</head>
<body>
    <div class="auth-center">
        <div class="auth-card">
            <h1>Login</h1>

            <form action="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>" method="post" novalidate>
                <div class="auth-field">
                    <label for="email">Email</label><br>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="150"
                        value="<?= htmlspecialchars((string)($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <?php if (!empty($errors['email'])): ?>
                        <span class="error"><?= htmlspecialchars((string)$errors['email'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <div class="auth-field">
                    <label for="password">Password</label><br>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        minlength="6"
                    >
                    <?php if (!empty($errors['password'])): ?>
                        <span class="error"><?= htmlspecialchars((string)$errors['password'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <button class="auth-submit" type="submit">Login</button>
            </form>

            <p>
                Don’t have an account?
                <a href="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>">Create one</a>
            </p>
        </div>
    </div>
</body>
</html>