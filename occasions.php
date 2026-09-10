<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$occasions = db()->query('SELECT * FROM occasions WHERE status = "active" ORDER BY id')->fetchAll();
$pageMeta = ['title' => 'Occasions | Smart Wishing', 'description' => 'Choose from twenty premium greeting occasions including Birthday, Eid, Ramadan and more.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="font-serif text-4xl">Occasions</h1>
    <p class="mt-2 text-slate-500">Every occasion has its own colors, animation, and suggested messages.</p>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($occasions as $occ): $v = occasion_visual($occ['slug']); ?>
            <a href="<?= e(url('create-wish.php?occasion=' . $occ['slug'])) ?>" class="rounded-3xl border border-slate-100 p-6 transition hover:-translate-y-1 dark:border-slate-800">
                <i class="fa-solid <?= e($occ['icon']) ?> text-2xl text-fuchsia-500"></i>
                <h2 class="mt-3 font-serif text-2xl"><?= e($occ['name']) ?></h2>
                <p class="mt-2 text-sm text-slate-500"><?= e($occ['description']) ?></p>
                <p class="mt-4 text-xs uppercase tracking-widest text-amber-600"><?= e($v['default_animation']) ?> animation</p>
            </a>
        <?php endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
