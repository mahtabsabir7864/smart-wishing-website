<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid session.';
    } else {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($name === '' || $subject === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please complete all fields with a valid email.';
        } else {
            db()->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)')
                ->execute([mb_substr($name, 0, 120), mb_substr($email, 0, 190), mb_substr($subject, 0, 180), $message]);
            flash_set('success', 'Message sent. We will get back to you soon.');
            redirect('contact.php');
        }
    }
}
$pageMeta = ['title' => 'Contact | Smart Wishing', 'description' => 'Contact the Smart Wishing team.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-xl px-4 py-16">
    <h1 class="font-serif text-4xl">Contact</h1>
    <?php if ($error): ?><p class="mt-4 text-rose-600"><?= e($error) ?></p><?php endif; ?>
    <form method="post" class="mt-8 space-y-4">
        <?= csrf_field() ?>
        <input name="name" required placeholder="Name" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <input type="email" name="email" required placeholder="Email" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <input name="subject" required placeholder="Subject" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
        <textarea name="message" required rows="6" placeholder="Message" class="w-full rounded-2xl border px-4 py-3 dark:bg-slate-900"></textarea>
        <button class="sw-btn">Send</button>
    </form>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
