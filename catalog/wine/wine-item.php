<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/../wine-data.php';
require __DIR__ . '/../helpers/product-template.php';

$uri = $_SERVER['REQUEST_URI'];
$cat = !empty($_SERVER['CATALOG_CATEGORY']) ? $_SERVER['CATALOG_CATEGORY'] : '';
$volume = 0;
if (!empty($_SERVER['CATALOG_VOLUME'])) {
    $volume = (int)$_SERVER['CATALOG_VOLUME'];
}
if (!$cat && preg_match('#/catalog/wine/(red-fermentation|white-fermentation|
storage-aging|cold-
stabilization|blending|
sulfitation|universal-tank)/#', $uri, $m)) {
    $cat = $m[1];
}
if (!$volume && preg_match('#/(\d+)l?/?#', $uri, $m)) {
    $volume = (int)$m[1];
}

if (!$cat || !isset($wineData[$cat])) {
    header('HTTP/1.0 404 Not Found');
    echo 'Категория не найдена';
    exit;
}

$data = $wineData[$cat];
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
    $baseUrl = '/catalog/wine/' . $catKey . '/';
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
<style>.article-section{background:#f8f9fb;padding:40px 0 56px;margin-top:8px}.article-card{background:#fff;border-radius:12px;padding:32px 36px;box-shadow:0 2px 12px rgba(0,0,0,.04)}.article-header{margin-bottom:20px}.article-tag{display:inline-block;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#F77C2A;background:rgba(247,124,42,.08);padding:3px 10px;border-radius:4px;margin-bottom:8px}
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
<a href="/catalog/wine/">Винодельческое оборудование</a><span class="ep">/</span>
<span class="current"><?= htmlspecialchars($h1) ?></span>
</div>
<div class="list-hero-inner">
<div class="list-hero-img"><img 
src="/<?= htmlspecialchars($data['image']) ?>" alt="<?= htmlspecialchars($data['name']) ?>"></div>
<div class="list-hero-text">
<h1><?= htmlspecialchars($h1) ?></h1>
<p class="hero-ub"><?php
$hooks = [
    'red-fermentation' => 'Открытая ферментация красных вин с регулярным перемешиванием мезги. Решётка, насос перекачки, рубашка охлаждения — всё для контроля экстракции.',
    'white-fermentation' => 'Закрытая ферментация белых и розовых вин. Полная герметизация, гидрозатвор, точный контроль температуры.',
    '
storage-aging' => 'Выдержка и хранение вина в AISI 304. Термоизоляция, градуировка, полная арматура. От 2000 до 200000 литров.',
    'cold-
stabilization' => 'Осаждение винного камня при -4..-8°C. Мощная рубашка, толстая изоляция, автоматика.',
    'blending' => 'Купажирование партий вина с точным контролем. Мешалка, мерное стекло, пробоотборник — вся оснастка в комплекте.',
    'sulfitation' => 'Обработка вина SO₂ в герметичных танках из AISI 316. Клапан сброса, ввод магистрали, CIP-мойка.',
    'universal-tank' => 'Один танк на все задачи: ферментация, выдержка, стабилизация. Нагрев + охлаждение, мешалка, PID-регулятор.',
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
    'red-fermentation' => [
        'Ферментационный танк для красных вин — это открытая ёмкость для брожения красных сортов винограда вместе с мезгой. Во время ферментации шапка из мезги поднимается на поверхность, и её необходимо регулярно перемешивать для извлечения цвета, танинов и аромата. Танк оснащается решёткой для удержания шапки и насосом перекачки.',
        'Как выбрать танк для красных вин: объём подбирается под урожай винограда. На 1 тонну винограда нужно около 1000 л объёма танка. Для малых виноделен подходят танки 500–2000 л, для средних хозяйств — 3000–10000 л. Важные параметры: наличие рубашки охлаждения для контроля температуры брожения (25–30°C) и решётки для шапки.',
        'Танки «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с рубашкой охлаждения и решёткой. Производим в Краснодаре с 2008 года. Гарантия 12 месяцев.',
    ],
    'white-fermentation' => [
        'Ферментационный танк для белых вин — это закрытая герметичная ёмкость для брожения сусла без мезги. Белые вина бродят при низкой температуре (14–18°C), поэтому танк оснащается рубашкой охлаждения. Герметичность предотвращает окисление и сохраняет свежие фруктовые ароматы.',
        'Как выбрать танк для белых вин: объём подбирается под объём сусла. Для малых виноделен оптимальны 500–2000 л. Важен точный контроль температуры (допуск ±0,5°C) и наличие гидрозатвора для отвода углекислого газа без доступа кислорода. Для премиальных вин рекомендуются танки с возможностью работы под давлением.',
        'Танки «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с рубашкой охлаждения и гидрозатвором. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
    'storage-aging' => [
        'Ёмкость для выдержки и хранения вина — это резервуар большого объёма для созревания вина после ферментации и хранения готовой продукции. В таких ёмкостях вино выдерживается от нескольких месяцев до нескольких лет, приобретая сложность и гармонию вкуса.',
        'Как выбрать ёмкость выдержки: объём подбирается под годовой объём производства с учётом ротации. Для выдержки важна термоизоляция (вино должно храниться при 10–14°C). Для хранения готовой продукции нужна полная герметизация и гидрозатвор. Наиболее популярны ёмкости от 2000 до 50000 л.',
        'Ёмкости «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с термоизоляцией ППУ и полной арматурой. Производим в Краснодаре с 2008 года. Гарантия 12 месяцев.',
    ],
    'cold-stabilization' => [
        'Танк холодной стабилизации (криостат) предназначен для обработки вина холодом с целью удаления винного камня (тартратов) и стабилизации цвета. Вино охлаждается до −4…−5°C и выдерживается несколько дней, после чего осадок удаляется. Это финальный этап подготовки вина к розливу.',
        'Как выбрать криостат: объём подбирается под партию вина. Для малых и средних виноделен популярны 1000–5000 л. Ключевой параметр — возможность быстрого охлаждения до −5°C и точное поддержание температуры. Танк должен быть с термоизоляцией и рубашкой охлаждения высокой мощности.',
        'Криостаты «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с мощной рубашкой охлаждения и автоматикой. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
    'blending' => [
        'Купажная ёмкость предназначена для смешивания различных виноматериалов для создания купажа, а также для внесения корректировок по сахару, кислотности и спирту. Оснащается мешалкой для равномерного перемешивания и уровнемером для точного контроля объёмов.',
        'Как выбрать купажную ёмкость: объём подбирается под размер партии купажа. Для небольших виноделен достаточно ёмкости на 1000–3000 л. Важны: мешалка с регулируемой скоростью (чтобы не насыщать вино кислородом) и точный уровнемер. Для удобства работы рекомендуется вертикальное исполнение.',
        'Купажные ёмкости «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с мешалкой и уровнемером. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.',
    ],
    'sulfitation' => [
        'Ёмкость сульфитации предназначена для обработки вина сернистым ангидридом (SO₂). Сульфитация защищает вино от окисления и микробиологической порчи, это стандартный этап виноделия. Ёмкость оснащается дозатором и барботером для равномерного растворения газа.',
        'Как выбрать сульфитатор: объём подбирается под партию вина. Для малых виноделен подходят ёмкости 500–2000 л. Важные параметры: герметичность (SO₂ токсичен), наличие дозатора и барботера. Рекомендуется изготавливать ёмкость из AISI 304 с усиленной полировкой.',
        'Ёмкости сульфитации «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с дозатором и барботером. Гарантия 12 месяцев.',
    ],
    'universal-tank' => [
        'Винификатор (универсальный терморегулируемый танк) — это многофункциональная ёмкость, подходящая для всех этапов виноделия: ферментации красных и белых вин, мацерации, яблочно-молочного брожения и хранения. Заменяет несколько специализированных танков, что экономит место и бюджет винодельни.',
        'Как выбрать винификатор: объём подбирается под максимальную сменную партию. Для крафтовых виноделен популярны 500–2000 л. Ключевые параметры: полная терморегуляция (от −5 до +40°C), герметичность, наличие рубашки охлаждения и возможность работы под небольшим давлением.',
        'Винификаторы «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с полной терморегуляцией и арматурой. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
];
$g = $seoGuide[$catKey] ?? reset($seoGuide);

$wineArticleGeneric = ['title' => 'Как оснастить винодельню: от приёмки до бутилирования', 'content' => ['Планирование винодельни начинается с расчёта объёмов переработки винограда. Оптимальный подход — зонирование: приёмка и дробление, ферментация, выдержка и хранение, стабилизация, розлив. Каждая зона требует своего набора ёмкостей.', 'Для старта небольших виноделен достаточно универсального винификатора (заменяет ферментацию, мацерацию и хранение), криостата для холодной стабилизации и купажной ёмкости с мешалкой. По мере роста производства добавляются специализированные танки для красных и белых вин, ёмкости выдержки и сульфитации.', '«ОБОРУДОВАНИЕ КУБАНИ» помогает подобрать комплект оборудования под размер винодельни, включая программу лояльности при заказе «под ключ». Оставьте заявку — инженер подготовит смету и план расстановки, учтёт розлив и складские помещения.']];
$wineTermArticle = ['title' => 'Как разобраться в названиях винодельческого оборудования', 'content' => ['В виноделии одно и то же оборудование может называться по-научному и по-рабочему. Понимание синонимов и профессионального жаргона поможет быстрее найти нужную позицию в каталоге и точнее сформулировать задачу инженеру.', 'Винификатор = УТТ (универсальный терморегулируемый танк) = универсальный танк. Криостат = танк холодной стабилизации = стабилизатор вина. Купажная ёмкость = купажер = купажный аппарат. Ёмкость сульфитации = сульфитатор. Ёмкость выдержки = винный резервуар = танк для вина.', 'Всё наше оборудование производится из нержавеющей стали AISI 304/316, поэтому материал не указывается в названии. Если вы встретили незнакомый термин — просто вбейте его в поиск на сайте, система найдёт подходящее оборудование. Или позвоните — инженер подскажет.']];

$articleGuide = [
    'red-fermentation' => [
        ['title' => 'Как выбрать танк для красных вин', 'content' => ['Объём танка подбирается под урожай винограда: на 1 тонну винограда нужно около 1 000 л объёма. Для малых виноделен — 500–2 000 л, для средних — 3 000–10 000 л. Рубашка охлаждения обязательна для контроля температуры брожения 25–30°C.', 'Конструкция: открытый верх для доступа к мезге, решётка для удержания шапки, насос перекачки для орошения шапки. Угол конуса — 60–70° для эффективного сбора осадка. По запросу — система автоматического перемешивания.', 'Материал — AISI 304 с полировкой. Опции: смотровой люк, пробоотборные краны на разных уровнях, CIP-мойка, датчики температуры и плотности. Гарантия 12 месяцев. Производим в Краснодаре.']],
        $wineArticleGeneric,
        $wineTermArticle,
    ],
    'white-fermentation' => [
        ['title' => 'Как выбрать танк для белых вин', 'content' => ['Объём подбирается под объём сусла. Для малых виноделен оптимальны 500–2 000 л. Точный контроль температуры (14–18°C, допуск ±0,5°C) — ключевой параметр. Герметичность предотвращает окисление и сохраняет фруктовые ароматы.', 'Конструкция: полная герметизация, гидрозатвор для отвода CO₂, рубашка охлаждения. Для премиальных вин — возможность работы под небольшим давлением (до 0,5 бар) для сохранения ароматики.', 'Материал — AISI 304 с зеркальной полировкой. Оснащение: пробоотборный кран, CIP-мойка, датчик температуры Pt100. По запросу — смотровой люк с подсветкой, исполнение из AISI 316. Гарантия 12 месяцев.']],
        $wineArticleGeneric,
        $wineTermArticle,
    ],
    'storage-aging' => [
        ['title' => 'Как выбрать ёмкость выдержки', 'content' => ['Объём подбирается под годовой объём производства с учётом ротации. Для выдержки важна термоизоляция (10–14°C). Популярны ёмкости от 2 000 до 50 000 л. Для хранения готовой продукции — полная герметизация и гидрозатвор.', 'Конструкция: вертикальное или горизонтальное исполнение. Для выдержки красных вин — возможность установки дубовой щепы или чипсов. Для белых — полная герметизация для защиты от окисления.', 'Материал — AISI 304 с полировкой Ra ≤ 0,8 мкм. Термоизоляция ППУ 50–100 мм. Оснащение: люк-лаз, уровнемер, CIP-мойка, пробоотборный кран. Гарантия 12 месяцев. Доставка по РФ и СНГ.']],
        $wineArticleGeneric,
        $wineTermArticle,
    ],
    'cold-stabilization' => [
        ['title' => 'Как выбрать криостат', 'content' => ['Объём криостата подбирается под партию вина. Для малых и средних виноделен популярны 1 000–5 000 л. Ключевой параметр — возможность быстрого охлаждения до −5°C и точное поддержание температуры в течение нескольких дней.', 'Конструкция: мощная рубашка охлаждения с пропиленгликолем, термоизоляция ППУ 100–150 мм. Автоматика с PID-регулятором и датчиком Pt100. Система рециркуляции для равномерного охлаждения.', 'Материал — AISI 304 с полировкой. Опции: смотровой люк, дренажный кран для осадка, CIP-мойка. По запросу — программируемый профиль охлаждения. Гарантия 12 месяцев. Производим в Краснодаре.']],
        $wineArticleGeneric,
        $wineTermArticle,
    ],
    'blending' => [
        ['title' => 'Как выбрать купажную ёмкость', 'content' => ['Объём подбирается под размер партии купажа. Для небольших виноделен — 1 000–3 000 л. Мешалка с регулируемой скоростью (чтобы не насыщать вино кислородом) — обязательна. Точный уровнемер для контроля объёмов компонентов.', 'Тип мешалки: пропеллерная (для жидких сред) или лопастная (для более вязких). Скорость — 30–150 об/мин. Вертикальное исполнение удобнее для отбора проб и замера уровня.', 'Материал — AISI 304 с полировкой. Оснащение: пробоотборные краны на разных уровнях, CIP-мойка, люк-лаз. По запросу — автоматическая система дозирования компонентов. Гарантия 12 месяцев.']],
        $wineArticleGeneric,
        $wineTermArticle,
    ],
    'sulfitation' => [
        ['title' => 'Как выбрать ёмкость сульфитации', 'content' => ['Объём подбирается под партию вина. Для малых виноделен — 500–2 000 л. Герметичность — критична, так как SO₂ токсичен. Наличие дозатора и барботера для равномерного растворения газа в вине.', 'Конструкция: герметичная крышка с уплотнителем, клапан сброса избыточного давления, патрубок ввода сернистого ангидрида. Барботер обеспечивает мелкодисперсное распределение газа.', 'Материал — AISI 304 с усиленной полировкой. Рекомендуется AISI 316 для длительного контакта с SO₂. Опции: датчик концентрации SO₂, автоматический дозатор, CIP-мойка. Гарантия 12 месяцев.']],
        $wineArticleGeneric,
        $wineTermArticle,
    ],
    'universal-tank' => [
        ['title' => 'Как выбрать винификатор', 'content' => ['Объём подбирается под максимальную сменную партию. Для крафтовых виноделен популярны 500–2 000 л. Полная терморегуляция (от −5 до +40°C) — ключевое преимущество. Винификатор заменяет 2–3 специализированных танка.', 'Конструкция: рубашка охлаждения + рубашка нагрева (или электрические ТЭНы), герметичная крышка, гидрозатвор, возможность работы под давлением до 0,5 бар. Мешалка — опционально для красных вин.', 'Материал — AISI 304 с полировкой. Оснащение: датчики температуры и плотности, CIP-мойка, пробоотборный кран, смотровой люк. По запросу — автоматические программы для разных типов вин. Гарантия 12 месяцев.']],
        $wineArticleGeneric,
        $wineTermArticle,
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
                    <a href="/winery.html#order-form" class="article-btn">Получить расчёт →</a>
                </div>
                </div>
                <button class="article-toggle" onclick="openArticleModal(this)">Читать статью полностью</button>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="article-modal" id="articleModal" onclick="if(event.target===this)closeArticleModal()"><div class="article-modal-backdrop"></div><div class="article-modal-card"><button class="article-modal-close" onclick="closeArticleModal()">&times;</button><div class="article-modal-header"><span class="article-tag">Статья по теме</span><h2 class="article-modal-title" id="articleModalTitle"></h2></div><div class="article-modal-body" id="articleModalBody"></div><div class="article-modal-cta"><p>Нужна консультация по выбору? Инженер подберёт оптимальную модель под вашу задачу и подготовит КП с точной стоимостью.</p><a href="/winery.html#order-form" class="article-btn">Получить расчёт →</a></div></div></div>
<script>function openArticleModal(b){var c=b.closest('.article-card'),t=c.querySelector('.article-title').textContent,h='';c.querySelectorAll('.article-full>*').forEach(function(e){if(!e.classList.contains('article-cta'))h+=e.outerHTML});document.getElementById('articleModalTitle').textContent=t;document.getElementById('articleModalBody').innerHTML=h;document.getElementById('articleModal').classList.add('active');document.body.style.overflow='hidden'}function closeArticleModal(){document.getElementById('articleModal').classList.remove('active');document.body.style.overflow=''}</script>
</main>
<?php require $_SERVER['DOCUMENT_ROOT'].'/php/footer.php'; ?>
<?php
}

function renderItemPage($catKey, $vol, $data) {
    $opts = [
        'canonical' => "https://ob-kub.ru/catalog/wine/{$catKey}/{$vol}l/",
        'baseUrl' => "/catalog/wine/{$catKey}/",
        'catPrefix' => "/catalog/wine/",
        'categoryName' => 'Винодельческое оборудование',
        'categoryUrl' => '/catalog/wine/',
        'formType' => 'wine-item',
        'specUnit' => 'л',
        'specLabel' => 'Объём',
        'allSpecs' => $data['specs'],
        'breadcrumbMiddle' => 'Винодельческое оборудование',
        'breadcrumbMiddleUrl' => '/catalog/wine/',
    ];
    renderProductPage($catKey, $vol, $data, $opts);
}
