<?php
// create.php – Create Citation form
require_once __DIR__ . '/core/functions.php';

$generated = '';
$error     = '';
$style     = $_POST['style'] ?? 'ieee';

$author  = $_POST['author']  ?? '';
$title   = $_POST['title']   ?? '';
$journal = $_POST['journal'] ?? '';
$volume  = $_POST['volume']  ?? '';
$number  = $_POST['number']  ?? '';
$pages   = $_POST['pages']   ?? '';
$year    = $_POST['year']    ?? '';
$url     = $_POST['url']     ?? '';   // NEW: URL online (optional)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $author  = trim($author);
    $title   = trim($title);
    $journal = trim($journal);
    $volume  = trim($volume);
    $number  = trim($number);
    $pages   = trim($pages);
    $year    = trim($year);
    $url     = trim($url);    // NEW
    $style   = trim($style);

    if ($author === '' || $title === '' || $journal === '' || $year === '') {
        $error = 'Author, Paper Title, Journal Title, and Year are required.';
    } elseif ($year !== '' && !is_intish($year)) {
        $error = 'Year must be numeric.';
    } elseif ($volume !== '' && !is_intish($volume)) {
        $error = 'Volume must be numeric.';
    } elseif ($number !== '' && !is_intish($number)) {
        $error = 'Number (issue) must be numeric.';
    } elseif ($pages !== '' && !is_intish($pages)) {
        $error = 'Pages must be numeric.';
    } else {
        // NEW: kirim $url ke format_cit
        $generated = format_cit(
            $style,
            $author,
            $title,
            $journal,
            $volume,
            $number,
            $pages,
            $year,
            $url
        );

        add_history_item($style, $generated, [
            'author'  => $author,
            'title'   => $title,
            'journal' => $journal,
            'volume'  => $volume,
            'number'  => $number,
            'pages'   => $pages,
            'year'    => $year,
            'url'     => $url,   // NEW: simpan url di meta
            'source'  => 'create',
        ]);
    }
}
?>

<div class="page-wrapper create-page">
    <header class="page-header">
        <h1 class="page-title">Create Citation</h1>
        <p class="page-subtext">
            Fill in the fields below, choose a style, and CiteScraper will generate your citation.
        </p>
    </header>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <div class="form-row">
            <label for="author">Author&rsquo;s Name</label>
            <input type="text" id="author" name="author" value="<?php echo e($author); ?>" required>
        </div>

        <div class="form-row">
            <label for="title">Paper Title</label>
            <input type="text" id="title" name="title" value="<?php echo e($title); ?>" required>
        </div>

        <div class="form-row">
            <label for="journal">Journal Title</label>
            <input type="text" id="journal" name="journal" value="<?php echo e($journal); ?>" required>
        </div>

        <div class="form-row">
            <label for="volume">Volume</label>
            <input type="text" id="volume" name="volume" value="<?php echo e($volume); ?>">
        </div>

        <div class="form-row">
            <label for="number">Number (Issue)</label>
            <input type="text" id="number" name="number" value="<?php echo e($number); ?>">
        </div>

        <div class="form-row">
            <label for="pages">Pages</label>
            <input type="text" id="pages" name="pages" value="<?php echo e($pages); ?>">
        </div>

        <div class="form-row">
            <label for="year">Year</label>
            <input type="text" id="year" name="year" value="<?php echo e($year); ?>" required>
        </div>

        <!-- NEW: URL online (optional) -->
        <div class="form-row">
            <label for="url">URL (optional)</label>
            <input
                type="text"
                id="url"
                name="url"
                placeholder="https://doi.org/... or article URL"
                value="<?php echo e($url); ?>">
        </div>

        <div class="form-row">
            <label for="style">Citation Style</label>
            <select id="style" name="style">
                <option value="ieee"     <?php echo $style === 'ieee' ? 'selected' : ''; ?>>IEEE (Institute of Electrical and Electronics Engineers)</option>
                <option value="apa"      <?php echo $style === 'apa' ? 'selected' : ''; ?>>APA (American Psychological Association)</option>
                <option value="mla"      <?php echo $style === 'mla' ? 'selected' : ''; ?>>MLA (Modern Language Association)</option>
                <option value="chicago"  <?php echo $style === 'chicago' ? 'selected' : ''; ?>>Chicago</option>
                <option value="harvard"  <?php echo $style === 'harvard' ? 'selected' : ''; ?>>Harvard</option>
                <option value="ama"      <?php echo $style === 'ama' ? 'selected' : ''; ?>>AMA (American Medical Association)</option>
                <option value="cse"      <?php echo $style === 'cse' ? 'selected' : ''; ?>>CSE (Council of Science Editors)</option>
                <option value="bluebook" <?php echo $style === 'bluebook' ? 'selected' : ''; ?>>Bluebook</option>
                <option value="mendeley" <?php echo $style === 'mendeley' ? 'selected' : ''; ?>>Mendeley</option>
            </select>
        </div>

        <?php if ($generated): ?>
            <section class="result-card form-row-full">
                <h2 class="result-title">Generated Citation (<?php echo strtoupper(e($style)); ?>)</h2>
                <p class="result-helper">
                    Select the text below and press <strong>Ctrl+C</strong> / <strong>Cmd+C</strong> to copy.
                </p>
                <textarea readonly rows="4" class="result-textarea"><?php echo e($generated); ?></textarea>
            </section>
        <?php endif; ?>

        <div class="form-actions form-row-full">
            <button type="submit" name="create" class="btn primary">
                Generate
            </button>
        </div>
    </form>
</div>