<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>V-Track Login</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    <style>
        body {
            background: url('<?= base_url('images/banner-bg.png') ?>') no-repeat center center fixed;
            background-size: cover;
        }
        .login-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(25, 118, 210, 0.15);
            padding: 40px 32px;
            min-width: 320px;
            text-align: center;
        }
        .login-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1976d2;
            margin-bottom: 24px;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .login-form input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #bdbdbd;
            font-size: 1rem;
        }
        .login-btn {
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-title">Login to V-Track</div>
        <?php if (isset($error)): ?>
            <div style="color: red; margin-bottom: 16px; font-weight: 500;">
                <?= esc($error) ?>
            </div>
        <?php endif; ?>
        <form class="login-form" method="post" action="<?= base_url('login') ?>">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>
</html>
