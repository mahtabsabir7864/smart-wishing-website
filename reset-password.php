<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid session.';
    } else {
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm'] ?? '');
        if (strlen($password) < 8 || $password !== $confirm) {
            $error = 'Passwords must match and be at least 8 characters.';
        } else {
            $hash = hash('sha256', $token);
            $stmt = db()->prepare('SELECT email FROM password_resets WHERE token_hash = ? AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
            $stmt->execute([$hash]);
            $row = $stmt->fetch();
            if (!$row) {
                $error = 'This reset link is invalid or expired.';
            } else {
                db()->prepare('UPDATE users SET password = ? WHERE email = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $row['email']]);
                db()->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$row['email']]);
                flash_set('success', 'Password updated. You can sign in now.');
                redirect('login.php');
            }
        }
    }
}
$pageMeta = ['title' => 'Reset password | Smart Wishing', 'description' => 'Choose a new password.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-md px-4 py-16">
    <h1 class="font-serif text-4xl">Reset password</h1>
    <?php if ($error): ?><p class="mt-4 text-rose-600"><?= e($error) ?></p><?php endif; ?>
    <form method="post" class="mt-8 space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <input type="password" name="password" required minlength="8" placeholder="New password" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <input type="password" name="confirm" required placeholder="Confirm password" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <button class="sw-btn w-full">Update password</button>
    </form>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
