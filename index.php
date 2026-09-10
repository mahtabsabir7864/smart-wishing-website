<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$occasions = db()->query('SELECT * FROM occasions WHERE status = "active" ORDER BY id')->fetchAll();
$templates = db()->query('SELECT t.*, o.name AS occasion_name FROM templates t JOIN occasions o ON o.id = t.occasion_id WHERE t.status = "active" ORDER BY t.id LIMIT 8')->fetchAll();
$latest = db()->query('SELECT w.unique_code, w.recipient_name, w.heading, w.created_at, o.name AS occasion_name FROM wishes w JOIN occasions o ON o.id = w.occasion_id WHERE w.status = "published" ORDER BY w.id DESC LIMIT 6')->fetchAll();

$pageMeta = [
    'title' => 'Smart Wishing | Create Beautiful Wishes in Seconds',
    'description' => 'Turn your feelings into beautiful digital greeting cards that people will remember.',
];
require __DIR__ . '/includes/header.php';
?>
<section class="hero-glow relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="float-y absolute left-[8%] top-16 text-3xl">❤</div>
        <div class="float-y absolute right-[12%] top-24 text-2xl" style="animation-delay:1s">✦</div>
        <div class="float-y absolute left-[40%] top-10 text-xl" style="animation-delay:2s">✧</div>
        <div class="float-y absolute right-[30%] bottom-20 text-3xl" style="animation-delay:.5s">★</div>
    </div>
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 lg:grid-cols-2 lg:py-24">
        <div data-aos="fade-up">
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-fuchsia-600">Premium digital greetings</p>
            <h1 class="mt-4 font-serif text-4xl leading-tight sm:text-6xl">Create Beautiful Wishes in Seconds</h1>
            <p class="mt-5 max-w-xl text-lg text-slate-600 dark:text-slate-300">Turn your feelings into beautiful digital greeting cards that people will remember.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="<?= e(url('create-wish.php')) ?>" class="sw-btn">Create a Wish</a>
                <a href="<?= e(url('templates.php')) ?>" class="rounded-full border border-slate-300 px-6 py-3 font-semibold">Explore Templates</a>
            </div>
        </div>
        <div class="float-y" data-aos="zoom-in">
            <div class="theme-Celebration rounded-[2rem] p-8 text-center shadow-2xl">
                <p class="font-script text-5xl">Happy Birthday</p>
                <p class="mt-4 text-4xl font-extrabold tracking-[0.25em]">MUHAMMAD</p>
                <p class="mx-auto mt-6 max-w-sm text-sm leading-7">May your life always be filled with happiness, success, health and countless beautiful moments.</p>
                <p class="mt-8 text-xs uppercase tracking-[0.3em] opacity-80">With Best Wishes</p>
                <p class="mt-2 font-bold tracking-[0.3em]">MAHTAB</p>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16">
    <div class="mb-8 flex items-end justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.3em] text-fuchsia-500">Occasions</p>
            <h2 class="font-serif text-3xl">Popular occasions</h2>
        </div>
        <a href="<?= e(url('occasions.php')) ?>" class="text-sm font-semibold">View all</a>
    </div>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-5">
        <?php foreach (array_slice($occasions, 0, 10) as $i => $occ): ?>
            <a href="<?= e(url('create-wish.php?occasion=' . $occ['slug'])) ?>" class="glass-card rounded-3xl p-5 transition hover:-translate-y-1" data-aos="fade-up" data-aos-delay="<?= $i * 40 ?>">
                <i class="fa-solid <?= e($occ['icon']) ?> text-2xl text-fuchsia-500"></i>
                <div class="mt-3 font-semibold"><?= e($occ['name']) ?></div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="bg-white py-16 dark:bg-slate-950">
    <div class="mx-auto max-w-7xl px-4">
        <h2 class="text-center font-serif text-3xl">How it works</h2>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            <?php
            $steps = [
                ['01', 'Choose an Occasion', 'Birthday, Eid, wedding, or a custom wish — start with the moment that matters.'],
                ['02', 'Personalize Your Wish', 'Add names, a heartfelt message, photos, themes, and animation.'],
                ['03', 'Share the Moment', 'Send a beautiful link, QR code, or downloadable card in seconds.'],
            ];
            foreach ($steps as $s): ?>
                <div class="rounded-3xl border border-slate-100 p-8 dark:border-slate-800" data-aos="fade-up">
                    <div class="text-3xl font-serif text-fuchsia-500"><?= $s[0] ?></div>
                    <h3 class="mt-3 text-xl font-semibold"><?= $s[1] ?></h3>
                    <p class="mt-2 text-slate-500"><?= $s[2] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16">
    <h2 class="font-serif text-3xl">Featured templates</h2>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($templates as $t): ?>
            <article class="overflow-hidden rounded-3xl border border-slate-100 dark:border-slate-800" data-aos="fade-up">
                <div class="theme-<?= e($t['theme']) ?> h-32"></div>
                <div class="p-5">
                    <p class="text-xs uppercase tracking-widest text-fuchsia-500"><?= e($t['occasion_name']) ?></p>
                    <h3 class="mt-1 font-semibold"><?= e($t['name']) ?></h3>
                    <a class="mt-4 inline-flex text-sm font-semibold text-fuchsia-600" href="<?= e(url('create-wish.php?template=' . $t['id'] . '&occasion=' . urlencode($t['occasion_name']))) ?>">Use Template</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="bg-gradient-to-br from-fuchsia-600 to-violet-800 py-16 text-white">
    <div class="mx-auto max-w-7xl px-4">
        <h2 class="font-serif text-3xl">Why Smart Wishing</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <div><i class="fa-solid fa-wand-magic-sparkles mb-3 text-2xl"></i><h3 class="font-semibold">Designed to feel premium</h3><p class="mt-2 text-white/80">Glass cards, cinematic animation, and elegant typography.</p></div>
            <div><i class="fa-solid fa-share-nodes mb-3 text-2xl"></i><h3 class="font-semibold">Share anywhere</h3><p class="mt-2 text-white/80">WhatsApp, QR codes, unique links, and downloadable images.</p></div>
            <div><i class="fa-solid fa-shield-heart mb-3 text-2xl"></i><h3 class="font-semibold">Private when you need it</h3><p class="mt-2 text-white/80">Password protection, expiration, and drafts for full control.</p></div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16">
    <h2 class="font-serif text-3xl">Latest wishes</h2>
    <div class="mt-8 grid gap-4 md:grid-cols-3">
        <?php foreach ($latest as $w): ?>
            <a href="<?= e(url('w/' . $w['unique_code'])) ?>" class="rounded-3xl border border-slate-100 p-6 dark:border-slate-800">
                <p class="text-xs uppercase tracking-widest text-fuchsia-500"><?= e($w['occasion_name']) ?></p>
                <h3 class="mt-2 font-serif text-2xl"><?= e($w['heading'] ?: 'A special wish') ?></h3>
                <p class="mt-1 text-slate-500">For <?= e($w['recipient_name']) ?></p>
            </a>
        <?php endforeach; ?>
        <?php if (!$latest): ?>
            <p class="text-slate-500">Be the first to publish a wish.</p>
        <?php endif; ?>
    </div>
</section>

<section class="bg-white py-16 dark:bg-slate-950">
    <div class="mx-auto max-w-7xl px-4">
        <h2 class="font-serif text-3xl">Loved by thoughtful senders</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <?php
            $quotes = [
                ['Amina', 'The Eid card I made for my parents made them cry happy tears.'],
                ['Hassan', 'I sent a birthday wish with fireworks. It felt more personal than a text.'],
                ['Sara', 'Beautiful, fast, and so easy to share on WhatsApp.'],
            ];
            foreach ($quotes as $q): ?>
                <blockquote class="rounded-3xl border border-slate-100 p-6 dark:border-slate-800">
                    <p class="text-slate-600 dark:text-slate-300">“<?= e($q[1]) ?>”</p>
                    <footer class="mt-4 font-semibold">— <?= e($q[0]) ?></footer>
                </blockquote>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 text-center">
    <h2 class="font-serif text-4xl">Ready to make someone smile?</h2>
    <p class="mt-3 text-slate-500">Create a personalized greeting in under a minute.</p>
    <a href="<?= e(url('create-wish.php')) ?>" class="sw-btn mt-6 inline-flex">Create a Wish</a>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
