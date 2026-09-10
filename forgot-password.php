<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$info = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid session.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $token = bin2hex(random_bytes(32));
            $hash = hash('sha256', $token);
            db()->prepare('INSERT INTO password_resets (email, token_hash, expires_at) VALUES (?,?,?)')
                ->execute([$email, $hash, date('Y-m-d H:i:s', time() + 3600)]);
            $link = url('reset-password.php?token=' . $token);
            $info = 'Reset link (valid 1 hour, shown because this local setup has no mail server): ' . $link;
        } else {
            $info = 'If that email exists, a reset link was prepared.';
        }
    }
}
$pageMeta = ['title' => 'Forgot password | Smart Wishing', 'description' => 'Reset your Smart Wishing password.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-md px-4 py-16">
    <h1 class="font-serif text-4xl">Forgot password</h1>
    <?php if ($error): ?><p class="mt-4 text-rose-600"><?= e($error) ?></p><?php endif; ?>
    <?php if ($info): ?><p class="mt-4 break-all text-sm text-emerald-700"><?= e($info) ?></p><?php endif; ?>
    <form method="post" class="mt-8 space-y-4">
        <?= csrf_field() ?>
        <input type="email" name="email" required placeholder="Email" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <button class="sw-btn w-full">Send reset link</button>
    </form>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
