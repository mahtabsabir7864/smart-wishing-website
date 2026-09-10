<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$pageMeta = ['title' => 'About Smart Wishing', 'description' => 'Smart Wishing is a digital greeting platform that lets you create personalized wishes for the people who matter to you.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-3xl px-4 py-16">
    <p class="text-sm uppercase tracking-[0.3em] text-fuchsia-500">Our story</p>
    <h1 class="mt-3 font-serif text-4xl">What is Smart Wishing?</h1>
    <p class="mt-6 text-lg leading-8 text-slate-600 dark:text-slate-300">Smart Wishing is a digital greeting platform that lets you create personalized wishes for the people who matter to you.</p>
    <h2 class="mt-12 font-serif text-3xl">Mission</h2>
    <p class="mt-3 leading-7 text-slate-600 dark:text-slate-300">We believe a message should feel like a gift. Smart Wishing turns simple words into cinematic, shareable greeting pages — for birthdays, Eid, weddings, duas, and everyday kindness.</p>
    <h2 class="mt-12 font-serif text-3xl">Features</h2>
    <ul class="mt-4 list-disc space-y-2 pl-6 text-slate-600 dark:text-slate-300">
        <li>Twenty occasion collections with unique visuals and animations</li>
        <li>Message library by relationship and tone</li>
        <li>Photo uploads, themes, layouts, music, countdown and secret wishes</li>
        <li>Share links, QR codes, and image downloads</li>
        <li>User dashboards and a secure admin studio</li>
    </ul>
    <h2 class="mt-12 font-serif text-3xl">Benefits</h2>
    <p class="mt-3 leading-7 text-slate-600 dark:text-slate-300">No design skills required. Create something beautiful in seconds, keep it private if you wish, and send it anywhere with a single link.</p>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
