<?php
// convert.php – Convert Citation
require_once __DIR__ . '/core/functions.php';

$raw      = $_POST['raw']      ?? '';
$current  = $_POST['current']  ?? 'ieee';
$target   = $_POST['target']   ?? 'apa';
$error    = '';
$converted= '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['convert'])) {
    $raw = trim($raw);

    if ($raw === '') {
        $error = 'Please enter the citation you want to convert.';
    } elseif (strpos($raw, '|') === false) {
        $error = 'Use structured format: author|title|journal|volume|number|pages|year';
    } else {
        $parts = explode('|', $raw);
        $parts = array_map('trim', $parts);
        $parts = array_pad($parts, 7, '');
        list($a,$t,$j,$v,$n,$p,$y) = $parts;

        if ($y !== '' && !is_intish($y)) {
            $error = 'Year must be numeric in the structured input.';
        } else {
            $converted = format_cit($target, $a,$t,$j,$v,$n,$p,$y);
            add_history_item($target, $converted, [
                'raw'     => $raw,
                'from'    => $current,
                'to'      => $target,
                'source'  => 'convert',
            ]);
        }
    }
}
?>

<div class="page-wrapper">
    <header class="page-header">
        <h1 class="page-title">Convert Citation</h1>
        <p class="page-subtext">
            Paste a structured citation and convert it into another style.
        </p>
        <p class="page-subtext small">
            Format: <code>author|title|journal|volume|number|pages|year</code>
        </p>
    </header>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <div class="form-row form-row-full">
            <label for="raw">Citation to convert</label>
            <textarea id="raw" name="raw" rows="4" class="textarea"><?php echo e($raw); ?></textarea>
        </div>

        <div class="form-row">
            <label for="current">Current Style (info only)</label>
            <select id="current" name="current">
                <option value="ieee"     <?php echo $current === 'ieee' ? 'selected' : ''; ?>>IEEE (Institute of Electrical and Electronics Engineers)</option>
                <option value="apa"      <?php echo $current === 'apa' ? 'selected' : ''; ?>>APA (American Psychological Association)</option>
                <option value="mla"      <?php echo $current === 'mla' ? 'selected' : ''; ?>>MLA (Modern Language Association)</option>
                <option value="chicago"  <?php echo $current === 'chicago' ? 'selected' : ''; ?>>Chicago</option>
                <option value="harvard"  <?php echo $current === 'harvard' ? 'selected' : ''; ?>>Harvard</option>
                <option value="ama"      <?php echo $current === 'ama' ? 'selected' : ''; ?>>AMA (American Medical Associaton)</option>
                <option value="cse"      <?php echo $current === 'cse' ? 'selected' : ''; ?>>CSE (Council Science Editors)</option>
                <option value="bluebook" <?php echo $current === 'bluebook' ? 'selected' : ''; ?>>Bluebook</option>
            </select>
        </div>

        <div class="form-row">
            <label for="target">Target Style</label>
            <select id="target" name="target">
                <option value="ieee"     <?php echo $target === 'ieee' ? 'selected' : ''; ?>>IEEE (Institute of Electrical and Electronics Engineers)</option>
                <option value="apa"      <?php echo $target === 'apa' ? 'selected' : ''; ?>>APA (American Psychological Association)</option>
                <option value="mla"      <?php echo $target === 'mla' ? 'selected' : ''; ?>>MLA (Modern Language Association)</option>
                <option value="chicago"  <?php echo $target === 'chicago' ? 'selected' : ''; ?>>Chicago</option>
                <option value="harvard"  <?php echo $target === 'harvard' ? 'selected' : ''; ?>>Harvard</option>
                <option value="ama"      <?php echo $target === 'ama' ? 'selected' : ''; ?>>AMA (American Medical Association)</option>
                <option value="cse"      <?php echo $target === 'cse' ? 'selected' : ''; ?>>CSE (Council of Science Editors)</option>
                <option value="bluebook" <?php echo $target === 'bluebook' ? 'selected' : ''; ?>>Bluebook</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary" name="convert">Convert</button>
        </div>
    </form>

    <?php if ($converted): ?>
        <section class="result-card">
            <h2 class="result-title">Converted Citation (<?php echo strtoupper(e($target)); ?>)</h2>
            <p class="result-helper">
                Select and copy manually using <strong>Ctrl+C</strong> / <strong>Cmd+C</strong>.
            </p>
            <textarea readonly rows="4" class="result-textarea"><?php echo e($converted); ?></textarea>
        </section>
    <?php endif; ?>
</div>
