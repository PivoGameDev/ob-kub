<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/../industrial-data.php';
require __DIR__ . '/../helpers/product-template.php';

$uri = $_SERVER['REQUEST_URI'];
$cat = !empty($_SERVER['CATALOG_CATEGORY']) ? $_SERVER['CATALOG_CATEGORY'] : '';
$volume = 0;
if (!empty($_SERVER['CATALOG_VOLUME'])) {
    $volume = (int)$_SERVER['CATALOG_VOLUME'];
}
if (!$cat && preg_match('#/catalog/industrial/(
storage|mixing|thermal|pressure)/#', $uri, $m)) {
    $cat = $m[1];
}
if (!$volume && preg_match('#/(\d+)l?/?#', $uri, $m)) {
    $volume = (int)$m[1];
}

if (!$cat || !isset($industrialData[$cat])) {
    header('HTTP/1.0 404 Not Found');
    echo 'Категория не найдена';
    exit;
}

$data = $industrialData[$cat];
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
    $baseUrl = '/catalog/industrial/' . $catKey . '/';
    $hasDim = !empty($data['specs'][$sorted[0]]['diameter']);
    header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
<title><?= htmlspecialchars($metaTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
<link rel="canonical" href="https://ob-kub.ru<?= $baseUrl ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style-original.css">
<link rel="stylesheet" href="/css/catalog-mobile.css">
<link rel="stylesheet" href="/css/header.css">
<link rel="icon" href="/favicon.png">
<?php require __DIR__ . '/../catalog-styles.php'; ?>
<style><?= $inlineStyles ?></style>
<style>.article-section{background:#f8f9fb;padding:40px 0 56px;margin-top:8px}.article-card{background:#fff;border-radius:12px;padding:32px 36px;box-shadow:0 2px 12px rgba(0,0,0,.04)}.article-header{margin-bottom:20px}.article-tag{display:inline-block;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#F77C2A;background:rgba(247,124,42,.08);padding:3px 10px;border-radius:4px;margin-bottom:8px}.article-title{font-size:20px;font-weight:800;color:#1a1a26;margin:8px 0 0;line-height:1.3}.article-body{font-size:15px;line-height:1.75;color:#555}.article-body h3{font-size:15px;font-weight:700;color:#1a1a26;margin:24px 0 8px;padding:0}.article-body p{margin:0 0 12px}.article-body a{color:#F77C2A;text-decoration:underline;text-underline-offset:2px}.article-body a:hover{color:#e06a1a}.article-cta{margin-top:24px;padding:20px 24px;background:linear-gradient(135deg,#fff8f0,#fff);border:1px solid #fed7a8;border-radius:10px}.article-cta p{margin:0 0 14px;font-size:14px;color:#1a1a26;font-weight:600}.article-full{overflow:hidden;transition:max-height .4s ease}.article-full.collapsed{max-height:0}.article-full.expanded{max-height:none}.article-btn{display:inline-block;padding:11px 24px;background:#1a1a26;color:#fff;border-radius:8px;font-size:14px;font-weight:700;text-decoration:none;transition:background .2s,transform .2s}.article-btn:hover{background:#333;color:#fff;transform:scale(1.02)}.article-toggle{color:#F77C2A;cursor:pointer;font-weight:600;font-size:13px;border:none;background:none;padding:4px 0 0;display:inline-flex;align-items:center;gap:6px;transition:color .2s;font-family:inherit;margin-top:4px}.article-toggle::after{content:'↓';font-size:11px;transition:transform .2s}.article-toggle:hover{color:#e06a1a}.article-toggle:hover::after{transform:translateY(2px)}.article-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}.article-modal{position:fixed;top:0;left:0;width:100%;height:100%;z-index:99999;opacity:0;pointer-events:none;transition:opacity .3s}.article-modal.active{opacity:1;pointer-events:auto}.article-modal-backdrop{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.55);backdrop-filter:blur(4px)}.article-modal-card{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:14px;max-width:680px;width:90%;max-height:80vh;overflow-y:auto;padding:36px 40px;box-shadow:0 20px 60px rgba(0,0,0,.3)}.article-modal-close{position:absolute;top:8px;right:12px;font-size:28px;background:none;border:none;cursor:pointer;color:#bbb;line-height:1;padding:4px 8px;border-radius:6px;transition:color .2s,background .2s;z-index:1}.article-modal-close:hover{color:#333;background:#f5f5f5}.article-modal-header{margin-bottom:20px;padding-right:30px}.article-modal-title{font-size:20px;font-weight:800;color:#1a1a26;margin:8px 0 0;line-height:1.3}.article-modal-body{font-size:15px;line-height:1.75;color:#555}.article-modal-body h3{font-size:15px;font-weight:700;color:#1a1a26;margin:24px 0 8px;padding:0}.article-modal-body p{margin:0 0 12px}.article-modal-cta{margin-top:24px;padding:20px 24px;background:linear-gradient(135deg,#fff8f0,#fff);border:1px solid #fed7a8;border-radius:10px}.article-modal-cta p{margin:0 0 14px;font-size:14px;color:#1a1a26;font-weight:600}@media(max-width:700px){.article-grid{grid-template-columns:1fr;gap:16px}.article-modal-card{padding:24px 20px;max-height:90vh}}</style>
</head>
<body class="brewery-page cct-page">
<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; ?>
<main>
<section class="list-hero">
<div class="container">
<div class="breadcrumbs">
<a href="/">Главная</a><span class="ep">/</span>
<a href="/catalog/">Каталог</a><span class="ep">/</span>
<a href="/catalog/industrial/">Промышленное оборудование</a><span class="ep">/</span>
<span class="current"><?= htmlspecialchars($h1) ?></span>
</div>
<div class="list-hero-inner">
<div class="list-hero-img"><img 
src="/<?= htmlspecialchars($data['image']) ?>" alt="<?= htmlspecialchars($data['name']) ?>"></div>
<div class="list-hero-text">
<h1><?= htmlspecialchars($h1) ?></h1>
<p class="hero-ub"><?php
$hooks = [
    '
storage' => 'Надёжные резервуары хранения из AISI 304 для молока, соков, пива, сиропов, масла и других пищевых жидкостей. Вертикальные и горизонтальные, с изоляцией и без.',
    'mixing' => 'Ёмкости с механической мешалкой для гомогенизации, растворения и перемешивания пищевых продуктов. Лопастные, якорные, турбинные и пропеллерные мешалки.',
    'thermal' => 'Терморегулируемые ёмкости с рубашкой нагрева и охлаждения. Для пастеризации, ферментации, выдержки, горячего розлива. PID-контроль температуры.',
    'pressure' => 'Напорные ёмкости и ресиверы из AISI 304, рабочее давление до 6 бар. Для хранения под давлением, выдачи, аэрации, CIP-мойки.',
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
</div>
</div>
</div>
</section>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"ItemList","name":"<?= htmlspecialchars($h1) ?>","description":"<?= htmlspecialchars($metaDesc) ?>","url":"https://ob-kub.ru<?= $_SERVER['REQUEST_URI'] ?>","numberOfItems":<?= count($sorted) ?>,"itemListElement":[<?php $idx=1; foreach ($sorted as $v): $price=$data['specs'][$v]['price']; if($idx>1) echo ','; ?>{"@type":"ListItem","position":<?= $idx++ ?>,"url":"https://ob-kub.ru<?= $baseUrl . $v ?>l/","name":"<?= htmlspecialchars($data['name']) ?> <?= $v ?> л","offers":{"@type":"Offer","price":"<?= $price ?>","priceCurrency":"RUB"}}<?php endforeach; ?>]}
</script>
<?php
$minVol = min($sorted);
$maxVol = max($sorted);
$volCount = count($sorted);
$seoDesc = htmlspecialchars($data['desc']);
$specUnit = $data['spec_unit'] ?? 'л';

$seoGuide = [
    'storage' => [
        'Промышленный резервуар для хранения — это универсальная ёмкость из нержавеющей стали AISI 304/316 для приёма, хранения и выдачи жидких продуктов. Используется на пищевых, молочных, соковых заводах, в фармацевтике и химической промышленности. Доступны вертикальные и горизонтальные исполнения до 200 000 литров.',
        'Как выбрать резервуар хранения: объём подбирается под суточный запас продукта с запасом 20–30%. Для пищевых производств достаточно AISI 304 с полировкой Ra ≤ 0,8 мкм. Для химических сред требуется AISI 316. Вертикальные резервуары экономят площадь, горизонтальные удобны при ограниченной высоте помещения.',
        'Резервуары «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304/316 с полной арматурой и люками. Производим в Краснодаре с 2008 года. Гарантия 12 месяцев.',
    ],
    'mixing' => [
        'Ёмкость с мешалкой предназначена для перемешивания, гомогенизации и смешивания жидких и вязких продуктов. Используется для приготовления купажей, растворения сыпучих компонентов, выравнивания температуры и состава продукта. Тип мешалки подбирается под конкретную задачу.',
        'Как выбрать ёмкость с мешалкой: объём подбирается под партию продукта. Для малых производств подходят 200–2000 л, для промышленных — 3000–50000 л. Тип мешалки: лопастная (для смешивания), пропеллерная (для растворения), турбинная (для эмульгирования), рамная (для вязких продуктов).',
        'Ёмкости «ОБОРУДОВАНИЕ КУБАНИ» комплектуются мешалками любого типа под вашу задачу. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
    'thermal' => [
        'Терморегулируемая ёмкость (термоёмкость) предназначена для нагрева, охлаждения и выдержки продуктов при заданной температуре. Оснащается рубашкой (паровой, водяной, гликолевой) с термоизоляцией ППУ. Используется в пищевой, молочной, пивоваренной и фармацевтической промышленности.',
        'Как выбрать термоёмкость: объём подбирается под партию продукта. Тип рубашки: паровая (нагрев до 150°C), водяная (до 90°C), гликолевая (охлаждение до −10°C). Важна точность поддержания температуры (допуск ±1°C) и наличие автоматики с датчиком Pt100.',
        'Термоёмкости «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304/316 с рубашкой и автоматикой. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.',
    ],
    'pressure' => [
        'Ёмкость под давлением — это герметичный резервуар для хранения и выдачи продуктов под избыточным давлением до 6 бар. Используется для углекислоты, аэрации воды, хранения пива, сиропов и других продуктов, требующих давления. Оснащается предохранительными клапанами и арматурой КИП.',
        'Как выбрать напорную ёмкость: объём подбирается под расход продукта и частоту заполнения. Рабочее давление выбирается по технологии: 1–2 бара для выдачи продуктов, 3–6 бар для аэрации и насыщения газами. Все ёмкости проходят гидравлические испытания с коэффициентом запаса 1.5.',
        'Ёмкости под давлением «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304/316 с арматурой КИП. Проходят гидроиспытания. Гарантия 12 месяцев.',
    ],
    'cip' => [
        'CIP-станция (Clean-In-Place) — это система безразборной мойки технологического оборудования. Станция автоматически подаёт моющий раствор (щёлочь, кислота, вода) по замкнутому контуру с заданной температурой, концентрацией и временем цикла. Используется на всех пищевых, молочных и пивоваренных производствах.',
        'Как выбрать CIP-станцию: количество контуров (1–3) подбирается под число независимых линий мойки. Объём баков — под ёмкость самого большого оборудования. Мощность нагрева — под скорость выхода на режим. Для малых производств достаточно компактной одно- или двухконтурной станции.',
        'CIP-станции «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с автоматикой и насосами. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
    'heat-exchanger' => [
        'Промышленный теплообменник — это устройство для нагрева или охлаждения жидких продуктов: молока, пива, соков, сиропов, воды. Пластинчатые теплообменники из AISI 304/316 обеспечивают высокую эффективность теплопередачи в компактном корпусе. Используются для пастеризации, охлаждения и CIP-мойки.',
        'Как выбрать теплообменник: производительность подбирается под объём перекачиваемой жидкости. Для охлаждения 1000 л/ч молока нужен теплообменник на 2000–3000 л/ч (с запасом). Количество и конфигурация пластин рассчитываются под конкретные температуры входа/выхода. Материал пластин — AISI 304 или AISI 316.',
        'Теплообменники «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304/316 с уплотнениями EPDM/Viton. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.',
    ],
];
$g = $seoGuide[$catKey] ?? reset($seoGuide);
$indTermArticle = ['title' => 'Как разобраться в названиях промышленного оборудования', 'content' => ['В промышленности одну и ту же ёмкость часто называют по-разному: технологи, снабженцы и монтажники используют свой жаргон. Понимание этих синонимов ускорит поиск нужного оборудования и исключит ошибки при заказе.', 'Резервуар для хранения = складской резервуар = накопитель = хранилище. Ёмкость с мешалкой = бак с мешалкой = танк с мешалкой = аппарат с мешалкой. Ёмкость с терморегуляцией = термостатированная ёмкость = ёмкость с рубашкой = танк с терморегуляцией. Ёмкость под давлением = напорная ёмкость = герметичный сосуд = сосуд под давлением.', 'Всё наше оборудование производится из нержавеющей стали AISI 304/316, поэтому материал — стандарт, а не особенность. Если сомневаетесь в выборе — просто вбейте свой запрос в поиск на сайте или позвоните инженеру.']];
$articleGuide = [
    'storage' => [
        ['title' => 'Как выбрать резервуар хранения', 'content' => ['Объём подбирается под суточный запас продукта с запасом 20–30%. Для пищевых производств достаточно AISI 304 с полировкой Ra ≤ 0,8 мкм. Для химических сред требуется AISI 316. Вертикальные экономят площадь, горизонтальные удобны при низких потолках.', 'Термоизоляция ППУ 50–100 мм для поддержания температуры. Полная арматура: люк-лаз, уровнемер, CIP-мойка, патрубки подачи и отбора. Для агрессивных сред — усиленная полировка и AISI 316.', 'По запросу: рубашка обогрева/охлаждения, мешалка, датчики температуры и уровня. Все резервуары проходят гидроиспытания. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.']],
        ['title' => 'Как заказать ёмкость из нержавейки: 5 шагов', 'content' => ['Разработка технического задания — первый и самый важный шаг. Укажите объём, тип продукта, рабочую температуру и давление, необходимые опции (рубашка, мешалка, CIP-мойка). Чем подробнее ТЗ, тем точнее будет расчёт и тем быстрее инженер подготовит КП.', 'После ТЗ мы готовим чертёж общего вида и коммерческое предложение. Согласовываем материалы (AISI 304 или 316), толщину стенки, тип изоляции, арматуру. После утверждения чертежа запускаем в производство — от 3 до 8 недель в зависимости от сложности.', 'Готовый резервуар проходит гидроиспытания, получает паспорт и сертификат. Отгружаем любой транспортной компанией по РФ и СНГ. Возможен шеф-монтаж на объекте инженерами «ОБОРУДОВАНИЕ КУБАНИ». Гарантия 12 месяцев.']],
        $indTermArticle,
    ],
    'mixing' => [
        ['title' => 'Как выбрать ёмкость с мешалкой', 'content' => ['Объём подбирается под партию продукта. Для малых производств — 200–2 000 л, для промышленных — 3 000–50 000 л. Тип мешалки: лопастная (для смешивания), пропеллерная (для растворения), турбинная (для эмульгирования), рамная (для вязких продуктов).', 'Мотор-редуктор подбирается под вязкость и объём. Для воды и маловязких жидкостей достаточно 0.5–3 кВт, для вязких — 5–15 кВт. Регулируемая скорость — опционально. Уплотнение вала — сальниковое или торцевое.', 'Материал — AISI 304, для агрессивных сред AISI 316. Оснащение: отбойные перегородки для улучшения перемешивания, смотровой люк, CIP-мойка. Гарантия 12 месяцев. Доставка по РФ и СНГ.']],
        ['title' => 'Как заказать ёмкость из нержавейки: 5 шагов', 'content' => ['Разработка технического задания — первый и самый важный шаг. Укажите объём, тип продукта, рабочую температуру и давление, необходимые опции (рубашка, мешалка, CIP-мойка). Чем подробнее ТЗ, тем точнее будет расчёт и тем быстрее инженер подготовит КП.', 'После ТЗ мы готовим чертёж общего вида и коммерческое предложение. Согласовываем материалы (AISI 304 или 316), толщину стенки, тип изоляции, арматуру. После утверждения чертежа запускаем в производство — от 3 до 8 недель в зависимости от сложности.', 'Готовый резервуар проходит гидроиспытания, получает паспорт и сертификат. Отгружаем любой транспортной компанией по РФ и СНГ. Возможен шеф-монтаж на объекте инженерами «ОБОРУДОВАНИЕ КУБАНИ». Гарантия 12 месяцев.']],
        $indTermArticle,
    ],
    'thermal' => [
        ['title' => 'Как выбрать термоёмкость', 'content' => ['Объём подбирается под партию продукта. Тип рубашки: паровая (нагрев до 150°C), водяная (до 90°C), гликолевая (охлаждение до −10°C). Точность поддержания температуры — ±1°C. Датчик Pt100 с PID-регулятором.', 'Термоизоляция ППУ 50–100 мм обязательна. Рубашка может быть цельной или секционной для разных зон нагрева/охлаждения. Для паровых рубашек — конденсатоотводчик и предохранительный клапан.', 'Материал — AISI 304/316 в зависимости от продукта. Оснащение: CIP-мойка, смотровой люк, пробоотборный кран. По запросу: мешалка, программируемый профиль температуры. Гарантия 12 месяцев.']],
        ['title' => 'Как заказать ёмкость из нержавейки: 5 шагов', 'content' => ['Разработка технического задания — первый и самый важный шаг. Укажите объём, тип продукта, рабочую температуру и давление, необходимые опции (рубашка, мешалка, CIP-мойка). Чем подробнее ТЗ, тем точнее будет расчёт и тем быстрее инженер подготовит КП.', 'После ТЗ мы готовим чертёж общего вида и коммерческое предложение. Согласовываем материалы (AISI 304 или 316), толщину стенки, тип изоляции, арматуру. После утверждения чертежа запускаем в производство — от 3 до 8 недель в зависимости от сложности.', 'Готовый резервуар проходит гидроиспытания, получает паспорт и сертификат. Отгружаем любой транспортной компанией по РФ и СНГ. Возможен шеф-монтаж на объекте инженерами «ОБОРУДОВАНИЕ КУБАНИ». Гарантия 12 месяцев.']],
        $indTermArticle,
    ],
    'pressure' => [
        ['title' => 'Как выбрать напорную ёмкость', 'content' => ['Объём подбирается под расход продукта и частоту заполнения. Рабочее давление: 1–2 бара для выдачи продуктов, 3–6 бар для аэрации и насыщения газами. Все ёмкости проходят гидравлические испытания с коэффициентом запаса 1.5.', 'Конструкция: эллиптическое или торосферическое днище, предохранительный клапан, манометр, арматура КИП. Для пищевых продуктов — санитарное исполнение с CIP-мойкой.', 'Материал — AISI 304, для агрессивных сред AISI 316. По запросу: рубашка обогрева, уровнемер, датчик давления. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.']],
        ['title' => 'Как заказать ёмкость из нержавейки: 5 шагов', 'content' => ['Разработка технического задания — первый и самый важный шаг. Укажите объём, тип продукта, рабочую температуру и давление, необходимые опции (рубашка, мешалка, CIP-мойка). Чем подробнее ТЗ, тем точнее будет расчёт и тем быстрее инженер подготовит КП.', 'После ТЗ мы готовим чертёж общего вида и коммерческое предложение. Согласовываем материалы (AISI 304 или 316), толщину стенки, тип изоляции, арматуру. После утверждения чертежа запускаем в производство — от 3 до 8 недель в зависимости от сложности.', 'Готовый резервуар проходит гидроиспытания, получает паспорт и сертификат. Отгружаем любой транспортной компанией по РФ и СНГ. Возможен шеф-монтаж на объекте инженерами «ОБОРУДОВАНИЕ КУБАНИ». Гарантия 12 месяцев.']],
        $indTermArticle,
    ],
];
$articleData = $articleGuide[$catKey] ?? reset($articleGuide);
?>
<div class="seo-text-wrap">
    <div class="seo-text-card">
        <div class="seo-text-head">Полезная информация</div>
        <div class="seo-text collapsed">
        <p><?= $g[0] ?></p>
        <p><strong>В каталоге представлено <?= $volCount ?> моделей объёмом от <?= $minVol ?> до <?= $maxVol ?> <?= $specUnit ?>.</strong> <?= $g[1] ?></p>
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
<div class="vol-label">Объём</div>
<div class="vol-value"><?= $vol ?><span class="vol-unit"> л</span></div>
<div class="price">от <?= $priceStr ?></div>
<div class="specs">
<?php if (!empty($s['full_volume'])): ?>
<div><span class="l">Полный:</span><span> <?= number_format($s['full_volume'], 0, '.', ' ') ?> л</span></div>
<div><span class="l">Рабочий:</span><span> <?= number_format($s['working_volume'], 0, '.', ' ') ?> л</span></div>
<?php endif; ?>
<?php if ($hasDim): ?>
<div><span class="l">Диаметр:</span><span> <?= $diamM ?> м</span></div>
<div><span class="l">Высота:</span><span> <?= $hM ?> м</span></div>
<?php endif; ?>
<div><span class="l">Вес:</span><span> <?= $s['weight'] ?> кг</span></div>
<div><span class="l">Мощность:</span><span> <?= $s['power'] ?> кВт</span></div>
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
                    <a href="/industrial.html#order-form" class="article-btn">Получить расчёт →</a>
                </div>
                </div>
                <button class="article-toggle" onclick="openArticleModal(this)">Читать статью полностью</button>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="article-modal" id="articleModal" onclick="if(event.target===this)closeArticleModal()"><div class="article-modal-backdrop"></div><div class="article-modal-card"><button class="article-modal-close" onclick="closeArticleModal()">&times;</button><div class="article-modal-header"><span class="article-tag">Статья по теме</span><h2 class="article-modal-title" id="articleModalTitle"></h2></div><div class="article-modal-body" id="articleModalBody"></div><div class="article-modal-cta"><p>Нужна консультация по выбору? Инженер подберёт оптимальную модель под вашу задачу и подготовит КП с точной стоимостью.</p><a href="/industrial.html#order-form" class="article-btn">Получить расчёт →</a></div></div></div>
<script>function openArticleModal(b){var c=b.closest('.article-card'),t=c.querySelector('.article-title').textContent,h='';c.querySelectorAll('.article-full>*').forEach(function(e){if(!e.classList.contains('article-cta'))h+=e.outerHTML});document.getElementById('articleModalTitle').textContent=t;document.getElementById('articleModalBody').innerHTML=h;document.getElementById('articleModal').classList.add('active');document.body.style.overflow='hidden'}function closeArticleModal(){document.getElementById('articleModal').classList.remove('active');document.body.style.overflow=''}</script>
</main>
<?php require $_SERVER['DOCUMENT_ROOT'].'/php/footer.php'; ?>
<?php
}

function renderItemPage($catKey, $vol, $data) {
    $opts = [
        'canonical' => "https://ob-kub.ru/catalog/industrial/{$catKey}/{$vol}l/",
        'baseUrl' => "/catalog/industrial/{$catKey}/",
        'catPrefix' => "/catalog/industrial/",
        'categoryName' => 'Промышленное оборудование',
        'categoryUrl' => '/catalog/industrial/',
        'formType' => 'industrial-item',
        'specUnit' => 'л',
        'specLabel' => 'Объём',
        'allSpecs' => $data['specs'],
        'breadcrumbMiddle' => 'Промышленное оборудование',
        'breadcrumbMiddleUrl' => '/catalog/industrial/',
    ];
    renderProductPage($catKey, $vol, $data, $opts);
}
