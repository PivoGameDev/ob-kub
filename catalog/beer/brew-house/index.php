<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/../../brew-house-data.php';

$uri = $_SERVER['REQUEST_URI'];
$type = !empty($_SERVER['CATALOG_TYPE']) ? $_SERVER['CATALOG_TYPE'] : '';
$volume = 0;
if (!empty($_SERVER['CATALOG_VOLUME'])) {
    $volume = (int)$_SERVER['CATALOG_VOLUME'];
}
if (!$type && preg_match('#/catalog/beer/brew-house/([a-z-]+)/#', $uri, $m)) {
    $type = $m[1];
}
if (!$volume && preg_match('#/(\d+)l?/?#', $uri, $m)) {
    $volume = (int)$m[1];
}

if ($type && !isset($brewData[$type])) {
    header('HTTP/1.0 404 Not Found');
    echo 'Категория не найдена';
    exit;
}

if ($type && $volume && isset($brewData[$type]['specs'][$volume])) {
    renderBrewPage($type, $volume, $brewData[$type], $brewData);
} elseif ($type) {
    renderTypeList($type, $brewData[$type], $brewData);
} else {
    renderBrewList($brewData);
}
exit;

function renderBrewList($allData) {
    $cat = $GLOBALS['brewCategory'];
    $metaTitle = $cat['title'];
    $metaDesc = $cat['desc'];
    $h1 = $cat['h1'];
    $canonical = 'https://ob-kub.ru/catalog/beer/brew-house/';
            $bodyClass = 'brewery-page cct-page';
    require __DIR__ . '/../../catalog-styles.php';
    require __DIR__ . '/../../layout-start.php';
?>
<section class="list-main-hero">
<div class="container">
<div class="breadcrumbs">
<a href="/">Главная</a><span class="ep">/</span>
<a href="/catalog/">Каталог</a><span class="ep">/</span>
<a href="/catalog/beer/">Пивоваренное оборудование</a><span class="ep">/</span>
<span class="current">Варочные порядки</span>
</div>
<div class="hero-text">
<h1><?= htmlspecialchars($h1) ?></h1>
<p><?= htmlspecialchars($metaDesc) ?></p>
</div>
</div>
</section>
<section class="container">
<div class="cat-grid">
<?php foreach ($allData as $key => $d): 
    $count = count($d['volumes']);
    $img = htmlspecialchars('/' . $d['image']);
    $url = htmlspecialchars('/catalog/beer/brew-house/' . $key . '/');
?>
<a href="<?= $url ?>" class="cat-card">
<div class="cat-card-img"><img 
src="<?= $img ?>" alt="<?= htmlspecialchars($d['name']) ?>"></div>
<div class="cat-card-body">
<div class="cat-name"><?= htmlspecialchars($d['name']) ?></div>
<div class="cat-desc"><?= htmlspecialchars($d['desc']) ?></div>
<div class="cat-count"><?= $count ?> объёмов</div>
</div>
<div class="cat-card-footer">
<span class="btn-view">Выбрать</span>
</div>
</a>
<?php endforeach; ?>
</div>
</section>
<?php require __DIR__ . '/../../layout-end.php'; }
function renderTypeList($typeKey, $data, $allData) {
    $sortedVols = $data['volumes'];
    
sort($sortedVols);
    $h1 = $data['h1'];
    $metaTitle = $data['name'] . ' — каталог объёмов';
    $metaDesc = $data['desc'];
    $baseUrl = '/catalog/beer/brew-house/' . $typeKey . '/';
    $canonical = 'https://ob-kub.ru' . $baseUrl;
            $bodyClass = 'brewery-page cct-page';
    require __DIR__ . '/../../catalog-styles.php';
    require __DIR__ . '/../../layout-start.php';
?>
<section class="list-hero">
<div class="container">
<div class="breadcrumbs">
<a href="/">Главная</a><span class="sep">/</span>
<a href="/catalog/">Каталог</a><span class="sep">/</span>
<a href="/catalog/beer/">Пивоваренное оборудование</a><span class="sep">/</span>
<a href="/catalog/beer/brew-house/">Варочные порядки</a><span class="sep">/</span>
<span class="current"><?= htmlspecialchars($h1) ?></span>
</div>
<div class="list-hero-inner">
<div class="list-hero-img"><img 
src="/<?= htmlspecialchars($data['image']) ?>" alt="<?= htmlspecialchars($data['name']) ?>"></div>
<div class="list-hero-text">
<h1><?= htmlspecialchars($h1) ?></h1>
<p class="hero-sub"><?php
$hooks = [
    'mash-tun' => 'Затирание солода и фильтрация сусла. Две рубашки нагрева, лопастная мешалка, термоизоляция ППУ.',
    'combined-kettle' => 'Два в одном: затирание и кипячение сусла. Экономия места и бюджета. Комплектация как у раздельных аппаратов.',
    'lauter-tun' => 'Отделение жидкого сусла от дробины. Фальш-дно с щелевыми прорезями, ножи для рыхления, автоматический выгруз.',
    'whirlpool' => 'Осветление сусла перед брожением. Удаление хмелевой дробины до 98%. Вихревой принцип.',
    'brew-kettle' => 'Кипячение сусла с хмелем. Интенсивный режим кипения, испарение ДМС, вихревая воронка.',
];
echo $hooks[$typeKey] ?? htmlspecialchars($data['desc']);
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
{"@context":"https://schema.org","@type":"ItemList","name":"<?= htmlspecialchars($h1) ?>","description":"<?= htmlspecialchars($metaDesc) ?>","url":"https://ob-kub.ru<?= $_SERVER['REQUEST_URI'] ?>","numberOfItems":<?= count($sortedVols) ?>,"itemListElement":[<?php $idx=1; foreach ($sortedVols as $v): $p=$data['specs'][$v]['price']; if($idx>1) echo ','; ?>{"@type":"ListItem","position":<?= $idx++ ?>,"url":"https://ob-kub.ru<?= $baseUrl . $v ?>l/","name":"<?= htmlspecialchars($data['name']) ?> <?= $v ?> литров","offers":{"@type":"Offer","price":"<?= $p ?>","priceCurrency":"RUB"}}<?php endforeach; ?>]}
</script>
<?php
$minVol = min($sortedVols);
$maxVol = max($sortedVols);
$volCount = count($sortedVols);
$seoName = htmlspecialchars($data['name'] ?? $h1);
$seoDesc = htmlspecialchars($data['desc']);

$seoGuide = [
    'mash-tun' => [
        'Заторный аппарат — это ёмкость для затирания солода, первый этап варки пива. В заторнике дроблёный солод смешивается с горячей водой, выдерживаются температурные паузы для превращения крахмала в сахара. Качество затирания напрямую влияет на вкус, аромат и плотность готового пива.',
        'Как выбрать объём заторника: рабочий объём должен быть в 1.2–1.5 раза больше объёма одной варки. Для варки 200 л пива нужен заторник на 250–300 л, для 500 л — на 600–750 л. Самые популярные объёмы среди крафтовых пивоварен — 500, 1000 и 2000 л. Все заторники оснащены двумя рубашками нагрева и лопастной мешалкой для равномерного затирания.',
        'Заторные аппараты «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с зеркальной полировкой и проходят гидравлические испытания. Производим в Краснодаре с 2008 года. Гарантия 12 месяцев. Оставьте заявку — инженер подготовит КП с точной стоимостью.',
    ],
    'combined-kettle' => [
        'Заторно-сусловарочный аппарат (ЗСА) — это комбинированное решение 2-в-1, совмещающее функции заторника и сусловарочного котла. В одном аппарате можно затирать солод и кипятить сусло с хмелем. Это экономит место в пивоварне и снижает бюджет по сравнению с покупкой двух ёмкостей.',
        'Как выбрать ЗСА: объём подбирается равным объёму варки с запасом на кипение. Если варите 300 л пива — берите ЗСА на 500 л. Для крафтовых пивоварен оптимальны модели 500–2000 л. ЗСА оснащён двумя рубашками нагрева, фильтрующим фальш-дном и лопастной мешалкой.',
        'Все ЗСА «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с зеркальной полировкой, проходят гидроиспытания. Гарантия 12 месяцев. Доставка по РФ и СНГ. Оставьте заявку — инженер подготовит КП.',
    ],
    'lauter-tun' => [
        'Фильтрационный аппарат (лаутер-тюн) отделяет жидкое сусло от отработанной дробины после затирания. Это второй этап варки. Лаутер-тюн оснащён щелевым ситом для фильтрации и мешалкой для промывания дробины горячей водой, что увеличивает выход сусла.',
        'Как выбрать фильтрационный аппарат: объём лаутер-тюна подбирается как и заторника — в 1.2–1.5 раза больше объёма варки. Ключевой параметр — площадь сита, от неё зависит скорость сцеживания. Для крафтовых пивоварен оптимальны 500–2000 л, для промышленных — от 3000 л.',
        'Фильтрационные аппараты «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с разборным щелевым ситом и равномерной дренажной системой. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.',
    ],
    'brew-kettle' => [
        'Сусловарочный аппарат предназначен для кипячения сусла с хмелем — третий этап варки после фильтрации. В сусловарке происходит охмеление, стерилизация, выпаривание нежелательных ароматов и коагуляция белков. От режима кипячения зависят горечь и стабильность пива.',
        'Как выбрать сусловарочный аппарат: объём подбирается равным объёму варки с запасом 15–25% на кипение. Для варки 400 л пива нужна сусловарка на 500 л. Мощность нагрева должна обеспечивать интенсивное кипение — 60–90 кВт для моделей 2000–5000 л.',
        'Сусловарочные аппараты «ОБОРУДОВАНИЕ КУБАНИ» оснащаются двумя рубашками нагрева и эффективным пароотводом. Изготавливаются из AISI 304 с полировкой Ra ≤ 0,8 мкм. Гарантия 12 месяцев.',
    ],
    'whirlpool' => [
        'Гидроциклонный аппарат (вирпул) отделяет взвеси — хмелевую дробину и белковый брух — от горячего сусла после кипячения. Сусло подаётся по касательной, центробежная сила собирает твёрдые частицы в центре дна. Компактная конструкция без дополнительных фильтров.',
        'Как выбрать вирпул: объём равен объёму сусловарки. Диаметр вирпула обычно больше высоты — это увеличивает площадь осаждения. Для крафтовых пивоварен популярны 500–2000 л. Важны дренажные краны на разных уровнях для отбора осветлённого сусла.',
        'Вирпулы «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с тангенциальным вводом и центральным успокоителем. Производим в Краснодаре с 2008 года. Гарантия 12 месяцев.',
    ],
    'wort-receiver' => [
        'Суслосборник — промежуточная ёмкость для приёма и временного хранения горячего сусла перед отправкой в ЦКТ на брожение. Позволяет накопить сусло с нескольких варок или согласовать работу варочного порядка с мощностями бродильного отделения.',
        'Как выбрать суслосборник: объём подбирается под объём варки или кратный ему. Для варки 1000 л достаточно суслосборника на 1000–1500 л. Для накопления с двух варок — 2000–3000 л. Суслосборник оснащается рубашкой охлаждения для быстрого снижения температуры перед внесением дрожжей.',
        'Суслосборники «ОБОРУДОВАНИЕ КУБАНИ» изготавливаются из AISI 304 с рубашкой охлаждения и термоизоляцией ППУ. Гарантия 12 месяцев. Доставка по РФ и СНГ.',
    ],
];
$g = $seoGuide[$typeKey] ?? reset($seoGuide);
$brewArticleGeneric = ['title' => 'Как сравнить варочные порядки: 8 ключевых параметров', 'content' => ['Выбор варочного порядка — ключевое решение для пивоварни. Основные параметры: объём варки (от 100 до 10 000+ л), тип аппарата (раздельный vs комбинированный), материал (AISI 304/316) и толщина стенки, нагревательные рубашки (количество, мощность, площадь), система фильтрации (щелевое сито vs трубы), пароотвод, автоматика и программа управления.', 'Раздельный варочный порядок (заторник + фильтрчан + сусловарка) гибче — можно варить разные сорта с разными температурными профилями. Комбинированный (ЗСА) дешевле на 20–30% и занимает меньше места, но менее гибкий. Выбор зависит от ассортиментной матрицы и бюджета.', '«ОБОРУДОВАНИЕ КУБАНИ» производит варочные порядки под ключ: заторные аппараты, фильтрационные, сусловарочные, комбинированные ЗСА, вирпулы и суслосборники. Оставьте заявку — инженер подберёт конфигурацию под ваш сорт и объём.']];
$brewTermArticle = ['title' => 'Как разобраться в названиях варочного оборудования', 'content' => ['В пивоваренной индустрии одно и то же оборудование часто называют по-разному — от профессионального жаргона до английских заимствований. Чтобы не путаться при выборе, достаточно знать основные синонимы.', 'Заторный аппарат = маш тюн (mash tun) = заторный чан. Фильтрационный аппарат = лаутер тюн (lauter tun) = фильтрчан. Сусловарочный аппарат = сусловарка = варочный котел = brew kettle. Гидроциклонный аппарат = вирпул (whirlpool) = гидроциклон. Суслосборник = сборник сусла = суслоприемник = wort receiver. ЗСА = заторно-сусловарочный аппарат = универсальный варочный агрегат.', 'Мы специально указываем в каталоге все распространённые названия, чтобы вы быстро нашли нужное. Всё оборудование — из нержавеющей стали AISI 304/316. Если сомневаетесь в выборе — просто вбейте запрос в поиск на сайте или позвоните инженеру.']];

$articleGuide = [
    'mash-tun' => [
        ['title' => 'Как выбрать заторный аппарат', 'content' => ['Объём заторника подбирается в 1.2–1.5 раза больше объёма варки. Для варки 200 л нужен заторник на 250–300 л. Популярные объёмы — 500, 1000 и 2000 л. Две рубашки нагрева и лопастная мешалка обеспечивают равномерное затирание.', 'Температурные паузы: 38°C (белковая), 52°C (мальтозная), 62–68°C (осахаривание), 72°C (финальная). Каждая пауза влияет на вкус и плотность пива. Автоматика PID управляет нагревом с точностью ±0.5°C.', 'Материал — AISI 304 с зеркальной полировкой. Фальш-дно с щелевыми прорезями для фильтрации затора. Мешалка — 20–40 об/мин. CIP-мойка, смотровой люк, датчик температуры. Гарантия 12 месяцев.']],
        $brewArticleGeneric,
        $brewTermArticle,
    ],
    'combined-kettle' => [
        ['title' => 'Как выбрать заторно-сусловарочный аппарат', 'content' => ['Объём ЗСА подбирается равным объёму варки с запасом на кипение. Для варки 300 л — ЗСА на 500 л. Для крафтовых пивоварен оптимальны 500–2000 л. Экономия места и бюджета до 30% по сравнению с раздельными аппаратами.', 'Конструкция: две рубашки нагрева, фильтрующее фальш-дно, лопастная мешалка. Переключение между режимами затирания и кипячения — переключением заслонок. Пароотвод для кипячения.', 'Материал — AISI 304 с полировкой Ra ≤ 0,8 мкм. Оснащение: датчики температуры, CIP-мойка, смотровой люк, кран слива сусла. Гарантия 12 месяцев. Доставка по РФ и СНГ.']],
        $brewArticleGeneric,
        $brewTermArticle,
    ],
    'lauter-tun' => [
        ['title' => 'Как выбрать фильтрационный аппарат', 'content' => ['Объём лаутер-тюна — в 1.2–1.5 раза больше объёма варки. Ключевой параметр — площадь сита, от которой зависит скорость сцеживания. Для крафтовых пивоварен оптимальны 500–2000 л, для промышленных — от 3000 л.', 'Конструкция: разборное щелевое сито (фальш-дно) с равномерной дренажной системой, мешалка с ножами для рыхления дробины, автоматический выгруз дробины. Щель сита — 0.6–0.8 мм.', 'Материал — AISI 304. Ножи мешалки — износостойкая сталь. Сито — из AISI 304 с лазерной резкой щелей. Опции: автоматическая промывка дробины, датчик давления фильтрации. Гарантия 12 месяцев.']],
        $brewArticleGeneric,
        $brewTermArticle,
    ],
    'brew-kettle' => [
        ['title' => 'Как выбрать сусловарочный аппарат', 'content' => ['Объём сусловарки — на 15–25% больше объёма варки. Для варки 400 л — сусловарка на 500 л. Мощность нагрева: для моделей 2000–5000 л требуется 60–90 кВт для интенсивного кипения и испарения ДМС.', 'Конструкция: две рубашки нагрева, эффективный пароотвод, вихревая воронка для осаждения хмеля. Интенсивность кипения — 8–12% испарения в час для качественного удаления ДМС.', 'Материал — AISI 304 с полировкой. По запросу — AISI 316. Оснащение: датчик температуры Pt100, пароотвод с конденсатоотводчиком, сито для хмеля (опция). Гарантия 12 месяцев.']],
        $brewArticleGeneric,
        $brewTermArticle,
    ],
    'whirlpool' => [
        ['title' => 'Как выбрать вирпул', 'content' => ['Объём вирпула равен объёму сусловарки. Диаметр обычно больше высоты — это увеличивает площадь осаждения. Для крафтовых пивоварен популярны 500–2000 л. Тангенциальный ввод сусла под углом 30° для эффективной центробежной сепарации.', 'Центральный успокоитель гасит вращение в центре, позволяя взвесям осесть на дне. Дренажные краны на разных уровнях для отбора осветлённого сусла без захвата осадка.', 'Материал — AISI 304 с полировкой. Оснащение: смотровой люк, CIP-мойка, градуировка уровня. Опции: рубашка охлаждения для быстрого снижения температуры. Гарантия 12 месяцев.']],
        $brewArticleGeneric,
        $brewTermArticle,
    ],
    'wort-receiver' => [
        ['title' => 'Как выбрать суслосборник', 'content' => ['Объём подбирается под варку или кратный ему. Для варки 1000 л — суслосборник на 1000–1500 л. Для накопления с двух варок — 2000–3000 л. Позволяет накопить сусло с нескольких варок и согласовать график варки с бродильным отделением.', 'Рубашка охлаждения для быстрого снижения температуры до 20°C перед внесением дрожжей. Термоизоляция ППУ для поддержания температуры. Мешалка для выравнивания температуры и аэрации.', 'Материал — AISI 304 с полировкой. Оснащение: аэратор (камень + фильтр), датчик температуры, CIP-мойка, уровнемер. Гарантия 12 месяцев. Производим в Краснодаре с 2008 года.']],
        $brewArticleGeneric,
        $brewTermArticle,
    ],
];
$articleData = $articleGuide[$typeKey] ?? reset($articleGuide);
?>
<div class="seo-text-wrap">
    <div class="seo-text-card">
        <div class="seo-text-head">Полезная информация</div>
        <div class="seo-text collapsed">
        <p><?= $g[0] ?></p>
        <p><strong>В каталоге представлено <?= $volCount ?> моделей объёмом от <?= $minVol ?> до <?= $maxVol ?> л.</strong> <?= $g[1] ?></p>
        <p><?= $g[2] ?></p>
        <p>«ОБОРУДОВАНИЕ КУБАНИ» — это 18 лет на рынке, собственное производство полного цикла в Краснодаре (цех 2000 м²), контроль качества и сертификаты соответствия. Доставляем по всей России и странам СНГ любой транспортной компанией. Гарантия — 12 месяцев. Оставьте заявку — инженер подготовит коммерческое предложение с точной стоимостью, сроками изготовления и доставки для вашего проекта.</p>
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
<?php foreach ($sortedVols as $vol):
    $s = $data['specs'][$vol];
    $price = $s['price'];
    $priceStr = $price >= 1000000 ? number_format($price/1000000,1,'.','').' млн ₽' : ($price >= 1000 ? number_format($price/1000,0,'.','').' тыс ₽' : number_format($price,0,'.',' ').' ₽');
    $volUrl = $baseUrl . $vol . 'l/';
    $fullVol = !empty($s['full_volume']) ? number_format($s['full_volume'], 0, '.', ' ') : '—';
    $workVol = !empty($s['working_volume']) ? number_format($s['working_volume'], 0, '.', ' ') : '—';
    $diamM = number_format($s['diameter']/1000, 2, '.', '');
    $hM = number_format($s['height']/1000, 2, '.', '');
?>
<a href="<?= htmlspecialchars($volUrl) ?>" class="vol-card">
    <div class="vol-card-body">
        <div class="vol-label">Объём</div>
        <div class="vol-value"><?= $vol ?><span class="vol-unit"> л</span></div>
        <div class="price">от <?= $priceStr ?></div>
        <div class="specs">
            <div><span class="l">Полный:</span><span> <?= $fullVol ?> л</span></div>
            <div><span class="l">Рабочий:</span><span> <?= $workVol ?> л</span></div>
            <div><span class="l">Диаметр:</span><span> <?= $diamM ?> м</span></div>
            <div><span class="l">Высота:</span><span> <?= $hM ?> м</span></div>
            <div><span class="l">Стенка:</span><span> <?= $s['wall'] ?> мм</span></div>
            <div><span class="l">Вес:</span><span> <?= $s['weight'] ?> кг</span></div>
            <div><span class="l">Нагрев:</span><span> <?= $s['power'] ?> кВт</span></div>
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
<?php require __DIR__ . '/../../layout-end.php'; }

function renderBrewPage($typeKey, $vol, $data, $allData) {
    $s = $data['specs'][$vol];
    $volStr = number_format($vol, 0, '.', ' ');
    $diameterM = number_format($s['diameter'] / 1000, 2, '.', '');
    $heightM = number_format($s['height'] / 1000, 2, '.', '');
    $price = $s['price'];
    $priceStr = $price >= 1000000 ? number_format($price/1000000,1,'.','').' млн ₽' : ($price >= 1000 ? number_format($price/1000,0,'.','').' тыс ₽' : number_format($price,0,'.',' ').' ₽');
    $canonical = "https://ob-kub.ru/catalog/beer/brew-house/{$typeKey}/{$vol}l/";
    
    $sortedVols = $data['volumes'];
    
sort($sortedVols);
    $prevVol = null; $nextVol = null;
    $idx = array_search($vol, $sortedVols);
    if ($idx > 0) $prevVol = $sortedVols[$idx - 1];
    if ($idx < count($sortedVols) - 1) $nextVol = $sortedVols[$idx + 1];
    
    $features = $data['features'];
    $metaTitle = $data['name'] . ' ' . $volStr . ' литров';
    $metaDesc = $data['name_short'] . ' ' . $volStr . ' л из нержавеющей стали AISI 304. Цена от ' . $priceStr . '. Диаметр ' . $diameterM . ' м, высота ' . $heightM . ' м, толщина стенки ' . $s['wall'] . ' мм. Закажите на ob-kub.ru';
    $image = htmlspecialchars('/' . $data['image']);
    
    $schemaProduct = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $data['name'] . ' ' . $volStr . ' литров',
        'description' => $metaDesc,
        'image' => 'https://ob-kub.ru' . $image,
        'brand' => ['@type' => 'Brand', 'name' => 'ОБОРУДОВАНИЕ КУБАНИ'],
        'category' => 'Пивоваренное оборудование',
        'offers' => ['@type' => 'Offer', 'price' => $price, 'priceCurrency' => 'RUB', 'availability' => 'https://schema.org/InStock'],
        'material' => 'Нержавеющая сталь AISI 304',
    ], JSON_UNESCAPED_UNICODE);
    
            $bodyClass = 'brewery-page cct-page';
    require __DIR__ . '/../../catalog-styles.php';
    require __DIR__ . '/../../layout-start.php';
?>
<section class="cct-hero">
<div class="container">
<div class="cct-breadcrumbs">
<a href="/">Главная</a><span class="ep">/</span>
<a href="/catalog/">Каталог</a><span class="ep">/</span>
<a href="/catalog/beer/">Пивоваренное оборудование</a><span class="ep">/</span>
<a href="/catalog/beer/brew-house/">Варочные порядки</a><span class="ep">/</span>
<a href="/catalog/beer/brew-house/<?= htmlspecialchars($typeKey) ?>/"><?= htmlspecialchars($data['name']) ?></a><span class="ep">/</span>
<span class="current"><?= $volStr ?> л</span>
</div>
<div class="cct-hero-inner">
<div class="cct-hero-img"><img 
src="<?= $image ?>" alt="<?= htmlspecialchars($data['name']) ?>" loading="lazy"></div>
<div class="cct-hero-info">
<div class="label"><?= htmlspecialchars($data['name_short']) ?></div>
<h1><?= htmlspecialchars($data['name']) ?> на <?= $volStr ?> литров</h1>
<p class="sub"><?= htmlspecialchars($data['desc']) ?></p>
<div class="cct-hero-price">от <?= $priceStr ?> <small>с НДС</small></div>
<div class="cct-hero-tags">
<?php
$tagFeatures = array_slice($features, 0, 5);
foreach ($tagFeatures as $f):
    $clean = preg_replace('/\(.*?\)|\[.*?\]/', '', $f);
    $clean = trim($clean, ' ·,');
    if (!$clean) continue;
    $parts = explode(':', $clean);
    if (count($parts) > 1) {
        $val = trim($parts[0]);
        $rest = trim($parts[1]);
        ?><span><strong><?= htmlspecialchars($val) ?>:</strong> <?= htmlspecialchars($rest) ?></span><?php
    } else {
        ?><span><?= htmlspecialchars($clean) ?></span><?php
    }
endforeach; ?>
</div>
<div class="vol-nav">
<?php foreach ($sortedVols as $v):
    if ($v == $vol): ?>
<a class="v-active"><?= $v ?>л</a>
<?php else: ?>
<a href="/catalog/beer/brew-house/<?= $typeKey ?>/<?= $v ?>l/" class="v-link"><?= $v ?>л</a>
<?php endif; endforeach; ?>
</div>
</div>
</div>
</div>
</section>

<div class="cct-adv">
<div class="cct-adv-item"><div class="cct-adv-icon">🏭</div><div class="cct-adv-title">18+ лет на рынке</div><div class="cct-adv-text">с 2008 года</div></div>
<div class="cct-adv-item"><div class="cct-adv-icon">⚙️</div><div class="cct-adv-title">Свой цех</div><div class="cct-adv-text">2000 м² в Краснодаре</div></div>
<div class="cct-adv-item"><div class="cct-adv-icon">📦</div><div class="cct-adv-title">Доставка по РФ</div><div class="cct-adv-text">любой ТК или своим транспортом</div></div>
<div class="cct-adv-item"><div class="cct-adv-icon">✅</div><div class="cct-adv-title">Гарантия 12 мес</div><div class="cct-adv-text">сертификат ТР ЕАЭС</div></div>
</div>

<div class="container">
<div class="cct-nav">
<a href="<?= $prevVol ? '/catalog/beer/brew-house/' . $typeKey . '/' . $prevVol . 'l/' : '#' ?>" class="<?= $prevVol ? 'prev' : 'dis' ?>">← <?= $prevVol ? number_format($prevVol, 0, '.', ' ') . ' л' : '—' ?></a>
<a href="/catalog/beer/brew-house/<?= $typeKey ?>/">📋 Все объёмы</a>
<a href="<?= $nextVol ? '/catalog/beer/brew-house/' . $typeKey . '/' . $nextVol . 'l/' : '#' ?>" class="<?= $nextVol ? 'next' : 'dis' ?>"><?= $nextVol ? number_format($nextVol, 0, '.', ' ') . ' л' : '—' ?> →</a>
</div>

<div class="cct-card">
<h2><span class="acc">📐</span> Технические характеристики</h2>
<table class="cct-specs">
<tr><td>Объём номинальный</td><td><?= $volStr ?> л</td></tr>
<?php if (!empty($s['full_volume'])): ?>
<tr><td>Полный объём</td><td><?= number_format($s['full_volume'], 0, '.', ' ') ?> л</td></tr>
<tr><td>Рабочий объём</td><td><?= number_format($s['working_volume'], 0, '.', ' ') ?> л</td></tr>
<?php endif; ?>
<tr><td>Диаметр</td><td><?= $diameterM ?> м (<?= $s['diameter'] ?> мм)</td></tr>
<tr><td>Высота</td><td><?= $heightM ?> м (<?= $s['height'] ?> мм)</td></tr>
<tr><td>Толщина стенки</td><td><?= $s['wall'] ?> мм</td></tr>
<tr><td>Вес (пустой)</td><td>≈ <?= $s['weight'] ?> кг</td></tr>
<?php if ($s['power'] > 0): ?>
<tr><td>Мощность нагрева</td><td><?= $s['power'] ?> кВт</td></tr>
<?php endif; ?>
<tr><td>Материал</td><td>AISI 304 / AISI 316 (опция)</td></tr>
<tr><td>Внутренняя обработка</td><td>Электрополировка Ra ≤ 0,8 мкм</td></tr>
</table>
</div>

<div class="cct-card">
<h2><span class="acc">🏗️</span> Конструкция и материалы</h2>
<p style="font-size:14px;color:#666;line-height:1.8;margin:0"><?= htmlspecialchars($data['desc']) ?></p>
<p style="font-size:14px;color:#666;line-height:1.8;margin-top:12px">Изготовлен из высококачественной нержавеющей стали AISI 304 с зеркальной полировкой Ra ≤ 0.8 мкм. Сварные швы выполняются аргонодуговой сваркой с последующей шлифовкой. Каждое изделие проходит гидравлические испытания перед отгрузкой. Возможно изготовление из AISI 316 для агрессивных сред.</p>
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

<div class="cct-cta"><button onclick="document.getElementById('order').scrollIntoView({behavior:'smooth'})">📩 Получить расчёт <?= htmlspecialchars($data['name']) ?> <?= $volStr ?> л</button></div>

<div class="cct-card" style="margin-top:20px">
<h2><span class="acc">📊</span> Все объёмы <?= htmlspecialchars($data['name_short']) ?></h2>
<table class="cct-table">
<thead><tr><th>Модель</th><th>Полный, л</th><th>Раб., л</th><th>D, мм</th><th>H, мм</th><th>Стенка</th><th>Вес, кг</th><th>Цена</th></tr></thead>
<tbody><?php foreach ($sortedVols as $v):
    $spec = $data['specs'][$v];
    $vFmt = number_format($v, 0, '.', ' ');
    $p = $spec['price'];
    $pStr = $p >= 1000000 ? number_format($p/1000000,1,'.','').' млн ₽' : ($p >= 1000 ? number_format($p/1000,0,'.','').' тыс ₽' : number_format($p,0,'.',' ').' ₽');
    $d = $spec['diameter'] ?? '—';
    $h = $spec['height'] ?? '—';
    $fv = !empty($spec['full_volume']) ? number_format($spec['full_volume'], 0, '.', ' ') : '—';
    $wv = !empty($spec['working_volume']) ? number_format($spec['working_volume'], 0, '.', ' ') : '—';
?><tr class="<?= $v === $vol ? 'act' : '' ?>"><td><a href="/catalog/beer/brew-house/<?= $typeKey ?>/<?= $v ?>l/"><?= $vFmt ?> л</a></td><td><?= $fv ?></td><td><?= $wv ?></td><td><?= $d ?></td><td><?= $h ?></td><td><?= $spec['wall'] ?> мм</td><td><?= $spec['weight'] ?></td><td><?= $pStr ?></td></tr><?php endforeach; ?></tbody>
</table>
</div>

<div class="cct-faq">
<h2><span class="acc">❓</span> Часто задаваемые вопросы</h2>
<div class="faq-item"><div class="faq-q" onclick="this.parentElement.classList.toggle('open')">Из какого материала изготавливается оборудование?</div><div class="faq-a">Стандартное исполнение — нержавеющая сталь AISI 304 (пищевая, кислотостойкая). Для агрессивных сред доступна сталь AISI 316. По запросу — AISI 316L для фармацевтики и особо чистых производств.</div></div>
<div class="faq-item"><div class="faq-q" onclick="this.parentElement.classList.toggle('open')">Какие сроки изготовления?</div><div class="faq-a">Стандартные позиции — от 3-5 рабочих дней. Крупные позиции — от 14-20 дней, в зависимости от сложности и загрузки производства.</div></div>
<div class="faq-item"><div class="faq-q" onclick="this.parentElement.classList.toggle('open')">Какая гарантия на оборудование?</div><div class="faq-a">Гарантия на оборудование — 12 месяцев с даты отгрузки. Распространяется на дефекты материалов и изготовления.</div></div>
<div class="faq-item"><div class="faq-q" onclick="this.parentElement.classList.toggle('open')">Доставляете и монтируете?</div><div class="faq-a">Да, осуществляем доставку по всей России и странам СНГ любой ТК. Также предоставляем услуги шеф-монтажа и пусконаладки силами наших инженеров.</div></div>
<div class="faq-item"><div class="faq-q" onclick="this.parentElement.classList.toggle('open')">Как заказать?</div><div class="faq-a">Оставьте заявку через форму ниже или позвоните по телефону <strong>8 (993) 594-01-07</strong>. Мы подготовим коммерческое предложение с точной стоимостью, сроками и условиями доставки.</div></div>
</div>

<div class="other-section">
<h2><span class="acc">🔗</span> Другие аппараты варочного порядка</h2>
<div class="other-flex">
<?php foreach ($allData as $ok => $od):
    if ($ok === $typeKey) continue;
    $oUrl = '/catalog/beer/brew-house/' . $ok . '/';
?>
<a href="<?= $oUrl ?>"><?= htmlspecialchars($od['name']) ?></a>
<?php endforeach; ?>
</div>
</div>

<div class="cct-form" id="order">
<h2>📩 Получить расчёт <?= htmlspecialchars($data['name']) ?> <?= $volStr ?> л</h2>
<p class="cft-ub">Оставьте заявку — подготовим КП с точной стоимостью, сроками изготовления и доставки. Отвечаем в течение 2 часов.</p>
<form method="post" action="/php/send.php">
<input type="hidden" name="form_type" value="brew-house-product">
<input type="hidden" name="product" value="<?= htmlspecialchars($data['name'] . ' ' . $vol . ' л') ?>">
<div class="row">
<div><label>Ваше имя</label><input type="text" name="name" required placeholder="Иван"></div>
<div><label>Телефон</label><input type="tel" name="phone" required placeholder="+7 (___) ___-__-__" class="phone-mask"></div>
</div>
<div class="row">
<div><label>Email</label><input type="email" name="email" placeholder="ivan@example.ru"></div>
<div><label>Количество</label><input type="number" name="quantity" value="1" min="1"></div>
</div>
<div class="full"><label>Требования / вопросы</label><textarea name="comment" rows="3" placeholder="Дополнительные требования, материал, опции, сроки..."><?= htmlspecialchars($data['name']) ?> <?= $volStr ?> л, количество: 1 шт. Прошу рассчитать стоимость и сроки.</textarea></div>
<div class="full" style="margin-bottom:16px">
<label class="chk-label" style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-weight:400;text-transform:none;letter-spacing:0;font-size:13px;color:#666">
<input type="checkbox" name="agreement" value="1" required style="width:auto;margin-top:2px;accent-color:#F77C2A;flex-shrink:0">
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
