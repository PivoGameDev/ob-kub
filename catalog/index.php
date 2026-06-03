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
?><style>.section-title{font-size:16px;font-weight:700;color:#1a1a26;margin:32px 0 16px;padding-bottom:8px;border-bottom:3px solid #F77C2A;text-transform:uppercase;letter-spacing:.4px}.section-title:first-of-type{margin-top:8px}@media(max-width:700px){.cat-grid{grid-template-columns:repeat(2,1fr)!important;gap:12px!important}.cat-card-img{height:140px!important}.cat-card-body{padding:10px 12px!important}.cat-card-body .cat-name{font-size:14px!important}.cat-card-body .cat-desc{font-size:12px!important;line-height:1.4!important}.cat-card-footer{padding:0 12px 12px!important}.cat-card-footer .btn-view{padding:10px!important;font-size:12px!important}.section-title{font-size:14px!important;margin:24px 0 10px!important}}@media(max-width:480px){.cat-grid{grid-template-columns:repeat(2,1fr)!important;gap:8px!important}.cat-card-img{height:90px!important}.cat-card-body{padding:8px 8px!important}.cat-card-body .cat-name{font-size:12px!important}.cat-card-body .cat-desc{display:none!important}.cat-card-body .cat-count{font-size:10px!important;margin-top:6px!important}}</style>
<style>
.db-aisi-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center}
.db-aisi-grid .aisi-gallery{position:relative;width:100%;background:#fff;border-radius:14px;overflow:hidden;aspect-ratio:4/3}
.db-aisi-grid .aisi-slide{position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;transition:opacity .8s ease}
.db-aisi-grid .aisi-slide.active{opacity:1}
.db-aisi-grid .aisi-slide .img-wrap{display:flex;align-items:center;justify-content:center;padding:20px;width:100%;height:100%;box-sizing:border-box;background:#fff}
.db-aisi-grid .aisi-slide .img-wrap img{width:100%;height:100%;display:block;object-fit:contain}
.db-aisi-grid .aisi-caption{height:34px;display:flex;align-items:center;justify-content:space-between;padding:0 4px;margin-top:5px;font-size:13px;color:#999;gap:10px}
.db-aisi-grid .aisi-caption .cap-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#555}
.db-aisi-grid .aisi-caption .cap-vol{flex-shrink:0;color:#F77C2A}
.db-aisi-grid .aisi-caption .cap-link{flex-shrink:0;color:#F77C2A;text-decoration:none;font-size:15px;font-weight:600;margin-left:auto;transition:opacity .2s}
.db-aisi-grid .aisi-caption .cap-link:hover{opacity:.7}
.db-aisi-grid h2{font-size:24px;font-weight:800;color:#1a1a26;margin-bottom:12px;text-transform:uppercase}
.db-aisi-grid p{font-size:14px;color:#555;line-height:1.7;margin-bottom:16px}
.db-aisi-features{list-style:none;padding:0;margin:0 0 24px}
.db-aisi-features li{padding:7px 0 7px 24px;position:relative;font-size:14px;color:#333}
.db-aisi-features li::before{content:'✓';position:absolute;left:0;color:#F77C2A;font-weight:700}
.db-aisi-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none;transition:opacity .25s,transform .25s}
.db-aisi-btn:hover{opacity:.9;transform:translateY(-2px)}
.db-prod-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center}
.db-prod-grid img{width:100%;border-radius:14px;display:block}
.db-prod-text p{font-size:14px;color:#555;line-height:1.7;margin:0 0 20px}
.db-prod-stats{display:flex;gap:32px}
.db-prod-stat .num{font-size:36px;font-weight:800;color:#F77C2A;line-height:1}
.db-prod-stat .lbl{font-size:12px;color:#888;margin-top:4px;line-height:1.4}
.db-prod-equip{display:grid;grid-template-columns:repeat(5,1fr);gap:12px}
.db-prod-equip-card{background:#fff;border:1px solid #e8e8e8;border-radius:10px;overflow:hidden;transition:opacity .6s ease,transform .6s ease,box-shadow .2s;opacity:0;transform:translateY(24px);position:relative}.db-prod-equip-card.visible{opacity:1;transform:translateY(0)}
.db-prod-equip-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08)}
.db-prod-equip-card .eq-overlay{position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(135deg,rgba(26,26,38,.85),rgba(26,26,38,.4));backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);z-index:2;opacity:1;transition:opacity .35s;pointer-events:none}
.db-prod-equip-card:hover .eq-overlay{opacity:0}
@media(hover:none){.db-prod-equip-card .eq-overlay{background:linear-gradient(135deg,rgba(26,26,38,.5),rgba(26,26,38,.2));backdrop-filter:blur(2px);-webkit-backdrop-filter:blur(2px)}}
.db-prod-equip-card .eq-img{width:100%;height:160px;display:flex;align-items:center;justify-content:center;padding:12px;background:#fff}
.db-prod-equip-card .eq-img img{max-width:100%;max-height:100%;display:block;width:auto;height:auto}
.db-prod-equip-card .eq-body{padding:8px 10px 10px;text-align:center;position:relative;z-index:3}
.db-prod-equip-card .eq-name{font-size:13px;font-weight:600;color:#fff;line-height:1.3;margin-bottom:2px;text-shadow:0 1px 4px rgba(0,0,0,.3)}
.db-prod-equip-card .eq-spec{font-size:12px;color:rgba(255,255,255,.7);line-height:1.3;text-shadow:0 1px 3px rgba(0,0,0,.3)}
.db-prod-equip-card:hover .eq-name{color:#333;text-shadow:none}
.db-prod-equip-card:hover .eq-spec{color:#999;text-shadow:none}
.db-prod-equip-title{font-size:14px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:.5px;margin:0 0 12px}
.db-prod-equip-section{margin-top:36px}
.db-row-section{padding:48px 0}
.db-row-section.alt{background:#fff}
.db-row-inner{display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center}
.db-row-inner.rev{direction:rtl}
.db-row-inner.rev>*{direction:ltr}
.db-arm-gallery{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.db-arm-gallery-item{background:#fff;border-radius:10px;overflow:hidden;display:flex;align-items:center;justify-content:center;aspect-ratio:4/3;transition:opacity .6s ease,transform .6s ease;opacity:0;transform:translateY(24px)}.db-arm-gallery-item.visible{opacity:1;transform:translateY(0)}
.db-arm-gallery-item img{max-width:100%;max-height:100%;display:block;object-fit:contain}
.db-projects-scroll{display:flex;gap:24px;overflow-x:auto;padding:8px 0 16px;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch}
.db-projects-scroll::-webkit-scrollbar{height:5px}
.db-projects-scroll::-webkit-scrollbar-thumb{background:#ddd;border-radius:3px}
.db-project-card{flex:0 0 340px;border-radius:14px;overflow:hidden;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid #eee;scroll-snap-align:start;transition:transform .35s,box-shadow .35s}
.db-project-card:hover{transform:translateY(-5px);box-shadow:0 12px 40px rgba(0,0,0,.12)}
.db-project-card img{width:100%;height:220px;object-fit:cover;display:block}
.db-project-body{padding:18px 20px}
.db-project-body h3{font-size:14px;font-weight:700;color:#1a1a26;margin-bottom:8px}
.db-project-body ul{list-style:none;padding:0;margin:0}
.db-project-body ul li{font-size:13px;color:#555;padding:4px 0 4px 18px;position:relative}
.db-project-body ul li::before{content:'•';position:absolute;left:4px;color:#F77C2A;font-weight:700}
.db-project-btn{display:inline-block;margin-top:8px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border:none;border-radius:8px;padding:9px 22px;font-size:13px;font-weight:600;cursor:pointer;transition:opacity .2s}
.db-project-btn:hover{opacity:.9}
.db-project-preview{display:block}
.db-project-details{display:none;padding-top:14px;margin-top:12px;border-top:1px solid #eee}
.db-project-details p{font-size:13px;color:#555;line-height:1.6;margin:0 0 10px}
.db-project-details ul{margin:0;padding:0;list-style:none}
.db-project-details ul li{font-size:13px;color:#555;padding:3px 0 3px 18px;position:relative;line-height:1.4}
.db-project-details ul li::before{content:'•';position:absolute;left:4px;color:#F77C2A;font-weight:700}
.db-project-card.expanded .db-project-preview{display:none}
.db-project-card.expanded .db-project-details{display:block}
.db-project-card.expanded img{height:140px}
.db-project-card.expanded{flex:0 0 360px}

.db-weld-note{margin-top:12px;padding:8px 14px;background:linear-gradient(135deg,#2b2b39,#1a1a26);color:#fff;border-radius:8px;font-size:12px;font-weight:600;display:inline-block}
.section-desc{font-size:13px;color:#888;margin:-10px 0 20px;line-height:1.5}
.db-wrap{max-width:1200px;margin:0 auto;padding:0 24px}
.db-section{padding:64px 0}
.db-section.alt{background:#fff}
.db-section+.db-section{padding-top:0!important}
.db-section-title{font-size:26px;font-weight:800;color:inherit;text-align:center;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px}
.db-section-sub{font-size:14px;color:inherit;opacity:.55;text-align:center;margin-bottom:36px;max-width:620px;margin-left:auto;margin-right:auto;line-height:1.6}
.db-section-line{width:48px;height:3px;background:#F77C2A;border-radius:2px;margin:0 auto 14px}
@media(max-width:1024px){.db-section-title{font-size:22px}.db-aisi-grid h2{font-size:20px}.db-aisi-grid,.db-prod-grid{grid-template-columns:1fr;gap:24px}.db-prod-grid img{display:block;margin:0 auto;max-width:90%}.db-prod-equip{grid-template-columns:repeat(3,1fr)}.db-row-inner{grid-template-columns:1fr;gap:20px}.db-row-inner.rev{direction:ltr}}
@media(max-width:700px){.db-row-section{padding:32px 0}.db-prod-stats{gap:16px}.db-prod-stat .num{font-size:28px}.db-prod-equip{grid-template-columns:repeat(2,1fr)}.db-prod-equip-card .eq-img{height:110px!important;padding:8px!important}.db-prod-equip-card .eq-body{padding:6px 8px 8px!important}.db-prod-equip-card .eq-name{font-size:12px!important;margin-bottom:2px!important}.db-prod-equip-card .eq-spec{font-size:11px!important}.db-arm-gallery{gap:8px}.db-project-card{flex:0 0 260px}.db-project-card img{height:160px}}
@media(max-width:480px){.db-section-title{font-size:16px}.db-section-sub{font-size:12px}.db-prod-stats{flex-direction:column;gap:14px}.db-prod-equip{gap:8px!important}.db-prod-equip-card .eq-img{height:90px!important;padding:6px!important}.db-prod-equip-card .eq-body{padding:4px 6px 6px!important}.db-prod-equip-card .eq-name{font-size:10px!important;margin-bottom:0!important}.db-prod-equip-card .eq-spec{font-size:9px!important}.db-prod-equip-title{font-size:12px!important}.db-prod-equip-section{margin-top:20px!important}.cat-card-img{height:130px!important}.cat-card-body .cat-desc{display:block!important;font-size:11px!important;line-height:1.3!important}.cat-card-body .cat-name{font-size:13px!important}}
</style>
<section class="main-hero">
<div class="container">
<div class="breadcrumbs">
<a href="/">Главная</a><span class="sep">•</span>
<span class="current">Каталог оборудования</span>
</div>
<div class="hero-text">
<h1>Каталог оборудования</h1>
<div class="title-accent"></div>
<p><?= htmlspecialchars($metaDesc) ?></p>
</div>
<div class="hero-adv-grid">
<div class="hero-adv-card"><div class="adv-title">Собственное производство</div><div class="adv-desc">Изготовим под ваш проект в Краснодаре</div></div>
<div class="hero-adv-card"><div class="adv-title">AISI 304/316</div><div class="adv-desc">Пищевая нержавеющая сталь с полным контролем качества</div></div>
<div class="hero-adv-card"><div class="adv-title">Любые объёмы</div><div class="adv-desc">От 100 до 200 000 литров — под ваши задачи</div></div>
<div class="hero-adv-card"><div class="adv-title">Доставка по РФ</div><div class="adv-desc">Отгружаем во все регионы, СНГ и на экспорт</div></div>
</div>
<div class="hero-cta-wrap">
<a href="/#order-form" class="hero-cta-btn">Поможем выбрать оборудование <span class="arr">→</span></a>
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

<!-- STEEL AISI -->
<section class="db-section alt" id="about">
<div class="db-wrap">
<div class="db-section-line"></div>
<h2 class="db-section-title">Сталь AISI 304/316</h2>
<p class="db-section-sub">Высококачественная нержавеющая сталь для пищевой промышленности</p>
<div class="db-aisi-grid">
<div class="aisi-gallery-col">
<div class="aisi-gallery" id="aisiGallery">

<div class="aisi-slide active" data-idx="0">
<div class="img-wrap"><img src="/aisi-steel-tanks.png" alt="Промышленные емкости из нержавеющей стали" loading="lazy"></div>
</div>
<div class="aisi-slide" data-idx="1" data-name="Гидроциклонный аппарат (Whirlpool)" data-vol="250–10 000 л" data-link="/catalog/beer/brew-house/">
<div class="img-wrap"><img data-src="/beer-whirlpool.jpg" alt="Гидроциклонный аппарат"></div>
</div>
<div class="aisi-slide" data-idx="2" data-name="Бак горячей воды" data-vol="250–50 000 л" data-link="/catalog/beer/hot-water-tank/">
<div class="img-wrap"><img data-src="/hot-water-tank.jpg" alt="Бак горячей воды"></div>
</div>
<div class="aisi-slide" data-idx="3" data-name="Фильтрационный аппарат (Фильтрчан)" data-vol="250–10 000 л" data-link="/catalog/beer/brew-house/">
<div class="img-wrap"><img data-src="/lauter-tun.jpg" alt="Фильтрационный аппарат"></div>
</div>
<div class="aisi-slide" data-idx="4" data-name="Танк холодной стабилизации" data-vol="500–50 000 л" data-link="/catalog/wine/cold-stabilization/">
<div class="img-wrap"><img data-src="/wine-cold-stabilization.jpg" alt="Танк холодной стабилизации"></div>
</div>
</div>
<div class="aisi-caption" id="aisiCaption" style="visibility:hidden">
<span class="cap-name"></span><span class="cap-vol"></span><a href="" class="cap-link">→</a>
</div>
</div>
<div>
<p>Мы специализируемся на производстве промышленных емкостей вертикального и горизонтального типа объёмом от 250 литров до 300 м³. Вся продукция изготавливается из пищевой нержавеющей стали AISI 304 и AISI 316.</p>
<ul class="db-aisi-features">
<li>Соответствие стандартам пищевой безопасности</li>
<li>Полная герметичность и гигиеничность конструкций</li>
<li>Устойчивость к температурным перепадам</li>
<li>Индивидуальные решения под ваш техпроцесс</li>
</ul>
</div>
</div>
</div>
</section>

<!-- QUIZ: 3-step equipment finder -->
<section class="db-section alt" style="background:linear-gradient(180deg,#fff,#f8f9fb)">
<div class="db-wrap" style="max-width:800px;margin:0 auto">
<h2 class="db-section-title">🔧 Быстрый подбор оборудования</h2>
<p class="db-section-sub" style="max-width:600px">Введите название, выберите объём и получите цену за 2 минуты — без звонков</p>

<div style="text-align:center;margin-bottom:32px">
<span style="display:inline-block;padding:8px 24px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border-radius:20px;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(247,124,42,.25)">🔥 Без звонков и ожидания — цена сразу</span>
</div>

<div class="db-quiz-steps" style="display:flex;justify-content:center;align-items:center;gap:0;margin-bottom:32px">
<div class="db-qstep active" id="qs1"><span class="qnum">1</span> Выбор</div>
<span class="qline"></span>
<div class="db-qstep" id="qs2"><span class="qnum">2</span> Объём</div>
<span class="qline"></span>
<div class="db-qstep" id="qs3"><span class="qnum">3</span> Цена</div>
</div>

<div class="db-qcard active" id="qc1">
<div class="db-qsearch"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2.5" stroke-linecap="round" style="flex-shrink:0;margin:0 8px 0 14px"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
<input type="text" id="quizInput" placeholder="ЦКТ, БГВ, ферментатор, сыроизготовитель..." autocomplete="off" style="flex:1;border:none;background:transparent;padding:14px 8px;font-size:16px;outline:none;font-family:inherit;color:#333"></div>
<div id="quizResults" style="background:#fff;border:1px solid #e8e8e8;border-radius:12px;display:none;box-shadow:0 8px 32px rgba(0,0,0,.1);padding:8px;overflow-y:auto;max-height:800px"></div>
<div style="text-align:center;margin-top:12px"><span id="catToggle" style="color:#F77C2A;font-size:13px;cursor:pointer;font-weight:600" onclick="toggleCatList()">📋 Или выберите из каталога ↓</span></div>
<div id="catList" style="display:none;margin-top:12px;padding:16px;background:#f8f9fb;border-radius:12px">
<div class="quiz-cat-group"><div class="quiz-cat-label" style="font-size:12px;font-weight:700;color:#F77C2A;margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px">🍺 Пивоваренное</div>
<div class="quiz-cat-items" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
<a onclick="pickCat('beer','cct')" class="quiz-cat-chip">ЦКТ</a>
<a onclick="pickCat('beer','brew-house')" class="quiz-cat-chip">Варочные порядки</a>
<a onclick="pickCat('beer','hot-water-tank')" class="quiz-cat-chip">Баки горячей воды</a>
<a onclick="pickCat('beer','grain-mill')" class="quiz-cat-chip">Дробилки солода</a>
<a onclick="pickCat('beer','steam-generator')" class="quiz-cat-chip">Парогенераторы</a>
<a onclick="pickCat('beer','chiller')" class="quiz-cat-chip">Чиллеры</a>
<a onclick="pickCat('beer','unitank')" class="quiz-cat-chip">Форфасы (BBT)</a>
<a onclick="pickCat('beer','heat-exchanger')" class="quiz-cat-chip">Теплообменники</a>
</div></div>
<div class="quiz-cat-group"><div class="quiz-cat-label" style="font-size:12px;font-weight:700;color:#F77C2A;margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px">🥛 Молочное</div>
<div class="quiz-cat-items" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
<a onclick="pickCat('dairy','reception')" class="quiz-cat-chip">Ёмкость приёмки молока</a>
<a onclick="pickCat('dairy','cooler')" class="quiz-cat-chip">Резервуар-охладитель</a>
<a onclick="pickCat('dairy','storage')" class="quiz-cat-chip">Резервуар хранения</a>
<a onclick="pickCat('dairy','vdp')" class="quiz-cat-chip">Ванна пастеризации (ВДП)</a>
<a onclick="pickCat('dairy','fermentation')" class="quiz-cat-chip">Ферментационный танк</a>
<a onclick="pickCat('dairy','cheese-maker')" class="quiz-cat-chip">Сыроизготовитель</a>
<a onclick="pickCat('dairy','cottage-cheese')" class="quiz-cat-chip">Творогоизготовитель</a>
<a onclick="pickCat('dairy','yeast')" class="quiz-cat-chip">Заквасочник</a>
<a onclick="pickCat('dairy','brine')" class="quiz-cat-chip">Контейнер для соления</a>
<a onclick="pickCat('dairy','cheese-shelves')" class="quiz-cat-chip">Стеллажи для сыра</a>
</div></div>
<div class="quiz-cat-group"><div class="quiz-cat-label" style="font-size:12px;font-weight:700;color:#F77C2A;margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px">🍷 Винодельческое</div>
<div class="quiz-cat-items" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
<a onclick="pickCat('wine','red-fermentation')" class="quiz-cat-chip">Ферментация красных вин</a>
<a onclick="pickCat('wine','white-fermentation')" class="quiz-cat-chip">Ферментация белых вин</a>
<a onclick="pickCat('wine','storage-aging')" class="quiz-cat-chip">Выдержка и хранение</a>
<a onclick="pickCat('wine','cold-stabilization')" class="quiz-cat-chip">Холодная стабилизация</a>
<a onclick="pickCat('wine','blending')" class="quiz-cat-chip">Купажирование</a>
<a onclick="pickCat('wine','sulfitation')" class="quiz-cat-chip">Сульфитация</a>
<a onclick="pickCat('wine','universal-tank')" class="quiz-cat-chip">Винификатор (УТТ)</a>
</div></div>
<div class="quiz-cat-group"><div class="quiz-cat-label" style="font-size:12px;font-weight:700;color:#F77C2A;margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px">🏭 Промышленное</div>
<div class="quiz-cat-items" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:4px">
<a onclick="pickCat('industrial','storage')" class="quiz-cat-chip">Резервуар для хранения</a>
<a onclick="pickCat('industrial','mixing')" class="quiz-cat-chip">Ёмкость с мешалкой</a>
<a onclick="pickCat('industrial','thermal')" class="quiz-cat-chip">Ёмкость с терморегуляцией</a>
<a onclick="pickCat('industrial','pressure')" class="quiz-cat-chip">Ёмкость под давлением</a>
</div></div>
</div>
</div>

<div class="db-qcard" id="qc2" style="text-align:center;background:#fff">
<div style="margin-bottom:16px">
<img id="qImg2" src="" alt="" style="width:144px;height:144px;object-fit:contain;border-radius:16px;background:#fff;flex-shrink:0;display:none;margin:0 auto">
<div style="margin-top:8px"><div style="font-size:13px;color:#888;margin-bottom:2px">Выбрано:</div>
<strong id="qSelName" style="color:#F77C2A;font-size:18px"></strong> <a style="color:#F77C2A;font-size:12px;cursor:pointer;margin-left:6px" onclick="quizBack()">(изменить)</a></div>
<div id="qDesc2" style="font-size:13px;color:#666;margin-top:8px;line-height:1.4;display:none"></div>
<ul id="qFeats2" style="display:none;list-style:none;margin:8px 0 0;padding:0;gap:4px;flex-wrap:wrap;justify-content:center"></ul></div>
<div class="db-qvolgrid" id="qVolGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px"></div>
<div style="margin-top:12px;font-size:13px;color:#999;text-align:center">
Не нашли объём? <a style="color:#F77C2A;cursor:pointer;font-weight:600" onclick="document.getElementById('qCustomVol').style.display='flex';this.style.display='none'">Укажите свой</a>
</div>
<div id="qCustomVol" style="display:none;margin-top:10px;gap:8px">
<input type="number" id="qCustomVal" placeholder="Ваш объём, л" style="flex:1;padding:10px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:14px;font-family:inherit;outline:none">
<button style="padding:10px 20px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit" onclick="selectQuizVol(0,0)">Выбрать</button>
</div>
</div>

<div class="db-qcard" id="qc3">
<div style="display:flex;align-items:flex-start;gap:20px;margin-bottom:16px">
<img id="qImg3" src="" alt="" style="width:180px;height:180px;border-radius:16px;object-fit:contain;background:#fff;flex-shrink:0">
<div style="flex:1;min-width:0"><div style="font-weight:700;color:#1a1a26;font-size:15px" id="qResName"></div><div style="font-size:13px;color:#888" id="qResVol"></div>
<div id="qDesc3" style="font-size:13px;color:#666;margin-top:6px;line-height:1.4;display:none"></div>
<ul id="qFeats3" style="display:none;list-style:none;margin:6px 0 0;padding:0;gap:4px;flex-wrap:wrap"></ul></div></div>
<div class="db-qprice" id="qPrice" style="display:none;background:linear-gradient(135deg,#fff8f0,#fff);border:2px solid #F77C2A;border-radius:12px;padding:20px;text-align:center;margin-bottom:20px">
<div style="font-size:12px;color:#888;margin-bottom:4px">💰 Ориентировочная цена с НДС</div>
<div style="font-size:36px;font-weight:900;color:#F77C2A;letter-spacing:-1px;line-height:1" id="qPriceVal"></div>
<div style="font-size:11px;color:#bbb;margin-top:4px">Точная цена — после расчёта инженером</div>
</div>
<form method="post" action="/php/send.php">
<input type="hidden" name="form_type" value="item">
<input type="hidden" name="product" id="qFormProduct" value="">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
<div><input type="text" name="name" placeholder="Имя" required style="width:100%;padding:10px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:14px;font-family:inherit;outline:none"></div>
<div><input type="tel" name="phone" placeholder="Телефон" required style="width:100%;padding:10px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:14px;font-family:inherit;outline:none"></div>
<div style="grid-column:1/-1"><input type="email" name="email" placeholder="Email для КП" style="width:100%;padding:10px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:14px;font-family:inherit;outline:none"></div>
<div style="grid-column:1/-1"><textarea name="comment" placeholder="Дополнительные требования..." style="width:100%;padding:10px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;min-height:56px;resize:vertical"></textarea></div>
</div>
<button type="submit" class="submit-btn" style="margin-top:12px">📩 Получить КП с точной ценой</button>
</form>
<div style="display:flex;justify-content:center;gap:16px;margin-top:12px;font-size:11px;color:#bbb;flex-wrap:wrap">
<span>🔒 Конфиденциально</span> <span>⚡ Ответ за 2 часа</span> <span>📋 Бесплатный расчёт</span>
</div>
</div>

<script>
var quizTimer = null;
var quizSelected = null;
var quizPrices = {};

// Category image/desc/feat mapping (same as test.php)
var QIMAGES = {'cct':'cct-tank.jpg','hot-water-tank':'hot-water-tank.jpg','reception':'dairy-reception.jpg','storage':'dairy-storage.jpg','vdp':'dairy-vdp.jpg','fermentation':'dairy-fermentation.jpg','cheese-maker':'dairy-cheese-maker.jpg','universal-tank':'wine-universal-tank.jpg','red-fermentation':'wine-red-fermentation.jpg','mixing':'industrial-mixing.jpg','thermal':'industrial-thermal.jpg','pressure':'industrial-pressure.jpg'};
var QDEFAULTS = {beer:'cct-tank.jpg',dairy:'dairy-reception.jpg',wine:'wine-red-fermentation.jpg',industrial:'industrial-cip.jpg'};
var QDESC={'cct':'Цилиндро-конические танки для брожения, дображивания и лагеризации пива из нержавеющей стали AISI 304','hot-water-tank':'Баки горячей воды из нержавейки для пивоварения','reception':'Ёмкости для приёмки и фильтрации молока','storage':'Резервуары для хранения молока','vdp':'Ванны длительной пастеризации','fermentation':'Танки для ферментации йогурта, сметаны, кефира','cheese-maker':'Аппараты для производства сыра','universal-tank':'Универсальные терморегулируемые танки для виноделия','cooler':'Резервуары-охладители молока','cottage-cheese':'Танки для производства творога','yeast':'Заквасочные аппараты','brine':'Контейнеры для соления сыра','cheese-shelves':'Стеллажи для созревания сыра','red-fermentation':'Ферментационные танки для красных вин','white-fermentation':'Ферментационные танки для белых вин','storage-aging':'Ёмкости для выдержки и хранения вина','cold-stabilization':'Танки криостабилизации','mixing':'Ёмкости с мешалкой','thermal':'Ёмкости с терморегуляцией','pressure':'Ёмкости под давлением','cip':'CIP-станции','heat-exchanger':'Промышленные теплообменники'};
var QFEATS={'cct':['AISI 304/316','до 4 зон охлаждения','угол конуса 60-70°','CIP-мойка'],'hot-water-tank':['Паровой нагрев','Термоизоляция ППУ','Люк-лаз DN400'],'reception':['Фильтр грубой очистки','Люк-лаз DN400','CIP-мойка'],'storage':['Объём до 200 000 л','Термоизоляция','CIP-мойка'],'vdp':['AISI 304','Ручная/автоматика','Сливной кран'],'fermentation':['Рубашка охлаждения','AISI 304','CIP-мойка'],'cheese-maker':['Плавный нагрев','AISI 304/316','Мешалка'],'universal-tank':['AISI 304/316','Рубашка охлаждения','2 зоны терморегуляции'],'cooler':['Охлаждение до 4°C','AISI 304','Автоматика'],'cottage-cheese':['AISI 304','Мешалка','Слив'],'red-fermentation':['AISI 304/316','Рубашка охлаждения','Гребнеотделитель'],'white-fermentation':['AISI 304/316','Термоизоляция','Фильтр'],'storage-aging':['AISI 304/316','Объём до 200 000 л','CIP-мойка'],'cold-stabilization':['AISI 304/316','Термоизоляция','Автоматика'],'mixing':['AISI 304/316','Мешалка','CIP-мойка'],'thermal':['AISI 304/316','Рубашка','Термоизоляция'],'pressure':['AISI 304/316','До 10 бар','Герметичность'],'cip':['AISI 304','Автоматика','Насосы Alfa Laval']};
function qImg(k,s){return '/'+(QIMAGES[k]||QDEFAULTS[s]||'cct-tank.jpg');}
function qFeats(k){return QFEATS[k]||['AISI 304','Гарантия 12 мес','Доставка по РФ'];}
function fmtP(p){return p>=1000000?(p/1000000).toFixed(1)+' млн ₽':(p>=1000?Math.round(p/1000)+' тыс ₽':p+' ₽');}
function toggleCatList(){var el=document.getElementById('catList'),t=document.getElementById('catToggle');if(el.style.display==='none'||!el.style.display){el.style.display='block';t.innerHTML='📋 Или выберите из каталога ↑'}else{el.style.display='none';t.innerHTML='📋 Или выберите из каталога ↓'}}
function pickCat(si,slug){var names={'cct':'ЦКТ (Цилиндро-конические танки)','brew-house':'Варочные порядки','hot-water-tank':'Бак горячей воды','grain-mill':'Дробилка солода','steam-generator':'Парогенератор','chiller':'Чиллер','unitank':'Форфас (BBT)','heat-exchanger':'Теплообменник пластинчатый','reception':'Ёмкость приёмки молока','cooler':'Резервуар-охладитель молока','storage':'Резервуар для хранения молока','vdp':'Ванна длительной пастеризации (ВДП)','fermentation':'Ферментационный танк','cheese-maker':'Сыроизготовитель','cottage-cheese':'Творогоизготовитель','yeast':'Заквасочник','brine':'Контейнер для соления сыра','cheese-shelves':'Стеллажи для созревания сыра','red-fermentation':'Ферментационный танк для красных вин','white-fermentation':'Ферментационный танк для белых вин','storage-aging':'Ёмкость для выдержки и хранения вина','cold-stabilization':'Танк холодной стабилизации','blending':'Ёмкость для купажирования','sulfitation':'Ёмкость сульфитации','universal-tank':'Винификатор (УТТ)','mixing':'Ёмкость с мешалкой','thermal':'Ёмкость с терморегуляцией','pressure':'Ёмкость под давлением'};
var urls={'cct':'/catalog/beer/cct/','brew-house':'/catalog/beer/brew-house/','hot-water-tank':'/catalog/beer/hot-water-tank/','grain-mill':'/catalog/beer/grain-mill/','steam-generator':'/catalog/beer/steam-generator/','chiller':'/catalog/beer/chiller/','unitank':'/catalog/beer/unitank/','heat-exchanger':'/catalog/beer/heat-exchanger/','reception':'/catalog/dairy/reception/','cooler':'/catalog/dairy/cooler/','storage':'/catalog/dairy/storage/','vdp':'/catalog/dairy/vdp/','fermentation':'/catalog/dairy/fermentation/','cheese-maker':'/catalog/dairy/cheese-maker/','cottage-cheese':'/catalog/dairy/cottage-cheese/','yeast':'/catalog/dairy/yeast/','brine':'/catalog/dairy/brine/','cheese-shelves':'/catalog/dairy/cheese-shelves/','red-fermentation':'/catalog/wine/red-fermentation/','white-fermentation':'/catalog/wine/white-fermentation/','storage-aging':'/catalog/wine/storage-aging/','cold-stabilization':'/catalog/wine/cold-stabilization/','blending':'/catalog/wine/blending/','sulfitation':'/catalog/wine/sulfitation/','universal-tank':'/catalog/wine/universal-tank/','mixing':'/catalog/industrial/mixing/','thermal':'/catalog/industrial/thermal/','pressure':'/catalog/industrial/pressure/'};
var secNames={beer:'Пивоваренное оборудование',dairy:'Молочное оборудование',wine:'Винодельческое оборудование',industrial:'Промышленное оборудование'};
var r={n:names[slug]||slug,u:urls[slug]||'/'+slug,si:si,s:secNames[si]||''};toggleCatList();selectQuiz(r);}
function nextQStep(){
    var step = quizStep;
    ['qs1','qs2','qs3'].forEach(function(id,i){
        var el=document.getElementById(id);
        el.classList.toggle('active',i===step);
        el.classList.toggle('done',i<step);
    });
    ['qc1','qc2','qc3'].forEach(function(id,i){
        document.getElementById(id).classList.toggle('active',i===step);
    });
    quizStep++;
    window.scrollTo({top:document.querySelector('.db-qcard.active').offsetTop-120,behavior:'smooth'});
}
var quizStep=1;
document.getElementById('quizInput').addEventListener('input',function(){
    clearTimeout(quizTimer);var q=this.value.trim();var res=document.getElementById('quizResults');
    if(!q){res.style.display='none';return;}
    quizTimer=setTimeout(function(){
        fetch('/php/search.php?q='+encodeURIComponent(q)).then(function(r){return r.json();}).then(function(d){
            res.innerHTML='';
            var items=d.results.filter(function(r){return !r.u.match(/\/(\d+)l?\/?$/);});
            var seen={};items=items.filter(function(r){var k=r.u;if(seen[k])return false;seen[k]=true;return true;});
            if(!items.length){res.innerHTML='<div style="padding:16px;text-align:center;color:#999;font-size:13px">Ничего не найдено</div>';res.style.display='block';return;}
            items.forEach(function(r){
                var k=r.u.split('/').filter(Boolean).pop();
                if(k.match(/^\d+l?$/))k=r.u.split('/').filter(Boolean).slice(-2,-1)[0];
                var im=qImg(k,r.si),feats=qFeats(k);
                var fh='';feats.slice(0,3).forEach(function(f){fh+='<li style="font-size:12px;padding:2px 0">✓ '+f+'</li>';});
                var div=document.createElement('div');
                div.style.cssText='display:flex;gap:24px;padding:20px;cursor:pointer;border-radius:12px;transition:background .15s;margin-bottom:8px';
                div.onmouseover=function(){this.style.background='#fff8f0';};
                div.onmouseout=function(){this.style.background='';};
                div.innerHTML='<img src="'+im+'" style="width:240px;height:240px;border-radius:16px;object-fit:contain;background:#fff;flex-shrink:0" onerror="this.style.display=\'none\'"><div style="flex:1;min-width:0"><div style="font-weight:700;font-size:15px;color:#1a1a26;margin-bottom:4px">'+r.n+'</div><div style="font-size:12px;color:#888;margin-bottom:4px">'+(QDESC[k]||r.s)+'</div><ul style="font-size:12px;color:#888;margin:0;padding:0;list-style:none">'+fh+'</ul></div><button style="align-self:center;padding:8px 18px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;flex-shrink:0">Выбрать</button>';
                div.querySelector('button').onclick=function(e){e.stopPropagation();selectQuiz(r);};
                div.onclick=function(){selectQuiz(r);};
                res.appendChild(div);
            });
            res.style.display='block';
        }).catch(function(){});
    },300);
});
document.getElementById('quizInput').addEventListener('blur',function(){setTimeout(function(){document.getElementById('quizResults').style.display='none';},300);});

function selectQuiz(r){
    document.getElementById('quizInput').value=r.n;document.getElementById('quizResults').style.display='none';quizSelected=r;
    var k=r.u.split('/').filter(Boolean).pop();if(k.match(/^\d+l?$/))k=r.u.split('/').filter(Boolean).slice(-2,-1)[0];
    var im=qImg(k,r.si);document.getElementById('qImg2').src=im;document.getElementById('qImg2').style.display='block';
    document.getElementById('qSelName').textContent=r.n;
    document.getElementById('qVolGrid').innerHTML='<div style="text-align:center;padding:24px;color:#999;font-size:13px">Загружаем цены...</div>';
    var desc=QDESC[k]||'',feats=qFeats(k);
    var d2=document.getElementById('qDesc2');d2.textContent=desc;d2.style.display=desc?'block':'none';
    var f2=document.getElementById('qFeats2');f2.innerHTML='';f2.style.display=feats.length?'flex':'none';feats.slice(0,4).forEach(function(f){var li=document.createElement('li');li.style.cssText='display:inline-flex;align-items:center;gap:4px;font-size:12px;color:#666;background:#f5f7fa;padding:4px 10px;border-radius:6px';li.innerHTML='✓ '+f;f2.appendChild(li);});
    var d3=document.getElementById('qDesc3');d3.textContent=desc;d3.style.display=desc?'block':'none';
    var f3=document.getElementById('qFeats3');f3.innerHTML='';f3.style.display=feats.length?'flex':'none';feats.slice(0,4).forEach(function(f){var li=document.createElement('li');li.style.cssText='display:inline-flex;align-items:center;gap:4px;font-size:12px;color:#666;background:#f5f7fa;padding:4px 10px;border-radius:6px';li.innerHTML='✓ '+f;f3.appendChild(li);});
    nextQStep();
    var u=new URL(r.u,location.origin);var pp=u.pathname.split('/').filter(Boolean);
    var last=pp[pp.length-1];var ck=last.match(/^\d+l?$/)?pp[pp.length-2]:last;
    var sm={beer:'beerExtra',dairy:'dairyData',wine:'wineData',industrial:'industrialData'};var src=sm[r.si]||'';
    if(pp.includes('brew-house'))src='brewData';if(pp.includes('cct'))src='cctData';
    fetch('?get_prices='+encodeURIComponent(ck)+'&src='+src).then(function(r){return r.json();}).then(function(d){
        quizPrices=d;var g=document.getElementById('qVolGrid');g.innerHTML='';
        if(!d.prices||!d.prices.length){g.innerHTML='<div style="text-align:center;padding:24px;color:#999">Нет данных о ценах</div>';return;}
        d.prices.sort(function(a,b){return a.vol-b.vol;});
        d.prices.forEach(function(p){
            var b=document.createElement('button');
            b.style.cssText='padding:10px;border:2px solid #e0e0e0;border-radius:8px;background:#fff;cursor:pointer;text-align:center;font-family:inherit;font-size:13px;font-weight:600;color:#333;transition:all .2s';
            b.innerHTML='<span style="font-size:16px;font-weight:800;color:#1a1a26;display:block">'+p.vol+'</span><span style="font-size:11px;color:#999">л</span>';
            b.onmouseover=function(){this.style.borderColor='#F77C2A';this.style.transform='translateY(-2px)';};
            b.onmouseout=function(){if(!this.classList.contains('sel')){this.style.borderColor='#e0e0e0';this.style.transform='';}};
            b.onclick=function(){
                g.querySelectorAll('button').forEach(function(b2){b2.classList.remove('sel');b2.style.background='';b2.style.color='#333';b2.querySelector('span').style.color='#1a1a26';});
                b.classList.add('sel');b.style.background='#F77C2A';b.style.color='#fff';b.querySelector('span').style.color='#fff';
                qShowPrice(d,p.vol,p.price);
            };
            g.appendChild(b);
        });
        // custom button
        var cb=document.createElement('button');
        cb.style.cssText='padding:10px;border:2px dashed #e0e0e0;border-radius:8px;background:#fff;cursor:pointer;text-align:center;font-family:inherit;font-size:13px;font-weight:600;color:#333;transition:all .2s';
        cb.innerHTML='<span style="font-size:14px;display:block">Свой</span><span style="font-size:11px;color:#999">объём</span>';
        cb.onclick=function(){quizCustom();};
        g.appendChild(cb);
    }).catch(function(){document.getElementById('qVolGrid').innerHTML='<div style="text-align:center;padding:24px;color:#e74c3c">Ошибка загрузки</div>';});
}

function qShowPrice(d,vol,price){
    var vs=vol>0?vol+' л':'нестандартный объём';
    document.getElementById('qFormProduct').value=d.name+' '+vs;
    document.getElementById('qResName').textContent=d.name;
    document.getElementById('qResVol').textContent=vs;
    var im=document.getElementById('qImg2').src;document.getElementById('qImg3').src=im;
    if(price>0){document.getElementById('qPriceVal').textContent='от '+fmtP(price);document.getElementById('qPrice').style.display='block';}
    else{document.getElementById('qPrice').style.display='block';document.getElementById('qPriceVal').textContent='По запросу';}
    nextQStep();
}
function quizCustom(){var v=parseInt(document.getElementById('qCustomVal').value);if(!v||v<=0){document.getElementById('qCustomVal').focus();return;}qShowPrice(quizPrices,v,0);}
function quizBack(){quizStep=1;
    ['qs1','qs2','qs3'].forEach(function(id,i){
        document.getElementById(id).classList.toggle('active',i===0);
        document.getElementById(id).classList.remove('done');
    });
    ['qc1','qc2','qc3'].forEach(function(id,i){
        document.getElementById(id).classList.toggle('active',i===0);
    });
    window.scrollTo({top:document.querySelector('.db-qcard.active').offsetTop-120,behavior:'smooth'});
}
</script>

<style>
.db-qstep{display:flex;align-items:center;gap:6px;font-size:13px;color:#ccc;font-weight:600;padding:6px 14px;border-radius:20px;transition:.3s}
.db-qstep .qnum{width:26px;height:26px;border-radius:50%;background:#e8e8e8;color:#bbb;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;transition:.3s;flex-shrink:0}
.db-qstep.active{color:#F77C2A}
.db-qstep.active .qnum{background:#F77C2A;color:#fff}
.db-qstep.done{color:#27ae60}
.db-qstep.done .qnum{background:#27ae60;color:#fff}
.qline{width:30px;height:2px;background:#e8e8e8;flex-shrink:0}
.db-qstep.done+.qline{background:#27ae60}
.db-qcard{background:#fff;border-radius:14px;padding:28px;border:1px solid #eee;box-shadow:0 2px 12px rgba(0,0,0,.04);margin-bottom:24px;display:none}
.db-qcard.active{display:block}
.db-qsearch{display:flex;align-items:center;background:#fff;border:2px solid #e0e0e0;border-radius:10px;transition:border-color .25s}
.db-qsearch:focus-within{border-color:#F77C2A}
.quiz-cat-chip{display:inline-block;padding:6px 14px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;font-size:12px;font-weight:500;color:#333;cursor:pointer;transition:all .15s;text-decoration:none}
.quiz-cat-chip:hover{background:#fff4e6;border-color:#F77C2A;color:#F77C2A}
.submit-btn{display:block;width:100%;padding:14px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:700;cursor:pointer;transition:opacity .2s;font-family:inherit}
.submit-btn:hover{opacity:.9}
</style>
</div>
</section>

<!-- PRODUCTION CAPACITY -->
<section class="db-section">
<div class="db-wrap">
<div class="db-section-line"></div>
<h2 class="db-section-title">Производственные мощности</h2>
<p class="db-section-sub">Крупнейшее предприятие в ЮФО по выпуску промышленных резервуаров, теплообменников и сосудов под давлением</p>
<div class="db-prod-grid">
<div class="db-prod-text">
<p>Наш цех был основан в 2008 году. Сегодня мы — крупнейшее предприятие в ЮФО, выпускающее промышленные резервуары, теплообменники и сосуды под давлением. Собственное производство площадью 2000 м² позволяет контролировать качество на всех этапах.</p>
<p>Мы используем лазерную резку, аргонодуговую сварку, вальцовку днищ — полный цикл металлообработки. Каждое изделие проходит гидравлические испытания перед отгрузкой.</p>
<div class="db-prod-stats">
<div class="db-prod-stat"><div class="num">17</div><div class="lbl">лет работы</div></div>
<div class="db-prod-stat"><div class="num">60+</div><div class="lbl">высококвалифицированных мастеров</div></div>
<div class="db-prod-stat"><div class="num">2000 м²</div><div class="lbl">собственное производство</div></div>
</div>
<button class="db-aisi-btn" style="margin-top:20px" onclick="document.getElementById('order-form').scrollIntoView({behavior:'smooth'})">Записаться на экскурсию →</button>
</div>
<img src="/production-facility.png" alt="Производственный цех" loading="lazy">
</div>
<div class="db-prod-equip-section">
<div class="db-prod-equip-title">Наше оборудование</div>
<div class="db-prod-equip">
<div class="db-prod-equip-card"><div class="eq-overlay"></div><div class="eq-img"><img src="/equipment/ostas-mill.jpg" alt="Вальцы OSTAS"></div><div class="eq-body"><div class="eq-name">Вальцы OSTAS 4R-OHS</div><div class="eq-spec">2570×250 мм</div></div></div>
<div class="db-prod-equip-card"><div class="eq-overlay"></div><div class="eq-img"><img src="/equipment/hydraulic-press.jpg" alt="Гидравлический пресс"></div><div class="eq-body"><div class="eq-name">Гидравлический пресс с ЧПУ</div><div class="eq-spec">гибка листового металла</div></div></div>
<div class="db-prod-equip-card"><div class="eq-overlay"></div><div class="eq-img"><img src="/equipment/cnc-guillotine.jpg" alt="Гильотина с ЧПУ"></div><div class="eq-body"><div class="eq-name">Гильотина с ЧПУ</div><div class="eq-spec">рез 3500×10 мм</div></div></div>
<div class="db-prod-equip-card"><div class="eq-overlay"></div><div class="eq-img"><img src="/equipment/band-saw.jpg" alt="Ленточнопильный станок"></div><div class="eq-body"><div class="eq-name">Ленточнопильный станок</div><div class="eq-spec">резка профильной и круглой трубы</div></div></div>
<div class="db-prod-equip-card"><div class="eq-overlay"></div><div class="eq-img"><img src="/equipment/laser-welding.jpg" alt="Лазерная сварка"></div><div class="eq-body"><div class="eq-name">Аппарат лазерной сварки</div><div class="eq-spec">точность до 0,1 мм</div></div></div>
</div>
</div>
</div>
</section>

<!-- ARMATURE — Навесная арматура -->
<section class="db-row-section">
<div class="db-wrap">
<div class="db-section-line"></div>
<h2 class="db-section-title">Навесная арматура</h2>
<p class="db-section-sub" style="margin-bottom:24px!important">Комплектующие ведущих европейских брендов: насосы, клапаны, шпунт-аппараты и мешалки для пищевого оборудования</p>
<div class="db-arm-gallery">
<div class="db-arm-gallery-item"><img src="/inoxpa-pump.jpg" alt="Насосы" loading="lazy"></div>
<div class="db-arm-gallery-item"><img src="/inoxpa-valves.jpg" alt="Клапаны" loading="lazy"></div>
<div class="db-arm-gallery-item"><img src="/inoxpa-schunt.jpg" alt="Шпунт-аппараты" loading="lazy"></div>
<div class="db-arm-gallery-item"><img src="/inoxpa-mixers.jpg" alt="Мешалки" loading="lazy"></div>
</div>
</div>
</section>

<!-- PROJECTS -->
<section class="db-section alt" id="projects">
<div class="db-wrap">
<div class="db-section-line"></div>
<h2 class="db-section-title">Примеры нашего оборудования</h2>
<p class="db-section-sub">Реализованные проекты для пищевой промышленности</p>
<div class="db-projects-scroll">
<div class="db-project-card">
<img src="/milk-tank.jpg" alt="Молочные резервуары" loading="lazy">
<div class="db-project-body">
<h3>Оснащение молочного завода, Краснодарский край</h3>
<div class="db-project-preview">
<ul><li>Герметичные конструкции двойные швы</li><li>Температурный диапазон 40°C–120°C</li><li>Увеличение производительности на 40%</li></ul>
<button class="db-project-btn js-toggle-case">Читать кейс</button>
</div>
<div class="db-project-details">
<p>Полный цикл оснащения молочного завода мощностью 50 тонн переработки в сутки. Проект включал приёмочное отделение, цех пастеризации, танки хранения и линии розлага.</p>
<ul>
<li>Приёмно-охладительные ёмкости 25 000 л × 2 шт.</li>
<li>Резервуары хранения молока 30 000 л × 4 шт.</li>
<li>Пастеризационно-охладительная установка 5000 л/час</li>
<li>CIP-мойка на 4 контура с программируемым циклом</li>
</ul>
<p><strong>Результат:</strong> увеличение производительности на 40%, сокращение потерь сырья на 15%.</p>
<button class="db-project-btn js-toggle-case">Свернуть</button>
</div>
</div>
</div>
<div class="db-project-card">
<img src="/kvas-tank.jpg" alt="ЦКТ для кваса" loading="lazy">
<div class="db-project-body">
<h3>Завод по производству кваса, Воронеж</h3>
<div class="db-project-preview">
<ul><li>Производственная мощность: 300 000 л/мес</li><li>Полностью автоматизированный процесс</li><li>Система управления ферментацией</li></ul>
<button class="db-project-btn js-toggle-case">Читать кейс</button>
</div>
<div class="db-project-details">
<p>Линия производства кваса полного цикла на базе пивоваренного оборудования. Уникальный рецепт сбраживания квасного сусла в ЦКТ с последующей купажой.</p>
<ul>
<li>Варочный порядок 3000 л для варки квасного сусла</li>
<li>ЦКТ 5000 л × 4 шт. с рубашкой охлаждения</li>
<li>Купажные ёмкости 3000 л × 2 шт.</li>
<li>Линия розлага ПЭТ 1,5 л (3000 бут/час)</li>
</ul>
<p><strong>Результат:</strong> 300 000 л/мес, 6 сортов кваса, запуск за 10 недель.</p>
<button class="db-project-btn js-toggle-case">Свернуть</button>
</div>
</div>
</div>
<div class="db-project-card">
<img src="/cheez-kont.jpg" alt="Стелажные системы" loading="lazy">
<div class="db-project-body">
<h3>Цех созревания сыров, Ленинградская область</h3>
<div class="db-project-preview">
<ul><li>Антикоррозийное покрытие двойной защиты</li><li>Регулируемая система вентиляции</li></ul>
<button class="db-project-btn js-toggle-case">Читать кейс</button>
</div>
<div class="db-project-details">
<p>Специализированный цех созревания твёрдых и полутвёрдых сыров на 20 тонн единовременного хранения. Полностью автоматизированное поддержание микроклимата.</p>
<ul>
<li>Стелажные системы из нержавеющей стали AISI 304</li>
<li>Камеры созревания 4 зоны: +8°C / +12°C / +15°C / +18°C</li>
<li>Система увлажнения и вентиляции с HEPA-фильтрацией</li>
<li>Автоматический контроль влажности 75–95%</li>
</ul>
<p><strong>Результат:</strong> стабильное качество сыра, снижение брака на 25%.</p>
<button class="db-project-btn js-toggle-case">Свернуть</button>
</div>
</div>
</div>
<div class="db-project-card">
<img src="/sir.jpg" alt="Сыроварня" loading="lazy">
<div class="db-project-body">
<h3>Сыроварня полного цикла, Московская область</h3>
<div class="db-project-preview">
<ul><li>Резервуары для хранения молока</li><li>Ёмкости для пастеризации</li><li>Ферментационные танки</li></ul>
<button class="db-project-btn js-toggle-case">Читать кейс</button>
</div>
<div class="db-project-details">
<p>Комплексное оснащение крафтовой сыроварни мощностью 2 тонны молока в смену. Все ёмкости из AISI 304 с зеркальной полировкой.</p>
<ul>
<li>Сыроизготовители 1000 л × 2 шт. с лазерной резкой сырного зерна</li>
<li>Ванны пастеризации 500 л с программируемым профилем</li>
<li>Ферментационные танки для заквасок 300 л × 3 шт.</li>
<li>Контейнеры для посола сыра 500 л × 2 шт.</li>
</ul>
<p><strong>Результат:</strong> 6 сортов сыра, 600 кг/смена, запуск за 8 недель.</p>
<button class="db-project-btn js-toggle-case">Свернуть</button>
</div>
</div>
</div>
<div class="db-project-card">
<img src="/wine-equipment.jpg" alt="Винодельческое оборудование" loading="lazy">
<div class="db-project-body">
<h3>Винодельня полного цикла, Ставрополье</h3>
<div class="db-project-preview">
<ul><li>Ферментаторы из нержавеющей стали</li><li>Система инертизации азотом</li><li>Терморегулируемые танки для выдержки</li></ul>
<button class="db-project-btn js-toggle-case">Читать кейс</button>
</div>
<div class="db-project-details">
<p>Винодельня полного цикла «под ключ» — от приёмки винограда до розлага готового вина. Проект включал бродильный цех, погреб выдержки и линию розлага.</p>
<ul>
<li>Танки для ферментации 10 000 л × 8 шт. с рубашками</li>
<li>Система инертизации азотом с генератором N₂</li>
<li>Погреб выдержки: бочки + ёмкости из нержавейки</li>
<li>Линия розлага 3000 бут/час, АСУ ТП Siemens</li>
</ul>
<p><strong>Результат:</strong> запуск за 16 недель, 500 000 бутылок/год, 7 сортов.</p>
<button class="db-project-btn js-toggle-case">Свернуть</button>
</div>
</div>
</div>
<div class="db-project-card">
<img src="/fermentation-tanks-milk.jpg" alt="Парк резервуаров хранения" loading="lazy">
<div class="db-project-body">
<h3>Парк резервуаров для масложирового комбината</h3>
<div class="db-project-preview">
<ul><li>Более 10 емкостей от 100 000 до 200 000 л</li><li>Система азотной подушки на каждом резервуаре</li><li>Полная автоматизация и CIP-мойка</li></ul>
<button class="db-project-btn js-toggle-case">Читать кейс</button>
</div>
<div class="db-project-details">
<p>Изготовление и монтаж крупного парка резервуаров из нержавеющей стали для масложирового комбината. Более 10 емкостей объёмом от 100 000 до 200 000 литров для хранения и обработки растительных масел.</p>
<ul>
<li>Резервуары хранения 100 000–200 000 л × более 10 шт. с азотной подушкой</li>
<li>Ёмкости с мешалкой 20 000 л × 2 шт. для купажирования</li>
<li>Теплообменники для нагрева/охлаждения масел</li>
<li>Система CIP-мойки всех резервуаров</li>
</ul>
<p><strong>Результат:</strong> увеличение складских мощностей на 300 тонн единовременного хранения.</p>
<button class="db-project-btn js-toggle-case">Свернуть</button>
</div>
</div>
</div>
<div class="db-project-card">
<img src="/projects/cip-station.jpg" alt="CIP-станция" loading="lazy">
<div class="db-project-body">
<h3>CIP-станция с программируемым циклом мойки</h3>
<div class="db-project-preview">
<ul><li>4 контура мойки с независимым управлением</li><li>Программируемый цикл: щелочь → кислота → ополаскивание</li><li>Полная герметизация, исключение контакта с внешней средой</li></ul>
<button class="db-project-btn js-toggle-case">Читать кейс</button>
</div>
<div class="db-project-details">
<p>Автоматизированная CIP-станция для безразборной мойки технологического оборудования. Корпус и трубопроводы из AISI 316, управление на базе Siemens S7-1200 с визуализацией процесса.</p>
<ul>
<li>4 независимых контура мойки с программируемыми профилями</li>
<li>Рециркуляция моющих растворов с подогревом до 95°C</li>
<li>Баки моющих растворов 1500 л × 4 шт. из AISI 316</li>
<li>Датчики контроля концентрации, температуры и расхода</li>
<li>Сертифицирована для пищевых производств и фармацевтики</li>
</ul>
<p><strong>Результат:</strong> сокращение времени мойки на 40%, снижение расхода моющих средств на 25%.</p>
<button class="db-project-btn js-toggle-case">Свернуть</button>
</div>
</div>
</div>
<div class="db-project-card">
<img src="/виниф.jpg" alt="Винификаторы" loading="lazy">
<div class="db-project-body">
<h3>Винификаторы с рубашкой для винодельни, Крым</h3>
<div class="db-project-preview">
<ul><li>3 винификатора с рубашкой охлаждения</li><li>Лазерная сварка, зеркальная полировка</li><li>Полная автоматизация процесса ферментации</li></ul>
<button class="db-project-btn js-toggle-case">Читать кейс</button>
</div>
<div class="db-project-details">
<p>Изготовление и установка трёх винификаторов из нержавеющей стали AISI 304 для винодельни полного цикла в Крыму. Каждый танк оснащён рубашкой охлаждения для точного контроля температуры брожения.</p>
<ul>
<li>Винификаторы 2 000 л × 3 шт. с рубашкой охлаждения</li>
<li>Лазерная сварка, зеркальная полировка Ra ≤ 0,4 мкм</li>
<li>Система терморегуляции на базе Siemens</li>
<li>Арматура: пробоотборники, смотровые фонари, люки</li>
</ul>
<p><strong>Результат:</strong> точный контроль температуры ферментации, выход на проектную мощность за 6 недель.</p>
<button class="db-project-btn js-toggle-case">Свернуть</button>
</div>
</div>
</div>
<div class="db-project-card">
<img src="/brewery-voronezh-1.jpg" alt="Пивоваренное оборудование" loading="lazy">
<div class="db-project-body">
<h3>Пивзавод (Гор. Воронеж)</h3>
<div class="db-project-preview">
<ul><li>Варочный порядок 5 000 л</li><li>Производственная мощность 40 000 л сусла в сутки</li><li>Автоматика «под ключ»</li></ul>
<button class="db-project-btn js-toggle-case">Читать кейс</button>
</div>
<div class="db-project-details">
<p>Изготовили и ввели в эксплуатацию варочный порядок на 5000 литров. Производственная мощность линии составляет 40 000 литров сусла в сутки, что равняется 8 варкам и поддерживает высокую технологичность.</p>
<p><strong>Автоматика «под ключ»</strong></p>
<p>Внедрена автоматизированная система управления технологическими процессами, разработанная нашей компанией. Система обеспечивает полуавтоматический режим работы с возможностью ручного вмешательства при необходимости. Автоматика контролирует основные параметры производства: температуру, давление, уровни заполнения, мутность, время технологических циклов. Реализовано управление запорной арматурой (электроприводные клапаны, датчики положения).</p>
<p><strong>Преимущества решения:</strong></p>
<ul>
<li>Снижение влияния человеческого фактора</li>
<li>Повышение повторяемости и стабильности качества продукции</li>
<li>Оптимизация производственных циклов</li>
<li>Возможность интеграции с системами учета и диспетчеризации</li>
</ul>
</div>
</div>
</div>
<div class="db-project-card">
<img src="/milk-park.jpg" alt="Молочный парк" loading="lazy">
<div class="db-project-body">
<h3>Молочный парк: резервуары хранения и приёмки</h3>
<div class="db-project-preview">
<ul><li>Крупный парк ёмкостей для молочного производства</li><li>Резервуары хранения от 25 000 до 50 000 л</li><li>Полный цикл: приёмка → хранение → переработка</li></ul>
<button class="db-project-btn js-toggle-case">Читать кейс</button>
</div>
<div class="db-project-details">
<p>Изготовление и монтаж парка ёмкостного оборудования для молочного завода. Резервуары из нержавеющей стали AISI 304 для приёмки, охлаждения и хранения молока, а также для технологических процессов переработки.</p>
<ul>
<li>Резервуары хранения 25 000–50 000 л × 6 шт. с теплоизоляцией</li>
<li>Приёмно-охладительные ёмкости 20 000 л × 3 шт.</li>
<li>Танки для созревания сливок 10 000 л × 2 шт.</li>
<li>Система CIP-мойки всех резервуаров</li>
</ul>
<p><strong>Результат:</strong> увеличение мощностей хранения до 500 тонн единовременного хранения молока.</p>
<button class="db-project-btn js-toggle-case">Свернуть</button>
</div>
</div>
</div>
</div>
</div>
</section>

<script>
document.querySelectorAll('.js-toggle-case').forEach(function(b){
    b.addEventListener('click',function(e){
        e.preventDefault();
        var c = this.closest('.db-project-card');
        if (c) c.classList.toggle('expanded');
    });
});
// AISI Gallery
(function(){
var g = document.getElementById('aisiGallery');
if (!g) return;
var slides = g.querySelectorAll('.aisi-slide');
var cap = document.getElementById('aisiCaption');
var current = 0;
var total = slides.length;
if (total < 2) return;
setTimeout(function(){
for (var i = 1; i < total; i++){
var img = slides[i].querySelector('img[data-src]');
if (img) (new Image()).src = img.getAttribute('data-src');
}
}, 1000);
function render(idx){
if (idx === current) return;
slides[current].classList.remove('active');
slides[idx].classList.add('active');
var img = slides[idx].querySelector('img[data-src]');
if (img) { img.src = img.getAttribute('data-src'); img.removeAttribute('data-src'); }
var s = slides[idx];
var name = s.getAttribute('data-name');
if (name) {
cap.querySelector('.cap-name').textContent = name;
cap.querySelector('.cap-vol').textContent = s.getAttribute('data-vol') || '';
cap.querySelector('.cap-link').href = s.getAttribute('data-link') || '#';
cap.style.visibility = 'visible';
} else {
cap.style.visibility = 'hidden';
}
current = idx;
}
render(0);
setInterval(function(){ render((current + 1) % total); }, 6000);
})();
// Animation observer for equipment, armature, hero tags
(function(){
if (!window.IntersectionObserver) return;
var els = [
{ sel: '.db-prod-equip', cards: '.db-prod-equip-card', delay: 120 },
{ sel: '.db-hero-tags', cards: 'a', delay: 150 },
{ sel: '.db-arm-gallery', cards: '.db-arm-gallery-item', delay: 120 }
];
els.forEach(function(cfg){
var el = document.querySelector(cfg.sel);
if (!el) return;
var cards = el.querySelectorAll(cfg.cards);
if (!cards.length) return;
var obs = new IntersectionObserver(function(entries){
if (entries[0].isIntersecting) {
cards.forEach(function(c, i){
setTimeout(function(){ c.classList.add('visible'); }, i * cfg.delay);
});
obs.disconnect();
}
}, {threshold: .25});
obs.observe(el);
});
})();
</script>

<?php require __DIR__ . '/layout-end.php'; }
