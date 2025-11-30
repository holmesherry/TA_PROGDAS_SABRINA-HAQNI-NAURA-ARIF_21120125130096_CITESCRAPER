<?php
// ===============================
//  core/functions.php (FINAL)
//  Citation Formatter + History
// ===============================

// Lokasi file history
define('HISTORY_FILE', __DIR__ . '/../data/history.json');

// MODUL 1: Variabel, Tipe Data, dan Array
// Array berisi style yang didukung aplikasi
$CITE_STYLES = [
    'ieee'     => 'IEEE',
    'apa'      => 'APA',
    'mla'      => 'MLA',
    'chicago'  => 'Chicago',
    'harvard'  => 'Harvard',
    'ama'      => 'AMA',
    'cse'      => 'CSE',
    'bluebook' => 'Bluebook',
    'mendeley' => 'Mendeley',
];

/**
 * MODUL 4: Function
 * Mengambil daftar style citation sebagai array (bisa dipakai di form select)
 *
 * @return array<string,string>
 */
function get_available_styles(): array
{
    // pakai keyword global -> jelas kalau ini variabel global
    global $CITE_STYLES;
    return $CITE_STYLES;
}

// =====================================
// SANITIZER
// =====================================
function e($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}


// =====================================
//  HISTORY FUNCTIONS
// =====================================

// Load history (JSON)
function load_history(): array
{
    if (!file_exists(HISTORY_FILE)) {

        // pastikan folder data ada
        if (!is_dir(dirname(HISTORY_FILE))) {
            mkdir(dirname(HISTORY_FILE), 0755, true);
        }

        file_put_contents(HISTORY_FILE, json_encode([]));
    }

    $txt = file_get_contents(HISTORY_FILE);
    $arr = json_decode($txt, true);

    return is_array($arr) ? $arr : [];
}

// Save history ke file
function save_history(array $items): void
{
    file_put_contents(HISTORY_FILE, json_encode(array_values($items), JSON_PRETTY_PRINT));
}

// Tambah entry ke history
function add_history_item(string $style, string $citation, array $meta = []): void
{
    $items = load_history();

    $items[] = [
        'id'       => uniqid('h_', true),
        'style'    => strtolower($style),
        'citation' => $citation,
        'meta'     => $meta,
        'ts'       => time(),
    ];

    save_history($items);
}

// Hapus 1 item
function delete_history_item(string $id): void
{
    $items = load_history();
    $items = array_values(array_filter($items, fn($item) => $item['id'] !== $id));
    save_history($items);
}

// Clear full history
function clear_history(): void
{
    save_history([]);
}


// =====================================
// VALIDATOR
// =====================================
function is_intish(string $s): bool
{
    return (bool)preg_match('/^\d+$/', trim($s));
}


// =====================================
// CITATION FORMATTER
// =====================================

function format_cit(
    string $style,
    string $author,
    string $title,
    string $journal,
    string $volume,
    string $number,
    string $pages,
    string $year,
    string $url = ''   // NEW: optional URL
): string {

    $style = strtolower(trim($style));

    return match ($style) {
        'apa'      => format_apa($author, $title, $journal, $volume, $number, $pages, $year, $url),
        'mla'      => format_mla($author, $title, $journal, $volume, $number, $pages, $year, $url),
        'chicago'  => format_chicago($author, $title, $journal, $volume, $number, $pages, $year, $url),
        'harvard'  => format_harvard($author, $title, $journal, $volume, $number, $pages, $year, $url),
        'ama'      => format_ama($author, $title, $journal, $volume, $number, $pages, $year, $url),
        'cse'      => format_cse($author, $title, $journal, $volume, $number, $pages, $year, $url),
        'bluebook' => format_bluebook($author, $title, $journal, $volume, $number, $pages, $year, $url),
        'mendeley' => format_mendeley($author, $title, $journal, $volume, $number, $pages, $year, $url),
        default    => format_ieee($author, $title, $journal, $volume, $number, $pages, $year, $url),
    };
}


// =====================================
// HELPER (untuk APA) – tetap sama
// =====================================

function normalize_author_initials(string $author): string
{
    $author = trim($author);
    if ($author === '') return '';

    $parts = preg_split('/\s+/', $author);
    $last = array_pop($parts);

    $initials = '';
    foreach ($parts as $p) {
        if ($p !== '') {
            $initials .= strtoupper($p[0]) . '. ';
        }
    }

    return trim($last . ', ' . $initials);
}


// =====================================
// CITATION STYLES
// =====================================

// ---------- IEEE ----------
function format_ieee($a, $t, $j, $v, $n, $p, $y, $url = ''): string
{
    $out = [];

    if ($a !== '') $out[] = $a;
    if ($t !== '') $out[] = '"' . $t . '"';
    if ($j !== '') $out[] = $j;

    $volIssue = [];
    if ($v !== '') $volIssue[] = "vol. $v";
    if ($n !== '') $volIssue[] = "no. $n";
    if ($volIssue) $out[] = implode(', ', $volIssue);

    if ($p !== '') $out[] = "pp. $p";
    if ($y !== '') $out[] = $y;

    $citation = implode(', ', $out) . '.';

    if ($url !== '') {
        $citation .= ' [Online]. Available: ' . $url . '.';
    }

    return $citation;
}


// ---------- APA ----------
function format_apa($a, $t, $j, $v, $n, $p, $y, $url = ''): string
{
    $aa = normalize_author_initials($a);
    $out = '';

    if ($aa !== '') $out .= $aa;
    if ($y !== '')  $out .= ($out ? ' ' : '') . "($y).";
    if ($t !== '')  $out .= " $t.";
    if ($j !== '')  $out .= " $j";
    if ($v !== '')  $out .= ", $v";
    if ($n !== '')  $out .= "($n)";
    if ($p !== '')  $out .= ", $p";

    $citation = rtrim($out) . '.';

    if ($url !== '') {
        $citation .= ' Retrieved from ' . $url;
    }

    return $citation;
}


// ---------- MLA ----------
function format_mla($a, $t, $j, $v, $n, $p, $y, $url = ''): string
{
    $out = '';

    if ($a !== '') $out .= "$a. ";
    if ($t !== '') $out .= "\"$t.\" ";
    if ($j !== '') $out .= "$j ";
    if ($v !== '') $out .= $v;
    if ($n !== '') $out .= ".$n";
    if ($y !== '') $out .= " ($y)";
    if ($p !== '') $out .= ": $p";

    $citation = rtrim($out) . '.';

    if ($url !== '') {
        $citation .= ' ' . $url . '.';
    }

    return $citation;
}


// ---------- Chicago ----------
function format_chicago($a, $t, $j, $v, $n, $p, $y, $url = ''): string
{
    $out = '';

    if ($a !== '') $out .= "$a. ";
    if ($y !== '') $out .= "($y). ";
    if ($t !== '') $out .= "\"$t.\" ";
    if ($j !== '') $out .= "$j ";
    if ($v !== '') $out .= $v;
    if ($n !== '') $out .= ", no. $n";
    if ($p !== '') $out .= ": $p";

    $citation = rtrim($out) . '.';

    if ($url !== '') {
        $citation .= ' Available at ' . $url . '.';
    }

    return $citation;
}


// ---------- Harvard ----------
function format_harvard($a, $t, $j, $v, $n, $p, $y, $url = ''): string
{
    $out = '';

    if ($a !== '') $out .= $a;
    if ($y !== '') $out .= " ($y)";
    if ($t !== '') $out .= " $t.";
    if ($j !== '') $out .= " $j";
    if ($v !== '') $out .= ", $v";
    if ($n !== '') $out .= "($n)";
    if ($p !== '') $out .= ", pp. $p";

    $citation = rtrim($out) . '.';

    if ($url !== '') {
        $citation .= ' Available at: ' . $url . '.';
    }

    return $citation;
}


// ---------- AMA ----------
function format_ama($a, $t, $j, $v, $n, $p, $y, $url = ''): string
{
    $out = '';

    if ($a !== '') $out .= "$a. ";
    if ($t !== '') $out .= "$t. ";
    if ($j !== '') $out .= "$j. ";
    if ($y !== '') $out .= $y;
    if ($v !== '') $out .= ";$v";
    if ($n !== '') $out .= "($n)";
    if ($p !== '') $out .= ":$p";

    $citation = rtrim($out) . '.';

    if ($url !== '') {
        $citation .= ' Available at: ' . $url . '.';
    }

    return $citation;
}


// ---------- CSE ----------
function format_cse($a, $t, $j, $v, $n, $p, $y, $url = ''): string
{
    $out = '';

    if ($a !== '') $out .= "$a. ";
    if ($y !== '') $out .= "$y. ";
    if ($t !== '') $out .= "$t. ";
    if ($j !== '') $out .= $j;
    if ($v !== '') $out .= ". $v";
    if ($n !== '') $out .= "($n)";
    if ($p !== '') $out .= ":$p";

    $citation = rtrim($out) . '.';

    if ($url !== '') {
        $citation .= ' Available from: ' . $url . '.';
    }

    return $citation;
}


// ---------- Bluebook ----------
function format_bluebook($a, $t, $j, $v, $n, $p, $y, $url = ''): string
{
    $out = '';

    if ($a !== '') $out .= "$a, ";
    if ($t !== '') $out .= "\"$t\", ";
    if ($v !== '') $out .= "$v ";
    if ($j !== '') $out .= "$j ";
    if ($p !== '') $out .= "$p ";
    if ($y !== '') $out .= "($y)";

    $citation = rtrim($out) . '.';

    if ($url !== '') {
        $citation .= ' available at ' . $url . '.';
    }

    return $citation;
}


// ---------- Mendeley-ish ----------
function format_mendeley($a, $t, $j, $v, $n, $p, $y, $url = ''): string
{
    $out = '';

    if ($a !== '') $out .= "$a. ";
    if ($y !== '') $out .= "$y. ";
    if ($t !== '') $out .= "$t. ";
    if ($j !== '') $out .= "$j. ";
    if ($v !== '') {
        $out .= $v;
        if ($n !== '') $out .= "($n)";
        $out .= ". ";
    }
    if ($p !== '') $out .= "$p.";

    $citation = trim($out);

    if ($url !== '') {
        $citation .= ' Available at: ' . $url . '.';
    }

    return $citation;
}

// =====================================
// MODUL 5 & 6:
//  - Object Oriented Programming
//  - Struktur Data Stack & Queue
// =====================================

/**
 * Class Citation
 * Representasi 1 citation (OOP)
 */
class Citation
{
    private string $style;
    private string $text;
    private int $createdAt;

    public function __construct(string $style, string $text, ?int $createdAt = null)
    {
        $this->style     = $style;
        $this->text      = $text;
        $this->createdAt = $createdAt ?? time();
    }

    // MODUL 4: method (function di dalam class)
    public function getStyle(): string
    {
        return $this->style;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'style'    => $this->style,
            'citation' => $this->text,
            'ts'       => $this->createdAt,
        ];
    }
}

/**
 * Class CitationManager
 * Menyimpan citation dalam bentuk stack & queue
 */
class CitationManager
{
    /** @var Citation[] */
    private array $stack = [];   // MODUL 6: struktur data stack (LIFO)
    /** @var Citation[] */
    private array $queue = [];   // MODUL 6: struktur data queue (FIFO)

    /**
     * @param array<int,array<string,mixed>> $historyItems
     */
    public function __construct(array $historyItems = [])
    {
        // MODUL 3: Perulangan (foreach)
        foreach ($historyItems as $item) {
            $citation = new Citation(
                (string)($item['style'] ?? 'ieee'),
                (string)($item['citation'] ?? ''),
                (int)   ($item['ts'] ?? time())
            );

            // isi kedua struktur sekaligus
            $this->pushToStack($citation);
            $this->enqueue($citation);
        }
    }

    // ---- Operasi Stack ----
    public function pushToStack(Citation $citation): void
    {
        $this->stack[] = $citation;
    }

    public function popFromStack(): ?Citation
    {
        if (empty($this->stack)) {
            return null;
        }
        return array_pop($this->stack);
    }

    public function getLatestFromStack(): ?Citation
    {
        if (empty($this->stack)) {
            return null;
        }
        return $this->stack[count($this->stack) - 1];
    }

    // ---- Operasi Queue ----
    public function enqueue(Citation $citation): void
    {
        $this->queue[] = $citation;
    }

    public function dequeue(): ?Citation
    {
        if (empty($this->queue)) {
            return null;
        }
        return array_shift($this->queue);
    }

    public function getNextFromQueue(): ?Citation
    {
        if (empty($this->queue)) {
            return null;
        }
        return $this->queue[0];
    }
}
