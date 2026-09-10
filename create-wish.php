<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/wish-service.php';

$occasions = db()->query('SELECT * FROM occasions WHERE status = "active" ORDER BY id')->fetchAll();
$preSlug = sanitize_slug((string) ($_GET['occasion'] ?? ''));
$preTemplate = (int) ($_GET['template'] ?? 0);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid session. Please try again.';
    } else {
        try {
            $data = wish_payload_from_request();
            $photos = [];
            if (!empty($_FILES['photos']) && is_array($_FILES['photos'])) {
                $photos = save_uploaded_images($_FILES['photos']);
            }
            $user = current_user();
            $wish = persist_wish($data, $photos, $user['id'] ?? null);
            flash_set('success', 'Wish Created Successfully 🎉');
            redirect('wish.php?code=' . urlencode($wish['unique_code']) . '&new=1');
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$pageMeta = [
    'title' => 'Create a Wish | Smart Wishing',
    'description' => 'Create a personalized digital greeting card in a few elegant steps.',
];
$extraScripts = [asset('js/wish-generator.js'), asset('js/download.js')];
require __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-6xl px-4 py-10">
    <div class="mb-8 text-center">
        <p class="text-sm uppercase tracking-[0.3em] text-fuchsia-500">Wish studio</p>
        <h1 class="mt-2 font-serif text-4xl">Create a beautiful wish</h1>
        <p class="mt-2 text-slate-500">Eight thoughtful steps. One unforgettable greeting.</p>
    </div>
    <?php if ($error): ?>
        <div class="mb-6 rounded-2xl bg-rose-50 p-4 text-rose-700"><?= e($error) ?></div>
    <?php endif; ?>
    <div class="mb-8 flex flex-wrap justify-center gap-2">
        <?php for ($i = 1; $i <= 8; $i++): ?>
            <button type="button" class="step-dot grid h-9 w-9 place-items-center rounded-full bg-slate-200 text-sm font-bold <?= $i === 1 ? 'active' : '' ?>" data-goto="<?= $i ?>"><?= $i ?></button>
        <?php endfor; ?>
    </div>
    <form id="wishWizard" method="post" enctype="multipart/form-data" class="glass-card rounded-[2rem] p-5 sm:p-8" data-preselect="<?= e($preSlug) ?>" data-template="<?= e((string) $preTemplate) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="occasion_id" value="">
        <input type="hidden" name="template_id" value="<?= e((string) $preTemplate) ?>">
        <input type="hidden" name="theme" value="Elegant">
        <input type="hidden" name="layout" value="classic">
        <input type="hidden" name="heading" value="A Special Message For You">
        <input type="hidden" name="animation" value="confetti">
        <textarea name="message" class="hidden"></textarea>

        <section data-step="1">
            <h2 class="mb-4 font-serif text-2xl">Choose an occasion</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                <?php foreach ($occasions as $occ): $v = occasion_visual($occ['slug']); ?>
                    <button type="button" class="occasion-card rounded-2xl border border-slate-200 bg-white p-4 text-left transition hover:-translate-y-1 dark:border-slate-700 dark:bg-slate-900" data-id="<?= (int)$occ['id'] ?>" data-slug="<?= e($occ['slug']) ?>" data-heading="<?= e($v['heading']) ?>" data-animation="<?= e($v['default_animation']) ?>">
                        <i class="fa-solid <?= e($occ['icon']) ?> mb-2 text-xl text-fuchsia-500"></i>
                        <div class="font-semibold"><?= e($occ['name']) ?></div>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="mt-6 flex justify-end"><button type="button" data-next class="sw-btn">Next</button></div>
        </section>

        <section data-step="2" class="hidden space-y-4">
            <h2 class="font-serif text-2xl">Recipient information</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-sm">Recipient Name<input required name="recipient_name" class="mt-1 w-full rounded-2xl border border-slate-200 bg-transparent px-4 py-3" placeholder="Muhammad"></label>
                <label class="block text-sm">Sender Name<input required name="sender_name" class="mt-1 w-full rounded-2xl border border-slate-200 bg-transparent px-4 py-3" placeholder="Mahtab"></label>
                <label class="block text-sm">Nickname<input name="nickname" class="mt-1 w-full rounded-2xl border border-slate-200 bg-transparent px-4 py-3"></label>
                <label class="block text-sm">Relationship
                    <select name="relationship" class="mt-1 w-full rounded-2xl border border-slate-200 bg-transparent px-4 py-3">
                        <?php foreach (relationships() as $r): ?><option><?= e($r) ?></option><?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div class="flex justify-between"><button type="button" data-prev class="rounded-full px-5 py-2">Back</button><button type="button" data-next class="sw-btn">Next</button></div>
        </section>

        <section data-step="3" class="hidden space-y-4">
            <h2 class="font-serif text-2xl">Choose a message</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm">Tone
                    <select name="tone" class="mt-1 w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
                        <?php foreach (tones() as $t): ?><option><?= e($t) ?></option><?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div id="messageList" class="grid gap-3 md:grid-cols-2"></div>
            <p class="text-sm text-slate-500">Or write your own. Formatting is sanitized for safety.</p>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="rounded-lg border px-3 py-1" data-cmd="bold"><b>B</b></button>
                <button type="button" class="rounded-lg border px-3 py-1" data-cmd="italic"><i>I</i></button>
                <button type="button" class="rounded-lg border px-3 py-1" data-cmd="align" data-val="Left">Left</button>
                <button type="button" class="rounded-lg border px-3 py-1" data-cmd="align" data-val="Center">Center</button>
                <button type="button" class="rounded-lg border px-3 py-1" data-cmd="align" data-val="Right">Right</button>
                <button type="button" class="rounded-lg border px-3 py-1" data-cmd="size" data-val="3">A</button>
                <button type="button" class="rounded-lg border px-3 py-1" data-cmd="size" data-val="5">A+</button>
            </div>
            <div id="editor" class="min-h-[160px] rounded-2xl border border-slate-200 p-4 dark:border-slate-700" contenteditable="true"></div>
            <div class="flex justify-between"><button type="button" data-prev class="rounded-full px-5 py-2">Back</button><button type="button" data-next class="sw-btn">Next</button></div>
        </section>

        <section data-step="4" class="hidden space-y-6">
            <h2 class="font-serif text-2xl">Theme & layout</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                <?php foreach (themes() as $th): ?>
                    <button type="button" class="theme-pick theme-<?= e($th) ?> h-24 rounded-2xl border p-3 text-sm font-semibold" data-theme="<?= e($th) ?>"><?= e($th) ?></button>
                <?php endforeach; ?>
            </div>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
                <?php foreach (layouts_list() as $key => $label): ?>
                    <button type="button" class="layout-pick rounded-2xl border p-3 text-sm" data-layout="<?= e($key) ?>"><?= e($label) ?></button>
                <?php endforeach; ?>
            </div>
            <div class="flex justify-between"><button type="button" data-prev class="rounded-full px-5 py-2">Back</button><button type="button" data-next class="sw-btn">Next</button></div>
        </section>

        <section data-step="5" class="hidden space-y-4">
            <h2 class="font-serif text-2xl">Photos</h2>
            <p class="text-sm text-slate-500">Optional. JPG, PNG or WEBP. Max 3MB each. You may select multiple photos.</p>
            <input type="file" name="photos[]" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple class="w-full rounded-2xl border p-4">
            <div class="flex justify-between"><button type="button" data-prev class="rounded-full px-5 py-2">Back</button><button type="button" data-next class="sw-btn">Next</button></div>
        </section>

        <section data-step="6" class="hidden space-y-4">
            <h2 class="font-serif text-2xl">Animation</h2>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
                <?php foreach (animations_list() as $an): ?>
                    <label class="rounded-2xl border p-4 text-sm"><input type="radio" name="animation_choice" value="<?= e($an) ?>" onchange="this.form.animation.value=this.value" <?= $an === 'confetti' ? 'checked' : '' ?>> <?= e(ucfirst($an)) ?></label>
                <?php endforeach; ?>
            </div>
            <div class="flex justify-between"><button type="button" data-prev class="rounded-full px-5 py-2">Back</button><button type="button" data-next class="sw-btn">Next</button></div>
        </section>

        <section data-step="7" class="hidden space-y-4">
            <h2 class="font-serif text-2xl">Music & extras</h2>
            <p class="text-sm text-slate-500">Music never autoplays. The recipient can start it after opening the wish.</p>
            <label class="block text-sm">Background music
                <select name="music" class="mt-1 w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
                    <?php foreach (music_presets() as $k => $label): ?><option value="<?= e($k) ?>"><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm">Visibility
                    <select name="visibility" class="mt-1 w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
                        <option value="published">Published</option>
                        <option value="private">Private</option>
                        <option value="draft">Draft</option>
                    </select>
                </label>
                <label class="text-sm">Expiration
                    <select name="expiration" class="mt-1 w-full rounded-2xl border px-4 py-3 dark:bg-slate-900">
                        <option value="none">No expiration</option>
                        <option value="1">1 day</option>
                        <option value="7">7 days</option>
                        <option value="30">30 days</option>
                        <option value="custom">Custom date</option>
                    </select>
                </label>
                <label class="text-sm">Custom expiry date<input type="datetime-local" name="expires_at" class="mt-1 w-full rounded-2xl border px-4 py-3"></label>
                <label class="text-sm">Countdown date (optional)<input type="datetime-local" name="event_at" class="mt-1 w-full rounded-2xl border px-4 py-3"></label>
                <label class="text-sm">Personalized URL
                    <div class="mt-1 flex items-center gap-2 rounded-2xl border px-4 py-3">
                        <span class="text-xs text-slate-400">/wish/</span>
                        <input name="custom_slug" class="w-full bg-transparent" placeholder="mahtab-birthday">
                    </div>
                </label>
                <label class="text-sm">Secret password (optional)<input type="password" name="secret_password" class="mt-1 w-full rounded-2xl border px-4 py-3" autocomplete="new-password"></label>
            </div>
            <div class="flex justify-between"><button type="button" data-prev class="rounded-full px-5 py-2">Back</button><button type="button" data-next class="sw-btn">Preview</button></div>
        </section>

        <section data-step="8" class="hidden space-y-4">
            <h2 class="font-serif text-2xl">Preview</h2>
            <div id="livePreview" class="min-h-[320px] rounded-3xl p-8"></div>
            <div class="flex flex-wrap gap-3">
                <button type="button" data-prev class="rounded-full border px-5 py-2">Edit</button>
                <button type="submit" class="sw-btn">Generate Wish</button>
            </div>
        </section>
    </form>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
