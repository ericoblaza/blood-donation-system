<?php

declare(strict_types=1);

/** @var string $email Set by HomeController::dashboard before this view is included */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php require BASE_PATH . '/app/Views/partials/theme_red.php'; ?>
    <style>
        body.dash-page {
            background: #f4f4f4;
            margin: 0;
            padding: 6px 8px 24px;
        }

        .dash-wrap {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            width: 100%;
            margin: 0 0 24px;
        }

        .dash-header h1 {
            margin: 0 0 8px;
            font-size: 2.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .dash-sub {
            margin: 0;
            color: #666;
            font-size: 1rem;
        }

        .dash-logout {
            padding: 8px 16px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            background: #fff;
            color: #111;
            margin: 0;
        }

        .dash-cards {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .dash-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(300px, 1fr));
            gap: 24px;
            width: min(100%, 900px);
            margin: 0;
        }

        .dash-card {
            background: #fff;
            border: 1px solid #d9d9d9;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .dash-card-title {
            margin: 0;
            padding: 24px 24px 18px;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .dash-links {
            display: flex;
            flex-direction: column;
            border-top: 1px solid #e5e5e5;
        }

        .dash-link {
            display: block;
            padding: 16px 24px;
            color: #111;
            text-decoration: none;
            border-bottom: 1px solid #e5e5e5;
        }

        .dash-link:last-child {
            border-bottom: 0;
        }

        @media (max-width: 720px) {
            .dash-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="dash-page">
    <div class="dash-wrap">
        <div class="dash-header">
            <div>
                <h1>Dashboard</h1>
                <p class="dash-sub">Welcome, <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>.</p>
            </div>
            <form method="post" action="<?= htmlspecialchars(app_url('/logout'), ENT_QUOTES, 'UTF-8') ?>" data-confirm="Log out now?">
                <button type="submit" class="dash-logout">Logout</button>
            </form>
        </div>

        <div class="dash-cards">
            <div class="dash-grid">
            <section class="dash-card">
                <h2 class="dash-card-title">Actions</h2>
                <nav class="dash-links">
                    <a class="dash-link" href="<?= htmlspecialchars(app_url('/requests/create'), ENT_QUOTES, 'UTF-8') ?>">Create Blood Request</a>
                    <a class="dash-link" href="<?= htmlspecialchars(app_url('/requests'), ENT_QUOTES, 'UTF-8') ?>">View Open Blood Requests</a>
                </nav>
            </section>

            <section class="dash-card">
                <h2 class="dash-card-title">My Activity</h2>
                <nav class="dash-links">
                    <a class="dash-link" href="<?= htmlspecialchars(app_url('/requests/history'), ENT_QUOTES, 'UTF-8') ?>">My Blood Requests</a>
                    <a class="dash-link" href="<?= htmlspecialchars(app_url('/requests/responses'), ENT_QUOTES, 'UTF-8') ?>">My Responses</a>
                </nav>
            </section>
            </div>
        </div>
    </div>
    <?php require BASE_PATH . '/app/Views/partials/confirm_modal.php'; ?>
</body>
</html>
