<?php
declare(strict_types=1);
if (!function_exists('url')) {
    require_once __DIR__ . '/includes/bootstrap.php';
}
if (!headers_sent()) {
    http_response_code(500);
}
$pageMeta = ['title' => 'Something went wrong | Smart Wishing', 'description' => 'A server error occurred.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-xl px-4 py-24 text-center">
    <p class="text-sm uppercase tracking-[0.3em] text-rose-500">500</p>
    <h1 class="mt-3 font-serif text-4xl">Something went wrong on our side.</h1>
    <p class="mt-4 text-slate-500">Please try again in a moment.</p>
    <a class="sw-btn mt-8 inline-flex" href="<?= e(url()) ?>">Go Home</a>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
