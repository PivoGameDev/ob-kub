<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/../dairy-data.php';
require __DIR__ . '/../helpers/product-template.php';

$uri = $_SERVER['REQUEST_URI'];
$cat = !empty($_SERVER['CATALOG_CATEGORY']) ? $_SERVER['CATALOG_CATEGORY'] : '';
$volume = 0;
if (!empty($_SERVER['CATALOG_VOLUME'])) {
    $volume = (int)$_SERVER['CATALOG_VOLUME'];
}
if (!$cat && preg_match('#/catalog/dairy/(reception|cooler|
storage|vdp|fermentation|cheese-maker|cottage-cheese|yeast|brine|cheese-shelves)/#', $uri, $m)) {
    $cat = $m[1];
}
if (!$volume && preg_match('#/(\d+)l?/?#', $uri, $m)) {
    $volume = (int)$m[1];
}

if (!$cat || !isset($dairyData[$cat])) {
    header('HTTP/1.0 404 Not Found');
    echo 'Категория не найдена';
    exit;
}

$data = $dairyData[$cat];
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
    $baseUrl = '/catalog/dairy/' . $catKey . '/';
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
<style>.article-section{background:#f8f9fb;padding:40px 0 56px;margin-top:8px}.article-card{background:#fff;border-radius:12px;padding:32px 36px;box-shadow:0 2px 12px rgba(0,0,0,.04)}.article-header{margin-bottom:20px}.article-tag{display:inline-block;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#F77C2A;background:rgba(247,124,42,.08);padding:3px 10px;border-radius:4px;margin-bottom:8px}.article-title{font-size:20px;font-weight:800;color:#1a1a26;margin:8px 0 0;line-height:1.3}.article-body{font-size:15px;line-height:1.75;color:#555}.article-body h3{font-size:15px;font-weight:700;color:#1a1a26;margin:24px 0 8px;padding:0}.article-body p{margin:0 0 12px}.article-body a{color:#F77C2A;text-decoration:underline;text-underline-offset:2px}.article-body a:hover{color:#e06a1a}.article-cta{margin-top:24px;padding:20px 24px;background:linear-gradient(135deg,#fff8f0,#fff);border:1px solid #fed7a8;border-radius:10px}.article-cta p{margin:0 0 14px;font-size:14px;color:#1a1a26;font-weight:600}.article-full{overflow:hidden;transition:max-height .4s ease}.article-full.collapsed{max-height:0}.article-full.expanded{max-height:none}.article-btn{display:inline-block;padding:11px 24px;background:#1a1a26;color:#fff;border-radius:8px;font-size:14px;font-weight:700;text-decoration:none;transition:background .2s,transform .2s}.article-btn:hover{background:#333;color:#fff;transform:scale(1.02)}.article-toggle{color:#F77C2A;cursor:pointer;font-weight:600;font-size:13px;border:none;background:none;padding:4px 0 0;display:inline-flex;align-items:center;gap:6px;transition:color .2s;font-family:inherit;margin-top:4px}.article-toggle::after{content:'↓';font-size:11px;transition:transform .2s}.article-toggle:hover{color:#e06a1a}.article-toggle:hover::after{transform:translateY(2px)}.article-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}.article-modal{position:fixed;top:0;left:0;width:100%;height:100%;z-index:99999;opacity:0;pointer-events:none;transition:opacity .3s}.article-modal.active{opacity:1;pointer-events:auto}.article-modal-overlay{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.55);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px)}.article-modal-card{position:relative;width:90%;max-width:700px;max-height:85vh;margin:5vh auto;background:#fff;border-radius:14px;padding:32px 36px;overflow-y:auto;box-shadow:0 12px 40px rgba(0,0,0,.15)}.article-modal-close{position:absolute;top:16px;right:20px;font-size:28px;color:#999;cursor:pointer;line-height:1;border:none;background:none;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:6px;transition:background .2s}.article-modal-close:hover{background:#f5f5f5;color:#333}.article-modal h2{font-size:18px;font-weight:800;color:#1a1a26;margin:0 0 20px}.article-modal .article-body{font-size:15px;line-height:1.75}.article-modal .article-body p{margin:0 0 14px}@media(max-width:700px){.article-grid{grid-template-columns:1fr;gap:16px}.article-modal-card{padding:24px 20px;max-height:90vh}}

</style>
</head>
<body class="brewery-page cct-page">
<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; ?>
<main>
<section class="list-hero">
<div class="container">
<div class="breadcrumbs">
<a href="/">Главная</a><span class="ep">/</span>
<a href="/catalog/">Каталог</a><span class="ep">/</span>
<a href="/catalog/dairy/">Молочное оборудование</a><span class="ep">/</span>
<span class="current"><?= htmlspecialchars($h1) ?></span>
</div>
<div class="list-hero-inner">
<div class="list-hero-img"><img 
src="/<?= htmlspecialchars($data['image']) ?>" alt="<?= htmlspecialchars($data['name']) ?>"></div>
<div class="list-hero-text">
<h1><?= htmlspecialchars($h1) ?></h1>
<p class="hero-ub"><?php
$hooks = [
    'reception' => 'Приёмка молока с фильтрацией и учётом. Корпус из AISI 304 с CIP-мойкой и люком-лазом для осмотра.',
    'cooler' => 'Быстрое охлаждение молока до +4°C. Термоизоляция ППУ, рубашка с пропиленгликолем, автоматика поддержания температуры.',
    '
storage' => 'Хранение молока до 100 000 литров. Вертикальное и горизонтальное исполнение с термоизоляцией.',
    'vdp' => 'Длительная пастеризация молока при 63–65°C. Двустенная рубашка, мешалка, точный PID-терморегулятор.',
    'fermentation' => 'Ферментация кефира, йогурта, сметаны и ряженки. Мешалка, рубашка + охлаждение, CIP-мойка.',
    'cheese-maker' => 'Производство твёрдых и полутвёрдых сыров. Режущая мешалка, рубашка нагрева, выгружной люк.',
    'cottage-cheese' => 'Нагрев до 95°C с разбивкой сгустка и дренажем сыворотки. Полная автоматика.',
    'yeast' => 'Подготовка молочных заквасок в компактных танках. Точный терморегулятор, все объёмы от 50 до 500 л.',
    'brine' => 'Соление сыра в солестойком AISI 316. Дренажная система, крышка с уплотнителем, усиленные стенки.',
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
{"@context":"https://schema.org","@type":"ItemList","name":"<?= htmlspecialchars($h1) ?>","description":"<?= htmlspecialchars($metaDesc) ?>","url":"https://ob-kub.ru<?= $_SERVER['REQUEST_URI'] ?>","numberOfItems":<?= count($sorted) ?>,"itemListElement":[<?php $idx=1; foreach ($sorted as $v): $price=$data['specs'][$v]['price']; if($idx>1) echo ','; ?>{"@type":"ListItem","position":<?= $idx++ ?>,"url":"https://ob-kub.ru<?= $baseUrl . $v ?>l/","name":"<?= htmlspecialchars($data['name']) ?> <?= $v ?> <?= $specUnit ?>","offers":{"@type":"Offer","price":"<?= $price ?>","priceCurrency":"RUB"}}<?php endforeach; ?>]}
</script>
<?php
$minVol = min($sorted);
$maxVol = max($sorted);
$volCount = count($sorted);
$seoDesc = htmlspecialchars($data['desc']);
$specUnit = $data['spec_unit'] ?? 'л';
$specLabel = $data['spec_label'] ?? 'Объём';

$seoGuide = [
    'reception' => [
        'Приёмная ёмкость для молока — это первый элемент технологической линии молочного производства. В неё поступает сырое молоко с молоковоза, фильтруется от механических примесей и временно хранится до отправки на переработку. От объёма приёмной ёмкости зависит, сколько молока можно принять за одну доставку.',
        'Как выбрать приёмную ёмкость: объём подбирается под разовую поставку молока. Стандартный молоковоз везёт 10–20 тонн, поэтому популярны ёмкости на 10 000–25 000 л. Для малых ферм достаточно 1000–5000 л. Оснащение: фильтр грубой очистки, уровнемер, CIP-мойка.',
        'Приёмные ёмкости «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с фильтром и уровнемером. Производим в Краснодаре с 2008 года. Гарантия 12 месяцев.',
    ],
    'cooler' => [
        'Резервуар-охладитель молока (танк-охладитель) предназначен для быстрого охлаждения свежего молока до +4°C и поддержания этой температуры до отправки. Охлаждение происходит через рубашку с пропиленгликолем. Термоизоляция ППУ сохраняет холод без дополнительных энергозатрат.',
        'Как выбрать охладитель: объём подбирается под суточный надой. Для фермы на 50 голов достаточно танка на 1000–2000 л, на 200 голов — 5000–10000 л. Важные параметры: время охлаждения (не более 3 часов до +4°C), наличие мешалки для равномерной температуры, тип охлаждения (гликоль или фреон).',
        'Резервуары-охладители «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с рубашкой охлаждения и термоизоляцией ППУ. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
    'storage' => [
        'Резервуар для хранения молока — это ёмкость большого объёма для накопления и хранения сырья или готовой продукции. Используется на молокозаводах и сыроварнях для создания запаса молока между поставками. Доступны вертикальные и горизонтальные исполнения до 200 000 литров.',
        'Как выбрать резервуар хранения: объём подбирается с запасом 20–30% к суточному объёму переработки. Необходимый параметр — термоизоляция для поддержания температуры +4°C. Для больших объёмов удобнее вертикальные резервуары (меньше площадь пола), для малых — горизонтальные.',
        'Резервуары хранения «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с термоизоляцией ППУ и полной арматурой. Производим в Краснодаре с 2008 года. Гарантия 12 месяцев.',
    ],
    'vdp' => [
        'Ванна длительной пастеризации (ВДП) — это оборудование для пастеризации молока при температуре 63–65°C с выдержкой 30 минут. Двустенная рубашка с паровым или водяным обогревом, мешалка для равномерного нагрева и автоматика для поддержания режима.',
        'Как выбрать ВДП: объём подбирается под партию продукции. Для мини-сыроварен и ферм популярны ВДП на 200–500 л. Для производств среднего масштаба — на 1000–3000 л. Важные параметры: наличие автоматики поддержания температуры, тип мешалки, материал (AISI 304 с полировкой Ra ≤ 0,8 мкм).',
        'ВДП «ОБОРУДОВАНИЕ КУБАНИ» оснащаются двустенной рубашкой и автоматикой. Изготавливаются из AISI 304. Гарантия 12 месяцев.',
    ],
    'fermentation' => [
        'Ферментационный танк для молочной продукции предназначен для сквашивания молока при производстве кефира, йогурта, сметаны и ряженки. Оснащается рубашкой для терморегуляции, мешалкой для равномерного распределения закваски и CIP-мойкой для санитарной обработки.',
        'Как выбрать ферментер: объём подбирается под сменную партию продукции. Для малых производств подходят танки на 500–2000 л, для средних и крупных — 3000–10000 л и более. Важен точный контроль температуры (допуск ±0,5°C) и наличие автоматической программы сквашивания.',
        'Ферментационные танки «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с рубашкой терморегуляции и автоматикой. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.',
    ],
    'cheese-maker' => [
        'Сыроизготовитель — это ёмкость для производства твёрдых, полутвёрдых и мягких сыров. Оснащается двустенной рубашкой для нагрева молока, мешалкой с режущими лопастями для резки сгустка, дренажным устройством для отделения сыворотки и автоматикой управления всеми стадиями.',
        'Как выбрать сыроизготовитель: объём подбирается под партию сыра. Для мини-сыроварен подходят модели на 200–500 л, для фермерских хозяйств — 1000–2000 л, для промышленных — 5000–10000 л. Ключевые параметры: наличие режущей мешалки, дренажного устройства и автоматической программы для разных типов сыра.',
        'Сыроизготовители «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304/316 с двустенной рубашкой и автоматикой. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
    'cottage-cheese' => [
        'Творогоизготовитель — это специализированная ёмкость для производства творога и творожных изделий. Оснащается двустенной рубашкой нагрева, мешалкой с режущими элементами для измельчения сгустка и дренажным устройством для отделения сыворотки.',
        'Как выбрать творогоизготовитель: объём подбирается под партию. Для фермерского производства популярны модели на 200–500 л, для крупных цехов — на 1000–3000 л. Важный параметр — возможность регулировки режимов нагрева и скорости резания мешалки для разных видов творога (зернёный, мягкий, классический).',
        'Творогоизготовители «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с двустенной рубашкой и дренажем. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.',
    ],
    'yeast' => [
        'Заквасочник — это оборудование для приготовления бактериальных заквасок для молочного производства. От качества закваски зависит вкус, консистенция и срок годности готовой продукции. Заквасочник обеспечивает стерильные условия и точную терморегуляцию.',
        'Как выбрать заквасочник: объём подбирается под объём перерабатываемого молока. Стандартное соотношение — 1–5% закваски от объёма молока. Для небольшой сыроварни на 1000 л молока в день подойдёт заквасочник на 50–100 л. Важны автоматический режим и возможность стерилизации.',
        'Заквасочники «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с автоматикой и терморегуляцией. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
    'brine' => [
        'Контейнер для соления сыра предназначен для выдерживания сырных головок в солевом растворе. Посол в рассоле — один из ключевых этапов сыроделия, влияющий на вкус, текстуру и срок хранения сыра. Контейнер изготавливается из кислотостойкой стали AISI 316, устойчивой к коррозии в солевой среде.',
        'Как выбрать контейнер для соления: объём подбирается под размер и количество сырных головок. Погружные контейнеры на 200–1000 л подходят для малых и средних сыроварен. Важно: контейнер должен быть из AISI 316 (обычная AISI 304 корродирует в соли). Удобны контейнеры с корзиной для погружения и извлечения сыра.',
        'Контейнеры соления «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 316 с корзиной для сыра. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.',
    ],
    'cheese-shelves' => [
        'Стеллажи для созревания сыра — это модульные конструкции из нержавеющей стали AISI 304 для размещения сырных головок на этапе созревания. Качественное созревание требует равномерной вентиляции и доступа для переворачивания сыра. Стеллажи выдерживают нагрузку до 200 кг/м².',
        'Как выбрать стеллажи: высота, длина и количество полок подбираются под объём производства и площадь камеры созревания. Для сыроварни с выпуском 50–100 кг сыра в день достаточно 3–4 стеллажей высотой до 2 м. Материал — только AISI 304, полки из прутка или перфорированного листа для вентиляции.',
        'Стеллажи «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с нагрузкой до 200 кг/м². Производим в Краснодаре. Гарантия 12 месяцев.',
    ],
];
$g = $seoGuide[$catKey] ?? reset($seoGuide);

$dairyArticleGeneric = ['title' => 'Как спланировать молочный цех: от приёмки до фасовки', 'content' => ['Планировка молочного производства начинается с расчёта мощности: сколько литров сырья перерабатывается в сутки. Исходя из этого подбирается оборудование каждого этапа: приёмка, охлаждение, хранение, пастеризация, сквашивание (для кисломолочки) или сыроделие, фасовка.', 'Оптимальная последовательность: приёмная ёмкость → резервуар-охладитель → танк хранения → пастеризатор → ферментер/сыроизготовитель → фасовочная линия. Между этапами важно предусмотреть буферные ёмкости для синхронизации — это позволяет не останавливать производство при мойке оборудования.', '«ОБОРУДОВАНИЕ КУБАНИ» помогает спроектировать молочный цех под ключ: от подбора единиц оборудования до планировки расстановки с учётом розлива и CIP-мойки. Оставьте заявку — инженер подготовит КП и план расстановки для вашего производства.']];
$dairyTermArticle = ['title' => 'Как разобраться в названиях молочного оборудования', 'content' => ['В молочной промышленности одно и то же оборудование часто называют по-разному: профессиональный жаргон, технологические сокращения, заимствования из английского. Мы собрали основные термины, чтобы вам было проще ориентироваться в каталоге и общаться с инженерами.', 'Приёмная ёмкость = молокоприёмник. Резервуар-охладитель = танк-охладитель = молокоохладитель. ВДП = ванна длительной пастеризации = пастеризатор. Сыроизготовитель = сыроделка = сырная ванна = сыродельный аппарат. Творогоизготовитель = творожница. Ферментационный танк = ферментер = йогуртный танк = заквасочный танк. Заквасочник = заквасочная ванна. Контейнер для соления = солильный бассейн = ёмкость для посола.', 'Всё наше оборудование производится из пищевой нержавеющей стали AISI 304/316, поэтому материал не указывается в названии — это стандарт. Если вы встретили незнакомый термин — просто вбейте его в поиск на сайте, система найдёт подходящее оборудование. Или позвоните — инженер подскажет, что вам нужно, и подготовит КП.']];

$articleGuide = [
    'reception' => [
        ['title' => 'Как выбрать приёмную ёмкость', 'content' => ['Объём приёмной ёмкости подбирается под разовую поставку молока. Стандартный молоковоз везёт 10–20 тонн, поэтому популярны ёмкости на 10 000–25 000 л. Для малых ферм достаточно 1 000–5 000 л. Ёмкость оснащается фильтром грубой очистки и уровнемером.', 'Материал — AISI 304 с полировкой Ra ≤ 0,8 мкм. Обязательна CIP-мойка и люк-лаз для осмотра. Приёмная ёмкость должна иметь патрубки для подачи молока сверху и отбора снизу, а также переливную трубу.', 'Для точного учёта молока устанавливаются весы или расходомер. Также важна термоизоляция, если молоко хранится более 2–3 часов. Все наши ёмкости комплектуются полной арматурой и проходят гидроиспытания.']],
        $dairyArticleGeneric,
        $dairyTermArticle,
    ],
    'cooler' => [
        ['title' => 'Как выбрать резервуар-охладитель', 'content' => ['Объём охладителя подбирается под суточный надой. Для фермы на 50 голов достаточно 1 000–2 000 л, на 200 голов — 5 000–10 000 л. Время охлаждения — не более 3 часов до +4°C. Мешалка обязательна для равномерной температуры по всему объёму.', 'Тип охлаждения: гликолевое (пропиленгликоль через рубашку) или фреоновое (непосредственное испарение). Гликолевое предпочтительнее для больших объёмов — равномернее охлаждение и меньше риск замерзания молока.', 'Термоизоляция ППУ 80–100 мм сохраняет холод без дополнительных энергозатрат. Автоматика: датчик температуры Pt100, контроллер, сигнализация при отклонении температуры. По запросу — GSM-модуль для удалённого мониторинга.']],
        $dairyArticleGeneric,
        $dairyTermArticle,
    ],
    'storage' => [
        ['title' => 'Как выбрать резервуар хранения', 'content' => ['Объём резервуара подбирается с запасом 20–30% к суточному объёму переработки. Для малых производств популярны 1 000–10 000 л, для средних — 10 000–50 000 л, для крупных заводов — до 200 000 л. Вертикальные резервуары экономят площадь пола.', 'Термоизоляция ППУ 50–100 мм — обязательна для поддержания +4°C. Исполнение: вертикальное (экономия места) или горизонтальное (удобно при низких потолках). Полная арматура: люк-лаз, уровнемер, CIP-мойка, патрубки подачи и отбора.', 'Материал — AISI 304 с полировкой. Для кислых сред — AISI 316. По запросу — рубашка охлаждения для активного охлаждения, мешалка для выравнивания температуры и жирности. Гарантия 12 месяцев.']],
        $dairyArticleGeneric,
        $dairyTermArticle,
    ],
    'vdp' => [
        ['title' => 'Как выбрать ВДП', 'content' => ['Объём ВДП подбирается под партию продукции. Для мини-сыроварен и ферм популярны ВДП на 200–500 л. Для средних производств — 1 000–3 000 л. Важна двустенная рубашка с паровым или водяным обогревом и автоматика поддержания температуры 63–65°C.', 'Тип мешалки: лопастная (для молока) или рамная (для вязких продуктов). Мешалка обеспечивает равномерный нагрев и предотвращает пригорание. Скорость вращения — регулируемая, 10–60 об/мин.', 'Материал — AISI 304 с полировкой Ra ≤ 0,8 мкм. ВДП оснащается: термометром, краном слива, CIP-мойкой, предохранительным клапаном на рубашке. Автоматика PID поддерживает температуру с точностью ±0,5°C.']],
        $dairyArticleGeneric,
        $dairyTermArticle,
    ],
    'fermentation' => [
        ['title' => 'Как выбрать ферментационный танк', 'content' => ['Объём ферментера подбирается под сменную партию продукции. Для малых производств — 500–2 000 л, для средних — 3 000–10 000 л. Точный контроль температуры (допуск ±0,5°C) — ключевой параметр для стабильного качества сквашивания.', 'Конструкция: рубашка терморегуляции (нагрев паром/водой + охлаждение гликолем), мешалка для равномерного распределения закваски, CIP-мойка. Автоматическая программа сквашивания с профилем температуры по времени.', 'Материал — AISI 304 с полировкой. По запросу — AISI 316 для кисломолочных продуктов с низким pH. Дополнительные опции: смотровой люк, пробоотборный кран, датчик pH, уровнемер. Гарантия 12 месяцев.']],
        $dairyArticleGeneric,
        $dairyTermArticle,
    ],
    'cheese-maker' => [
        ['title' => 'Как выбрать сыроизготовитель', 'content' => ['Объём сыроизготовителя подбирается под партию сыра. Для мини-сыроварен — 200–500 л, для фермерских хозяйств — 1 000–2 000 л, для промышленных — 5 000–10 000 л. Из 100 л молока получается около 10–12 кг твёрдого сыра.', 'Ключевые элементы: двустенная рубашка нагрева, режущая мешалка с регулируемой скоростью, дренажное устройство для отделения сыворотки, автоматическая программа для разных типов сыра (твёрдый, полутвёрдый, мягкий).', 'Материал — AISI 304 с полировкой. Для рассольных сыров рекомендуется AISI 316. Опции: смотровой люк, датчик pH, CIP-мойка, пневматическая выгрузка. Гарантия 12 месяцев. Доставка по РФ и СНГ.']],
        $dairyArticleGeneric,
        $dairyTermArticle,
    ],
    'cottage-cheese' => [
        ['title' => 'Как выбрать творогоизготовитель', 'content' => ['Объём подбирается под партию творога. Для фермерского производства — 200–500 л, для крупных цехов — 1 000–3 000 л. Регулировка режимов нагрева и скорости резания мешалки — ключевые параметры для разных видов творога: зернёного, мягкого, классического.', 'Конструкция: двустенная рубашка нагрева (нагрев до 95°C), мешалка с режущими элементами для измельчения сгустка, дренажное устройство с сетчатым фильтром для отделения сыворотки.', 'Материал — AISI 304 с полировкой. Автоматика: PID-регулятор температуры, таймер программы, датчик уровня. По запросу — пневматический выгружной клапан. Гарантия 12 месяцев. Производим в Краснодаре.']],
        $dairyArticleGeneric,
        $dairyTermArticle,
    ],
    'yeast' => [
        ['title' => 'Как выбрать заквасочник', 'content' => ['Объём заквасочника подбирается под объём перерабатываемого молока. Стандартное соотношение — 1–5% закваски от объёма молока. Для сыроварни на 1 000 л молока в день подойдёт заквасочник на 50–100 л.', 'Заквасочник должен обеспечивать стерильные условия: полная герметизация, CIP-мойка, возможность паровой стерилизации. Точная терморегуляция (допуск ±0,3°C) — критична для развития заквасочной микрофлоры.', 'Материал — AISI 304 с зеркальной полировкой. Оснащение: рубашка терморегуляции, мешалка, пробоотборный кран, гидрозатвор. Автоматическая программа стерилизации и культивирования. Гарантия 12 месяцев.']],
        $dairyArticleGeneric,
        $dairyTermArticle,
    ],
    'brine' => [
        ['title' => 'Как выбрать контейнер для соления', 'content' => ['Объём контейнера подбирается под размер и количество сырных головок. Погружные контейнеры на 200–1 000 л подходят для малых и средних сыроварен. Контейнер обязательно изготавливается из AISI 316 — обычная AISI 304 корродирует в солевой среде.', 'Конструкция: корзина для погружения и извлечения сыра из рассола, крышка с уплотнителем для предотвращения испарения, дренажная система для слива и замены рассола. Толщина стенки — от 2 мм.', 'Опции: циркуляционный насос для равномерной концентрации соли, термометр, градуировка уровня. Для больших объёмов — разделение на секции для разных партий сыра. Производим в Краснодаре с 2008 года.']],
        $dairyArticleGeneric,
        $dairyTermArticle,
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
<div class="vol-label"><?= htmlspecialchars($specLabel) ?></div>
<div class="vol-value"><?= $vol ?><span class="vol-unit"> <?= $specUnit ?></span></div>
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
                    <a href="/dairy.html#order-form" class="article-btn">Получить расчёт →</a>
                </div>
                </div>
                <button class="article-toggle" onclick="openArticleModal(this)">Читать статью полностью</button>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="article-modal" id="articleModal" onclick="if(event.target===this)closeArticleModal()"><div class="article-modal-backdrop"></div><div class="article-modal-card"><button class="article-modal-close" onclick="closeArticleModal()">&times;</button><div class="article-modal-header"><span class="article-tag">Статья по теме</span><h2 class="article-modal-title" id="articleModalTitle"></h2></div><div class="article-modal-body" id="articleModalBody"></div><div class="article-modal-cta"><p>Нужна консультация по выбору? Инженер подберёт оптимальную модель под вашу задачу и подготовит КП с точной стоимостью.</p><a href="/dairy.html#order-form" class="article-btn">Получить расчёт →</a></div></div></div>
<script>function openArticleModal(b){var c=b.closest('.article-card'),t=c.querySelector('.article-title').textContent,h='';c.querySelectorAll('.article-full>*').forEach(function(e){if(!e.classList.contains('article-cta'))h+=e.outerHTML});document.getElementById('articleModalTitle').textContent=t;document.getElementById('articleModalBody').innerHTML=h;document.getElementById('articleModal').classList.add('active');document.body.style.overflow='hidden'}function closeArticleModal(){document.getElementById('articleModal').classList.remove('active');document.body.style.overflow=''}</script>
</main>
<?php require $_SERVER['DOCUMENT_ROOT'].'/php/footer.php'; ?>
<?php
}

function renderItemPage($catKey, $vol, $data) {
    $opts = [
        'canonical' => "https://ob-kub.ru/catalog/dairy/{$catKey}/{$vol}l/",
        'baseUrl' => "/catalog/dairy/{$catKey}/",
        'catPrefix' => "/catalog/dairy/",
        'categoryName' => 'Молочное оборудование',
        'categoryUrl' => '/catalog/dairy/',
        'formType' => 'dairy-item',
        'specUnit' => 'л',
        'specLabel' => 'Объём',
        'allSpecs' => $data['specs'],
        'breadcrumbMiddle' => 'Молочное оборудование',
        'breadcrumbMiddleUrl' => '/catalog/dairy/',
    ];
    renderProductPage($catKey, $vol, $data, $opts);
}
