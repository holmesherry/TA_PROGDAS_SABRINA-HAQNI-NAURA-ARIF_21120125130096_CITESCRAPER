<?php
// bookmarks.php
require_once __DIR__ . '/core/functions.php';

if (!isset($_SESSION['bookmarks'])) {
    $_SESSION['bookmarks'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_bm'])) {
        $txt = trim($_POST['bm_text'] ?? '');
        if ($txt !== '') {
            $_SESSION['bookmarks'][] = [
                'id'   => time() . rand(10,99),
                'text' => $txt,
                'ts'   => time(),
            ];
        }
    } elseif (isset($_POST['del_bm'])) {
        $id = $_POST['del_bm'];
        $_SESSION['bookmarks'] = array_values(array_filter(
            $_SESSION['bookmarks'],
            fn($b) => $b['id'] !== $id
        ));
    }
}
?>

<div class="page-wrapper bookmarks-page">
    <header class="page-header">
        <h1 class="page-title">Bookmarks</h1>
        <p class="page-subtext">
            This is where your saved citations goes.
        </p>
    </header>

    <form method="post" class="form-grid">
        <div class="form-row form-row-full">
            <label for="bm_text">Add a citation to bookmarks</label>
            <textarea id="bm_text" name="bm_text" rows="3" class="textarea"
                      placeholder="Paste or type a citation you want to save..."></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" name="add_bm" class="btn primary">Add</button>
        </div>
    </form>

    <?php if (empty($_SESSION['bookmarks'])): ?>
        <div class="card card-empty">
            No bookmarks yet.
        </div>
    <?php else: ?>
        <div class="bookmark-list">
            <?php foreach (array_reverse($_SESSION['bookmarks']) as $bm): ?>
                <div class="card bookmark-item">
                    <div class="bookmark-main">
                        <div class="bookmark-text"><?php echo e($bm['text']); ?></div>
                        <div class="bookmark-meta">
                            <?php echo date('Y-m-d H:i', $bm['ts']); ?>
                        </div>
                    </div>
                    <form method="post">
                        <button type="submit" name="del_bm"
                                value="<?php echo e($bm['id']); ?>"
                                class="btn danger small">
                            Delete
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>