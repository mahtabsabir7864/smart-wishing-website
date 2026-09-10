<?php
declare(strict_types=1);
if (!function_exists('url')) {
    require_once __DIR__ . '/includes/bootstrap.php';
}
if (!headers_sent()) {
    http_response_code(404);
}
$pageMeta = ['title' => 'Wish not found | Smart Wishing', 'description' => 'This page could not be found.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-xl px-4 py-24 text-center">
    <p class="text-sm uppercase tracking-[0.3em] text-fuchsia-500">404</p>
    <h1 class="mt-3 font-serif text-4xl">Oops! This wish could not be found.</h1>
    <p class="mt-4 text-slate-500">The link may be incorrect, expired, or the greeting was removed.</p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a class="sw-btn" href="<?= e(url()) ?>">Go Home</a>
        <a class="rounded-full border px-6 py-3" href="<?= e(url('create-wish.php')) ?>">Create New Wish</a>
    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
