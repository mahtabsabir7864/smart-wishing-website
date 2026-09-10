<?php
declare(strict_types=1);
if (!function_exists('url')) {
    require_once __DIR__ . '/includes/bootstrap.php';
}
if (!headers_sent()) {
    http_response_code(403);
}
$pageMeta = ['title' => 'Access denied | Smart Wishing', 'description' => 'You do not have permission to view this page.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-xl px-4 py-24 text-center">
    <p class="text-sm uppercase tracking-[0.3em] text-amber-500">403</p>
    <h1 class="mt-3 font-serif text-4xl">This greeting is not available.</h1>
    <p class="mt-4 text-slate-500">You may need to sign in, or this wish is still a private draft.</p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a class="sw-btn" href="<?= e(url()) ?>">Go Home</a>
        <a class="rounded-full border px-6 py-3" href="<?= e(url('login.php')) ?>">Login</a>
    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
