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
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter your name and a valid email.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $exists = db()->prepare('SELECT id FROM users WHERE email = ?');
            $exists->execute([$email]);
            if ($exists->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                db()->prepare('INSERT INTO users (name, email, password) VALUES (?,?,?)')->execute([$name, $email, $hash]);
                $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
                $stmt->execute([$email]);
                login_user($stmt->fetch());
                flash_set('success', 'Welcome to Smart Wishing.');
                redirect('dashboard/index.php');
            }
        }
    }
}
$pageMeta = ['title' => 'Register | Smart Wishing', 'description' => 'Create a Smart Wishing account.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-md px-4 py-16">
    <h1 class="font-serif text-4xl">Create your account</h1>
    <?php if ($error): ?><p class="mt-4 text-rose-600"><?= e($error) ?></p><?php endif; ?>
    <form method="post" class="mt-8 space-y-4">
        <?= csrf_field() ?>
        <input name="name" required placeholder="Name" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <input type="email" name="email" required placeholder="Email" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <input type="password" name="password" required minlength="8" placeholder="Password" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <input type="password" name="confirm" required placeholder="Confirm password" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <button class="sw-btn w-full">Register</button>
    </form>
    <p class="mt-4 text-sm">Already have an account? <a class="font-semibold text-fuchsia-600" href="<?= e(url('login.php')) ?>">Login</a></p>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
