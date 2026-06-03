<?php
// Каталог оборудования — маршрутизатор
error_reporting(0);
ini_set('display_errors', 0);

// Определяем путь из URL
$uri = $_SERVER['REQUEST_URI'];
$uri = parse_url($uri, PHP_URL_PATH);
// Нормализация: если REQUEST_URI содержит index.php, берём только директорию
$uri = preg_replace('#/index\.php$#', '/', $uri);
$uri = trim($uri, '/');
$parts = explode('/', $uri);

// Все URL /catalog/... обрабатываются здесь
if ($parts[0] === 'catalog') {
    
    // ЦКТ: catalog/beer/cct/[volume]l/
    if (($parts[1] ?? '') === 'beer' && ($parts[2] ?? '') === 'cct') {
        require __DIR__ . '/cct-data.php';
        
        if (isset($parts[3]) && preg_match('/^(\d+)l?$/', $parts[3], $m)) {
            $volume = (int)$m[1];
            if (isset($cctData[$volume])) {
                renderCctPage($volume, $cctData[$volume], $cctData, $cctCategory);
                exit;
            }
        }
        // Если объём не указан или не найден — список
        renderCctList($cctData, $cctCategory);
        exit;
    }
    
    // Всё остальное (/catalog/, /catalog/beer/, /catalog/dairy/…) — главная каталога
    require __DIR__ . '/beer-extra-data.php';
    require __DIR__ . '/brew-house-data.php';
    require __DIR__ . '/cct-data.php';
    require __DIR__ . '/dairy-data.php';
    require __DIR__ . '/wine-data.php';
    require __DIR__ . '/industrial-data.php';
    renderMainCatalog();
    exit;
}

// 404 — только для URL не из /catalog/
http_response_code(404);
?><!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><title>404 — Страница не найдена</title>
<meta name="robots" content="noindex">
<style>body{font-family:sans-serif;text-align:center;padding:60px 20px;color:#333}h1{font-size:28px;color:#F77C2A}p{font-size:16px;color:#666}a{color:#F77C2A}</style>
</head>
<body>
<h1>404</h1>
<p>Страница не найдена</p>
<p><a href="/catalog/">Вернуться в каталог</a></p>
</body>
</html>
<?php
exit;

// ============================================================
function renderMainCatalog() {
    $metaTitle = 'Каталог оборудования из нержавеющей стали | ОБОРУДОВАНИЕ КУБАНИ';
    $metaDesc = 'Полный каталог промышленного оборудования из нержавеющей стали AISI 304/316: пивоваренное, молочное, винодельческое и пищевое промышленное оборудование.';

    $sections = [
        [
            'name' => 'Пивоваренное оборудование',
            'url' => '/catalog/beer/',
            'image' => '/cct-tank.jpg',
            'desc' => 'ЦКТ (100–50000 л), варочные порядки (6 типов), дробилки солода, баки горячей воды, парогенераторы, чиллеры, форфасы, теплообменники.',
            'count' => '13 категорий',
        ],
        [
            'name' => 'Молочное оборудование',
            'url' => '/catalog/dairy/',
            'image' => '/dairy-reception.jpg',
            'desc' => 'Ёмкости приёмки молока, резервуары-охладители, резервуары хранения, ванны длительной пастеризации, ферментационные танки, сыроизготовители, творогоизготовители, заквасочники, контейнеры для соления, стеллажи для созревания сыра.',
            'count' => '10 категорий',
        ],
        [
            'name' => 'Винодельческое оборудование',
            'url' => '/catalog/wine/',
            'image' => '/wine-red-fermentation.jpg',
            'desc' => 'Ферментация красных и белых вин, выдержка и хранение, холодная стабилизация, купажирование, сульфитация, универсальные танки.',
            'count' => '7 категорий',
        ],
        [
            'name' => 'Промышленное оборудование',
            'url' => '/catalog/industrial/',
            'image' => '/industrial-cip.jpg',
            'desc' => 'Резервуары для хранения, ёмкости с мешалкой, ёмкости с терморегуляцией, ёмкости под давлением, CIP-станции, теплообменники.',
            'count' => '6 категорий + опции',
        ],
    ];

    header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
<title><?= htmlspecialchars($metaTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
<link rel="canonical" href="https://ob-kub.ru/catalog/">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style-original.css">
<link rel="stylesheet" href="/css/catalog-mobile.css">
<link rel="stylesheet" href="/css/header.css">
<link rel="icon" href="/favicon.png">
<style>*,*::before,*::after{box-sizing:border-box}.catalog-page{font-family:'Source Sans Pro',sans-serif;color:#2c3e50;background:#f5f6f8}.catalog-page .container{max-width:1100px;margin:0 auto;padding:0 24px}.main-hero{background:linear-gradient(135deg,#2b2b39 0%,#1a1a26 100%);position:relative;overflow:hidden;padding:40px 0}.main-hero::before{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background:url(data:image/vg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+) repeat;pointer-events:none}.main-hero .hero-text{position:relative;z-index:1;text-align:center}.main-hero .hero-text h1{font-size:26px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;color:#fff;margin:0 0 6px}.main-hero .hero-text p{font-size:14px;color:rgba(255,255,255,.6);margin:0;max-width:650px;margin-left:auto;margin-right:auto}.main-hero .breadcrumbs{padding:0 0 10px;font-size:11px;color:rgba(255,255,255,.3)}.main-hero .breadcrumbs a{color:rgba(255,255,255,.45);text-decoration:none;transition:color .2s}.main-hero .breadcrumbs a:hover{color:#F77C2A}.main-hero .breadcrumbs .ep{margin:0 5px;color:rgba(255,255,255,.12)}.main-hero .breadcrumbs .current{color:rgba(255,255,255,.5)}.section-title{font-size:16px;font-weight:700;color:#1a1a26;margin:32px 0 16px;padding-bottom:8px;border-bottom:3px solid #F77C2A;text-transform:uppercase;letter-spacing:.4px}.cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:24px}.cat-card{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.06);transition:transform .2s,box-shadow .2s;text-decoration:none;color:inherit;display:flex;flex-direction:column;border:1px solid #eee;overflow:hidden}.cat-card:hover{transform:translateY(-6px);box-shadow:0 12px 36px rgba(247,124,42,.15);border-color:#fde0c0}.cat-card-img{width:100%;height:200px;overflow:hidden;background:#fff;display:flex;align-items:center;justify-content:center}.cat-card-img img{max-width:100%;max-height:100%;display:block}.cat-card-body{padding:20px 24px 20px;flex:1;display:flex;flex-direction:column;text-align:center}.cat-card-body .cat-name{font-size:18px;font-weight:700;color:#1a1a26;margin-bottom:8px;line-height:1.3}.cat-card-body .cat-desc{font-size:13px;color:#666;line-height:1.6;flex:1}.cat-card-body .cat-count{font-size:12px;color:#F77C2A;font-weight:600;margin-top:12px}.cat-card-footer{padding:0 24px 24px}.cat-card-footer .btn-view{display:block;width:100%;padding:14px;background:#F77C2A;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;text-align:center;transition:background .2s}.cat-card-footer .btn-view:hover{background:#e06a1a}.mega-menu-wrap{position:relative;display:inline-block}.nav .mega-menu-link{display:inline-flex;align-items:center;gap:4px}.nav .mega-menu-link:hover{color:#F77C2A}.mega-arrow{font-size:9px;transition:transform .25s;display:inline-block;margin-left:2px}.mega-menu-wrap:hover .mega-arrow,.mega-menu-wrap.active .mega-arrow{transform:rotate(180deg)}.mega-menu{position:absolute;top:100%;left:50%;transform:translateX(-50%) translateY(10px);background:#fff;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,.18),0 4px 16px rgba(0,0,0,.08);padding:24px;display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;min-width:820px;z-index:1000;opacity:0;visibility:hidden;transition:all .25s ease;margin-top:8px;border:1px solid rgba(0,0,0,.04)}.mega-menu::before{content:'';position:absolute;top:-8px;left:50%;transform:translateX(-50%);border:8px solid transparent;border-bottom-color:#fff}.mega-menu::after{content:'';position:absolute;top:0;left:24px;right:24px;height:3px;background:linear-gradient(90deg,#F77C2A,#FF8C42);border-radius:0 0 3px 3px}.mega-menu-wrap:hover .mega-menu,.mega-menu-wrap.active .mega-menu{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0)}.mega-col h3{font-size:11px;font-weight:700;color:#2b2b39;margin:0 0 10px;padding-bottom:7px;border-bottom:2px solid #F77C2A;text-transform:uppercase;letter-spacing:.4px}.mega-col a{display:block;padding:4px 0;font-size:13px;color:#555;text-decoration:none;transition:color .2s;line-height:1.4;white-space:normal}.mega-col a:hover{color:#F77C2A}.mega-col-link{display:block;text-decoration:none;color:inherit;padding:0;font-size:inherit;line-height:inherit}.mega-col-link:hover h3{color:#F77C2A}.catalog-page .header{overflow:visible !important}@media@media</style>
</head>
<body class="brewery-page catalog-page">
<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; ?>
<main>
<section class="main-hero">
<div class="container">
<div class="breadcrumbs">
<a href="/">Главная</a><span class="ep">/</span>
<span class="current">Каталог оборудования</span>
</div>
<div class="hero-text">
<h1>Каталог оборудования</h1>
<p><?= htmlspecialchars($metaDesc) ?></p>
</div>
</div>
</section>
<section class="container" 
style="padding-top:24px;padding-bottom:48px">
<div class="cat-grid">
<?php foreach ($sections as $s): ?>
<a href="<?= htmlspecialchars($s['url']) ?>" class="cat-card">
<div class="cat-card-img"><img 
src="<?= htmlspecialchars($s['image']) ?>" alt="<?= htmlspecialchars($s['name']) ?>"></div>
<div class="cat-card-body">
<div class="cat-name"><?= htmlspecialchars($s['name']) ?></div>
<div class="cat-desc"><?= htmlspecialchars($s['desc']) ?></div>
<div class="cat-count"><?= htmlspecialchars($s['count']) ?></div>
</div>
<div class="cat-card-footer"><span class="btn-view">Перейти к каталогу</span></div>
</a>
<?php endforeach; ?>
</div>
</section>
</main>
<?php require $_SERVER['DOCUMENT_ROOT'].'/php/footer.php'; ?>
<?php
}

// ============================================================
function renderCctPage($vol, $d, $allData, $cat) {
    $volStr = number_format($vol, 0, '.', ' ');
    $diameterM = number_format($d['diameter'] / 1000, 2, '.', '');
    $heightM  = number_format($d['height_full'] / 1000, 2, '.', '');
    $priceStr = fmtPrice($d['from_price']);
    
    $canonical = "https://ob-kub.ru/catalog/beer/cct/{$vol}l/";
    
    // Соседние объёмы для навигации
    $volumes = array_keys($allData);
    
sort($volumes);
    $prevVol = null; $nextVol = null;
    $idx = array_search($vol, $volumes);
    if ($idx > 0) $prevVol = $volumes[$idx - 1];
    if ($idx < count($volumes) - 1) $nextVol = $volumes[$idx + 1];
    
    // Хлебные крошки
    $breadcrumbs = [
        ['name' => 'Главная', 'url' => 'https://ob-kub.ru/'],
        ['name' => 'Пивоваренное оборудование', 'url' => 'https://ob-kub.ru/beer.html'],
        ['name' => 'ЦКТ', 'url' => 'https://ob-kub.ru/catalog/beer/cct/'],
        ['name' => "{$volStr} л", 'url' => $canonical],
    ];
    
    // Schema.org Products
    $schemaProduct = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => "ЦКТ {$volStr} литров",
        'description' => $d['desc'],
        'image' => 'https://ob-kub.ru/brewery-equipment.png',
        'brand' => ['@type' => 'Brand', 'name' => 'ОБОРУДОВАНИЕ КУБАНИ'],
        'category' => 'Пивоваренное оборудование',
        'offers' => [
            '@type' => 'Offer',
            'price' => $d['from_price'],
            'priceCurrency' => 'RUB',
            'availability' => 'https://schema.org/InStock',
        ],
        'material' => 'Нержавеющая сталь AISI 304',
    ], JSON_UNESCAPED_UNICODE);
    
    // BreadcrumbList
    $schemaBread = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_map(function($b, $i) {
            return ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $b['name'], 'item' => $b['url']];
        }, $breadcrumbs, array_keys($breadcrumbs)),
    ], JSON_UNESCAPED_UNICODE);
    
    $metaTitle = $d['title'];
    $metaDesc = $d['desc'];
    
    // === HTML ===
    header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= htmlspecialchars($metaTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($metaTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta property="og:type" content="product">
    <meta property="og:url" content="<?= $canonical ?>">
    <meta property="og:image" content="https://ob-kub.ru/logo.png">
    <link rel="canonical" href="<?= $canonical ?>">
    <script type="application/ld+json"><?= $schemaBread ?></script>
    <script type="application/ld+json"><?= $schemaProduct ?></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style-original.css">
<link rel="stylesheet" href="/css/catalog-mobile.css">
<link rel="stylesheet" href="/css/header.css">
    <link rel="icon" href="/favicon.png" type="image/png">
    <style>        .catalog-wrap{max-width:1000px;margin:0 auto;padding:20px}        .catalog-breadcrumbs{font-size:13px;color:#888;margin-bottom:20px}        .catalog-breadcrumbs a{color:#F77C2A;text-decoration:none}        .catalog-breadcrumbs a:hover{text-decoration:underline}        .catalog-breadcrumbs span{color:#aaa}        .cct-hero{display:flex;gap:30px;margin-bottom:30px;align-items:flex-tart}        .cct-hero-img{flex:0 0 300px;max-width:300px}        .cct-hero-img img{width:100%;height:auto;border-radius:8px}        .cct-hero-text{flex:1}        .cct-hero-text h1{font-size:22px;color:#333;margin:0 0 10px}        .cct-hero-text p{font-size:14px;color:#555;line-height:1.6;margin:0 0 15px}        .cct-price{font-size:24px;font-weight:700;color:#F77C2A}        .cct-price small{font-size:13px;font-weight:400;color:#888}        .cct-specs{background:#fff;border:1px solid #e8e8e8;border-radius:10px;padding:20px;margin-bottom:30px}        .cct-specs h2{font-size:16px;color:#333;margin:0 0 15px}        .cct-specs table{width:100%;border-collapse:collapse}        .cct-specs td{padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:14px}        .cct-specs td:first-child{color:#888;width:200px}        .cct-specs td:last-child{color:#333;font-weight:500}        .cct-jackets-info{display:flex;gap:10px;margin:15px 0}        .cct-jacket{background:#f5f7fa;border-radius:8px;padding:12px;flex:1;text-align:center;font-size:13px}        .cct-jacket strong{display:block;font-size:15px;color:#F77C2A;margin-bottom:3px}        .cct-cta{text-align:center;margin:30px 0}        .cct-cta a,.cct-cta button{display:inline-block;padding:14px 40px;background:#F77C2A;color:#fff;border:none;border-radius:8px;font-size:16px;text-decoration:none;cursor:pointer}        .cct-cta a:hover,.cct-cta button:hover{background:#e06a1a}        .cct-nav{display:flex;justify-content:pace-between;gap:15px;margin:20px 0}        .cct-nav a{flex:1;padding:12px;background:#f5f7fa;border-radius:8px;text-align:center;text-decoration:none;color:#F77C2A;font-weight:600;font-size:14px}        .cct-nav a:hover{background:#e8f0fe}        .cct-nav a.disabled{opacity:.3;pointer-events:none}        .cct-volumes-table{width:100%;border-collapse:collapse;margin:20px 0}        .cct-volumes-table th{padding:8px 10px;background:#f5f7fa;color:#888;font-size:12px;text-align:left;border-bottom:2px solid #e0e0e0}        .cct-volumes-table td{padding:8px 10px;border-bottom:1px solid #f0f0f0;font-size:13px}        .cct-volumes-table tr:hover td{background:#fafbfc}        .cct-volumes-table a{color:#F77C2A;text-decoration:none;font-weight:600}        .cct-volumes-table a:hover{text-decoration:underline}        .cct-volumes-table .active-row{background:#fff8f0}        .cct-volumes-table .active-row td{font-weight:600;color:#F77C2A}        .cct-faq{margin:30px 0}        .cct-faq h2{font-size:16px;margin:0 0 15px;color:#333}        .cct-faq-item{margin-bottom:10px;border:1px solid #e8e8e8;border-radius:8px;overflow:hidden}        .cct-faq-q{padding:12px 15px;background:#fafbfc;cursor:pointer;font-weight:600;font-size:14px;color:#333}        .cct-faq-a{padding:12px 15px;font-size:13px;color:#555;line-height:1.5;display:none}        .cct-faq-item.open .cct-faq-a{display:block}        .cct-options{margin:20px 0}        .cct-options h2{font-size:16px;color:#333;margin:0 0 10px}        .cct-options-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}        .cct-option{background:#f5f7fa;border-radius:6px;padding:10px 12px;font-size:13px;color:#555;display:flex;align-items:center;gap:8px}        .cct-option .ok{color:#27ae60}        @media(max-width:768px){            .cct-hero{flex-direction:column}            .cct-hero-img{flex:none;max-width:200px;margin:0 auto}            .cct-options-grid{grid-template-columns:1fr}            .cct-nav{flex-direction:column}        }    @media</style>
</head>
<body class="brewery-page">
<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; ?>

<main>
<div class="catalog-wrap">

    <!-- Хлебные крошки -->
    <div class="catalog-breadcrumbs">
        <a href="/">Главная</a> <span>›</span>
        <a href="/beer.html">Пивоваренное</a> <span>›</span>
        <a href="/catalog/beer/cct/">ЦКТ</a> <span>›</span>
        <?= $volStr ?> л
    </div>

    <!-- Герой -->
    <div class="cct-hero">
        <div class="cct-hero-img">
            <img 
src="/brewery-equipment.png" alt="ЦКТ <?= $volStr ?> литров" loading="lazy">
        </div>
        <div class="cct-hero-text">
            <h1>ЦКТ <?= $volStr ?> литров</h1>
            <p><?= htmlspecialchars($d['desc']) ?></p>
            <div class="cct-price">от <?= $priceStr ?> <small>с НДС</small></div>
        </div>
    </div>

    <!-- Навигация по объёмам -->
    <div class="cct-nav">
        <a href="<?= $prevVol ? "/catalog/beer/cct/{$prevVol}l/" : '#' ?>" class="<?= $prevVol ? '' : 'disabled' ?>">← <?= $prevVol ? number_format($prevVol, 0, '.', ' ') . ' л' : '—' ?></a>
        <a href="/catalog/beer/cct/">📋 Все объёмы</a>
        <a href="<?= $nextVol ? "/catalog/beer/cct/{$nextVol}l/" : '#' ?>" class="<?= $nextVol ? '' : 'disabled' ?>"><?= $nextVol ? number_format($nextVol, 0, '.', ' ') . ' л' : '—' ?> →</a>
    </div>

    <!-- Характеристики -->
    <div class="cct-specs">
        <h2>📐 Технические характеристики</h2>
        <table>
            <tr><td>Объём полный</td><td><?= $volStr ?> литров</td></tr>
            <tr><td>Диаметр</td><td><?= $diameterM ?> м (<?= $d['diameter'] ?> мм)</td></tr>
            <tr><td>Высота цилиндрической части</td><td><?= number_format($d['height_cyl'] / 1000, 2, '.', '') ?> м (<?= $d['height_cyl'] ?> мм)</td></tr>
            <tr><td>Высота конуса</td><td><?= number_format($d['height_cone'] / 1000, 2, '.', '') ?> м (<?= $d['height_cone'] ?> мм)</td></tr>
            <tr><td>Высота общая</td><td><?= $heightM ?> м (<?= $d['height_full'] ?> мм)</td></tr>
            <tr><td>Материал</td><td>AISI 304 (пищевая нержавеющая сталь) / AISI 316 (опционально)</td></tr>
            <tr><td>Толщина стенки</td><td><?= $d['wall'] ?> мм</td></tr>
            <tr><td>Рабочее давление</td><td>до <?= $d['pressure'] ?> бар</td></tr>
            <tr><td>Вес (пустой)</td><td>≈ <?= $d['weight'] ?> кг</td></tr>
            <tr><td>Полировка</td><td>Ra ≤ 0,8 мкм (зеркальная)</td></tr>
        </table>
    </div>

    <!-- Рубашки охлаждения -->
    <div class="cct-specs">
        <h2>❄️ Система охлаждения</h2>
        <p 
style="font-size:14px;color:#555;margin:0 0 10px"><?= $d['jackets_desc'] ?></p>
        <div class="cct-jackets-info">
            <div class="cct-jacket"><strong>❄️ Колба</strong>Основная зона — охлаждение сусла после варки, контроль температуры брожения</div>
            <div class="cct-jacket"><strong>❄️ Конус</strong>Охлаждение конусной части — осаждение дрожжей, управление дображиванием</div>
            <?php if ($d['jackets'] >= 3): ?>
            <div class="cct-jacket"><strong>❄️ Колба (2)</strong>Дополнительная зона для равномерного охлаждения больших объёмов</div>
            <?php endif; ?>
        </div>
        <p 
style="font-size:13px;color:#888;margin:10px 0 0">Теплоизоляция: пенополиуретан 50–200 мм (зависит от объёма и условий эксплуатации)</p>
    </div>

    <!-- Комплектация -->
    <div class="cct-specs">
        <h2>📦 Базовая комплектация</h2>
        <div class="cct-options-grid">
            <div class="cct-option"><span class="ok">✅</span> Теплоизоляция ППУ 50 мм</div>
            <div class="cct-option"><span class="ok">✅</span> Шпунт-аппарат</div>
            <div class="cct-option"><span class="ok">✅</span> Предохранительный клапан</div>
            <div class="cct-option"><span class="ok">✅</span> Дисковый затвор DN50/DN65</div>
            <div class="cct-option"><span class="ok">✅</span> Пробоотборный кран</div>
            <div class="cct-option"><span class="ok">✅</span> Люк-лаз DN400</div>
            <div class="cct-option"><span class="ok">✅</span> Ротационная головка CIP</div>
            <div class="cct-option"><span class="ok">✅</span> Карман под термопару</div>
            <div class="cct-option"><span class="ok">✅</span> Манометр</div>
            <div class="cct-option"><span class="ok">✅</span> Опоры (лапы / юбка)</div>
        </div>
    </div>

    <!-- Дополнительные опции -->
    <div class="cct-specs">
        <h2>🔧 Дополнительные опции</h2>
        <div class="cct-options-grid">
            <div class="cct-option">➕ Увеличенная теплоизоляция (100/150/200 мм)</div>
            <div class="cct-option">➕ Смотровой люк с подсветкой</div>
            <div class="cct-option">➕ Пневматическая запорная арматура</div>
            <div class="cct-option">➕ Датчики температуры / давления / уровня</div>
            <div class="cct-option">➕ Система автоматизации (PID-регулятор)</div>
            <div class="cct-option">➕ Мешалка (для специальных сортов)</div>
            <div class="cct-option">➕ Дополнительный дисковый затвор на конус</div>
            <div class="cct-option">➕ AISI 316 (для кислых сред и кваса)</div>
        </div>
    </div>

    <!-- CTA -->
    <div class="cct-cta">
        <button onclick="document.getElementById('cct-form').scrollIntoView({behavior:'smooth'})">📩 Рассчитать ЦКТ <?= $volStr ?> л</button>
    </div>

    <!-- Таблица всех объёмов -->
    <div class="cct-specs">
        <h2>📊 Сравнение всех объёмов ЦКТ</h2>
        <table class="cct-volumes-table">
            <thead>
                <tr><th>Объём</th><th>D, мм</th><th>H полн., мм</th><th>Стенка, мм</th><th>Рубашки</th><th>Вес, кг</th><th>Цена</th></tr>
            </thead>
            <tbody>
            <?php foreach ($allData as $v => $item):
                $isActive = ($v === $vol);
                $vFmt = number_format($v, 0, '.', ' ');
            ?>
                <tr class="<?= $isActive ? 'active-row' : '' ?>">
                    <td><a href="/catalog/beer/cct/<?= $v ?>l/"><?= $vFmt ?> л</a></td>
                    <td><?= $item['diameter'] ?></td>
                    <td><?= $item['height_full'] ?></td>
                    <td><?= $item['wall'] ?></td>
                    <td><?= $item['jackets'] ?></td>
                    <td><?= $item['weight'] ?></td>
                    <td><?= fmtPrice($item['from_price']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- FAQ -->
    <div class="cct-faq">
        <h2>❓ Часто задаваемые вопросы о ЦКТ</h2>
        <div class="cct-faq-item">
            <div class="cct-faq-q" onclick="this.parentElement.classList.toggle('open')">Какое рабочее давление у ЦКТ?</div>
            <div class="cct-faq-a">Стандартное рабочее давление цилиндро-конических танков — до 2,5 бар. Этого достаточно для брожения и дображивания большинства сортов пива. По запросу изготавливаем ЦКТ с усилением до 4–6 бар.</div>
        </div>
        <div class="cct-faq-item">
            <div class="cct-faq-q" onclick="this.parentElement.classList.toggle('open')">Из какой стали делаете ЦКТ?</div>
            <div class="cct-faq-a">Стандартно — AISI 304 (пищевая нержавейка). Для кваса, кислых сортов (кислые эли, ламбики) и агрессивных сред используем AISI 316. Толщина стенки рассчитывается под объём танка.</div>
        </div>
        <div class="cct-faq-item">
            <div class="cct-faq-q" onclick="this.parentElement.classList.toggle('open')">Какая теплоизоляция устанавливается?</div>
            <div class="cct-faq-a">Пенополиуретан (ППУ) толщиной 50 мм в базе. Для регионов с низкими температурами или уличной установки — 100–200 мм.</div>
        </div>
        <div class="cct-faq-item">
            <div class="cct-faq-q" onclick="this.parentElement.classList.toggle('open')">Сколько ЦКТ нужно для пивоварни?</div>
            <div class="cct-faq-a">Минимально — 3 ЦКТ на один сорт (брожение + дображивание + освобождение под следующую варку). Оптимально — 4–6 ЦКТ для варки 2–3 сортов одновременно.</div>
        </div>
        <div class="cct-faq-item">
            <div class="cct-faq-q" onclick="this.parentElement.classList.toggle('open')">Какой срок изготовления ЦКТ?</div>
            <div class="cct-faq-a">От 3 до 8 недель в зависимости от объёма и сложности. ЦКТ до 1000 л — 3–4 недели, до 10000 л — 5–6 недель, свыше — от 8 недель.</div>
        </div>
    </div>

    <!-- Форма заявки -->
    <div class="cct-specs" id="cct-form">
        <h2>📩 Получить расчёт ЦКТ <?= $volStr ?> литров</h2>
        <form method="post" action="/php/send.php" 
style="max-width:500px">
            <input type="hidden" name="form_type" value="item">
            <input type="hidden" name="product" value="ЦКТ <?= $volStr ?> л">
            <div 
style="margin-bottom:12px">
                <label 
style="display:block;font-size:13px;color:#666;margin-bottom:4px">Ваше имя</label>
                <input type="text" name="name" required 
style="width:100%;padding:10px;border:1px solid #d0d0d0;border-radius:6px;font-size:14px">
            </div>
            <div 
style="margin-bottom:12px">
                <label 
style="display:block;font-size:13px;color:#666;margin-bottom:4px">Телефон</label>
                <input type="tel" name="phone" required 
style="width:100%;padding:10px;border:1px solid #d0d0d0;border-radius:6px;font-size:14px">
            </div>
            <div 
style="margin-bottom:12px">
                <label 
style="display:block;font-size:13px;color:#666;margin-bottom:4px">Email</label>
                <input type="email" name="email" 
style="width:100%;padding:10px;border:1px solid #d0d0d0;border-radius:6px;font-size:14px">
            </div>
            <div 
style="margin-bottom:12px">
                <label 
style="display:block;font-size:13px;color:#666;margin-bottom:4px">Количество ЦКТ</label>
                <input type="number" name="quantity" value="3" min="1" 
style="width:100%;padding:10px;border:1px solid #d0d0d0;border-radius:6px;font-size:14px">
            </div>
            <div 
style="margin-bottom:16px">
                <label 
style="display:block;font-size:13px;color:#666;margin-bottom:4px">Дополнительные требования</label>
                <textarea name="comment" rows="3" 
style="width:100%;padding:10px;border:1px solid #d0d0d0;border-radius:6px;font-size:13px"></textarea>
            </div>
            <button type="submit" class="submit-btn" 
style="width:100%;padding:12px;background:#F77C2A;color:#fff;border:none;border-radius:8px;font-size:16px;cursor:pointer">Получить расчёт</button>
        </form>
    </div>

</div>
</main>

<?php require $_SERVER['DOCUMENT_ROOT'].'/php/footer.php'; ?>
<?php
}

// ============================================================
function renderCctList($allData, $cat) {
    $volumes = array_keys($allData);
    
sort($volumes);
    
    $canonical = 'https://ob-kub.ru/catalog/beer/cct/';
    
    $breadcrumbs = [
        ['name' => 'Главная', 'url' => 'https://ob-kub.ru/'],
        ['name' => 'Пивоваренное оборудование', 'url' => 'https://ob-kub.ru/beer.html'],
        ['name' => 'ЦКТ', 'url' => $canonical],
    ];
    
    $schemaBread = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_map(function($b, $i) {
            return ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $b['name'], 'item' => $b['url']];
        }, $breadcrumbs, array_keys($breadcrumbs)),
    ], JSON_UNESCAPED_UNICODE);
    
    header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= htmlspecialchars($cat['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($cat['desc']) ?>">
    <link rel="canonical" href="<?= $canonical ?>">
    <script type="application/ld+json"><?= $schemaBread ?></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style-original.css">
<link rel="stylesheet" href="/css/catalog-mobile.css">
<link rel="stylesheet" href="/css/header.css">
    <link rel="icon" href="/favicon.png" type="image/png">
    <style>        .catalog-wrap{max-width:1000px;margin:0 auto;padding:20px}        .catalog-breadcrumbs{font-size:13px;color:#888;margin-bottom:20px}        .catalog-breadcrumbs a{color:#F77C2A;text-decoration:none}        .catalog-breadcrumbs a:hover{text-decoration:underline}        .catalog-breadcrumbs span{color:#aaa}        .catalog-h1{font-size:22px;color:#333;margin:0 0 10px}        .catalog-desc{font-size:14px;color:#555;line-height:1.6;margin:0 0 20px}        .cct-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;margin:20px 0}        .cct-card{background:#fff;border:1px solid #e8e8e8;border-radius:10px;padding:16px;text-align:center;transition:box-shadow .2s;text-decoration:none;color:inherit;display:block}        .cct-card:hover{border-color:#F77C2A;box-shadow:0 2px 8px rgba(247,124,42,.15)}        .cct-card .vol{font-size:20px;font-weight:700;color:#F77C2A}        .cct-card .pec{font-size:12px;color:#888;margin-top:4px}        @media(max-width:768px){.cct-grid{grid-template-columns:repeat(auto-fill,minmax(150px,1fr))}}    @media</style>
</head>
<body class="brewery-page">
<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; ?>

<main>
<div class="catalog-wrap">
    <div class="catalog-breadcrumbs">
        <a href="/">Главная</a> <span>›</span>
        <a href="/beer.html">Пивоваренное</a> <span>›</span>
        ЦКТ
    </div>
    <h1 class="catalog-h1"><?= htmlspecialchars($cat['h1']) ?></h1>
    <p class="catalog-desc"><?= htmlspecialchars($cat['desc']) ?></p>
    
    <div class="cct-grid">
    <?php foreach ($volumes as $v):
        $d = $allData[$v];
        $vFmt = number_format($v, 0, '.', ' ');
    ?>
        <a href="/catalog/beer/cct/<?= $v ?>l/" class="cct-card">
            <div class="vol"><?= $vFmt ?> л</div>
            <div class="pec">D <?= $d['diameter'] ?> мм · H <?= $d['height_full'] ?> мм</div>
            <div class="pec">стенка <?= $d['wall'] ?> мм · <?= $d['jackets'] ?> зоны охл.</div>
            <div class="pec" 
style="color:#F77C2A;font-weight:600;margin-top:6px">от <?= fmtPrice($d['from_price']) ?></div>
        </a>
    <?php endforeach; ?>
    </div>
</div>
</main>

<?php require $_SERVER['DOCUMENT_ROOT'].'/php/footer.php'; ?>
<?php
}
