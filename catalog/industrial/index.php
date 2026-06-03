<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/../industrial-data.php';

$bodyClass = 'brewery-page cct-page';
$canonical = 'https://ob-kub.ru/catalog/industrial/';
$inlineStyles = '*,*::before,*::after{box-sizing:border-box}.cct-page{font-family:\'Source Sans Pro\',sans-serif;color:#2c3e50;background:#f5f6f8}.cct-page .container{max-width:1100px;margin:0 auto;padding:0 24px}.list-main-hero{background:linear-gradient(135deg,#2b2b39 0%,#1a1a26 100%);position:relative;overflow:hidden;padding:32px 0}.list-main-hero::before{content:\'\';position:absolute;top:0;left:0;right:0;bottom:0;background:url(data:image/vg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+) repeat;pointer-events:none}.list-main-hero .hero-text{position:relative;z-index:1;text-align:center}.list-main-hero .hero-text h1{font-size:22px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;color:#fff;margin:0 0 4px}.list-main-hero .hero-text p{font-size:14px;color:rgba(255,255,255,.6);margin:0;max-width:600px;margin-left:auto;margin-right:auto}.list-main-hero .breadcrumbs{padding:0 0 8px;font-size:11px;color:rgba(255,255,255,.3)}.list-main-hero .breadcrumbs a{color:rgba(255,255,255,.45);text-decoration:none;transition:color .2s}.list-main-hero .breadcrumbs a:hover{color:#F77C2A}.list-main-hero .breadcrumbs .ep{margin:0 5px;color:rgba(255,255,255,.12)}.list-main-hero .breadcrumbs .current{color:rgba(255,255,255,.5)}.cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:20px;padding:40px 0 56px}.cat-card{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.06);transition:transform .2s,box-shadow .2s;text-decoration:none;color:inherit;display:flex;flex-direction:column;border:1px solid #eee;overflow:hidden}.cat-card:hover{transform:translateY(-6px);box-shadow:0 12px 36px rgba(247,124,42,.15);border-color:#fde0c0}.cat-card-img{width:100%;height:180px;overflow:hidden;background:#fff;display:flex;align-items:center;justify-content:center}.cat-card-img img{max-width:100%;max-height:100%;display:block}.cat-card-body{padding:20px 20px 16px;flex:1;display:flex;flex-direction:column}.cat-card-body .cat-name{font-size:16px;font-weight:700;color:#1a1a26;margin-bottom:4px;line-height:1.3}.cat-card-body .cat-desc{font-size:13px;color:#666;line-height:1.5;flex:1}.cat-card-body .cat-count{font-size:12px;color:#F77C2A;font-weight:600;margin-top:10px}.cat-card-footer{padding:0 20px 20px}.cat-card-footer .btn-view{display:block;width:100%;padding:12px;background:#F77C2A;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none;text-align:center;transition:background .2s}.cat-card-footer .btn-view:hover{background:#e06a1a}/* Mega Menu */.mega-menu-wrap{position:relative;display:inline-block}.nav .mega-menu-link{display:inline-flex;align-items:center;gap:4px}.nav .mega-menu-link:hover{color:#F77C2A}.mega-arrow{font-size:9px;transition:transform .25s;display:inline-block;margin-left:2px}.mega-menu-wrap:hover .mega-arrow,.mega-menu-wrap.active .mega-arrow{transform:rotate(180deg)}.mega-menu{position:absolute;top:100%;left:50%;transform:translateX(-50%) translateY(10px);background:#fff;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,.18),0 4px 16px rgba(0,0,0,.08);padding:24px;display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;min-width:820px;z-index:1000;opacity:0;visibility:hidden;transition:all .25s ease;margin-top:8px;border:1px solid rgba(0,0,0,.04)}.mega-menu::before{content:\'\';position:absolute;top:-8px;left:50%;transform:translateX(-50%);border:8px solid transparent;border-bottom-color:#fff}.mega-menu::after{content:\'\';position:absolute;top:0;left:24px;right:24px;height:3px;background:linear-gradient(90deg,#F77C2A,#FF8C42);border-radius:0 0 3px 3px}.mega-menu-wrap:hover .mega-menu,.mega-menu-wrap.active .mega-menu{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0)}.mega-col h3{font-size:11px;font-weight:700;color:#2b2b39;margin:0 0 10px;padding-bottom:7px;border-bottom:2px solid #F77C2A;text-transform:uppercase;letter-spacing:.4px}.mega-col a{display:block;padding:4px 0;font-size:13px;color:#555;text-decoration:none;transition:color .2s;line-height:1.4;white-space:normal}.mega-col a:hover{color:#F77C2A}.mega-aux{grid-column:1 / -1;margin-top:4px;padding-top:14px;border-top:1px solid #eee;text-align:center}.mega-aux a{display:inline-block;padding:8px 18px;font-size:13px;color:#F77C2A;text-decoration:none;font-weight:600;border-radius:8px;transition:background .2s}.mega-aux a:hover{background:#fff8f0}.cct-page .header{overflow:visible !important}@media.mega-col-link{display:block;text-decoration:none;color:inherit;padding:0;font-size:inherit;line-height:inherit}.mega-col-link:hover h3{color:#F77C2A}';

$categories = [
    ['key' => 'storage', 'emoji' => '🏭'],
    ['key' => 'mixing', 'emoji' => '🔀'],
    ['key' => 'thermal', 'emoji' => '🌡️'],
    ['key' => 'pressure', 'emoji' => '💨'],
    ['key' => 'cip', 'emoji' => '🧼', 'custom' => true],
    ['key' => 'heat-exchanger', 'emoji' => '🔁', 'custom' => true],
];
$h1 = $industrialCategory['h1'];
$metaTitle = $industrialCategory['title'];
$metaDesc = $industrialCategory['desc'];
require __DIR__ . '/../layout-start.php';
?><style>@media(max-width:700px){.cat-grid{grid-template-columns:repeat(2,1fr)!important;gap:12px!important}.cat-card-img{height:120px!important}.cat-card-body{padding:10px 12px!important}.cat-card-body .cat-name{font-size:14px!important}.cat-card-body .cat-desc{font-size:12px!important;line-height:1.4!important}.cat-card-footer{padding:0 12px 12px!important}.cat-card-footer .btn-view{padding:10px!important;font-size:12px!important}}@media(max-width:480px){.cat-grid{grid-template-columns:repeat(2,1fr)!important;gap:8px!important}.cat-card-img{height:90px!important}.cat-card-body{padding:8px 8px!important}.cat-card-body .cat-name{font-size:12px!important}.cat-card-body .cat-desc{display:none!important}}</style>
<section class="list-main-hero">
<div class="container">
<div class="breadcrumbs">
<a href="/">Главная</a><span class="ep">/</span>
<a href="/catalog/">Каталог</a><span class="ep">/</span>
<span class="current">Промышленное оборудование</span>
</div>
<div class="hero-text">
<h1><?= htmlspecialchars($h1) ?></h1>
<p><?= htmlspecialchars($metaDesc) ?></p>
</div>
</div>
</section>
<section class="container">
<div class="cat-grid">
<?php foreach ($categories as $c):
    if (!empty($c['custom'])) {
        $key = $c['key'];
        if ($key === 'cip') $dk = $industrialCip;
        else $dk = $industrialHeatExchanger;
        $countStr = $key === 'cip' ? 'Под заказ' : 'Пластинчатые / кожухотрубные';
    } else {
        $dk = $industrialData[$c['key']];
        $volCount = count($dk['volumes']);
        $minVol = min($dk['volumes']);
        $maxVol = max($dk['volumes']);
        $countStr = $volCount . ' объёмов: ' . $minVol . ' – ' . number_format($maxVol, 0, '.', ' ') . ' л';
    }
?>
<a href="/catalog/industrial/<?= $c['key'] ?>/" class="cat-card">
<div class="cat-card-img"><img 
src="/<?= htmlspecialchars($dk['image']) ?>" alt="<?= htmlspecialchars($dk['name']) ?>"></div>
<div class="cat-card-body">
<div class="cat-name"><?= htmlspecialchars($dk['name']) ?></div>
<div class="cat-desc"><?= htmlspecialchars($dk['desc']) ?></div>
<div class="cat-count"><?= $countStr ?></div>
</div>
<div class="cat-card-footer"><span class="btn-view">Выбрать объём</span></div>
</a>
<?php endforeach; ?>
</div>
</section>
<?php require __DIR__ . '/../layout-end.php'; ?>
