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
<section class="cct-hero">
<div class="container">
<div class="cct-breadcrumbs">
<a href="/">Главная</a><span class="ep">/</span>
<a href="/catalog/">Каталог</a><span class="ep">/</span>
<?php if ($breadcrumbMiddle): ?><a href="<?= htmlspecialchars($breadcrumbMiddleUrl) ?>"><?= htmlspecialchars($breadcrumbMiddle) ?></a><span class="ep">/</span><?php endif; ?>
<a href="<?= htmlspecialchars($baseUrl) ?>"><?= htmlspecialchars($data['name_short']) ?></a><span class="ep">/</span>
<span class="current"><?= $volStr ?> <?= $specUnit ?></span>
</div>
<div class="cct-hero-inner">
<div class="cct-hero-img"><img id="mainImg" src="<?= $image ?>" alt="<?= htmlspecialchars($data['name']) ?>" loading="lazy" style="width:100%;height:auto;max-height:280px;object-fit:contain;display:block;border-radius:8px"></div>
<div class="cct-hero-info">
<div class="label"><?= htmlspecialchars($data['name_short']) ?></div>
<h1><?= htmlspecialchars($data['name']) ?> на <?= $volStr ?> <?= $specUnit ?></h1>
<p class="sub"><?= htmlspecialchars($data['desc']) ?></p>
<div class="cct-hero-price">от <?= $priceStr ?> <small>с НДС</small></div>
<div class="cct-hero-tags">
<?php foreach ($tagFeatures as $f):
    $clean = preg_replace('/\(.*?\)|\[.*?\]/', '', $f);
    $clean = trim($clean, ' ·,');
    if (!$clean) continue;
    $parts = explode(':', $clean);
    if (count($parts) > 1) {
        $val = trim($parts[0]);
        $rest = trim($parts[1]);
        echo '<span><strong>' . htmlspecialchars($val) . ':</strong> ' . htmlspecialchars($rest) . '</span>';
    } else {
        echo '<span>' . htmlspecialchars($clean) . '</span>';
    }
endforeach; ?>
</div>
<?php if ($catKey === 'hot-water-tank'): ?>
<div style="display:flex;gap:4px;margin-top:12px;align-items:center">
<img src="<?= $image ?>" alt="Бак горячей воды" loading="lazy" class="thumb-img" onclick="switchImg(this)" style="height:50px;width:auto;display:block;border-radius:4px;cursor:pointer;border:2px solid #F77C2A;transition:border-color .2s">
<img src="/hot-water-tank-2.jpg" alt="Бак горячей воды" loading="lazy" class="thumb-img" onclick="switchImg(this)" style="height:50px;width:auto;display:block;border-radius:4px;cursor:pointer;border:2px solid transparent;transition:border-color .2s">
<img src="/hot-water-tank-3.jpg" alt="Бак горячей воды" loading="lazy" class="thumb-img" onclick="switchImg(this)" style="height:50px;width:auto;display:block;border-radius:4px;cursor:pointer;border:2px solid transparent;transition:border-color .2s">
</div>
<script>function switchImg(el){document.querySelectorAll('.thumb-img').forEach(function(t){t.style.borderColor='transparent'});el.style.borderColor='#F77C2A';document.getElementById('mainImg').src=el.src}</script>
<?php endif; ?>
</div>
</div>
</div>
</section>

<div class="cct-adv">
<?php foreach ($advItems as $a): ?>
<div class="cct-adv-item"><div class="cct-adv-icon"><?= $a['icon'] ?></div><div class="cct-adv-title"><?= htmlspecialchars($a['title']) ?></div><div class="cct-adv-text"><?= htmlspecialchars($a['text']) ?></div></div>
<?php endforeach; ?>
</div>

<div class="container">
<div class="cct-nav">
<a href="<?= $prevVol ? "{$catPrefix}{$catKey}/{$prevVol}l/" : '#' ?>" class="<?= $prevVol ? '' : 'dis' ?>">← <?= $prevVol ? number_format($prevVol, 0, '.', ' ') . ' ' . $specUnit : '—' ?></a>
<a href="<?= htmlspecialchars($baseUrl) ?>">📋 Все объёмы</a>
<a href="<?= $nextVol ? "{$catPrefix}{$catKey}/{$nextVol}l/" : '#' ?>" class="<?= $nextVol ? '' : 'dis' ?>"><?= $nextVol ? number_format($nextVol, 0, '.', ' ') . ' ' . $specUnit : '—' ?> →</a>
</div>

<div class="cct-card">
<h2><span class="acc">📐</span> Технические характеристики</h2>
<table class="cct-specs">
<tr><td><?= $specLabel ?></td><td><?= $volStr ?> <?= $specUnit ?></td></tr>
<?php if (!empty($s['full_volume'])): ?>
<tr><td>Полный объём</td><td><?= number_format($s['full_volume'], 0, '.', ' ') ?> л</td></tr>
<tr><td>Рабочий объём</td><td><?= number_format($s['working_volume'], 0, '.', ' ') ?> л</td></tr>
<?php endif; ?>
<?php if ($hasDim): ?>
<tr><td>Диаметр</td><td><?= $diameterM ?> м (<?= $s['diameter'] ?> мм)</td></tr>
<tr><td>Высота</td><td><?= $heightM ?> м (<?= $s['height'] ?> мм)</td></tr>
<?php endif; ?>
<tr><td>Толщина стенки</td><td><?= $s['wall'] ?> мм</td></tr>
<tr><td>Вес (пустой)</td><td>≈ <?= $s['weight'] ?> кг</td></tr>
<?php if ($s['power'] > 0): ?>
<tr><td>Мощность</td><td><?= $s['power'] ?> кВт</td></tr>
<?php endif; ?>
<tr><td>Материал</td><td>AISI 304 / AISI 316 (опция)</td></tr>
<tr><td>Внутренняя обработка</td><td>Электрополировка Ra ≤ 0,8 мкм</td></tr>
</table>
</div>

<div class="cct-card">
<h2><span class="acc">🏗️</span> Конструкция и материалы</h2>
<p 
style="font-size:14px;color:#666;line-height:1.8;margin:0"><?= htmlspecialchars($data['desc']) ?></p>
<p 
style="font-size:14px;color:#666;line-height:1.8;margin-top:12px">Изготовлен из высококачественной нержавеющей стали AISI 304 с зеркальной полировкой Ra ≤ 0.8 мкм. Сварные швы выполняются аргонодуговой сваркой с последующей шлифовкой. Каждое изделие проходит гидравлические испытания перед отгрузкой. Возможно изготовление из AISI 316 для агрессивных сред.</p>
</div>

<div class="cct-card">
<h2><span class="acc">📦</span> Комплектация и особенности</h2>
<div class="cct-grid">
<?php foreach ($features as $f): ?>
<div class="cct-grid-item"><span class="ok">✓</span> <?= htmlspecialchars($f) ?></div>
<?php endforeach; ?>
</div>
</div>

<div class="cct-card">
<h2><span class="acc">🔧</span> Дополнительные опции</h2>
<div class="cct-grid">
<div class="cct-grid-item"><span class="plus">+</span> Увеличенная теплоизоляция (100/150/200 мм)</div>
<div class="cct-grid-item"><span class="plus">+</span> Датчики температуры / давления / уровня</div>
<div class="cct-grid-item"><span class="plus">+</span> PID-регулятор температуры</div>
<div class="cct-grid-item"><span class="plus">+</span> Мешалка (мотор-редуктор)</div>
<div class="cct-grid-item"><span class="plus">+</span> CIP-мойка (ротационная головка)</div>
<div class="cct-grid-item"><span class="plus">+</span> Исполнение из AISI 316</div>
</div>
</div>

<div class="cct-cta"><button onclick="document.getElementById('order').scrollIntoView({behavior:'smooth'})">📩 Получить расчёт <?= htmlspecialchars($data['name']) ?> <?= $volStr ?> <?= $specUnit ?></button></div>

<?php if (!empty($allSpecs)): ?>
<div class="cct-card">
<h2><span class="acc">📊</span> Все объёмы</h2>
<table class="cct-table">
<thead><tr><th><?= $specLabel ?></th><th>Полный, л</th><th>Раб., л</th><th>D, мм</th><th>H, мм</th><th>Стенка</th><th>Вес, кг</th><th>Цена</th></tr></thead>
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
?>
<tr class="<?= $v === $vol ? 'act' : '' ?>"><td><a href="<?= "{$catPrefix}{$catKey}/{$v}l/" ?>"><?= $vFmt ?> <?= $specUnit ?></a></td><td><?= $fv ?></td><td><?= $wv ?></td><td><?= $d ?></td><td><?= $h ?></td><td><?= $spec['wall'] ?> мм</td><td><?= $spec['weight'] ?></td><td><?= $pStr ?></td></tr>
<?php endforeach; ?></tbody>
</table>
</div>
<?php endif; ?>

<div class="cct-faq">
<h2><span class="acc">❓</span> Часто задаваемые вопросы</h2>
<div class="faq-item"><div class="faq-q">Из какого материала изготавливается оборудование?</div><div class="faq-a">Стандартное исполнение — нержавеющая сталь AISI 304 (пищевая, кислотостойкая). Для агрессивных сред доступна сталь AISI 316. По запросу — AISI 316L для фармацевтики и особо чистых производств.</div></div>
<div class="faq-item"><div class="faq-q">Какие сроки изготовления?</div><div class="faq-a">Стандартные позиции — от 3-5 рабочих дней. Крупные позиции — от 14-20 дней, в зависимости от сложности и загрузки производства.</div></div>
<div class="faq-item"><div class="faq-q">Какая гарантия на оборудование?</div><div class="faq-a">Гарантия на оборудование — 12 месяцев с даты отгрузки. Распространяется на дефекты материалов и изготовления.</div></div>
<div class="faq-item"><div class="faq-q">Доставляете и монтируете?</div><div class="faq-a">Да, осуществляем доставку по всей России и странам СНГ любой ТК. Также предоставляем услуги шеф-монтажа и пусконаладки силами наших инженеров.</div></div>
<div class="faq-item"><div class="faq-q">Как заказать?</div><div class="faq-a">Оставьте заявку через форму ниже или позвоните по телефону <strong>8 (993) 594-01-07</strong>. Мы подготовим коммерческое предложение с точной стоимостью, сроками и условиями доставки.</div></div>
</div>

<div class="cct-form" id="order">
<h2>📩 Получить расчёт <?= htmlspecialchars($data['name']) ?> <?= $volStr ?> <?= $specUnit ?></h2>
<p class="cft-ub">Оставьте заявку — подготовим КП с точной стоимостью, сроками изготовления и доставки. Отвечаем в течение 2 часов.</p>
<form method="post" action="/php/send.php">
<input type="hidden" name="csrf" id="csrfToken" value="">
<input type="hidden" name="form_type" value="<?= htmlspecialchars($formType) ?>">
<input type="hidden" name="product" value="<?= htmlspecialchars($data['name'] . ' ' . $vol . ' ' . $specUnit) ?>">
<div class="row">
<div><label>Ваше имя</label><input type="text" name="name" required placeholder="Иван"></div>
<div><label>Телефон</label><input type="tel" name="phone" required placeholder="+7 (___) ___-__-__" class="phone-mask"></div>
</div>
<div class="row">
<div><label>Email</label><input type="email" name="email" placeholder="ivan@example.ru"></div>
<div><label>Количество</label><input type="number" name="quantity" value="1" min="1"></div>
</div>
<div class="full"><label>Требования / вопросы</label><textarea name="comment" rows="3" placeholder="Дополнительные требования, материал, опции, сроки..."><?= htmlspecialchars($data['name']) ?> <?= $volStr ?> <?= $specUnit ?>, количество: 1 шт. Прошу рассчитать стоимость и сроки.</textarea></div>
<div class="full" 
style="margin-bottom:16px">
<label class="chk-label" 
style="display:flex;align-items:flex-tart;gap:10px;cursor:pointer;font-weight:400;text-transform:none;letter-spacing:0;font-size:13px;color:#666">
<input type="checkbox" name="agreement" value="1" required 
style="width:auto;margin-top:2px;accent-color:#F77C2A;flex-hrink:0">
<span>Я согласен(а) на обработку персональных данных в соответствии с <a href="/privacy.html" target="_blank" 
style="color:#F77C2A">Политикой конфиденциальности</a></span>
</label>
</div>
<button type="submit" class="submit-btn">Получить расчёт</button>
<div class="form-success-message"></div>
</form>
</div>
</div>
<script>document.getElementById("csrfToken").value=btoa(String(Math.floor(Date.now()/1e3)));</script>
<?php require __DIR__ . '/../layout-end.php'; } ?>
