<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/wish-card.php';

$code = preg_replace('/[^A-Za-z0-9]/', '', (string) ($_GET['code'] ?? $_GET['id'] ?? ''));
$slug = sanitize_slug((string) ($_GET['slug'] ?? ''));

if ($code !== '') {
    $stmt = db()->prepare('SELECT w.*, o.name AS occasion_name, o.slug AS occasion_slug, o.icon FROM wishes w JOIN occasions o ON o.id = w.occasion_id WHERE w.unique_code = ? LIMIT 1');
    $stmt->execute([$code]);
} elseif ($slug !== '') {
    $stmt = db()->prepare('SELECT w.*, o.name AS occasion_name, o.slug AS occasion_slug, o.icon FROM wishes w JOIN occasions o ON o.id = w.occasion_id WHERE w.custom_slug = ? LIMIT 1');
    $stmt->execute([$slug]);
} else {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}
$wish = $stmt->fetch();
if (!$wish || $wish['status'] === 'removed') {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$user = current_user();
$isOwner = $user && (int) $user['id'] === (int) ($wish['user_id'] ?? 0);
if ($wish['status'] === 'draft' && !$isOwner && !current_admin()) {
    http_response_code(403);
    require __DIR__ . '/403.php';
    exit;
}

if (is_expired($wish['expires_at'])) {
    $pageMeta = ['title' => 'This wish has expired | Smart Wishing', 'description' => 'This greeting is no longer available.'];
    require __DIR__ . '/includes/header.php';
    echo '<main class="mx-auto max-w-xl px-4 py-24 text-center"><h1 class="font-serif text-4xl">This wish has expired.</h1><p class="mt-4 text-slate-500">The creator set a time limit for this greeting.</p><a class="sw-btn mt-8 inline-flex" href="'.e(url('create-wish.php')).'">Create New Wish</a></main>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$unlocked = empty($wish['secret_password']);
if (!$unlocked && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf() && password_verify((string) ($_POST['wish_password'] ?? ''), $wish['secret_password'])) {
        sw_session_start();
        $_SESSION['wish_unlock'][$wish['id']] = true;
        $unlocked = true;
    } else {
        $passError = 'Incorrect password.';
    }
}
if (!empty($_SESSION['wish_unlock'][$wish['id']])) {
    $unlocked = true;
}

if ($unlocked && empty($_GET['new']) && $wish['status'] !== 'draft') {
    $ua = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
    db()->prepare('INSERT INTO wish_views (wish_id, user_agent) VALUES (?,?)')->execute([$wish['id'], $ua]);
    db()->prepare('UPDATE wishes SET views = views + 1 WHERE id = ?')->execute([$wish['id']]);
    $wish['views']++;
}

$mediaStmt = db()->prepare('SELECT * FROM wish_media WHERE wish_id = ?');
$mediaStmt->execute([$wish['id']]);
$media = $mediaStmt->fetchAll();
$occasion = ['slug' => $wish['occasion_slug'], 'name' => $wish['occasion_name']];
$visual = occasion_visual($wish['occasion_slug']);
$publicUrl = wish_public_url($wish);
$isNew = !empty($_GET['new']);

$pageMeta = [
    'title' => $wish['heading'] . ', ' . $wish['recipient_name'] . ' | Smart Wishing',
    'description' => 'A special digital wish created with Smart Wishing.',
];
$hideChrome = true;
$bodyClass = 'wish-stage';
$extraScripts = [asset('js/download.js')];
require __DIR__ . '/includes/header.php';
?>
<canvas id="fx" class="wish-canvas"></canvas>
<style>body{background:<?= $visual['bg'] ?>!important;}</style>
<main class="relative z-10 mx-auto flex min-h-screen max-w-5xl flex-col items-center justify-center px-4 py-10">
    <?php if (!$unlocked): ?>
        <form method="post" class="glass-card w-full max-w-md rounded-3xl p-8 text-center">
            <?= csrf_field() ?>
            <h1 class="font-serif text-3xl">This wish is protected</h1>
            <p class="mt-2 text-sm opacity-80">Enter the password shared with you.</p>
            <?php if (!empty($passError)): ?><p class="mt-3 text-rose-300"><?= e($passError) ?></p><?php endif; ?>
            <input type="password" name="wish_password" class="mt-6 w-full rounded-2xl border bg-white/10 px-4 py-3" required>
            <button class="sw-btn mt-4 w-full">Open Wish</button>
        </form>
    <?php else: ?>
        <div id="closedGate" class="<?= $isNew ? 'hidden' : '' ?> text-center">
            <button id="openWish" class="sw-btn text-lg">Open Wish 💌</button>
        </div>
        <div id="wishContent" class="<?= $isNew ? '' : 'hidden' ?> w-full">
            <?php if (!empty($wish['event_at'])): ?>
                <div id="countdown" class="mb-6 grid grid-cols-4 gap-2 text-center text-white" data-at="<?= e($wish['event_at']) ?>"></div>
            <?php endif; ?>
            <?php render_wish_card($wish, $occasion, $media, true); ?>
            <div class="no-capture no-print mt-8 flex flex-wrap justify-center gap-3 text-white">
                <button id="makeWish" class="rounded-full bg-white/15 px-5 py-2">Make a Wish ✨</button>
                <button id="musicBtn" class="rounded-full bg-white/15 px-5 py-2" data-preset="<?= e($wish['music']) ?>">Music</button>
                <input id="vol" type="range" min="0" max="0.2" step="0.01" value="0.08" class="w-24">
                <button onclick="downloadGreeting('#greetingCard','smart-wish','png')" class="rounded-full bg-white/15 px-5 py-2">Download PNG</button>
                <button onclick="downloadGreeting('#greetingCard','smart-wish','jpg')" class="rounded-full bg-white/15 px-5 py-2">Download JPG</button>
                <a class="rounded-full bg-white/15 px-5 py-2" href="<?= e(url('create-wish.php')) ?>">Create another</a>
            </div>
            <div class="no-capture mx-auto mt-8 max-w-lg rounded-3xl bg-black/20 p-6 text-center text-white">
                <p class="mb-3 font-semibold">Share this wish</p>
                <div class="flex flex-wrap justify-center gap-2 text-sm">
                    <a class="rounded-full bg-green-500 px-4 py-2" target="_blank" rel="noopener" href="https://wa.me/?text=<?= urlencode($publicUrl) ?>">WhatsApp</a>
                    <a class="rounded-full bg-blue-600 px-4 py-2" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($publicUrl) ?>">Facebook</a>
                    <a class="rounded-full bg-slate-800 px-4 py-2" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?= urlencode($publicUrl) ?>">X</a>
                    <a class="rounded-full bg-sky-500 px-4 py-2" target="_blank" rel="noopener" href="https://t.me/share/url?url=<?= urlencode($publicUrl) ?>">Telegram</a>
                    <button id="copyLink" class="rounded-full bg-white/20 px-4 py-2" data-url="<?= e($publicUrl) ?>">Copy Link</button>
                    <button id="webShare" class="rounded-full bg-white/20 px-4 py-2">Share</button>
                </div>
                <p class="mt-5 text-sm opacity-80">Scan to Open Wish</p>
                <div id="qr" class="mx-auto mt-3 inline-block rounded-xl bg-white p-3"></div>
                <button id="dlQr" class="mt-3 text-sm underline">Download QR Code</button>
                <?php if ($isOwner || current_user()): ?>
                <form method="post" action="<?= e(url('api/report.php')) ?>" class="mt-6 text-left text-sm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="code" value="<?= e($wish['unique_code']) ?>">
                    <button class="opacity-70">Report this wish</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</main>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const openBtn = document.getElementById('openWish');
  const gate = document.getElementById('closedGate');
  const content = document.getElementById('wishContent');
  const fx = document.getElementById('fx');
  let anim = null;
  const startFx = () => {
    if (!fx || anim) return;
    anim = new WishAnimations(fx, <?= json_encode($wish['animation']) ?>);
    anim.start();
  };
  const reveal = () => {
    if (gate) gate.classList.add('hidden');
    if (content) content.classList.remove('hidden');
    startFx();
  };
  if (openBtn) openBtn.addEventListener('click', reveal);
  <?php if ($isNew): ?>startFx();<?php endif; ?>
  document.getElementById('makeWish')?.addEventListener('click', () => anim && anim.burst());
  const musicBtn = document.getElementById('musicBtn');
  musicBtn?.addEventListener('click', () => {
    const preset = musicBtn.dataset.preset;
    if (WishMusic.playing) { WishMusic.stop(); musicBtn.textContent = 'Music'; }
    else { WishMusic.play(preset); musicBtn.textContent = 'Pause'; }
  });
  document.getElementById('vol')?.addEventListener('input', (e) => WishMusic.setVolume(e.target.value));
  const url = <?= json_encode($publicUrl) ?>;
  document.getElementById('copyLink')?.addEventListener('click', async () => {
    await navigator.clipboard.writeText(url);
    Swal.fire({ icon:'success', title:'Your wish has been copied!' });
  });
  document.getElementById('webShare')?.addEventListener('click', async () => {
    if (navigator.share) await navigator.share({ title: document.title, url });
    else navigator.clipboard.writeText(url);
  });
  const qrBox = document.getElementById('qr');
  if (qrBox && window.QRCode) {
    new QRCode(qrBox, { text: url, width: 160, height: 160 });
  }
  document.getElementById('dlQr')?.addEventListener('click', () => {
    const img = qrBox.querySelector('img, canvas');
    if (!img) return;
    const a = document.createElement('a');
    a.download = 'wish-qr.png';
    a.href = img.src || img.toDataURL();
    a.click();
  });
  const cd = document.getElementById('countdown');
  if (cd) {
    const target = new Date(cd.dataset.at).getTime();
    const tick = () => {
      const d = Math.max(0, target - Date.now());
      const days = Math.floor(d/86400000), h = Math.floor(d/3600000)%24, m = Math.floor(d/60000)%60, s = Math.floor(d/1000)%60;
      cd.innerHTML = [days+'d', h+'h', m+'m', s+'s'].map(v => `<div class="rounded-2xl bg-white/15 p-3 text-xl font-bold">${v}</div>`).join('');
    };
    tick(); setInterval(tick, 1000);
  }
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
