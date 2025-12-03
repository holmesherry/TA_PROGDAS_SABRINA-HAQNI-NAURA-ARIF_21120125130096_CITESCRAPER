<?php
require_once __DIR__ . '/core/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_all'])) {
        clear_history();
    } elseif (isset($_POST['del'])) {
        delete_history_item($_POST['del']);
    }
}

$history = load_history();
?>

<div class="page-wrapper history-page">
    <header class="page-header">
        <h1 class="page-title">History</h1>
        <p class="page-subtext">
            This is where your created citations and converted citations goes.
        </p>
    </header>

    <?php if (empty($history)): ?>
        <div class="card card-empty">
            No history yet.
        </div>

        <!-- Clear All di bawah "No history yet" -->
        <form method="post" class="history-actions">
            <button type="submit" name="clear_all" class="btn danger">
                Clear All
            </button>
        </form>

    <?php else: ?>

        <div class="history-summary">
            <p class="page-subtext small">
                <strong>Latest:</strong> <?php echo e(end($history)['citation']); ?>
            </p>
            <p class="page-subtext small">
                <strong>Oldest:</strong> <?php echo e(reset($history)['citation']); ?>
            </p>
        </div>

        <div class="history-list">
    <?php foreach (array_reverse($history) as $row): ?>
        <form method="post" class="card history-item">
            <div class="history-main">
                <div class="history-text"><?php echo e($row['citation']); ?></div>
                <div class="history-meta">
                    <?php echo strtoupper(e($row['style'])); ?>
                    &nbsp;•&nbsp;<?php echo date('Y-m-d', $row['ts']); ?>
                    &nbsp;•&nbsp;<?php echo ucfirst(e($row['meta']['source'] ?? '')); ?>
                </div>
            </div>
            <div class="history-actions-right">
                <div class="copy-hint">Select text to copy manually.</div>
                <button type="submit"
                        name="del"
                        value="<?php echo e($row['id']); ?>"
                        class="btn danger small">
                    Delete
                </button>
            </div>
        </form>
    <?php endforeach; ?>
</div>

        <!-- SATU Clear All di paling bawah list -->
        <form method="post" class="history-actions">
            <button type="submit" name="clear_all" class="btn danger">
                Clear All
            </button>
        </form>

    <?php endif; ?>
</div>
