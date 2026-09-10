<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$q = trim((string) ($_GET['q'] ?? ''));
$occasionId = (int) ($_GET['occasion'] ?? 0);
$theme = (string) ($_GET['theme'] ?? '');
$sort = (string) ($_GET['sort'] ?? 'newest');
$page = (int) ($_GET['page'] ?? 1);

$sql = 'SELECT t.*, o.name AS occasion_name, o.slug AS occasion_slug FROM templates t JOIN occasions o ON o.id = t.occasion_id WHERE t.status = "active"';
$params = [];
if ($q !== '') {
    $sql .= ' AND (t.name LIKE ? OR o.name LIKE ? OR t.theme LIKE ?)';
    $like = '%' . $q . '%';
    $params = array_merge($params, [$like, $like, $like]);
}
if ($occasionId) {
    $sql .= ' AND t.occasion_id = ?';
    $params[] = $occasionId;
}
if ($theme !== '' && in_array($theme, themes(), true)) {
    $sql .= ' AND t.theme = ?';
    $params[] = $theme;
}
$countSql = preg_replace('/SELECT t\.\*, o\.name AS occasion_name, o\.slug AS occasion_slug/', 'SELECT COUNT(*)', $sql, 1);
$stmt = db()->prepare($countSql);
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();
$pg = paginate($total, $page, 12);
$sql .= $sort === 'popular' ? ' ORDER BY t.id ASC' : ' ORDER BY t.id DESC';
$sql .= ' LIMIT ' . (int) $pg['per_page'] . ' OFFSET ' . (int) $pg['offset'];
$stmt = db()->prepare($sql);
$stmt->execute($params);
$templates = $stmt->fetchAll();
$occasions = db()->query('SELECT id, name FROM occasions WHERE status = "active" ORDER BY name')->fetchAll();

$pageMeta = ['title' => 'Templates | Smart Wishing', 'description' => 'Browse premium greeting templates for birthdays, Eid, weddings and more.'];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="font-serif text-4xl">Templates</h1>
    <form class="mt-6 grid gap-3 rounded-3xl border border-slate-100 p-4 md:grid-cols-5 dark:border-slate-800">
        <input name="q" value="<?= e($q) ?>" placeholder="Search Birthday, Eid, Wedding..." class="rounded-2xl border px-4 py-3 md:col-span-2 dark:bg-slate-900">
        <select name="occasion" class="rounded-2xl border px-4 py-3 dark:bg-slate-900">
            <option value="">All occasions</option>
            <?php foreach ($occasions as $o): ?>
                <option value="<?= (int)$o['id'] ?>" <?= $occasionId === (int)$o['id'] ? 'selected' : '' ?>><?= e($o['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="theme" class="rounded-2xl border px-4 py-3 dark:bg-slate-900">
            <option value="">All themes</option>
            <?php foreach (themes() as $th): ?>
                <option <?= $theme === $th ? 'selected' : '' ?>><?= e($th) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="sort" class="rounded-2xl border px-4 py-3 dark:bg-slate-900">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
            <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Popular</option>
        </select>
        <button class="sw-btn md:col-span-5">Search</button>
    </form>
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($templates as $t): ?>
            <article class="overflow-hidden rounded-3xl border border-slate-100 dark:border-slate-800">
                <div class="theme-<?= e($t['theme']) ?> h-36"></div>
                <div class="p-5">
                    <p class="text-xs uppercase tracking-widest text-fuchsia-500"><?= e($t['occasion_name']) ?> · <?= e($t['theme']) ?></p>
                    <h2 class="mt-1 font-serif text-2xl"><?= e($t['name']) ?></h2>
                    <a class="sw-btn mt-4 inline-flex text-sm" href="<?= e(url('create-wish.php?template=' . $t['id'] . '&occasion=' . $t['occasion_slug'])) ?>">Use Template</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if ($pg['pages'] > 1): ?>
        <div class="mt-8 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $pg['pages']; $i++): ?>
                <a class="rounded-full px-3 py-1 <?= $i === $pg['page'] ? 'bg-fuchsia-600 text-white' : 'border' ?>" href="?page=<?= $i ?>&q=<?= urlencode($q) ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
