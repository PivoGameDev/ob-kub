<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/../beer-extra-data.php';
require __DIR__ . '/../helpers/product-template.php';

$uri = $_SERVER['REQUEST_URI'];
$cat = !empty($_SERVER['CATALOG_CATEGORY']) ? $_SERVER['CATALOG_CATEGORY'] : '';
$volume = 0;
if (!empty($_SERVER['CATALOG_VOLUME'])) {
    $volume = (int)$_SERVER['CATALOG_VOLUME'];
}
if (!$cat && preg_match('#/catalog/beer/(hot-water-tank|grain-mill|steam-generator|chiller|unitank|heat-exchanger)/#', $uri, $m)) {
    $cat = $m[1];
}
if (!$volume && preg_match('#/(\d+)l?/?#', $uri, $m)) {
    $volume = (int)$m[1];
}

if (!$cat || !isset($beerExtra[$cat])) {
    header('HTTP/1.0 404 Not Found');
    echo 'Категория не найдена';
    exit;
}

$data = $beerExtra[$cat];
if ($volume && isset($data['specs'][$volume])) {
    renderItemPage($cat, $volume, $data);
} else {
    renderItemList($cat, $data);
}
exit;

function renderItemList($catKey, $data) {
    $sorted = $data['volumes'];
    
sort($sorted);
    $metaTitle = $data['title'];
    $metaDesc = $data['desc'];
    $h1 = $data['h1'];
    $baseUrl = '/catalog/beer/' . $catKey . '/';
    $hasDim = !empty($data['specs'][$sorted[0]]['diameter']);
    $specUnit = $data['spec_unit'] ?? 'л';
    $specLabel = $data['spec_label'] ?? 'Объём';
    $bodyClass = 'brewery-page cct-page';
    $canonical = 'https://ob-kub.ru' . $baseUrl;
    require __DIR__ . '/../catalog-styles.php';
    require __DIR__ . '/../layout-start.php';
?>

<section class="list-hero">
<div class="container">
<div class="breadcrumbs">
<a href="/">Главная</a><span class="sep">/</span>
<a href="/catalog/">Каталог</a><span class="sep">/</span>
<a href="/catalog/beer/">Пивоваренное оборудование</a><span class="sep">/</span>
<span class="current"><?= htmlspecialchars($h1) ?></span>
</div>
<div class="list-hero-inner">
<div class="list-hero-img"><img id="mainImg" src="/<?= htmlspecialchars($data['image']) ?>" alt="<?= htmlspecialchars($data['name']) ?>" style="width:100%;height:auto;max-height:260px;object-fit:contain;display:block;border-radius:8px"></div>
<div class="list-hero-text">
<h1><?= htmlspecialchars($h1) ?></h1>
<p class="hero-sub"><?php
$hooks = [
    'hot-water-tank' => 'Стабильная подача горячей воды для затирания, CIP-мойки и отопления цеха. Термоизоляция ППУ — минимальные теплопотери.',
    'grain-mill' => 'Правильный помол — до 85% экстрактивности. Промышленные вальцы из закалённой стали для любых объёмов варки.',
    'steam-generator' => 'Компактный источник пара для варочного порядка, ЦКТ и CIP. Быстрый выход на режим, автоматика безопасности.',
    'chiller' => 'Точное управление температурой брожения. Пропиленгликоль, пластинчатый испаритель, автоматика Danfoss.',
    'unitank' => 'Дображивание, карбонизация и хранение пива под давлением. Полная герметичность, рубашка охлаждения, предохранительный клапан.',
    'heat-exchanger' => 'Максимальная теплопередача до 7000 Вт/м²·°C. Компактный, разборный, легко моется. AISI 304/316.',
];
echo $hooks[$catKey] ?? htmlspecialchars($metaDesc);
?></p>
<div class="hero-tags">
<?php
$tagPool = $data['features'] ?? [];

shuffle($tagPool);
$shown = 0;
foreach ($tagPool as $f):
    if ($shown >= 3) break;
    $clean = preg_replace('/\(.*?\)|\[.*?\]/', '', $f);
    $clean = trim($clean, ' ·,');
    if (!$clean) continue;
    ?><span><?= htmlspecialchars($clean) ?></span><?php
    $shown++;
endforeach;
?>
</div>
<div class="hero-trust">AISI 304/316 · 18+ лет на рынке · Доставка по РФ</div>
<?php if ($catKey === 'hot-water-tank'): ?>
<div style="display:flex;gap:4px;margin-top:12px;align-items:center">
<img src="/<?= htmlspecialchars($data['image']) ?>" alt="Бак горячей воды" class="thumb-img" onclick="switchImg(this)" style="height:50px;width:auto;display:block;border-radius:4px;cursor:pointer;border:2px solid #F77C2A;transition:border-color .2s">
<img src="/hot-water-tank-2.jpg" alt="Бак горячей воды" class="thumb-img" onclick="switchImg(this)" style="height:50px;width:auto;display:block;border-radius:4px;cursor:pointer;border:2px solid transparent;transition:border-color .2s">
<img src="/hot-water-tank-3.jpg" alt="Бак горячей воды" class="thumb-img" onclick="switchImg(this)" style="height:50px;width:auto;display:block;border-radius:4px;cursor:pointer;border:2px solid transparent;transition:border-color .2s">
</div>
<script>function switchImg(el){document.querySelectorAll('.thumb-img').forEach(function(t){t.style.borderColor='transparent'});el.style.borderColor='#F77C2A';document.getElementById('mainImg').src=el.src}</script>
<?php endif; ?>
</div>
</div>
</div>
</section>
<script type="application/ld+json">
{"@context":"https://
schema.org","@type":"ItemList","name":"<?= htmlspecialchars($h1) ?>","description":"<?= htmlspecialchars($metaDesc) ?>","url":"https://ob-kub.ru<?= $_SERVER['REQUEST_URI'] ?>","numberOfItems":<?= count($sorted) ?>,"itemListElement":[<?php $idx=1; foreach ($sorted as $v): $price=$data['specs'][$v]['price']; if($idx>1) echo ','; ?>{"@type":"ListItem","position":<?= $idx++ ?>,"url":"https://ob-kub.ru<?= $baseUrl . $v ?>l/","name":"<?= htmlspecialchars($data['name']) ?> <?= $v ?> <?= $specUnit ?>","offers":{"@type":"Offer","price":"<?= $price ?>","priceCurrency":"RUB"}}<?php endforeach; ?>]}
</script>
<?php
$minVol = min($sorted);
$maxVol = max($sorted);
$volCount = count($sorted);
$seoDesc = htmlspecialchars($data['desc']);

$seoGuide = [
    'chiller' => [
        'Промышленный чиллер — это холодильный агрегат для охлаждения сусла и пива на пивоварне. После варки сусло нужно быстро охладить до температуры внесения дрожжей — с этим справляется чиллер. Он подаёт охлаждённый пропиленгликоль в рубашки танков и теплообменники. Без чиллера невозможно контролировать температуру брожения и созревания пива.',
        'Как выбрать чиллер: холодопроизводительность подбирается под суммарный объём ЦКТ и скорость охлаждения. Для крафтовой пивоварни с 4–6 ЦКТ по 500–1000 л достаточно чиллера на 15–30 кВт. Для промышленной пивоварни с ЦКТ от 5000 л нужны модели 60–150 кВт. Тип хладагента — пропиленгликоль или этиленгликоль.',
        'Чиллеры «ОБОРУДОВАНИЕ КУБАНИ» комплектуются пластинчатыми теплообменниками и автоматикой. Производим в Краснодаре с 2008 года. Гарантия 12 месяцев.',
    ],
    'grain-mill' => [
        'Дробилка солода — это оборудование для измельчения солода перед затиранием. Правильный помол определяет эффективность фильтрации и выход экстракта. Двух- и четырёхвальцовые дробилки обеспечивают равномерное дробление: шелуха остаётся целой (фильтрующий слой), а эндосперм измельчается в муку.',
        'Как выбрать дробилку: производительность подбирается под объём варки. Для варок 200–500 л достаточно дробилки на 100–300 кг/ч. Для крафтовой пивоварни с варками 1000–2000 л нужна производительность 500–1000 кг/ч. Важные параметры: зазор между вальцами, количество вальцов (2 или 4), наличие магнита для металла.',
        'Дробилки «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с регулируемым зазором вальцов. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
    'hot-water-tank' => [
        'Бак горячей воды (БГВ) — это ёмкость для подготовки и хранения горячей воды на пивоварне. Горячая вода используется для затирания солода, промывания дробины после фильтрации и мойки оборудования. БГВ с паровой рубашкой и термоизоляцией ППУ позволяет быстро нагреть воду и поддерживать нужную температуру.',
        'Как выбрать БГВ: объём бака должен быть в 2–3 раза больше объёма варки. Если варите 500 л пива, запас горячей воды нужен минимум 1000–1500 л. Учитывайте расход на затирание (3–4 л воды на 1 кг солода) и промывание дробины. Самые популярные объёмы: 1000, 2000 и 5000 л.',
        'Баки горячей воды «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с паровой рубашкой и термоизоляцией ППУ. Производим в Краснодаре с 2008 года. Гарантия 12 месяцев.',
    ],
    'steam-generator' => [
        'Парогенератор — это источник пара для обогрева пивоваренного оборудования: заторников, сусловарок, рубашек ЦКТ и теплообменников. Электрические и газовые парогенераторы от 20 до 700 кг пара/ч с давлением до 6 бар. Компактные, не требуют отдельного здания котельной.',
        'Как выбрать парогенератор: производительность по пару подбирается под суммарную мощность потребителей. Для крафтовой пивоварни с варками 500–1000 л нужно 50–100 кг пара/ч. Для промышленных линий — 200–700 кг/ч. При выборе учитывайте тип топлива (электричество или газ) и наличие водоподготовки.',
        'Парогенераторы «ОБОРУДОВАНИЕ КУБАНИ» оснащаются автоматикой и соответствуют требованиям промбезопасности. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
    'unitank' => [
        'Форфас (Bright Beer Tank) — это ёмкость для дображивания, карбонизации и хранения пива под давлением. В форфасе пиво насыщается углекислотой, осветляется и готовится к розливу. В отличие от ЦКТ, форфасы рассчитаны на рабочее давление до 3 бар и оснащены полным комплектом арматуры КИП.',
        'Как выбрать форфас: объём подбирается под сменную партию пива. Для крафтовой пивоварни стандартный объём форфаса — 500–2000 л. Важно: пиво подаётся в форфас из ЦКТ после окончания брожения, поэтому форфасов должно быть столько же, сколько сортов пива в одновременной продаже.',
        'Форфасы «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с рубашкой охлаждения, термоизоляцией и полной арматурой под давление. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.',
    ],
    'heat-exchanger' => [
        'Пластинчатый теплообменник — это устройство для нагрева или охлаждения жидкостей: сусла, пива, воды, моющих растворов. Компактная конструкция из набора пластин из AISI 304/316 обеспечивает высокую эффективность теплопередачи. Используется на пивоварнях для охлаждения сусла, пастеризации и CIP-мойки.',
        'Как выбрать теплообменник: производительность подбирается под объём перекачиваемой жидкости. Для охлаждения сусла с 95°C до 20°C на варку 500 л нужен теплообменник на 2000–3000 л/ч. Для варки 2000 л — 5000–8000 л/ч. Количество пластин и их конфигурация рассчитываются под конкретную задачу.',
        'Теплообменники «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304/316 с уплотнениями EPDM. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
];
$g = $seoGuide[$catKey] ?? reset($seoGuide);

$beerArticleGeneric = ['title' => 'Как спланировать оснащение пивоварни: от варки до розлива', 'content' => ['Оснащение пивоварни начинается с расчёта производственной мощности: объём варки и цикличность. Для крафтовой пивоварни оптимальный старт — 500–1000 л на варку с возможностью сдвоенных варок в день. Полный цикл варки занимает 4–6 часов, брожение — 7–14 дней, дозревание — 14–30 дней.', 'Ключевой принцип: горячее оборудование (варочный порядок, БГВ, парогенератор) должно быть сгруппировано компактно для минимизации теплопотерь. Холодное (ЦКТ, форфасы) — в отдельном помещении с поддержанием 10–14°C. Такое зонирование повышает качество пива и снижает энергозатраты.', '«ОБОРУДОВАНИЕ КУБАНИ» поставляет комплекты для пивоварен под ключ: варочный порядок, ЦКТ, форфасы, чиллер, КИП и обвязка. Оставьте заявку — инженер подберёт оборудование по вашему ТИ и бюджету.']];
$beerTermArticle = ['title' => 'Как разобраться в названиях пивоваренного оборудования', 'content' => ['В пивоварении одна и та же единица оборудования может называться по-разному: профессиональный жаргон, английские термины, сокращения. Понимание синонимов поможет быстрее разобраться в каталоге и при общении с поставщиками.', 'ЦКТ = цилиндро-конический танк = танк брожения = конический танк. Заторный аппарат = маш тюн = заторный чан. Фильтрационный аппарат = лаутер тюн = фильтрчан. Сусловарочный аппарат = сусловарка = варочный котел. Гидроциклонный аппарат = вирпул = whirlpool. Форфас = лагерный танк = юнит = unitank = BBT. Бак горячей воды = БГВ. ЗСА = заторно-сусловарочный аппарат.', 'Всё наше оборудование производится из нержавеющей стали AISI 304/316, поэтому материал в названии не указывается. Если вы встретили незнакомый термин — вбейте его в поиск на сайте, система найдёт правильную категорию. Или позвоните нам — поможем разобраться и подготовим КП.']];

$articleGuide = [
    'chiller' => [
        ['title' => 'Как выбрать чиллер', 'content' => ['Холодопроизводительность чиллера подбирается под суммарный объём ЦКТ. Для крафтовой пивоварни с 4–6 танками по 500–1000 л достаточно 15–30 кВт. Для промышленных линий с ЦКТ от 5000 л — от 60 кВт. Важен тип хладагента: пропиленгликоль оптимален для пищевого производства.', 'При выборе чиллера обратите внимание на тип компрессора и испарителя. Спиральные компрессоры Copeland/Zanotti надёжнее поршневых. Пластинчатый испаритель из AISI 316 эффективнее кожухотрубного. Наличие гидромодуля (насос + бак гликоля) упрощает монтаж и обвязку.', 'Чиллер обязательно комплектуется автоматикой: датчик температуры гликоля, реле протока, защита по давлению. Современные модели поддерживают Modbus для интеграции в общую систему управления пивоварней.']],
        $beerArticleGeneric,
        $beerTermArticle,
    ],
    'grain-mill' => [
        ['title' => 'Как выбрать дробилку солода', 'content' => ['Производительность дробилки подбирается под объём варки. Для крафтовой пивоварни с варками 200–500 л достаточно 100–300 кг/ч. Для варок 1000–2000 л нужна производительность 500–1000 кг/ч. Для промышленных пивоварен — от 1500 кг/ч.', 'Ключевые параметры: количество вальцов (2 или 4), регулировка зазора, наличие магнита для металлических примесей. Четырёхвальцовые дробилки дают более равномерный помол, но требуют больше обслуживания. Зазор между вальцами — основной параметр настройки под разные сорта солода.', 'Материал вальцов — закалённая сталь, корпус — AISI 304. Обязательно наличие приёмного бункера и системы аспирации для удаления пыли. Для автоматизации можно установить частотный регулятор скорости подачи солода.']],
        $beerArticleGeneric,
        $beerTermArticle,
    ],
    'hot-water-tank' => [
        ['title' => 'Как выбрать бак горячей воды', 'content' => ['Объём бака горячей воды (БГВ) должен быть в 2–3 раза больше объёма варки. На варку 500 л пива нужно 1000–1500 л горячей воды. Расход на затирание — 3–4 л воды на 1 кг солода, на промывание дробины — ещё 1–2 л/кг. Самые популярные объёмы БГВ: 1000, 2000 и 5000 л.', 'Нагрев воды осуществляется паровой рубашкой или электрическими ТЭНами. Паровая рубашка обеспечивает более быстрый нагрев и равномерное распределение температуры. Термоизоляция ППУ 50–100 мм обязательна для снижения теплопотерь.', 'БГВ оснащается: термометром, уровнемером, предохранительным клапаном, патрубками подачи/отбора воды. Для автоматизации устанавливается датчик температуры Pt100 и электромагнитный клапан подачи пара.']],
        $beerArticleGeneric,
        $beerTermArticle,
    ],
    'steam-generator' => [
        ['title' => 'Как выбрать парогенератор', 'content' => ['Производительность парогенератора подбирается под суммарную мощность потребителей пара. Для крафтовой пивоварни с варками 500–1000 л нужно 50–100 кг пара/ч. Для промышленных линий — 200–700 кг/ч. Давление пара — стандартно 4–6 бар.', 'Тип топлива: электрические парогенераторы (20–150 кВт) компактны и не требуют дымохода, но дороги в эксплуатации. Газовые — экономичнее при больших объёмах, но требуют проекта газификации и обслуживания. Электрические популярнее для малых и средних пивоварен.', 'Парогенератор обязательно оснащается: автоматикой безопасности, предохранительным клапаном, системой водоподготовки (умягчение воды). Наличие блока ТЭНов в сборе упрощает обслуживание. Современные модели выходят на режим за 5–15 минут.']],
        $beerArticleGeneric,
        $beerTermArticle,
    ],
    'unitank' => [
        ['title' => 'Как выбрать форфас', 'content' => ['Объём форфаса (BBT) подбирается под сменную партию пива. Для крафтовой пивоварни стандартный объём — 500–2000 л. Важно: форфасов должно быть столько же, сколько сортов пива в одновременной продаже. Минимальное количество — 2 на сорт (один в работе, второй на мойке).', 'Рабочее давление — до 3 бар. Этого достаточно для карбонизации и дозированной выдачи пива. Конструкция: рубашка охлаждения (для поддержания 0–4°C), термоизоляция ППУ, предохранительный клапан, манометр, калиброванный трубопровод.', 'Для розла изотермического пива форфас комплектуется: пеногасителем, клапаном CO₂, пробоотборным краном, CIP-головкой. По запросу — рубашка охлаждения на конус и смотровой люк с подсветкой. Горизонтальные форфасы удобнее для низких помещений.']],
        $beerArticleGeneric,
        $beerTermArticle,
    ],
    'heat-exchanger' => [
        ['title' => 'Как выбрать теплообменник', 'content' => ['Производительность теплообменника подбирается под объём перекачиваемой жидкости. Для охлаждения сусла с 95°C до 20°C на варку 500 л нужен теплообменник на 2000–3000 л/ч. Для варки 2000 л — 5000–8000 л/ч. Количество пластин и их конфигурация рассчитываются инженером.', 'Материал пластин — AISI 304 или AISI 316. Для охлаждения сусла и пива достаточно AISI 304. Для агрессивных моющих сред (кислоты, щёлочи) рекомендуется AISI 316. Уплотнения — EPDM (до 140°C) или Viton (до 180°C для CIP-мойки).', 'Пластинчатый теплообменник компактен и легко разбирается для осмотра и чистки. Важный параметр — возможность добавления пластин для увеличения производительности. Для пивоварен популярны теплообменники с зоной рекуперации (предварительный подогрев воды).']],
        $beerArticleGeneric,
        $beerTermArticle,
    ],
];
$articleData = $articleGuide[$catKey] ?? reset($articleGuide);
?>
<div class="seo-text-wrap">
    <div class="seo-text-card">
        <div class="seo-text-head">Полезная информация</div>
        <div class="seo-text collapsed">
        <p><?= $g[0] ?></p>
        <p><strong>В каталоге представлено <?= $volCount ?> моделей от <?= $minVol ?> до <?= $maxVol ?> <?= $specUnit ?>.</strong> <?= $g[1] ?></p>
        <p><?= $g[2] ?></p>
        <p>«ОБОРУДОВАНИЕ КУБАНИ» — это 18 лет на рынке, собственное производство полного цикла в Краснодаре (цех 2000 м²), контроль качества и сертификаты соответствия. Доставляем по всей России и странам СНГ любой транспортной компанией. Гарантия на оборудование — 12 месяцев. Оставьте заявку — инженер подготовит коммерческое предложение с точной стоимостью, сроками изготовления и доставки для вашего проекта.</p>
        </div>
        <button class="seo-text-toggle" onclick="var t=this.previousElementSibling;t.classList.toggle('expanded');t.classList.toggle('collapsed');this.textContent=t.classList.contains('expanded')?'Свернуть':'Читать полностью'">Читать полностью</button>
    </div>
</div>
<section class="container">
    <div class="section-head">
        <h2 class="section-title">Выберите подходящий объём</h2>
        <p class="section-desc">Нажмите на карточку, чтобы перейти к подробным характеристикам, чертежам и стоимости</p>
    </div>

<div class="volumes-grid">
<?php
$midIdx = floor(count($sorted) / 2);
$popular = $sorted[$midIdx] ?? null;
foreach ($sorted as $i => $vol):
    $s = $data['specs'][$vol];
    $price = $s['price'];
    $priceStr = $price >= 1000000 ? number_format($price/1000000,1,'.','').' млн ₽' : ($price >= 1000 ? number_format($price/1000,0,'.','').' тыс ₽' : number_format($price,0,'.',' ').' ₽');
    $volUrl = $baseUrl . $vol . 'l/';
    $diamM = $hasDim ? number_format($s['diameter']/1000,2,'.','') : '';
    $hM = $hasDim ? number_format($s['height']/1000,2,'.','') : '';
    $isPopular = ($vol === $popular);
?>
<a href="<?= htmlspecialchars($volUrl) ?>" class="vol-card">
<?php if ($isPopular): ?><div class="popular-badge">⭐ Популярный</div><?php endif; ?>
<div class="vol-card-body">
<div class="vol-label"><?= htmlspecialchars($specLabel) ?></div>
<div class="vol-value"><?= $vol ?><span class="vol-unit"> <?= $specUnit ?></span></div>
<div class="price">от <?= $priceStr ?></div>
<div class="specs">
<?php if (!empty($s['full_volume'])): ?>
<div><span class="sl">Полный:</span><span> <?= number_format($s['full_volume'], 0, '.', ' ') ?> л</span></div>
<div><span class="sl">Рабочий:</span><span> <?= number_format($s['working_volume'], 0, '.', ' ') ?> л</span></div>
<?php endif; ?>
<?php if ($hasDim): ?>
<div><span class="sl">Диаметр:</span><span> <?= $diamM ?> м</span></div>
<div><span class="sl">Высота:</span><span> <?= $hM ?> м</span></div>
<?php endif; ?>
<div><span class="sl">Вес:</span><span> <?= $s['weight'] ?> кг</span></div>
<div><span class="sl">Мощность:</span><span> <?= $s['power'] ?> кВт</span></div>
</div>
</div>
<div class="vol-card-footer"><span class="btn-elect">Выбрать</span></div>
</a>
<?php endforeach; ?>
</div>
</section>

<section class="article-section">
    <div class="container">
        <div class="article-grid">
        <?php foreach ($articleData as $ad): ?>
        <div class="article-card">
            <div class="article-header">
                <span class="article-tag">Статья по теме</span>
                <h2 class="article-title"><?= htmlspecialchars($ad['title']) ?></h2>
            </div>
            <div class="article-body">
                <p><?= $ad['content'][0] ?></p>
                <div class="article-full collapsed">
                <p><?= $ad['content'][1] ?></p>
                <p><?= $ad['content'][2] ?></p>
                <div class="article-cta">
                    <p>Нужна консультация по выбору? Инженер подберёт оптимальную модель под вашу задачу и подготовит КП с точной стоимостью.</p>
                    <a href="/beer.html#order-form" class="article-btn">Получить расчёт →</a>
                </div>
                </div>
                <button class="article-toggle" onclick="openArticleModal(this)">Читать статью полностью</button>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="article-modal" id="articleModal" onclick="if(event.target===this)closeArticleModal()"><div class="article-modal-backdrop"></div><div class="article-modal-card"><button class="article-modal-close" onclick="closeArticleModal()">&times;</button><div class="article-modal-header"><span class="article-tag">Статья по теме</span><h2 class="article-modal-title" id="articleModalTitle"></h2></div><div class="article-modal-body" id="articleModalBody"></div><div class="article-modal-cta"><p>Нужна консультация по выбору? Инженер подберёт оптимальную модель под вашу задачу и подготовит КП с точной стоимостью.</p><a href="/beer.html#order-form" class="article-btn">Получить расчёт →</a></div></div></div>
<script>function openArticleModal(b){var c=b.closest('.article-card'),t=c.querySelector('.article-title').textContent,h='';c.querySelectorAll('.article-full>*').forEach(function(e){if(!e.classList.contains('article-cta'))h+=e.outerHTML});document.getElementById('articleModalTitle').textContent=t;document.getElementById('articleModalBody').innerHTML=h;document.getElementById('articleModal').classList.add('active');document.body.style.overflow='hidden'}function closeArticleModal(){document.getElementById('articleModal').classList.remove('active');document.body.style.overflow=''}</script>
<?php require __DIR__ . '/../layout-end.php'; }

function renderItemPage($catKey, $vol, $data) {
    $opts = [
        'canonical' => "https://ob-kub.ru/catalog/beer/{$catKey}/{$vol}l/",
        'baseUrl' => "/catalog/beer/{$catKey}/",
        'catPrefix' => "/catalog/beer/",
        'categoryName' => 'Пивоваренное оборудование',
        'categoryUrl' => '/catalog/beer/',
        'formType' => 'beer-item',
        'specUnit' => $data['spec_unit'] ?? 'л',
        'specLabel' => $data['spec_label'] ?? 'Объём',
        'allSpecs' => $data['specs'],
        'breadcrumbMiddle' => 'Пивоваренное оборудование',
        'breadcrumbMiddleUrl' => '/catalog/beer/',
    ];
    renderProductPage($catKey, $vol, $data, $opts);
}
