<?php
// index.php – shell utama, NO JS

session_start();
require_once __DIR__ . '/core/functions.php';

// ----------------------
// Page routing
// ----------------------
$allowedPages = ['home', 'citationtools', 'create', 'convert', 'history', 'bookmarks', 'styles'];
$page = isset($_GET['page']) ? strtolower($_GET['page']) : 'home';
if (!in_array($page, $allowedPages, true)) {
    $page = 'home';
}

// ----------------------
// Theme (light / dark) via cookie
// ----------------------
if (isset($_GET['theme'])) {
    $t = strtolower($_GET['theme']) === 'dark' ? 'dark' : 'light';
    setcookie('site_theme', $t, time() + 60 * 60 * 24 * 30, '/');
    $_COOKIE['site_theme'] = $t;
}
$theme = (isset($_COOKIE['site_theme']) && $_COOKIE['site_theme'] === 'dark') ? 'dark' : 'light';

// ----------------------
// Sidebar collapse (0 = expanded, 1 = collapsed)
// ----------------------
if (isset($_GET['collapse'])) {
    $c = ($_GET['collapse'] === '1') ? '1' : '0';
    setcookie('sidebar_collapsed', $c, time() + 60 * 60 * 24 * 30, '/');
    $_COOKIE['sidebar_collapsed'] = $c;
}
$collapsed = (isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] === '1');

// ----------------------
// Menu (submenu) state
// ----------------------
$menu = isset($_GET['menu']) ? $_GET['menu'] : null;
if (!in_array($menu, ['tools', 'themes'], true)) {
    $menu = null;
}

// auto open tools submenu saat di citationtools / create / convert, kecuali kalau collapsed
if (!$collapsed && in_array($page, ['citationtools', 'create', 'convert'], true)) {
    $menu = 'tools';
}

// kalau collapsed, semua submenu tertutup
if ($collapsed) {
    $menu = null;
}

// ----------------------
// Helper untuk build link
// ----------------------
function mklink(array $overrides = []): string
{
    $params = [];

    // base: page sekarang
    $params['page'] = isset($overrides['page'])
        ? $overrides['page']
        : (isset($_GET['page']) ? $_GET['page'] : 'home');

    // pertahankan menu
    if (array_key_exists('menu', $overrides)) {
        if ($overrides['menu'] === null) {
            // explicit clear: jangan bawa menu dari URL lama
            // (artinya submenu ditutup)
        } else {
            $params['menu'] = $overrides['menu'];
        }
    } elseif (isset($_GET['menu'])) {
        $params['menu'] = $_GET['menu'];
    }


    // pertahankan theme
    if (isset($overrides['theme'])) {
        $params['theme'] = $overrides['theme'];
    } elseif (isset($_GET['theme'])) {
        $params['theme'] = $_GET['theme'];
    }

    // pertahankan collapse
    if (isset($overrides['collapse'])) {
        $params['collapse'] = $overrides['collapse'];
    } elseif (isset($_GET['collapse'])) {
        $params['collapse'] = $_GET['collapse'];
    }

    $qs = http_build_query($params);
    return 'index.php' . ($qs ? '?' . $qs : '');
}

// ----------------------
// Helper icon (home, tools, history, theme, bookmark, create, convert)
// ----------------------
function icon_src(string $name, bool $active, string $theme): string
{
    if ($active) {
        return "assets/icons/{$name}-active.png";
    }
    $variant = $theme === 'dark' ? 'dark' : 'light';
    return "assets/icons/{$variant}/{$name}-{$variant}.png";
}

// status aktif tiap menu
$isHome       = ($page === 'home');
$isToolsMain  = in_array($page, ['citationtools', 'create', 'convert'], true);
$isStyles     = ($page === 'styles');
$isHistory    = ($page === 'history');
$isBookmarks  = ($page === 'bookmarks');
$isCreate     = ($page === 'create');
$isConvert    = ($page === 'convert');
$toolsOpen    = ($menu === 'tools') && !$collapsed;
$themesOpen   = ($menu === 'themes') && !$collapsed;

// logo home (judul besar di tengah page) – dipakai di home & citation tools
$logoPath = $theme === 'dark'
    ? 'assets/img/logo-dark.png'
    : 'assets/img/logo-light.png';

$lightActive = ($theme === 'light');
$darkActive  = ($theme === 'dark');

$lightIconPath = 'assets/icons/themesun-' . ($lightActive ? 'active' : 'light') . '.png';
$darkIconPath  = 'assets/icons/thememoon-' . ($darkActive ? 'active' : 'dark') . '.png';

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CiteScraper</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="style.css">
</head>
<body class="theme-<?php echo e($theme); ?>">

<div class="app-shell">

    <!-- SIDEBAR -->
    <aside class="sidebar <?php echo $collapsed ? 'is-collapsed' : ''; ?>">
        <div class="sidebar-inner">

            <!-- Collapse button (ikon hamburger / bars pojok kiri bawah di PDF) -->
            <button class="collapse-toggle"
                    type="button"
                    onclick="window.location.href='<?php echo mklink(['collapse' => $collapsed ? '0' : '1']); ?>';">
                <span class="collapse-bar"></span>
                <span class="collapse-bar"></span>
                <span class="collapse-bar"></span>
            </button>

            <!-- NAV RAIL -->
            <nav class="nav-main">

                <!-- HOME -->
                <a href="<?php echo mklink(['page' => 'home', 'menu' => null]); ?>"
                   class="nav-item <?php echo $isHome ? 'is-active' : ''; ?>">
                    <img class="nav-icon"
                         src="<?php echo e(icon_src('home', $isHome, $theme)); ?>"
                         alt="Home">
                    <span class="nav-label">Home</span>
                </a>

                <!-- CITATE TOOLS (PAGE BARU) -->
                <div class="nav-group <?php echo $toolsOpen ? 'is-open' : ''; ?>">
                    <a href="<?php echo mklink([
                        'page' => 'citationtools',
                        'menu' => $toolsOpen ? null : 'tools'
                    ]); ?>"
                       class="nav-item <?php echo $isToolsMain ? 'is-active' : ''; ?>">
                        <img class="nav-icon"
                             src="<?php echo e(icon_src('tools', $isToolsMain, $theme)); ?>"
                             alt="Citate Tools">
                        <span class="nav-label">Citate Tools</span>
                        <span class="nav-chevron"><?php echo $toolsOpen ? '▾' : '▸'; ?></span>
                    </a>

                    <?php if (!$collapsed): ?>
                        <div class="submenu <?php echo $toolsOpen ? 'is-open' : ''; ?>">
                            <a href="<?php echo mklink(['page' => 'create', 'menu' => 'tools']); ?>"
                               class="submenu-item <?php echo $isCreate ? 'is-active' : ''; ?>">
                                <img class="submenu-icon"
                                     src="<?php echo e(icon_src('create', $isCreate, $theme)); ?>"
                                     alt="">
                                <span>Create Citation</span>
                            </a>
                            <a href="<?php echo mklink(['page' => 'convert', 'menu' => 'tools']); ?>"
                               class="submenu-item <?php echo $isConvert ? 'is-active' : ''; ?>">
                                <img class="submenu-icon"
                                     src="<?php echo e(icon_src('convert', $isConvert, $theme)); ?>"
                                     alt="">
                                <span>Convert Citation</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- CITATION STYLES – page baru -->
                <a href="<?php echo mklink(['page' => 'styles', 'menu' => null]); ?>"
                   class="nav-item <?php echo $isStyles ? 'is-active' : ''; ?>">
                    <img class="nav-icon"
                         src="<?php echo e(icon_src('info', $isStyles, $theme)); ?>"
                         alt="Citation Styles">
                    <span class="nav-label">About</span>
                </a>

                <!-- HISTORY -->
                <a href="<?php echo mklink(['page' => 'h    istory', 'menu' => null]); ?>"
                   class="nav-item <?php echo $isHistory ? 'is-active' : ''; ?>">
                    <img class="nav-icon"
                         src="<?php echo e(icon_src('history', $isHistory, $theme)); ?>"
                         alt="History">
                    <span class="nav-label">History</span>
                </a>

                <!-- THEMES -->
                <div class="nav-group <?php echo $themesOpen ? 'is-open' : ''; ?>">
                    <a href="<?php echo mklink(['menu' => $themesOpen ? null : 'themes']); ?>"
                       class="nav-item <?php echo $themesOpen ? 'is-active' : ''; ?>">
                        <img class="nav-icon"
                             src="<?php echo e(icon_src('theme', $themesOpen, $theme)); ?>"
                             alt="Themes">
                        <span class="nav-label">Themes</span>
                        <span class="nav-chevron"><?php echo $themesOpen ? '▾' : '▸'; ?></span>
                    </a>

                    <?php if (!$collapsed): ?>
                        <div class="submenu <?php echo $themesOpen ? 'is-open' : ''; ?>">
                            <a href="<?php echo mklink(['theme' => 'light', 'menu' => 'themes']); ?>"
                               class="submenu-item <?php echo $theme === 'light' ? 'is-active' : ''; ?>">
                                <span class="submenu-dot"></span>
                                <span>Light</span>
                            </a>
                            <a href="<?php echo mklink(['theme' => 'dark', 'menu' => 'themes']); ?>"
                               class="submenu-item <?php echo $theme === 'dark' ? 'is-active' : ''; ?>">
                                <span class="submenu-dot"></span>
                                <span>Dark</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- BOOKMARKS -->
                <a href="<?php echo mklink(['page' => 'bookmarks', 'menu' => null]); ?>"
                   class="nav-item <?php echo $isBookmarks ? 'is-active' : ''; ?>">
                    <img class="nav-icon"
                         src="<?php echo e(icon_src('bookmark', $isBookmarks, $theme)); ?>"
                         alt="Bookmarks">
                    <span class="nav-label">Bookmarks</span>
                </a>

            </nav>
        </div>
    </aside>

        <!-- MAIN CONTENT -->
    <main class="content content-<?php echo e($page); ?>" id="main-content">
        <?php
        // variabel yang bisa dipakai di page: $theme, $logoPath
        include __DIR__ . '/' . $page . '.php';
        ?>
    </main>


</div>

</body>
</html>
