<?php
// history.php
require_once __DIR__ . '/core/functions.php';

// MODUL 2: Pengkondisian (cek method & aksi)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_all'])) {
        clear_history();
    } elseif (isset($_POST['del'])) {
        delete_history_item($_POST['del']);
    }
}

// MODUL 1: Array berisi riwayat citation
$history = load_history();

// MODUL 5 & 6: OOP + Struktur Data Stack & Queue
// CitationManager dibangun dari data history (array of array)
$historyManager  = new CitationManager($history);
$latestFromStack = $historyManager->getLatestFromStack(); // citation paling baru (stack / LIFO)
$nextFromQueue   = $historyManager->getNextFromQueue();   // citation paling awal (queue / FIFO)

// MODUL 3: Perulangan (nanti dipakai di foreach di bagian HTML)
$displayHistory = array_reverse($history); // supaya yang terbaru tampil di atas
?>

<div class="page-wrapper">
    <header class="page-header">
        <h1 class="page-title">History</h1>
        <p class="page-subtext">
            This is where your created citations and converted citations goes.
        </p>
    </header>

    <!-- MODUL 5 & 6: Tampilkan contoh penggunaan Stack & Queue di GUI (MODUL 7) -->
    <section class="history-extra">
        <?php if ($latestFromStack): // MODUL 2: Pengkondisian ?>
            <p class="history-extra-item">
                <strong>Latest:</strong>
                <?php echo e($latestFromStack->getText()); ?>
            </p>
        <?php endif; ?>

        <?php if ($nextFromQueue): ?>
            <p class="history-extra-item">
                <strong>Oldest:</strong>
                <?php echo e($nextFromQueue->getText()); ?>
            </p>
        <?php endif; ?>
    </section>

    <?php if (empty($history)): // MODUL 2: Pengkondisian ?>
        <div class="card card-empty">
            No history yet.
        </div>
    <?php else: ?>
        <div class="history-list">
            <?php foreach ($displayHistory as $row): // MODUL 3: Perulangan ?>
                <div class="card history-item">
                    <div class="history-main">
                        <div class="history-text"><?php echo e($row['citation']); ?></div>
                        <div class="history-meta">
                            <?php echo strtoupper(e($row['style'])); ?>
                            &nbsp;•&nbsp;
                            <?php echo date('Y-m-d H:i', $row['ts']); ?>
                            <?php if (!empty($row['meta']['source'])): ?>
                                &nbsp;•&nbsp;<?php echo ucfirst(e($row['meta']['source'])); ?>
                            <?php endif; ?>

        <form method="post" class="history-actions">
        <button type="submit" name="clear_all" class="btn danger">
            Clear All
        </button>
    </form>
                        </div>
                    </div>
                    <div class="history-actions-right">
                        <div class="copy-hint">Select text to copy manually.</div>
                        <button type="submit" name="del" value="<?php echo e($row['id']); ?>" class="btn danger small">
                            Delete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>