<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/../../cct-data.php';

// Редирект старых ссылок /catalog/beer/cct/500l/ → /catalog/beer/cct/volume.php?vol=500
$vol = isset($_GET['vol']) ? (int)$_GET['vol'] : 0;
if (!$vol) {
    // Старый формат ?volume=500
    $vol = isset($_GET['volume']) ? (int)$_GET['volume'] : 0;
}
if (!$vol) {
    // Старый формат /500l/ — редирект
    $uri = $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?? '';
    if (preg_match('#/catalog/beer/cct/(\d+)l?/?#', $uri, $m)) {
        header('Location: /catalog/beer/cct/volume.php?vol=' . (int)$m[1], true, 301);
        exit;
    }
}

$cctCat = $cctData['cct'];
if ($vol && isset($cctCat['specs'][$vol])) {
    renderCctPage($vol, $cctCat['specs'][$vol], $cctCat['specs'], $cctCat);
} else {
    renderCctList($cctCat['specs'], $cctCat);
}
exit;

function renderCctPage($vol, $d, $allData, $cat) {
    $volStr = number_format($vol, 0, '.', ' ');
    $diameterM = number_format($d['diameter'] / 1000, 2, '.', '');
    $heightM  = number_format($d['height_full'] / 1000, 2, '.', '');
    $priceStr = fmtPrice($d['from_price']);
    $canonical = "https://ob-kub.ru/catalog/beer/cct/{$vol}l/";
    $volumes = array_keys($allData); sort($volumes);
    $prevVol = null; $nextVol = null;
    $idx = array_search($vol, $volumes);
    if ($idx > 0) $prevVol = $volumes[$idx - 1];
    if ($idx < count($volumes) - 1) $nextVol = $volumes[$idx + 1];
    if ($vol <= 500) $brewSuggestion = '250-500 л';
    elseif ($vol <= 2000) $brewSuggestion = '500-1000 л';
    elseif ($vol <= 6000) $brewSuggestion = '2000-3000 л';
    elseif ($vol <= 15000) $brewSuggestion = '3000-5000 л';
    else $brewSuggestion = 'от 5000 л';
    $metaTitle = $d['title'];
    $metaDesc = $d['desc'];
    $bodyClass = 'brewery-page cct-page';
    require __DIR__ . '/../../catalog-styles.php';
    require __DIR__ . '/../../layout-start.php';
?>
<section class="cct-hero">
<div class="container">
<div class="cct-breadcrumbs">
<a href="/">Главная</a><span class="sep">/</span>
<a href="/catalog/">Каталог</a><span class="sep">/</span>
<a href="/catalog/beer/">Пивоваренное оборудование</a><span class="sep">/</span>
<a href="/catalog/beer/cct/">ЦКТ</a><span class="sep">/</span>
<span class="current"><?= $volStr ?> л</span>
</div>
<div class="cct-hero-inner">
<div class="cct-hero-img"><img src="/cct-tank.jpg" alt="ЦКТ <?= $volStr ?> литров" loading="lazy"></div>
<div class="cct-hero-info">
<div class="label">Цилиндро-конический танк</div>
<h1>ЦКТ <?= $volStr ?> литров</h1>
<p class="sub"><?= htmlspecialchars($d['desc']) ?></p>
<div class="cct-hero-price">от <?= $priceStr ?> <small>с НДС</small></div>
<div class="cct-hero-tags">
<span><strong>±0.3°C</strong> точность</span>
<span><strong>до <?= $d['pressure'] ?> бар</strong></span>
<span><strong>Ra ≤ 0.8</strong> мкм</span>
<span><strong><?= $d['jackets'] ?> зоны</strong> охлаждения</span>
</div>
</div>
</div>
</div>
</section>

<div class="cct-adv">
<div class="cct-adv-item"><div class="cct-adv-icon">🏭</div><div class="cct-adv-title">50+ пивоварен</div><div class="cct-adv-text">запущено по РФ и СНГ</div></div>
<div class="cct-adv-item"><div class="cct-adv-icon">⚙️</div><div class="cct-adv-title">Свой цех</div><div class="cct-adv-text">2000 м² в Краснодаре</div></div>
<div class="cct-adv-item"><div class="cct-adv-icon">📦</div><div class="cct-adv-title">Доставка по РФ</div><div class="cct-adv-text">любой ТК или нашим транспортом</div></div>
<div class="cct-adv-item"><div class="cct-adv-icon">✅</div><div class="cct-adv-title">Гарантия 12 мес</div><div class="cct-adv-text">сертификат ТР ЕАЭС</div></div>
</div>

<div class="container">
<div class="cct-nav">
<a href="<?= $prevVol ? "/catalog/beer/cct/volume.php?vol={$prevVol}" : '#' ?>" class="<?= $prevVol ? '' : 'dis' ?>">← <?= $prevVol ? number_format($prevVol, 0, '.', ' ') . ' л' : '—' ?></a>
<a href="/catalog/beer/cct/">📋 Все объёмы</a>
<a href="<?= $nextVol ? "/catalog/beer/cct/volume.php?vol={$nextVol}" : '#' ?>" class="<?= $nextVol ? '' : 'dis' ?>"><?= $nextVol ? number_format($nextVol, 0, '.', ' ') . ' л' : '—' ?> →</a>
</div>

<div class="cct-card">
<h2><span class="acc">📐</span> Технические характеристики</h2>
<table class="cct-specs">
<tr><td>Полный объём</td><td><?= number_format($d['full_volume'], 0, '.', ' ') ?> л</td></tr>
<tr><td>Рабочий объём</td><td><?= number_format($d['working_volume'], 0, '.', ' ') ?> л</td></tr>
<tr><td>Диаметр</td><td><?= $diameterM ?> м (<?= $d['diameter'] ?> мм)</td></tr>
<tr><td>Высота цилиндрической части</td><td><?= number_format($d['height_cyl'] / 1000, 2, '.', '') ?> м (<?= $d['height_cyl'] ?> мм)</td></tr>
<tr><td>Высота конуса</td><td><?= number_format($d['height_cone'] / 1000, 2, '.', '') ?> м (<?= $d['height_cone'] ?> мм)</td></tr>
<tr><td>Высота общая</td><td><?= $heightM ?> м (<?= $d['height_full'] ?> мм)</td></tr>
<tr><td>Угол конуса</td><td>60-70° (для сбора дрожжей)</td></tr>
<tr><td>Материал</td><td>AISI 304 / AISI 316 (опция)</td></tr>
<tr><td>Толщина стенки</td><td><?= $d['wall'] ?> мм</td></tr>
<tr><td>Рабочее давление</td><td>до <?= $d['pressure'] ?> бар</td></tr>
<tr><td>Внутренняя обработка</td><td>Электрополировка Ra ≤ 0,8 мкм</td></tr>
<tr><td>Вес (пустой)</td><td>≈ <?= $d['weight'] ?> кг</td></tr>
</table>
</div>

<div class="cct-card">
<h2><span class="acc">🏗️</span> Конструкция и материалы</h2>
<p style="font-size:14px;color:#666;line-height:1.8;margin:0">ЦКТ состоит из цилиндрической верхней части и конического днища (угол 60-70°). Коническая форма обеспечивает эффективное осаждение и сбор дрожжей после ферментации. Корпус и днище изготавливаются из пищевой нержавеющей стали AISI 304 с обязательной внутренней электрополировкой. Для кваса и кислых сортов пива доступно исполнение из AISI 316. Сварные швы выполняются аргонодуговой сваркой с последующей шлифовкой. Каждый танк проходит гидравлические испытания перед отгрузкой.</p>
</div>

<div class="cct-card">
<h2><span class="acc">❄️</span> Система охлаждения</h2>
<p style="font-size:14px;color:#666;margin:0 0 5px;line-height:1.6"><?= $d['jackets_desc'] ?>. Хладагент — пропиленгликоль до -4°C.</p>
<div class="cct-jackets">
<div class="cct-jacket"><strong>❄️ Колба</strong>Охлаждение и контроль брожения</div>
<div class="cct-jacket"><strong>❄️ Конус</strong>Осаждение дрожжей</div>
<?php if ($d['jackets'] >= 3): ?><div class="cct-jacket"><strong>❄️ Колба (2)</strong>Дополнительная зона</div><?php endif; ?>
</div>
<p style="font-size:13px;color:#999;margin:8px 0 0">Теплоизоляция: ППУ 50-200 мм в зависимости от условий установки</p>
</div>

<div class="cct-card">
<h2><span class="acc">📦</span> Базовая комплектация</h2>
<div class="cct-grid">
<div class="cct-grid-item"><span class="ok">✓</span> Теплоизоляция ППУ 50 мм</div>
<div class="cct-grid-item"><span class="ok">✓</span> Шпунт-аппарат</div>
<div class="cct-grid-item"><span class="ok">✓</span> Предохранительный клапан</div>
<div class="cct-grid-item"><span class="ok">✓</span> Дисковый затвор DN50/DN65</div>
<div class="cct-grid-item"><span class="ok">✓</span> Пробоотборный кран</div>
<div class="cct-grid-item"><span class="ok">✓</span> Люк-лаз DN400</div>
<div class="cct-grid-item"><span class="ok">✓</span> Ротационная головка CIP</div>
<div class="cct-grid-item"><span class="ok">✓</span> Карман под термопару</div>
<div class="cct-grid-item"><span class="ok">✓</span> Манометр</div>
<div class="cct-grid-item"><span class="ok">✓</span> Опоры (лапы / юбка)</div>
</div>
</div>

<div class="cct-card">
<h2><span class="acc">🔧</span> Дополнительные опции</h2>
<div class="cct-grid">
<div class="cct-grid-item"><span class="plus">+</span> Увеличенная теплоизоляция (100/150/200 мм)</div>
<div class="cct-grid-item"><span class="plus">+</span> Смотровой люк с подсветкой</div>
<div class="cct-grid-item"><span class="plus">+</span> Пневматическая арматура</div>
<div class="cct-grid-item"><span class="plus">+</span> Датчики T/P/уровня</div>
<div class="cct-grid-item"><span class="plus">+</span> PID-регулятор температуры</div>
<div class="cct-grid-item"><span class="plus">+</span> Мешалка (мотор-редуктор)</div>
<div class="cct-grid-item"><span class="plus">+</span> Дополнительный затвор на конус</div>
<div class="cct-grid-item"><span class="plus">+</span> Исполнение из AISI 316</div>
</div>
</div>

<div class="cct-pair">
<span class="ico">💡</span>
<span class="txt"><strong>Рекомендуемая пара:</strong> ЦКТ <?=$volStr?> л оптимально сочетается с варочным порядком <?=$brewSuggestion?>. <a href="/beer.html">Подробнее →</a></span>
</div>

<div class="cct-cta"><button onclick="document.getElementById('order').scrollIntoView({behavior:'smooth'})">📩 Получить расчёт ЦКТ <?= $volStr ?> л</button></div>

<div class="cct-card">
<h2><span class="acc">📊</span> Все объёмы ЦКТ</h2>
<table class="cct-table">
<thead><tr><th>Модель</th><th>Полный, л</th><th>Раб., л</th><th>D, мм</th><th>H, мм</th><th>Стенка</th><th>Руб.</th><th>Вес, кг</th><th>Цена</th></tr></thead>
<tbody><?php foreach ($allData as $v => $item):
    $vFmt = number_format($v, 0, '.', ' '); ?>
<tr class="<?= $v === $vol ? 'act' : '' ?>"><td><a href="/catalog/beer/cct/volume.php?vol=<?= $v ?>"><?= $vFmt ?> л</a></td><td><?= number_format($item['full_volume'], 0, '.', ' ') ?></td><td><?= number_format($item['working_volume'], 0, '.', ' ') ?></td><td><?= $item['diameter'] ?></td><td><?= $item['height_full'] ?></td><td><?= $item['wall'] ?> мм</td><td><?= $item['jackets'] ?></td><td><?= $item['weight'] ?></td><td><?= fmtPrice($item['from_price']) ?></td></tr>
<?php endforeach; ?></tbody>
</table>
</div>

<div class="cct-faq">
<h2 style="font-size:18px;font-weight:700;color:#1a1a26;margin:0 0 16px;display:flex;align-items:center;gap:8px"><span style="color:#F77C2A">❓</span> Часто задаваемые вопросы</h2>
<div class="faq-item"><div class="faq-q">Какое рабочее давление у ЦКТ?</div><div class="faq-a">Стандартное — до 2,5 бар. Для брожения и дображивания достаточно. По запросу до 4-6 бар. Танк является сосудом под давлением, оснащается предохранительным клапаном.</div></div>
<div class="faq-item"><div class="faq-q">Из какой стали делаете ЦКТ?</div><div class="faq-a">Стандартно AISI 304 с внутренней электрополировкой. Для кваса и кислых сортов пива — AISI 316. Толщина стенки: <?=$d['wall']?> мм для данного объёма (от 2 до 8 мм в зависимости от объёма).</div></div>
<div class="faq-item"><div class="faq-q">Какая теплоизоляция?</div><div class="faq-a">ППУ 50 мм в базовой комплектации. Для уличной установки рекомендуется 100-200 мм. Изоляция закрывается обшивкой из нержавеющей стали.</div></div>
<div class="faq-item"><div class="faq-q">Сколько ЦКТ нужно на пивоварню?</div><div class="faq-a">Минимум 3 на один сорт (брожение, дображивание, освобождение). Оптимально 4-6 ЦКТ для варки 2-3 сортов одновременно. Для объёма <?=$volStr?> л рекомендуем от 3 штук.</div></div>
<div class="faq-item"><div class="faq-q">Срок изготовления?</div><div class="faq-a">3-8 недель в зависимости от объёма и сложности. До 1000 л — 3-4 нед, до 10000 л — 5-6 нед, свыше — 6-8 нед. Возможно срочное изготовление за 3-4 недели.</div></div>
<div class="faq-item"><div class="faq-q">Нужен ли чиллер для ЦКТ?</div><div class="faq-a">Да, для работы рубашек охлаждения требуется холодильная установка (чиллер). Мы подбираем чиллер под количество и объём ЦКТ. Примерно: на 3 ЦКТ <?=$volStr?> л потребуется чиллер от <?=max(5, round($vol * 0.003))?> кВт. <a href="/beer.html" style="color:#F77C2A">Подробнее →</a></div></div>
</div>

<div class="cct-form" id="order">
<h2>📩 Получить расчёт ЦКТ <?=$volStr?> литров</h2>
<p class="cft-sub">Оставьте заявку — подготовим КП с точной стоимостью, сроками изготовления и доставки. Отвечаем в течение 2 часов.</p>
<form method="post" action="/php/send.php">
<input type="hidden" name="form_type" value="quick">
<input type="hidden" name="product" value="ЦКТ <?=$volStr?> л">
<div class="row">
<div><label>Ваше имя</label><input type="text" name="name" required placeholder="Иван"></div>
<div><label>Телефон</label><input type="tel" name="phone" required placeholder="+7 (___) ___-__-__"></div>
</div>
<div class="row">
<div><label>Email</label><input type="email" name="email" placeholder="ivan@example.ru"></div>
<div><label>Количество ЦКТ</label><input type="number" name="quantity" value="3" min="1"></div>
</div>
<div class="full"><label>Требования / вопросы</label><textarea name="comment" rows="3" placeholder="Дополнительные требования...">ЦКТ <?=$volStr?> л, количество: 3 шт. Прошу рассчитать стоимость и сроки.</textarea></div>
<div class="full">
<label class="chk-label">
<input type="checkbox" name="agreement" value="1" required>
<span>Я согласен(а) на обработку персональных данных в соответствии с <a href="/privacy.html" target="_blank" style="color:#F77C2A">Политикой конфиденциальности</a></span>
</label>
</div>
<input type="hidden" id="csrfToken" name="csrf" value="">
				<button type="submit" class="submit-btn">Получить расчёт</button>
<div class="form-success-message"></div>
</form>
</div>
</div>
<?php require __DIR__ . '/../../layout-end.php'; }

function renderCctList($allData, $cat) {
    $volumes = array_keys($allData); sort($volumes);
    $canonical = 'https://ob-kub.ru/catalog/beer/cct/';
    $metaTitle = $cat['title'];
    $metaDesc = $cat['desc'];
    $bodyClass = 'brewery-page cct-page';
    require __DIR__ . '/../../catalog-styles.php';
    require __DIR__ . '/../../layout-start.php';
?><style>.container{max-width:1200px!important}</style>
<section style="background:#fff;border-bottom:1px solid #f0f0f0;padding:14px 0">
<div class="container">
<div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px">
<div style="font-size:11px;color:#999">
<a href="/" style="color:#F77C2A;text-decoration:none">Главная</a>
<span style="color:#ccc;margin:0 5px">/</span>
<a href="/catalog/" style="color:#F77C2A;text-decoration:none">Каталог</a>
<span style="color:#ccc;margin:0 5px">/</span>
<a href="/catalog/beer/" style="color:#F77C2A;text-decoration:none">Пивоваренное</a>
<span style="color:#ccc;margin:0 5px">/</span>
<span style="color:#999">ЦКТ</span>
</div>
<div style="display:flex;gap:6px;flex-wrap:wrap">
<span style="display:inline-flex;align-items:center;gap:3px;padding:4px 8px;background:rgba(247,124,42,.08);border:1px solid rgba(247,124,42,.15);border-radius:4px;font-size:10px;font-weight:600;color:#F77C2A">🏭 50+ пивоварен</span>
<span style="display:inline-flex;align-items:center;gap:3px;padding:4px 8px;background:rgba(247,124,42,.08);border:1px solid rgba(247,124,42,.15);border-radius:4px;font-size:10px;font-weight:600;color:#F77C2A">⚙️ Свой цех</span>
<span style="display:inline-flex;align-items:center;gap:3px;padding:4px 8px;background:rgba(247,124,42,.08);border:1px solid rgba(247,124,42,.15);border-radius:4px;font-size:10px;font-weight:600;color:#F77C2A">📦 Доставка</span>
<span style="display:inline-flex;align-items:center;gap:3px;padding:4px 8px;background:rgba(247,124,42,.08);border:1px solid rgba(247,124,42,.15);border-radius:4px;font-size:10px;font-weight:600;color:#F77C2A">✅ Гарантия 12 мес</span>
</div>
</div>
</div>
</section>

<section style="background:linear-gradient(135deg,#2b2b39,#1a1a26);position:relative;overflow:hidden;padding:36px 0">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<div class="container">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center">
<div style="display:flex;align-items:center;justify-content:center;background:rgba(247,124,42,.03);padding:36px">
<img src="/cct-tank.jpg" alt="ЦКТ" style="max-width:100%;max-height:340px;border-radius:10px;display:block;box-shadow:0 6px 24px rgba(0,0,0,.35)">
</div>
<div>
<h1 style="font-size:26px;font-weight:800;color:#fff;margin:0 0 8px;text-transform:uppercase;letter-spacing:.4px">Цилиндро-конические танки (ЦКТ)</h1>
<p style="font-size:14px;color:rgba(255,255,255,.55);line-height:1.6;margin:0 0 14px">ЦКТ из нержавеющей стали AISI 304 для брожения, дображивания и лагеризации пива. Полный комплект арматуры, рубашки охлаждения, автоматика.</p>
<div style="display:flex;gap:6px;flex-wrap:wrap">
<span style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:rgba(247,124,42,.1);border:1px solid rgba(247,124,42,.15);border-radius:4px;font-size:11px;font-weight:600;color:#F77C2A">AISI 304 / 316</span>
<span style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:rgba(247,124,42,.1);border:1px solid rgba(247,124,42,.15);border-radius:4px;font-size:11px;font-weight:600;color:#F77C2A">до 4 зон охлаждения</span>
<span style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:rgba(247,124,42,.1);border:1px solid rgba(247,124,42,.15);border-radius:4px;font-size:11px;font-weight:600;color:#F77C2A">угол конуса 60-70°</span>
</div>
<div onclick="document.querySelector('.container:nth-of-type(3)').scrollIntoView({behavior:'smooth'})" style="display:inline-flex;align-items:center;gap:6px;padding:10px 22px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;margin-top:4px">Выбрать объём →</div>
</div>
</div>
</div>
</section>

<?php
$minVol = min($volumes);
$maxVol = max($volumes);
$volCount = count($volumes);
?>

<section class="container" style="margin-top:24px">

<div id="volBlock" style="position:relative;min-height:300px;border-radius:14px;overflow:hidden">
<div id="volBg" style="position:absolute;top:-5%;left:0;width:100%;height:120%;background:url(/banner-beer.jpg) center/cover;z-index:0"></div>
<div style="position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(135deg,rgba(26,26,38,.55),rgba(26,26,38,.1));z-index:1"></div>
<div style="position:relative;z-index:2;display:flex;flex-direction:column;justify-content:space-between;min-height:300px;padding:28px 32px">
<div>
<div style="display:inline-block;background:#fff;border-radius:8px;padding:12px 20px;box-shadow:0 4px 16px rgba(0,0,0,.12)">
<div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#F77C2A;font-weight:600">Выберите объём ЦКТ</div>
</div>
</div>
<div>
<div style="display:inline-flex;flex-wrap:wrap;gap:6px;align-items:center;background:#fff;border-radius:8px;padding:14px 20px;box-shadow:0 4px 16px rgba(0,0,0,.12)">
<?php foreach ($volumes as $v): ?>
<a href="/catalog/beer/cct/volume.php?vol=<?= $v ?>" style="display:inline-flex;align-items:center;justify-content:center;padding:7px 14px;background:#F77C2A;color:#fff;border:none;border-radius:5px;font-size:12px;font-weight:700;text-decoration:none;transition:all .2s" onmouseover="this.style.background='#e06a15'" onmouseout="this.style.background='#F77C2A'"><?= number_format($v, 0, '.', ' ') ?> л</a>
<?php endforeach; ?>
</div>
</div>
</div>
</div>
</section>
<script>
(function(){var el=document.getElementById('volBlock'),bg=document.getElementById('volBg');if(!el||!bg)return;var ticking=false;function update(){var rect=el.getBoundingClientRect(),vh=window.innerHeight;if(rect.bottom<0||rect.top>vh){ticking=false;return}var mid=rect.top+rect.height/2,sc=Math.max(-1,Math.min(1,(vh/2-mid)/(vh/2)));var maxMove=rect.height*0.08;bg.style.transform='translateY('+((sc*maxMove)-rect.height*0.02)+'px)';ticking=false}window.addEventListener('scroll',function(){if(!ticking){requestAnimationFrame(update);ticking=true}},{passive:true});update()})();
</script>

<div class="seo-text-wrap">
    <div class="seo-text-card">
        <div class="seo-text-head">Полезная информация</div>
        <div class="seo-text collapsed">
        <p>Цилиндро-конический танк (ЦКТ) — это основной элемент бродильного отделения любой пивоварни. В ЦКТ происходит главный этап превращения сусла в пиво: брожение, дображивание, созревание и насыщение углекислотой. Коническая форма обеспечивает эффективный сбор и удаление дрожжей, а рубашки охлаждения позволяют точно контролировать температуру — от главного брожения 8–16°C до лагерного созревания 0–4°C.</p>
        <p><strong>В каталоге представлено <?= $volCount ?> моделей объёмом от <?= $minVol ?> до <?= $maxVol ?> л — от компактных ЦКТ для крафтовых пивоварен до промышленных танков высокой ёмкости.</strong></p>
        <p>Как выбрать ЦКТ: объём подбирается под сменную партию пива. Для крафтовой пивоварни с варками 200–500 л оптимальны ЦКТ на 500–1000 л. Для варок 1000–2000 л — танки на 1500–3000 л. Важные параметры: угол конуса (60–75° для эффективного сбора дрожжей), рубашки охлаждения (полный или частичный охват), комплектация арматурой КИП для автоматизации. Количество ЦКТ рассчитывается исходя из сортов пива в производстве — на каждый сорт нужен отдельный танк на полный цикл.</p>
        <p>Каждый ЦКТ «ОБОРУДОВАНИЕ КУБАНИ» изготавливается из пищевой AISI 304 с зеркальной полировкой Ra ≤ 0,8 мкм, угол конуса 60–75°, рубашки охлаждения, полная арматура КИП и СИП-мойка. По запросу — AISI 316. Каждый танк проходит гидравлические испытания. Производим в Краснодаре с 2008 года. Гарантия 12 месяцев.</p>
        <p>«ОБОРУДОВАНИЕ КУБАНИ» — это 18 лет на рынке, собственное производство полного цикла (цех 2000 м²), контроль качества и сертификаты соответствия. Доставляем по всей России и странам СНГ транспортными компаниями. Оставьте заявку — инженер подготовит коммерческое предложение с точной стоимостью, сроками изготовления и доставки для вашего проекта.</p>
        </div>
        <button class="seo-text-toggle" onclick="var t=this.previousElementSibling;t.classList.toggle('expanded');t.classList.toggle('collapsed');this.textContent=t.classList.contains('expanded')?'Свернуть':'Читать полностью'">Читать полностью</button>
    </div>
</div>

<style>.proj-card.expanded .proj-detail{display:block!important}.proj-card{cursor:pointer}</style>
<section class="db-section" style="padding:20px 0;background:transparent">
<div class="db-wrap" style="max-width:1200px">
<div style="padding:36px 40px;background:linear-gradient(135deg,#1a1a26,#2b2b39);border-radius:14px;position:relative;overflow:hidden">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<div style="text-align:center;margin-bottom:24px">
<div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#F77C2A;font-weight:600;margin-bottom:6px">Проекты</div>
<h2 style="font-size:22px;font-weight:800;color:#fff;margin:0 0 4px">Примеры реализованных проектов</h2>
<p style="font-size:14px;color:rgba(255,255,255,.5);margin:0">Оборудование в работе на пивоварнях и производствах</p>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;text-align:center">
<div style="background:rgba(255,255,255,.04);border-radius:10px;padding:20px 10px"><div style="font-size:32px;font-weight:800;color:#F77C2A;line-height:1">400+</div><div style="font-size:12px;color:rgba(255,255,255,.5);margin-top:4px">проектов по всей России</div></div>
<div style="background:rgba(255,255,255,.04);border-radius:10px;padding:20px 10px"><div style="font-size:32px;font-weight:800;color:#F77C2A;line-height:1">5000 м²</div><div style="font-size:12px;color:rgba(255,255,255,.5);margin-top:4px">собственное производство</div></div>
<div style="background:rgba(255,255,255,.04);border-radius:10px;padding:20px 10px"><div style="font-size:32px;font-weight:800;color:#F77C2A;line-height:1">17</div><div style="font-size:12px;color:rgba(255,255,255,.5);margin-top:4px">лет на рынке</div></div>
<div style="background:rgba(255,255,255,.04);border-radius:10px;padding:20px 10px"><div style="font-size:32px;font-weight:800;color:#F77C2A;line-height:1">12</div><div style="font-size:12px;color:rgba(255,255,255,.5);margin-top:4px">месяцев гарантии</div></div>
</div>

<div style="display:flex;gap:20px;overflow-x:auto;padding-bottom:8px">
<div class="proj-card" onclick="toggleProj(this)" style="flex:0 0 320px;background:#fff;border-radius:12px;overflow:hidden;cursor:pointer;transition:all .2s">
<img src="/brewery-voronezh.jpg" alt="" style="width:100%;height:180px;object-fit:cover;display:block">
<div style="padding:14px 18px 8px">
<h3 style="font-size:14px;font-weight:700;color:#1a1a26;margin:0 0 4px">🍺 Пивзавод, Воронеж</h3>
<p style="font-size:12px;color:#888;margin:0 0 6px">Варочный порядок 5 000 л · 8 варок/сутки</p>
<div class="proj-detail" style="display:none;font-size:12px;color:#555;line-height:1.5;padding:8px 0;border-top:1px solid #f0f0f0">
<p style="margin:0 0 6px">Изготовили и ввели в эксплуатацию варочный порядок на 5000 литров. Производственная мощность — 40 000 литров сусла в сутки. Автоматизация на Siemens.</p>
<p style="margin:0 0 4px"><strong>Оборудование:</strong> Варочный порядок 5000 л, ЦКТ 10000 л × 8, парогенератор, чиллер, CIP-мойка.</p>
<p style="color:#F77C2A;font-weight:600">✅ Результат: 8 варок/сутки, 6 сортов пива</p>
</div>
<p style="font-size:11px;color:#F77C2A;font-weight:600;margin:0;padding:6px 0;border-top:1px solid #f0f0f0">Подробнее →</p>
</div>
</div>
<div class="proj-card" onclick="toggleProj(this)" style="flex:0 0 320px;background:#fff;border-radius:12px;overflow:hidden;cursor:pointer;transition:all .2s">
<img src="/brewery-abkhazia.jpg" alt="" style="width:100%;height:180px;object-fit:cover;display:block">
<div style="padding:14px 18px 8px">
<h3 style="font-size:14px;font-weight:700;color:#1a1a26;margin:0 0 4px">🍺 Пивоварня, Абхазия</h3>
<p style="font-size:12px;color:#888;margin:0 0 6px">Комплексное оснащение под ключ</p>
<div class="proj-detail" style="display:none;font-size:12px;color:#555;line-height:1.5;padding:8px 0;border-top:1px solid #f0f0f0">
<p style="margin:0 0 6px">Полный цикл оснащения пивоварни «под ключ»: от проекта до первой варки. Все ёмкости из AISI 304.</p>
<p style="margin:0 0 4px"><strong>Оборудование:</strong> Варочный порядок 1000 л, ЦКТ 2000 л × 6, БГВ, парогенератор, чиллер.</p>
<p style="color:#F77C2A;font-weight:600">✅ Результат: запуск за 6 недель, 4 сорта пива</p>
</div>
<p style="font-size:11px;color:#F77C2A;font-weight:600;margin:0;padding:6px 0;border-top:1px solid #f0f0f0">Подробнее →</p>
</div>
</div>
<div class="proj-card" onclick="toggleProj(this)" style="flex:0 0 320px;background:#fff;border-radius:12px;overflow:hidden;cursor:pointer;transition:all .2s">
<img src="/brewery-chekhov.jpg" alt="" style="width:100%;height:180px;object-fit:cover;display:block">
<div style="padding:14px 18px 8px">
<h3 style="font-size:14px;font-weight:700;color:#1a1a26;margin:0 0 4px">🍺 Пивоварня, г. Чехов</h3>
<p style="font-size:12px;color:#888;margin:0 0 6px">Мини-пивоварня ресторанного типа</p>
<div class="proj-detail" style="display:none;font-size:12px;color:#555;line-height:1.5;padding:8px 0;border-top:1px solid #f0f0f0">
<p style="margin:0 0 6px">Небольшая пивоварня ресторанного формата на 5 сортов пива. Проект реализован под ключ за 4 недели.</p>
<p style="margin:0 0 4px"><strong>Оборудование:</strong> Варочный порядок 250 л, ЦКТ 500 л × 3, парогенератор 60 кВт, чиллер 8 кВт.</p>
<p style="color:#F77C2A;font-weight:600">✅ Результат: 5 сортов, окупаемость 14 месяцев</p>
</div>
<p style="font-size:11px;color:#F77C2A;font-weight:600;margin:0;padding:6px 0;border-top:1px solid #f0f0f0">Подробнее →</p>
</div>
</div>
<div class="proj-card" onclick="toggleProj(this)" style="flex:0 0 320px;background:#fff;border-radius:12px;overflow:hidden;cursor:pointer;transition:all .2s">
<img src="/kvas-tank.jpg" alt="" style="width:100%;height:180px;object-fit:cover;display:block">
<div style="padding:14px 18px 8px">
<h3 style="font-size:14px;font-weight:700;color:#1a1a26;margin:0 0 4px">🧃 Завод кваса, Воронеж</h3>
<p style="font-size:12px;color:#888;margin:0 0 6px">300 000 л/мес · 6 сортов</p>
<div class="proj-detail" style="display:none;font-size:12px;color:#555;line-height:1.5;padding:8px 0;border-top:1px solid #f0f0f0">
<p style="margin:0 0 6px">Линия производства кваса на базе пивоваренного оборудования. Сбраживание в ЦКТ.</p>
<p style="margin:0 0 4px"><strong>Оборудование:</strong> Варочный порядок 3000 л, ЦКТ 5000 л × 4, купажные ёмкости.</p>
<p style="color:#F77C2A;font-weight:600">✅ Результат: 300 000 л/мес, 6 сортов, запуск за 10 недель</p>
</div>
<p style="font-size:11px;color:#F77C2A;font-weight:600;margin:0;padding:6px 0;border-top:1px solid #f0f0f0">Подробнее →</p>
</div>
</div>
<div class="proj-card" onclick="toggleProj(this)" style="flex:0 0 320px;background:#fff;border-radius:12px;overflow:hidden;cursor:pointer;transition:all .2s">
<img src="/projects/cip-station.jpg" alt="" style="width:100%;height:180px;object-fit:cover;display:block">
<div style="padding:14px 18px 8px">
<h3 style="font-size:14px;font-weight:700;color:#1a1a26;margin:0 0 4px">🧼 CIP-станция</h3>
<p style="font-size:12px;color:#888;margin:0 0 6px">Автоматическая безразборная мойка</p>
<div class="proj-detail" style="display:none;font-size:12px;color:#555;line-height:1.5;padding:8px 0;border-top:1px solid #f0f0f0">
<p style="margin:0 0 6px">Автоматизированная CIP-станция для мойки технологического оборудования. Корпус из AISI 316.</p>
<p style="margin:0 0 4px"><strong>Оборудование:</strong> 4 контура мойки, баки 1500 л × 4 из AISI 316, Siemens S7-1200.</p>
<p style="color:#F77C2A;font-weight:600">✅ Результат: -40% времени мойки, -25% расхода моющих средств</p>
</div>
<p style="font-size:11px;color:#F77C2A;font-weight:600;margin:0;padding:6px 0;border-top:1px solid #f0f0f0">Подробнее →</p>
</div>
</div>
</div>
</div>
</section>

<style>
.article-card{background:#fff!important;border-radius:10px!important;padding:20px 24px!important;box-shadow:0 2px 12px rgba(0,0,0,.08)!important}
.article-card .article-header{margin-bottom:12px!important}
.article-card .article-tag{font-size:9px!important;padding:2px 8px!important;margin-bottom:6px!important}
.article-card .article-title{font-size:15px!important;margin:6px 0 0!important}
.article-card .article-body{font-size:13px!important;line-height:1.6!important}
.article-card .article-body p{margin:0 0 8px!important}
.article-card .article-btn{padding:8px 18px!important;font-size:12px!important}
.article-card .article-toggle{font-size:12px!important;padding:2px 0 0!important}
</style>
<section style="margin-top:32px;margin-bottom:48px">
<div class="container">
<div style="background:linear-gradient(135deg,#1a1a26,#2b2b39);border-radius:14px;position:relative;overflow:hidden;padding:28px 32px">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<h2 style="font-size:16px;font-weight:800;color:#fff;margin:0 0 16px;text-transform:uppercase;letter-spacing:.4px">📖 Статьи по теме</h2>
<div class="article-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <?php foreach ($cctArticles as $ad): ?>
        <div class="article-card">
            <div class="article-header">
                <span class="article-tag">Статья по теме</span>
                <h2 class="article-title"><?= htmlspecialchars($ad['title']) ?></h2>
            </div>
            <div class="article-body">
                <?= $ad['content'][0] ?>
                <div class="article-full collapsed">
                <?= $ad['content'][1] ?>
                <div class="article-cta">
                    <p><?= strip_tags($ad['content'][2]) ?></p>
                    <a href="/beer.html#order-form" class="article-btn">Получить расчёт →</a>
                </div>
                </div>
                <button class="article-toggle" onclick="openArticleModal(this)">Читать статью полностью</button>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
    </div>
</section>

<div class="article-modal" id="articleModal" onclick="if(event.target===this)closeArticleModal()"><div class="article-modal-backdrop"></div><div class="article-modal-card"><button class="article-modal-close" onclick="closeArticleModal()">&times;</button><div class="article-modal-header"><span class="article-tag">Статья по теме</span><h2 class="article-modal-title" id="articleModalTitle"></h2></div><div class="article-modal-body" id="articleModalBody"></div><div class="article-modal-cta"><p>Нужна консультация по выбору? Инженер подберёт оптимальный объём под вашу варку, рассчитает количество танков и подготовит КП с точной стоимостью.</p><a href="/beer.html#order-form" class="article-btn">Получить расчёт →</a></div></div></div>
 <script>function toggleProj(el){el.classList.toggle('expanded')} function openArticleModal(b){var c=b.closest('.article-card'),t=c.querySelector('.article-title').textContent,h='';c.querySelectorAll('.article-full>*').forEach(function(e){if(!e.classList.contains('article-cta'))h+=e.outerHTML});document.getElementById('articleModalTitle').textContent=t;document.getElementById('articleModalBody').innerHTML=h;document.getElementById('articleModal').classList.add('active');document.body.style.overflow='hidden'}function closeArticleModal(){document.getElementById('articleModal').classList.remove('active');document.body.style.overflow=''}</script>
<?php require __DIR__ . '/../../layout-end.php'; }
