<?php
function renderProductPage($catKey, $vol, $data, $opts = []) {
    $s = $data['specs'][$vol];
    $volStr = number_format($vol, 0, '.', ' ');
    $hasDim = !empty($s['diameter']);
    $price = $s['price'];
    $priceStr = $price >= 1000000 ? number_format($price/1000000,1,'.','').' млн ₽' : ($price >= 1000 ? number_format($price/1000,0,'.','').' тыс ₽' : number_format($price,0,'.',' ').' ₽');
    $specUnit = $opts['specUnit'] ?? 'л';
    $specLabel = $opts['specLabel'] ?? 'Объём';
    $canonical = $opts['canonical'] ?? '';
    $baseUrl = $opts['baseUrl'] ?? '/';
    $catPrefix = $opts['catPrefix'] ?? '/';
    $categoryName = $opts['categoryName'] ?? 'Каталог';
    $categoryUrl = $opts['categoryUrl'] ?? '/catalog/';
    $formType = $opts['formType'] ?? 'item';
    $advItems = $opts['advItems'] ?? [
        ['icon' => '🏭', 'title' => '18+ лет на рынке', 'text' => 'с 2008 года'],
        ['icon' => '⚙️', 'title' => 'Свой цех', 'text' => '2000 м² в Краснодаре'],
        ['icon' => '📦', 'title' => 'Доставка по РФ', 'text' => 'любой ТК или нашим транспортом'],
        ['icon' => '✅', 'title' => 'Гарантия 12 мес', 'text' => 'сертификат ТР ЕАЭС'],
    ];
    $allSpecs = $opts['allSpecs'] ?? $data['specs'] ?? [];
    $breadcrumbMiddle = $opts['breadcrumbMiddle'] ?? '';
    $breadcrumbMiddleUrl = $opts['breadcrumbMiddleUrl'] ?? '';

    $diameterM = $hasDim ? number_format($s['diameter'] / 1000, 2, '.', '') : '';
    $heightM = $hasDim ? number_format($s['height'] / 1000, 2, '.', '') : '';

    $sorted = $data['volumes'];
    
sort($sorted);
    $prevVol = null; $nextVol = null;
    $idx = array_search($vol, $sorted);
    if ($idx > 0) $prevVol = $sorted[$idx - 1];
    if ($idx < count($sorted) - 1) $nextVol = $sorted[$idx + 1];

    $features = $data['features'];
    $metaTitle = $data['name'] . ' ' . $volStr . ' ' . $specUnit;
    $metaDesc = $data['name_short'] . ' ' . $volStr . ' ' . $specUnit . ' из нержавеющей стали AISI 304. Цена от ' . $priceStr . '. Закажите на ob-kub.ru';
    $image = htmlspecialchars('/' . $data['image']);

    $schemaProduct = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $data['name'] . ' ' . $volStr . ' ' . $specUnit,
        'description' => $metaDesc,
        'image' => 'https://ob-kub.ru' . $image,
        'brand' => ['@type' => 'Brand', 'name' => 'ОБОРУДОВАНИЕ КУБАНИ'],
        'category' => $categoryName,
        'offers' => ['@type' => 'Offer', 'price' => $price, 'priceCurrency' => 'RUB', 'availability' => 'https://schema.org/InStock'],
        'material' => 'Нержавеющая сталь AISI 304',
    ], JSON_UNESCAPED_UNICODE);

    $tagFeatures = array_slice($features, 0, 5);

    $bodyClass = 'brewery-page cct-page';
    require __DIR__ . '/../catalog-styles.php';
    require __DIR__ . '/../layout-start.php';
?>
<section style="background:linear-gradient(135deg,#2b2b39,#1a1a26);position:relative;overflow:hidden;padding:28px 0 36px">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<div class="container">
<div style="font-size:11px;color:rgba(255,255,255,.3);margin-bottom:12px">
<a href="/" style="color:rgba(255,255,255,.45);text-decoration:none">Главная</a>
<span style="display:inline-block;margin:0 6px;color:rgba(255,255,255,.15)">•</span>
<a href="/catalog/" style="color:rgba(255,255,255,.45);text-decoration:none">Каталог</a>
<span style="display:inline-block;margin:0 6px;color:rgba(255,255,255,.15)">•</span>
<?php if ($breadcrumbMiddle): ?><a href="<?= htmlspecialchars($breadcrumbMiddleUrl) ?>" style="color:rgba(255,255,255,.45);text-decoration:none"><?= htmlspecialchars($breadcrumbMiddle) ?></a><span style="display:inline-block;margin:0 6px;color:rgba(255,255,255,.15)">•</span><?php endif; ?>
<a href="<?= htmlspecialchars($baseUrl) ?>" style="color:rgba(255,255,255,.45);text-decoration:none"><?= htmlspecialchars($data['name_short']) ?></a>
<span style="display:inline-block;margin:0 6px;color:rgba(255,255,255,.15)">•</span>
<span style="color:rgba(255,255,255,.5)"><?= $volStr ?> <?= $specUnit ?></span>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:36px;align-items:center">
<div style="display:flex;align-items:center;justify-content:center">
<img id="mainImg" src="<?= $image ?>" alt="<?= htmlspecialchars($data['name']) ?>" style="max-width:100%;max-height:320px;border-radius:10px;display:block;box-shadow:0 6px 24px rgba(0,0,0,.35)">
</div>
<div>
<div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#F77C2A;font-weight:600;margin-bottom:6px"><?= htmlspecialchars($data['name_short']) ?></div>
<h1 style="font-size:26px;font-weight:800;color:#fff;margin:0 0 8px"><?= htmlspecialchars($data['name']) ?> на <?= $volStr ?> <?= $specUnit ?></h1>
<p style="font-size:14px;color:rgba(255,255,255,.55);line-height:1.6;margin:0 0 14px"><?= htmlspecialchars($data['desc']) ?></p>
<div style="font-size:24px;font-weight:800;color:#F77C2A;margin-bottom:16px">от <?= $priceStr ?> <span style="font-size:13px;font-weight:400;color:rgba(255,255,255,.35)">с НДС</span></div>
<div style="display:flex;gap:6px;flex-wrap:wrap">
<?php foreach ($tagFeatures as $f):
$clean = preg_replace('/\(.*?\)|\[.*?\]/', '', $f);
$clean = trim($clean, ' ·,');
if (!$clean) continue;
$parts = explode(':', $clean);
if (count($parts) > 1):
$val = trim($parts[0]);
$rest = trim($parts[1]);
echo '<span style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:rgba(247,124,42,.1);border:1px solid rgba(247,124,42,.15);border-radius:4px;font-size:11px;font-weight:600;color:#F77C2A"><strong>' . htmlspecialchars($val) . ':</strong> ' . htmlspecialchars($rest) . '</span>';
else:
echo '<span style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:rgba(247,124,42,.1);border:1px solid rgba(247,124,42,.15);border-radius:4px;font-size:11px;font-weight:600;color:#F77C2A">' . htmlspecialchars($clean) . '</span>';
endif;
endforeach; ?>
</div>
<?php if ($catKey === 'hot-water-tank'): ?>
<div style="display:flex;gap:6px;margin-top:12px">
<img src="<?= $image ?>" alt="" class="thumb-img" onclick="switchImg(this)" style="height:45px;width:auto;border-radius:4px;cursor:pointer;border:2px solid #F77C2A">
<img src="/hot-water-tank-2.jpg" alt="" class="thumb-img" onclick="switchImg(this)" style="height:45px;width:auto;border-radius:4px;cursor:pointer;border:2px solid transparent">
<img src="/hot-water-tank-3.jpg" alt="" class="thumb-img" onclick="switchImg(this)" style="height:45px;width:auto;border-radius:4px;cursor:pointer;border:2px solid transparent">
</div>
<script>function switchImg(el){document.querySelectorAll('.thumb-img').forEach(function(t){t.style.borderColor='transparent'});el.style.borderColor='#F77C2A';document.getElementById('mainImg').src=el.src}</script>
<?php endif; ?>
</div>
</div>
</div>
</section>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;max-width:1100px;margin:28px auto;padding:0 24px">
<?php foreach ($advItems as $a): ?>
<div style="background:rgba(247,124,42,.06);border:1px solid rgba(247,124,42,.1);border-radius:8px;padding:14px 12px;text-align:center">
<div style="font-size:22px;margin-bottom:4px"><?= $a['icon'] ?></div>
<div style="font-size:13px;font-weight:700;color:#1a1a26;margin-bottom:2px"><?= htmlspecialchars($a['title']) ?></div>
<div style="font-size:11px;color:#999"><?= htmlspecialchars($a['text']) ?></div>
</div>
<?php endforeach; ?>
</div>

<div class="container">
<div style="display:flex;justify-content:space-between;align-items:center;margin:20px 0;gap:12px">
<?php if ($prevVol): ?><a href="<?= "{$catPrefix}{$catKey}/{$prevVol}l/" ?>" style="display:inline-flex;align-items:center;gap:4px;padding:8px 16px;background:rgba(247,124,42,.08);border:1px solid rgba(247,124,42,.15);border-radius:6px;font-size:13px;font-weight:600;color:#F77C2A;text-decoration:none">← <?= number_format($prevVol, 0, '.', ' ') ?> <?= $specUnit ?></a><?php else: ?><div></div><?php endif; ?>
<a href="<?= htmlspecialchars($baseUrl) ?>" style="display:inline-flex;align-items:center;gap:4px;padding:8px 16px;border:1px solid #ddd;border-radius:6px;font-size:13px;font-weight:600;color:#666;text-decoration:none">📋 Все объёмы</a>
<?php if ($nextVol): ?><a href="<?= "{$catPrefix}{$catKey}/{$nextVol}l/" ?>" style="display:inline-flex;align-items:center;gap:4px;padding:8px 16px;background:rgba(247,124,42,.08);border:1px solid rgba(247,124,42,.15);border-radius:6px;font-size:13px;font-weight:600;color:#F77C2A;text-decoration:none"><?= number_format($nextVol, 0, '.', ' ') ?> <?= $specUnit ?> →</a><?php else: ?><div></div><?php endif; ?>
</div>

<div class="db-weld-frame" style="padding:28px 32px">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<h2 style="font-size:18px;font-weight:800;color:#fff;margin:0 0 16px;display:flex;align-items:center;gap:8px"><span style="color:#F77C2A">📐</span> Технические характеристики</h2>
<table style="width:100%;border-collapse:collapse;font-size:14px">
<tr style="border-bottom:1px solid rgba(255,255,255,.06)"><td style="padding:8px 10px;color:rgba(255,255,255,.4);width:200px"><?= $specLabel ?></td><td style="padding:8px 10px;color:#fff;font-weight:600"><?= $volStr ?> <?= $specUnit ?></td></tr>
<?php if (!empty($s['full_volume'])): ?>
<tr style="border-bottom:1px solid rgba(255,255,255,.06)"><td style="padding:8px 10px;color:rgba(255,255,255,.4)">Полный объём</td><td style="padding:8px 10px;color:#fff;font-weight:600"><?= number_format($s['full_volume'], 0, '.', ' ') ?> л</td></tr>
<tr style="border-bottom:1px solid rgba(255,255,255,.06)"><td style="padding:8px 10px;color:rgba(255,255,255,.4)">Рабочий объём</td><td style="padding:8px 10px;color:#fff;font-weight:600"><?= number_format($s['working_volume'], 0, '.', ' ') ?> л</td></tr>
<?php endif; ?>
<?php if ($hasDim): ?>
<tr style="border-bottom:1px solid rgba(255,255,255,.06)"><td style="padding:8px 10px;color:rgba(255,255,255,.4)">Диаметр</td><td style="padding:8px 10px;color:#fff;font-weight:600"><?= $diameterM ?> м (<?= $s['diameter'] ?> мм)</td></tr>
<tr style="border-bottom:1px solid rgba(255,255,255,.06)"><td style="padding:8px 10px;color:rgba(255,255,255,.4)">Высота</td><td style="padding:8px 10px;color:#fff;font-weight:600"><?= $heightM ?> м (<?= $s['height'] ?> мм)</td></tr>
<?php endif; ?>
<tr style="border-bottom:1px solid rgba(255,255,255,.06)"><td style="padding:8px 10px;color:rgba(255,255,255,.4)">Толщина стенки</td><td style="padding:8px 10px;color:#fff;font-weight:600"><?= $s['wall'] ?> мм</td></tr>
<tr style="border-bottom:1px solid rgba(255,255,255,.06)"><td style="padding:8px 10px;color:rgba(255,255,255,.4)">Вес (пустой)</td><td style="padding:8px 10px;color:#fff;font-weight:600">≈ <?= $s['weight'] ?> кг</td></tr>
<?php if ($s['power'] > 0): ?>
<tr style="border-bottom:1px solid rgba(255,255,255,.06)"><td style="padding:8px 10px;color:rgba(255,255,255,.4)">Мощность</td><td style="padding:8px 10px;color:#fff;font-weight:600"><?= $s['power'] ?> кВт</td></tr>
<?php endif; ?>
<tr style="border-bottom:1px solid rgba(255,255,255,.06)"><td style="padding:8px 10px;color:rgba(255,255,255,.4)">Материал</td><td style="padding:8px 10px;color:#fff;font-weight:600">AISI 304 / AISI 316 (опция)</td></tr>
<tr><td style="padding:8px 10px;color:rgba(255,255,255,.4)">Внутренняя обработка</td><td style="padding:8px 10px;color:#fff;font-weight:600">Электрополировка Ra ≤ 0,8 мкм</td></tr>
</table>
</div>

<div class="db-weld-frame" style="padding:28px 32px;margin-top:20px">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<h2 style="font-size:18px;font-weight:800;color:#fff;margin:0 0 12px;display:flex;align-items:center;gap:8px"><span style="color:#F77C2A">🏗️</span> Конструкция и материалы</h2>
<p style="font-size:14px;color:rgba(255,255,255,.55);line-height:1.7;margin:0 0 12px"><?= htmlspecialchars($data['desc']) ?></p>
<p style="font-size:14px;color:rgba(255,255,255,.55);line-height:1.7;margin:0">Изготовлен из высококачественной нержавеющей стали AISI 304 с зеркальной полировкой Ra ≤ 0.8 мкм. Сварные швы выполняются аргонодуговой сваркой с последующей шлифовкой. Каждое изделие проходит гидравлические испытания перед отгрузкой. Возможно изготовление из AISI 316 для агрессивных сред.</p>
</div>

<div class="db-weld-frame" style="padding:28px 32px;margin-top:20px">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<h2 style="font-size:18px;font-weight:800;color:#fff;margin:0 0 14px;display:flex;align-items:center;gap:8px"><span style="color:#F77C2A">📦</span> Комплектация</h2>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
<?php foreach ($features as $f): ?>
<div style="display:flex;align-items:center;gap:6px;padding:4px 0;font-size:14px;color:rgba(255,255,255,.6)"><span style="color:#2ecc71;font-weight:700">✓</span> <?= htmlspecialchars($f) ?></div>
<?php endforeach; ?>
</div>
</div>

<div class="cct-cta" style="text-align:center;margin:28px 0"><button onclick="document.getElementById('order').scrollIntoView({behavior:'smooth'})" style="display:inline-block;padding:14px 36px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:700;cursor:pointer;font-family:inherit">📩 Получить расчёт <?= htmlspecialchars($data['name']) ?> <?= $volStr ?> <?= $specUnit ?></button></div>

<?php if (!empty($allSpecs)): ?>
<div class="db-weld-frame" style="padding:28px 32px;margin-top:20px">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<h2 style="font-size:18px;font-weight:800;color:#fff;margin:0 0 14px;display:flex;align-items:center;gap:8px"><span style="color:#F77C2A">📊</span> Все объёмы</h2>
<div style="overflow-x:auto">
<table style="width:100%;border-collapse:collapse;font-size:13px">
<thead><tr style="background:rgba(255,255,255,.04)"><?php
$cols = ['<th style="padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);font-weight:700">' . $specLabel . '</th>',
'<th style="padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);font-weight:700">Полный, л</th>',
'<th style="padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);font-weight:700">Раб., л</th>'];
if (empty($s['diameter'])) { $cols[] = '<th style="padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);font-weight:700">D, мм</th><th style="padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);font-weight:700">H, мм</th>'; }
$cols[] = '<th style="padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);font-weight:700">Стенка</th>';
$cols[] = '<th style="padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);font-weight:700">Вес, кг</th>';
$cols[] = '<th style="padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);font-weight:700">Цена</th>';
echo implode("\n", $cols);
?></tr></thead>
<tbody><?php
$allVols = array_keys($allSpecs);
sort($allVols);
foreach ($allVols as $v):
$spec = $allSpecs[$v];
$vFmt = number_format($v, 0, '.', ' ');
$p = $spec['price'];
$pStr = $p >= 1000000 ? number_format($p/1000000,1,'.','').' млн ₽' : ($p >= 1000 ? number_format($p/1000,0,'.','').' тыс ₽' : number_format($p,0,'.',' ').' ₽');
$d = !empty($spec['diameter']) ? $spec['diameter'] : '—';
$h = !empty($spec['height']) ? $spec['height'] : '—';
$fv = !empty($spec['full_volume']) ? number_format($spec['full_volume'], 0, '.', ' ') : '—';
$wv = !empty($spec['working_volume']) ? number_format($spec['working_volume'], 0, '.', ' ') : '—';
$isAct = $v === $vol ? 'background:rgba(247,124,42,.08)' : '';
?>
<tr style="border-bottom:1px solid rgba(255,255,255,.04);<?= $isAct ?>">
<td style="padding:8px 10px;color:#F77C2A;font-weight:600"><a href="<?= "{$catPrefix}{$catKey}/{$v}l/" ?>" style="color:#F77C2A;text-decoration:none"><?= $vFmt ?> <?= $specUnit ?></a></td>
<td style="padding:8px 10px;color:rgba(255,255,255,.6)"><?= $fv ?></td>
<td style="padding:8px 10px;color:rgba(255,255,255,.6)"><?= $wv ?></td>
<?php if (empty($s['diameter'])): ?><td style="padding:8px 10px;color:rgba(255,255,255,.6)"><?= $d ?></td>
<td style="padding:8px 10px;color:rgba(255,255,255,.6)"><?= $h ?></td><?php endif; ?>
<td style="padding:8px 10px;color:rgba(255,255,255,.6)"><?= $spec['wall'] ?> мм</td>
<td style="padding:8px 10px;color:rgba(255,255,255,.6)"><?= $spec['weight'] ?></td>
<td style="padding:8px 10px;color:#F77C2A;font-weight:600"><?= $pStr ?></td>
</tr>
<?php endforeach; ?></tbody>
</table>
</div>
</div>
<?php endif; ?>

<div class="db-weld-frame" style="padding:28px 32px;margin-top:20px">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<h2 style="font-size:18px;font-weight:800;color:#fff;margin:0 0 16px;display:flex;align-items:center;gap:8px"><span style="color:#F77C2A">❓</span> Часто задаваемые вопросы</h2>
<div class="faq-item" style="border-bottom:1px solid rgba(255,255,255,.06)"><div class="faq-q" style="padding:14px 0;font-weight:600;color:#fff;cursor:pointer;font-size:14px" onclick="this.closest('.faq-item').classList.toggle('open')">Из какого материала изготавливается оборудование?</div><div class="faq-a" style="max-height:0;overflow:hidden;transition:max-height .3s;font-size:14px;color:rgba(255,255,255,.5);line-height:1.6">Стандартное исполнение — нержавеющая сталь AISI 304 (пищевая, кислотостойкая). Для агрессивных сред доступна сталь AISI 316. По запросу — AISI 316L для фармацевтики и особо чистых производств.</div></div>
<div class="faq-item" style="border-bottom:1px solid rgba(255,255,255,.06)"><div class="faq-q" style="padding:14px 0;font-weight:600;color:#fff;cursor:pointer;font-size:14px" onclick="this.closest('.faq-item').classList.toggle('open')">Какие сроки изготовления?</div><div class="faq-a" style="max-height:0;overflow:hidden;transition:max-height .3s;font-size:14px;color:rgba(255,255,255,.5);line-height:1.6">Стандартные позиции — от 3-5 рабочих дней. Крупные позиции — от 14-20 дней, в зависимости от сложности и загрузки производства.</div></div>
<div class="faq-item" style="border-bottom:1px solid rgba(255,255,255,.06)"><div class="faq-q" style="padding:14px 0;font-weight:600;color:#fff;cursor:pointer;font-size:14px" onclick="this.closest('.faq-item').classList.toggle('open')">Какая гарантия на оборудование?</div><div class="faq-a" style="max-height:0;overflow:hidden;transition:max-height .3s;font-size:14px;color:rgba(255,255,255,.5);line-height:1.6">Гарантия на оборудование — 12 месяцев с даты отгрузки. Распространяется на дефекты материалов и изготовления.</div></div>
<div class="faq-item" style="border-bottom:1px solid rgba(255,255,255,.06)"><div class="faq-q" style="padding:14px 0;font-weight:600;color:#fff;cursor:pointer;font-size:14px" onclick="this.closest('.faq-item').classList.toggle('open')">Доставляете и монтируете?</div><div class="faq-a" style="max-height:0;overflow:hidden;transition:max-height .3s;font-size:14px;color:rgba(255,255,255,.5);line-height:1.6">Да, осуществляем доставку по всей России и странам СНГ любой ТК. Также предоставляем услуги шеф-монтажа и пусконаладки силами наших инженеров.</div></div>
<div class="faq-item"><div class="faq-q" style="padding:14px 0;font-weight:600;color:#fff;cursor:pointer;font-size:14px" onclick="this.closest('.faq-item').classList.toggle('open')">Как заказать?</div><div class="faq-a" style="max-height:0;overflow:hidden;transition:max-height .3s;font-size:14px;color:rgba(255,255,255,.5);line-height:1.6">Оставьте заявку через форму ниже или позвоните по телефону <strong style="color:#fff">8 (993) 594-01-07</strong>. Мы подготовим коммерческое предложение с точной стоимостью, сроками и условиями доставки.</div></div>
<style>.faq-item.open .faq-a{max-height:200px!important;padding:0 0 14px!important}.faq-q::after{content:'+';font-size:18px;color:rgba(255,255,255,.2);transition:transform .2s;flex-shrink:0}.faq-item.open .faq-q::after{content:'−';color:#F77C2A}</style>
</div>

<div class="db-weld-frame" style="padding:32px 36px;margin:28px 0 48px" id="order">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#F77C2A;font-weight:600;margin-bottom:4px">Заявка</div>
<h2 style="font-size:20px;font-weight:800;color:#fff;margin:0 0 4px">Получить расчёт <?= htmlspecialchars($data['name']) ?> <?= $volStr ?> <?= $specUnit ?></h2>
<p style="font-size:13px;color:rgba(255,255,255,.45);margin-bottom:28px;line-height:1.5">Оставьте заявку — подготовим КП с точной стоимостью, сроками изготовления и доставки. Отвечаем в течение 2 часов.</p>
<form method="post" action="/php/send.php">
<input type="hidden" name="csrf" id="csrfToken" value="">
<input type="hidden" name="form_type" value="<?= htmlspecialchars($formType) ?>">
<input type="hidden" name="product" value="<?= htmlspecialchars($data['name'] . ' ' . $vol . ' ' . $specUnit) ?>">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
<div><input type="text" name="name" required placeholder="Ваше имя" style="width:100%;padding:12px 16px;border-radius:8px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:#fff;font-size:14px;font-family:inherit;outline:none;box-sizing:border-box"></div>
<div><input type="tel" name="phone" required placeholder="Телефон" class="phone-mask" style="width:100%;padding:12px 16px;border-radius:8px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:#fff;font-size:14px;font-family:inherit;outline:none;box-sizing:border-box"></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
<div><input type="email" name="email" placeholder="Email для КП" style="width:100%;padding:12px 16px;border-radius:8px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:#fff;font-size:14px;font-family:inherit;outline:none;box-sizing:border-box"></div>
<div><input type="number" name="quantity" value="1" min="1" placeholder="Количество" style="width:100%;padding:12px 16px;border-radius:8px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:#fff;font-size:14px;font-family:inherit;outline:none;box-sizing:border-box"></div>
</div>
<div style="margin-bottom:14px">
<textarea name="comment" rows="3" placeholder="Дополнительные требования, материал, опции, сроки..." style="width:100%;padding:12px 16px;border-radius:8px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:#fff;font-size:14px;font-family:inherit;outline:none;resize:vertical;min-height:80px;box-sizing:border-box"><?= htmlspecialchars($data['name']) ?> <?= $volStr ?> <?= $specUnit ?>, количество: 1 шт. Прошу рассчитать стоимость и сроки.</textarea>
</div>
<label style="display:flex;align-items:flex-start;gap:8px;margin-bottom:16px;font-size:12px;color:rgba(255,255,255,.5);cursor:pointer">
<input type="checkbox" name="agreement" value="1" required style="margin-top:2px;accent-color:#F77C2A">
<span>Я согласен(а) на обработку персональных данных в соответствии с <a href="/privacy.html" target="_blank" style="color:#F77C2A;text-decoration:none">Политикой конфиденциальности</a></span>
</label>
<button type="submit" style="width:100%;padding:14px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit">📩 Получить расчёт</button>
<div class="form-success-message"></div>
</form>
</div>
</div>
<script>document.getElementById("csrfToken").value=btoa(String(Math.floor(Date.now()/1e3)));</script>
<?php require __DIR__ . '/../layout-end.php'; } ?>
