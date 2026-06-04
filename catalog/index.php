<?php
error_reporting(0);
ini_set('display_errors', 0);

$reqUri = $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?? '';
$uri = parse_url($reqUri, PHP_URL_PATH);
$uri = preg_replace('#/index\.php$#', '/', $uri);
$uri = trim($uri, '/');
$parts = explode('/', $uri);

// AJAX endpoint for prices (quiz)
if (isset($_GET['get_prices'])) {
    header('Content-Type: application/json; charset=utf-8');
    $cat = $_GET['get_prices'];
    $src = $_GET['src'] ?? '';
    $files = [
        'beerExtra' => __DIR__ . '/beer-extra-data.php',
        'brewData' => __DIR__ . '/brew-house-data.php',
        'cctData' => __DIR__ . '/cct-data.php',
        'dairyData' => __DIR__ . '/dairy-data.php',
        'wineData' => __DIR__ . '/wine-data.php',
        'industrialData' => __DIR__ . '/industrial-data.php',
    ];
    if (isset($files[$src]) && file_exists($files[$src])) {
        require $files[$src];
        $data = &$$src;
        if (isset($data[$cat]['specs'])) {
            $result = [];
            foreach ($data[$cat]['specs'] as $vol => $spec) {
                $p = $spec['price'] ?? $spec['from_price'] ?? 0;
                $result[] = ['vol' => $vol, 'price' => $p];
            }
            echo json_encode(['name' => $data[$cat]['name'] ?? $data[$cat]['h1'] ?? $cat, 'prices' => $result], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    echo '{"prices":[]}';
    exit;
}

if ($parts[0] === 'catalog') {
    // CCT-specific: redirect to standalone volume.php
    if (($parts[1] ?? '') === 'beer' && ($parts[2] ?? '') === 'cct') {
        if (isset($parts[3]) && preg_match('/^(\d+)l?$/', $parts[3], $m)) {
            header('Location: /catalog/beer/cct/volume.php?vol=' . (int)$m[1], true, 301);
            exit;
        }
        header('Location: /catalog/beer/cct/');
        exit;
    }

    // Generic: walk URL path from right to left,
    // find the deepest subdirectory that has index.php,
    // include it with CATALOG_VOLUME set when a volume segment is present.
    $catParts = array_slice($parts, 1);
    $found = false;
    for ($i = count($catParts); $i >= 1; $i--) {
        $subParts = array_slice($catParts, 0, $i);
        $subDir = __DIR__ . '/' . implode('/', $subParts);
        if (is_dir($subDir) && file_exists($subDir . '/index.php')) {
            $removed = array_slice($catParts, $i);
            if (isset($removed[0]) && preg_match('/^(\d+)l?$/', $removed[0], $m)) {
                $_SERVER['CATALOG_VOLUME'] = (int)$m[1];
            }
            include $subDir . '/index.php';
            $found = true;
            break;
        }
    }
    if (!$found) {
        renderMainCatalog();
    }
    exit;
}

// 404 — только для URL не из /catalog/
http_response_code(404);
?><!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><title>404 — Страница не найдена</title>
<meta name="robots" content="noindex">
<style>body{font-family:sans-serif;text-align:center;padding:60px 20px;color:#333}h1{font-size:28px;color:#F77C2A}p{font-size:16px;color:#666}a{color:#F77C2A}</style>
</head>
<body>
<div style="font-size:28px;font-weight:700;color:#F77C2A">404</div>
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
            'desc' => 'Ёмкости приёмки молока, резервуары-охладители, резервуары хранения, ванны длительной пастеризации, ферментационные танки, сыроизготовители, творогоизготовители, заквасочники, стеллажи для созревания сыра.',
            'count' => '9 категорий',
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
    // Add storage tanks from industrial catalog
    require __DIR__ . '/industrial-data.php';
    if (isset($industrialData['storage'])) {
        $s = $industrialData['storage'];
        $vols = $s['volumes'];
        $sections[] = [
            'name' => $s['name'],
            'url' => '/catalog/industrial/storage/',
            'image' => '/' . $s['image'],
            'desc' => $s['desc'],
            'count' => count($vols) . ' объёмов: ' . min($vols) . ' – ' . number_format(max($vols), 0, '.', ' ') . ' л',
        ];
    }

    $bodyClass = 'brewery-page catalog-page';
    $canonical = 'https://ob-kub.ru/catalog/';
    $inlineStyles = '*,*::before,*::after{box-sizing:border-box}.catalog-page{font-family:\'Source Sans Pro\',sans-serif;color:#2c3e50;background:#f5f6f8}.catalog-page .container{max-width:1100px;margin:0 auto;padding:0 24px}.main-hero{background:linear-gradient(135deg,#2b2b39 0%,#1a1a26 100%);position:relative;overflow:hidden;padding:44px 0 40px}.main-hero::before{content:\'\';position:absolute;top:0;left:0;right:0;bottom:0;background:url(data:image/vg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+) repeat;pointer-events:none}.main-hero::after{content:\'\';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(247,124,42,.3),transparent)}.main-hero .hero-text{position:relative;z-index:1;text-align:center}.main-hero .hero-text h1{font-size:28px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#fff;margin:0 0 4px}.main-hero .hero-text .title-accent{width:40px;height:3px;background:#F77C2A;border-radius:2px;margin:10px auto 0}.main-hero .hero-text p{font-size:14px;color:rgba(255,255,255,.6);margin:10px 0 0;max-width:650px;margin-left:auto;margin-right:auto;line-height:1.5}.main-hero .breadcrumbs{padding:0 0 12px;font-size:11px;color:rgba(255,255,255,.3)}.main-hero .breadcrumbs a{color:rgba(255,255,255,.45);text-decoration:none;transition:color .2s}.main-hero .breadcrumbs a:hover{color:#F77C2A}.main-hero .breadcrumbs .sep{display:inline-block;margin:0 6px;color:rgba(255,255,255,.15)}.main-hero .breadcrumbs .current{color:rgba(255,255,255,.5)}.section-title{font-size:16px;font-weight:700;color:#1a1a26;margin:32px 0 16px;padding-bottom:8px;border-bottom:3px solid #F77C2A;text-transform:uppercase;letter-spacing:.4px}.cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:24px}.cat-card{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.06);transition:transform .2s,box-shadow .2s;text-decoration:none;color:inherit;display:flex;flex-direction:column;border:1px solid #eee;overflow:hidden}.cat-card:hover{transform:translateY(-6px);box-shadow:0 12px 36px rgba(247,124,42,.15);border-color:#fde0c0}.cat-card-img{width:100%;height:200px;overflow:hidden;background:#fff;display:flex;align-items:center;justify-content:center}.cat-card-img img{max-width:100%;max-height:100%;display:block}.cat-card-body{padding:20px 24px 20px;flex:1;display:flex;flex-direction:column;text-align:center}.cat-card-body .cat-name{font-size:18px;font-weight:700;color:#1a1a26;margin-bottom:8px;line-height:1.3}.cat-card-body .cat-desc{font-size:13px;color:#666;line-height:1.6;flex:1}.cat-card-body .cat-count{font-size:12px;color:#F77C2A;font-weight:600;margin-top:12px}.cat-card-footer{padding:0 24px 24px}.cat-card-footer .btn-view{display:block;width:100%;padding:14px;background:#F77C2A;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;text-align:center;transition:background .2s}.cat-card-footer .btn-view:hover{background:#e06a1a}.mega-menu-wrap{position:relative;display:inline-block}.nav .mega-menu-link{display:inline-flex;align-items:center;gap:4px}.nav .mega-menu-link:hover{color:#F77C2A}.mega-arrow{font-size:9px;transition:transform .25s;display:inline-block;margin-left:2px}.mega-menu-wrap:hover .mega-arrow,.mega-menu-wrap.active .mega-arrow{transform:rotate(180deg)}.mega-menu{position:absolute;top:100%;left:50%;transform:translateX(-50%) translateY(10px);background:#fff;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,.18),0 4px 16px rgba(0,0,0,.08);padding:24px;display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;min-width:820px;z-index:1000;opacity:0;visibility:hidden;transition:all .25s ease;margin-top:8px;border:1px solid rgba(0,0,0,.04)}.mega-menu::before{content:\'\';position:absolute;top:-8px;left:50%;transform:translateX(-50%);border:8px solid transparent;border-bottom-color:#fff}.mega-menu::after{content:\'\';position:absolute;top:0;left:24px;right:24px;height:3px;background:linear-gradient(90deg,#F77C2A,#FF8C42);border-radius:0 0 3px 3px}.mega-menu-wrap:hover .mega-menu,.mega-menu-wrap.active .mega-menu{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0)}.mega-col h3{font-size:11px;font-weight:700;color:#2b2b39;margin:0 0 10px;padding-bottom:7px;border-bottom:2px solid #F77C2A;text-transform:uppercase;letter-spacing:.4px}.mega-col a{display:block;padding:4px 0;font-size:13px;color:#555;text-decoration:none;transition:color .2s;line-height:1.4;white-space:normal}.mega-col a:hover{color:#F77C2A}.mega-col-link{display:block;text-decoration:none;color:inherit;padding:0;font-size:inherit;line-height:inherit}.mega-col-link:hover h3{color:#F77C2A}.catalog-page .header{overflow:visible !important}.hero-adv-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:22px;position:relative;z-index:1}.hero-adv-card{background:#fff;border-radius:8px;padding:14px 16px;text-align:left;border-top:3px solid #F77C2A;box-shadow:0 2px 8px rgba(0,0,0,.08);transition:transform .2s,box-shadow .2s}.hero-adv-card:hover{transform:translateY(-3px);box-shadow:0 6px 20px rgba(0,0,0,.12)}.hero-adv-card .adv-title{font-size:13px;font-weight:700;color:#1a1a26;margin-bottom:2px}.hero-adv-card .adv-desc{font-size:11px;color:#666;line-height:1.35}.hero-cta-wrap{text-align:center;margin-top:20px;position:relative;z-index:1}.hero-cta-btn{display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:rgba(247,124,42,.88);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;transition:background .2s,transform .2s,box-shadow .2s}.hero-cta-btn:hover{background:#F77C2A;box-shadow:0 4px 16px rgba(247,124,42,.3)}.hero-cta-btn .arr{font-size:16px;transition:transform .2s}.hero-cta-btn:hover .arr{transform:translateX(3px)}@media(max-width:768px){.hero-adv-grid{grid-template-columns:repeat(2,1fr);gap:10px;margin-top:14px}.hero-adv-card{padding:10px 12px}.main-hero .hero-text h1{font-size:22px}.main-hero{padding:30px 0 28px}.hero-cta-wrap{margin-top:14px}.hero-cta-btn{padding:9px 20px;font-size:13px}}@media(max-width:480px){.hero-adv-grid{grid-template-columns:1fr 1fr}}';
    require __DIR__ . '/layout-start.php';
?><div style="background:linear-gradient(135deg,#2b2b39,#1a1a26);padding:32px 0 24px">
<div class="db-wrap">
<div style="font-size:11px;color:rgba(255,255,255,.3);margin-bottom:8px">
<a href="/" style="color:rgba(255,255,255,.45);text-decoration:none">Главная</a>
<span style="display:inline-block;margin:0 6px;color:rgba(255,255,255,.15)">•</span>
<span style="color:rgba(255,255,255,.5)">Каталог</span>
</div>
<h1 style="font-size:24px;font-weight:800;color:#fff;margin:0;text-transform:uppercase;letter-spacing:.4px">Каталог оборудования</h1>
</div>
</div>
<!-- EQUIPMENT CATEGORIES -->
<section class="db-section" id="equipment" style="padding:48px 0;background:#f5f6f8">
<div class="db-wrap" style="max-width:1200px">
<div class="db-weld-frame" style="padding:36px 40px;background:linear-gradient(135deg,#1a1a26,#2b2b39)">
<div style="text-align:center;margin-bottom:24px">
<div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#F77C2A;font-weight:600;margin-bottom:6px">Каталог</div>
<h2 style="font-size:24px;font-weight:800;color:#fff;margin:0 0 4px">Оборудование по отраслям</h2>
<p style="font-size:14px;color:rgba(255,255,255,.5);margin:0">Выберите вашу отрасль</p>
</div>

<div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-bottom:24px" id="catTabs2">
<button class="cat-tab2 active" data-i="0" style="padding:12px 24px;border-radius:8px;border:none;cursor:pointer;font-size:14px;font-weight:600;font-family:inherit;background:#F77C2A;color:#fff">🥛 Молочное</button>
<button class="cat-tab2" data-i="1" style="padding:12px 24px;border-radius:8px;border:none;cursor:pointer;font-size:14px;font-weight:600;font-family:inherit;background:rgba(255,255,255,.06);color:rgba(255,255,255,.5)">🍷 Винодельческое</button>
<button class="cat-tab2" data-i="2" style="padding:12px 24px;border-radius:8px;border:none;cursor:pointer;font-size:14px;font-weight:600;font-family:inherit;background:rgba(255,255,255,.06);color:rgba(255,255,255,.5)">🍺 Пивоваренное</button>
<button class="cat-tab2" data-i="3" style="padding:12px 24px;border-radius:8px;border:none;cursor:pointer;font-size:14px;font-weight:600;font-family:inherit;background:rgba(255,255,255,.06);color:rgba(255,255,255,.5)">💧 Вода</button>
<button class="cat-tab2" data-i="4" style="padding:12px 24px;border-radius:8px;border:none;cursor:pointer;font-size:14px;font-weight:600;font-family:inherit;background:rgba(255,255,255,.06);color:rgba(255,255,255,.5)">🫒 Масло</button>
<button class="cat-tab2" data-i="5" style="padding:12px 24px;border-radius:8px;border:none;cursor:pointer;font-size:14px;font-weight:600;font-family:inherit;background:rgba(255,255,255,.06);color:rgba(255,255,255,.5)">🍯 Кондитерская</button>
</div>

<div class="banner2" style="position:relative;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.2)">
<img id="bimg2" src="banner-dairy.jpg" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover">
<div style="position:absolute;top:0;left:0;right:0;bottom:0;background:linear-gradient(135deg,rgba(26,26,38,.92) 30%,rgba(26,26,38,.5) 70%,rgba(26,26,38,.1) 100%);z-index:2"></div>
<div style="position:relative;z-index:3;padding:30px 36px 32px">
<div style="font-size:24px;font-weight:800;color:#fff;margin-bottom:2px" id="ttl2">🥛 Молочное</div>
<div style="font-size:14px;color:rgba(255,255,255,.5);margin-bottom:16px" id="ssub2">Оборудование для молочной промышленности</div>
<div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap">
<span style="background:rgba(255,255,255,.06);border-radius:6px;padding:6px 14px;font-size:13px;color:#fff;font-weight:600" id="vvol2">500 - 200 000 л</span>
<span style="background:rgba(255,255,255,.06);border-radius:6px;padding:6px 14px;font-size:13px;color:#fff;font-weight:600">AISI 304/316</span>
<span style="background:rgba(255,255,255,.06);border-radius:6px;padding:6px 14px;font-size:13px;color:#F77C2A;font-weight:700" id="ppr2">от 195 000 ₽</span>
</div>
<div style="font-size:13px;color:rgba(255,255,255,.5);line-height:1.5;margin-bottom:16px;max-width:600px" id="ddesc2">Оборудование для молочной промышленности: ёмкости приёмки и хранения, резервуары-охладители, ВДП, ферментационные танки, сыро- и творогоизготовители.</div>
<div class="prods2" id="pprod2" style="display:flex;gap:8px;overflow-x:auto;padding-bottom:4px"></div>
</div>
</div>
</div>
</div>
</section>

<style>
.prods2 a{flex:0 0 auto;width:150px;text-decoration:none;color:inherit}
.prods2 div{background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.06);min-height:170px;display:flex;flex-direction:column}
.prods2::-webkit-scrollbar{height:4px}
.prods2::-webkit-scrollbar-thumb{background:rgba(255,255,255,.2);border-radius:2px}
.prods2 img{width:100%;height:100px;object-fit:contain;display:block;background:#fff;padding:8px;flex-shrink:0}
.prods2 span{display:flex;align-items:center;justify-content:center;padding:6px 8px;font-size:11px;color:#333;font-weight:600;text-align:center;line-height:1.2;min-height:36px;flex:1}
.prods2 .pr-btn{display:block;padding:5px 8px;font-size:10px;color:#F77C2A;font-weight:700;text-align:center;border-top:1px solid #eee;flex-shrink:0}
</style>

<script>
(function(){
var D = [
{img:"banner-dairy.jpg",title:"🥛 Молочное",sub:"Оборудование для молочной промышленности",vol:"500 – 200 000 л",price:"от 195 000 ₽",desc:"Оборудование для молочной промышленности: ёмкости приёмки и хранения, резервуары-охладители, ВДП, ферментационные танки, сыро- и творогоизготовители.",pd:'<a href="/catalog/dairy/reception/"><div><img src="dairy-reception.jpg"><span>Ёмкость приёмки молока</span><span class="pr-btn">1 000 – 50 000 л →</span></div></a><a href="/catalog/dairy/storage/"><div><img src="dairy-storage.jpg"><span>Резервуар хранения молока</span><span class="pr-btn">3 000 – 200 000 л →</span></div></a><a href="/catalog/dairy/cooler/"><div><img src="dairy-cooler.jpg"><span>Резервуар-охладитель</span><span class="pr-btn">1 000 – 50 000 л →</span></div></a><a href="/catalog/dairy/vdp/"><div><img src="dairy-vdp.jpg"><span>Ванна пастеризации ВДП</span><span class="pr-btn">200 – 10 000 л →</span></div></a><a href="/catalog/dairy/fermentation/"><div><img src="dairy-fermentation.jpg"><span>Ферментационный танк</span><span class="pr-btn">500 – 50 000 л →</span></div></a><a href="/catalog/dairy/cheese-maker/"><div><img src="dairy-cheese-maker.jpg"><span>Сыроизготовитель</span><span class="pr-btn">200 – 10 000 л →</span></div></a><a href="/catalog/dairy/cottage-cheese/"><div><img src="dairy-cottage-cheese.jpg"><span>Творогоизготовитель</span><span class="pr-btn">200 – 6 300 л →</span></div></a><a href="/catalog/dairy/yeast/"><div><img src="dairy-yeast.jpg"><span>Заквасочник</span><span class="pr-btn">50 – 1 000 л →</span></div></a>'},
{img:"banner-wine.jpg",title:"🍷 Винодельческое",sub:"Оборудование для виноделия",vol:"500 – 50 000 л",price:"от 280 000 ₽",desc:"Винодельческое оборудование: ферментационные танки для красных и белых вин, ёмкости выдержки и хранения, криостабилизация, купажирование, сульфитация, винификаторы.",pd:'<a href="/catalog/wine/red-fermentation/"><div><img src="wine-red-fermentation.jpg"><span>Ферментация красных вин</span><span class="pr-btn">500 – 50 000 л →</span></div></a><a href="/catalog/wine/white-fermentation/"><div><img src="wine-white-fermentation.jpg"><span>Ферментация белых вин</span><span class="pr-btn">500 – 31 500 л →</span></div></a><a href="/catalog/wine/storage-aging/"><div><img src="wine-storage-aging.jpg"><span>Выдержка и хранение</span><span class="pr-btn">1 000 – 200 000 л →</span></div></a><a href="/catalog/wine/cold-stabilization/"><div><img src="wine-cold-stabilization.jpg"><span>Танк криостабилизации</span><span class="pr-btn">500 – 31 500 л →</span></div></a><a href="/catalog/wine/blending/"><div><img src="wine-blending.png"><span>Ёмкость для купажирования</span><span class="pr-btn">1 000 – 50 000 л →</span></div></a><a href="/catalog/wine/sulfitation/"><div><img src="wine-sulfitation.jpg"><span>Ёмкость сульфитации</span><span class="pr-btn">500 – 31 500 л →</span></div></a><a href="/catalog/wine/universal-tank/"><div><img src="wine-universal-tank.jpg"><span>Винификатор УТТ</span><span class="pr-btn">500 – 50 000 л →</span></div></a>'},
{img:"banner-beer.jpg",title:"🍺 Пивоваренное",sub:"Оборудование для пивоварен",vol:"100 – 200 000 л",price:"от 94 000 ₽",desc:"Полный цикл пивоваренного оборудования: ЦКТ, варочные порядки, дробилки, БГВ, парогенераторы, чиллеры, форфасы, теплообменники.",pd:'<a href="/catalog/beer/cct/"><div><img src="cct-tank.jpg"><span>ЦКТ для брожения</span><span class="pr-btn">100 – 200 000 л →</span></div></a><a href="/catalog/beer/brew-house/mash-tun/"><div><img src="mash-tun.jpg"><span>Заторный аппарат</span><span class="pr-btn">250 – 5 000 л →</span></div></a><a href="/catalog/beer/brew-house/combined-kettle/"><div><img src="combined-kettle.jpg"><span>Заторно-сусловарочный</span><span class="pr-btn">250 – 5 000 л →</span></div></a><a href="/catalog/beer/brew-house/lauter-tun/"><div><img src="lauter-tun.jpg"><span>Фильтрационный аппарат</span><span class="pr-btn">250 – 5 000 л →</span></div></a><a href="/catalog/beer/brew-house/brew-kettle/"><div><img src="brew-kettle.jpg"><span>Сусловарочный аппарат</span><span class="pr-btn">250 – 5 000 л →</span></div></a><a href="/catalog/beer/brew-house/whirlpool/"><div><img src="whirlpool.jpg"><span>Гидроциклон Вирпул</span><span class="pr-btn">250 – 5 000 л →</span></div></a><a href="/catalog/beer/hot-water-tank/"><div><img src="beer-hot-water-tank.jpg"><span>Бак горячей воды</span><span class="pr-btn">500 – 20 000 л →</span></div></a><a href="/catalog/beer/unitank/"><div><img src="unitank.jpg"><span>Форфас (BBT)</span><span class="pr-btn">250 – 5 000 л →</span></div></a><a href="/catalog/beer/chiller/"><div><img src="chiller.jpg"><span>Чиллер</span><span class="pr-btn">8 – 150 кВт →</span></div></a><a href="/catalog/beer/steam-generator/"><div><img src="steam-generator.jpg"><span>Парогенератор</span><span class="pr-btn">20 – 700 кг/ч →</span></div></a>'},
{img:"banner-water.jpg",title:"💧 Для воды",sub:"Баки горячей воды и резервуары хранения",vol:"250 – 50 000 л",price:"от 150 000 ₽",desc:"Резервуары для воды из AISI 304/316.",pd:'<a href="/catalog/beer/hot-water-tank/"><div><img src="hot-water-tank.jpg"><span>Бак горячей воды</span><span class="pr-btn">500 – 20 000 л →</span></div></a><a href="/catalog/industrial/storage/"><div><img src="industrial-storage.jpg"><span>Резервуар хранения</span><span class="pr-btn">1 000 – 100 000 л →</span></div></a>'},
{img:"banner-oil.jpg",title:"🫒 Для масла",sub:"Ёмкости для пищевых масел",vol:"500 – 50 000 л",price:"от 220 000 ₽",desc:"Ёмкости для пищевых масел и жиров: резервуары хранения, ёмкости с мешалкой.",pd:'<a href="/catalog/industrial/mixing/"><div><img src="industrial-mixing.jpg"><span>Ёмкость с мешалкой</span><span class="pr-btn">500 – 50 000 л →</span></div></a><a href="/catalog/industrial/storage/"><div><img src="industrial-storage.jpg"><span>Резервуар хранения</span><span class="pr-btn">1 000 – 100 000 л →</span></div></a>'},
{img:"banner-conf.jpg",title:"🍯 Кондитерская",sub:"Ёмкости для глазурей и сиропов",vol:"500 – 20 000 л",price:"от 200 000 ₽",desc:"Ёмкости с мешалкой для глазурей, сиропов, кондитерских масс.",pd:'<a href="/catalog/industrial/mixing/"><div><img src="industrial-mixing.jpg"><span>Ёмкость с мешалкой</span><span class="pr-btn">500 – 50 000 л →</span></div></a>'}
];
var cur=-1;
function sw(i){
if(i===cur)return;
var d=D[i];
document.querySelectorAll('.cat-tab2').forEach(function(t,j){t.style.background=j===i?'#F77C2A':'rgba(255,255,255,.06)';t.style.color=j===i?'#fff':'rgba(255,255,255,.5)'});
document.getElementById('bimg2').src=d.img;
document.getElementById('ttl2').textContent=d.title;
document.getElementById('ssub2').textContent=d.sub;
document.getElementById('vvol2').textContent=d.vol;
document.getElementById('ppr2').textContent=d.price;
document.getElementById('ddesc2').textContent=d.desc;
document.getElementById('pprod2').innerHTML=d.pd;
cur=i;
}
document.querySelectorAll('.cat-tab2').forEach(function(b){b.addEventListener('click',function(){sw(parseInt(this.dataset.i))})});
sw(0);
})();
</script>
<?php require __DIR__ . '/layout-end.php'; }
