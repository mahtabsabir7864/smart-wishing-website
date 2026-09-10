<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
if (current_user()) {
    redirect('dashboard/index.php');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid session.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid email or password.';
        } elseif ($user['status'] !== 'active') {
            $error = 'This account is suspended.';
        } else {
            login_user($user);
            $next = (string) ($_GET['next'] ?? 'dashboard/index.php');
            if (!str_starts_with($next, '/') && !preg_match('#^[a-z0-9_./-]+$#i', $next)) {
                $next = 'dashboard/index.php';
            }
            redirect($next);
        }
    }
}
$pageMeta = ['title' => 'Login | Smart Wishing', 'description' => 'Sign in to manage your wishes.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-md px-4 py-16">
    <h1 class="font-serif text-4xl">Welcome back</h1>
    <?php if ($error): ?><p class="mt-4 text-rose-600"><?= e($error) ?></p><?php endif; ?>
    <form method="post" class="mt-8 space-y-4">
        <?= csrf_field() ?>
        <input type="email" name="email" required placeholder="Email" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <input type="password" name="password" required placeholder="Password" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <button class="sw-btn w-full">Login</button>
    </form>
    <p class="mt-4 text-sm"><a href="<?= e(url('forgot-password.php')) ?>">Forgot password?</a></p>
    <p class="mt-2 text-sm">New here? <a class="font-semibold text-fuchsia-600" href="<?= e(url('register.php')) ?>">Register</a></p>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
