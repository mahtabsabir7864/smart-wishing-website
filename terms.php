<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$pageMeta = ['title' => 'Terms & Conditions | Smart Wishing', 'description' => 'Terms of use for Smart Wishing accounts, greetings, and uploaded content.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-3xl px-4 py-16 leading-7 text-slate-600 dark:text-slate-300">
    <h1 class="font-serif text-4xl text-slate-900 dark:text-white">Terms &amp; Conditions</h1>
    <p class="mt-6">By using Smart Wishing you agree to create greetings respectfully and lawfully.</p>
    <h2 class="mt-8 font-serif text-2xl text-slate-900 dark:text-white">User responsibilities</h2>
    <p class="mt-2">Do not publish harassment, spam, impersonation, or content you do not have rights to. You remain responsible for messages and photos you upload.</p>
    <h2 class="mt-8 font-serif text-2xl text-slate-900 dark:text-white">Accounts</h2>
    <p class="mt-2">You must provide an accurate email and keep your password secure. We may suspend accounts that abuse the service.</p>
    <h2 class="mt-8 font-serif text-2xl text-slate-900 dark:text-white">Content moderation</h2>
    <p class="mt-2">We may unpublish or delete wishes that violate these terms or that are reported as inappropriate.</p>
    <h2 class="mt-8 font-serif text-2xl text-slate-900 dark:text-white">Availability</h2>
    <p class="mt-2">Greetings may expire if you set an expiration date. Links are unique; treat them like invitations.</p>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
