<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$pageMeta = ['title' => 'Privacy Policy | Smart Wishing', 'description' => 'How Smart Wishing handles accounts, uploaded photos, cookies and user-generated greetings.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-3xl px-4 py-16 leading-7 text-slate-600 dark:text-slate-300">
    <h1 class="font-serif text-4xl text-slate-900 dark:text-white">Privacy Policy</h1>
    <p class="mt-6">Smart Wishing stores the information needed to create accounts, greetings, and support requests. We do not sell personal data.</p>
    <h2 class="mt-8 font-serif text-2xl text-slate-900 dark:text-white">User-generated content</h2>
    <p class="mt-2">Messages, names, and greetings you publish are stored so recipients can open them via a unique link. You are responsible for the content you create. Reported wishes may be reviewed and removed.</p>
    <h2 class="mt-8 font-serif text-2xl text-slate-900 dark:text-white">Uploaded photos</h2>
    <p class="mt-2">Photos are stored on the server for display inside your greeting. Upload only images you have the right to share. Do not upload sensitive documents. File type and size are validated.</p>
    <h2 class="mt-8 font-serif text-2xl text-slate-900 dark:text-white">Cookies</h2>
    <p class="mt-2">We use a session cookie to keep you signed in and to protect forms with CSRF tokens. Theme preference is stored in your browser via localStorage.</p>
    <h2 class="mt-8 font-serif text-2xl text-slate-900 dark:text-white">Data storage</h2>
    <p class="mt-2">Account emails, hashed passwords, wish content, view timestamps, and basic user-agent strings for wish analytics are stored in MySQL. View logs do not collect unnecessary personal information.</p>
    <h2 class="mt-8 font-serif text-2xl text-slate-900 dark:text-white">Account security</h2>
    <p class="mt-2">Passwords are hashed with PHP’s password_hash. Secret wish passwords are also hashed. Keep your password private and sign out on shared devices.</p>
    <h2 class="mt-8 font-serif text-2xl text-slate-900 dark:text-white">Content moderation</h2>
    <p class="mt-2">Administrators may remove greetings that are abusive, illegal, or reported by the community.</p>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
