<?php
error_reporting(0);
ini_set('display_errors', 0);

// AJAX endpoint: ?get_prices=category&src=datavar
if (isset($_GET['get_prices'])) {
    header('Content-Type: application/json; charset=utf-8');
    $cat = $_GET['get_prices'];
    $src = $_GET['src'] ?? '';
    $files = [
        'beerExtra' => __DIR__ . '/catalog/beer-extra-data.php',
        'brewData' => __DIR__ . '/catalog/brew-house-data.php',
        'cctData' => __DIR__ . '/catalog/cct-data.php',
        'dairyData' => __DIR__ . '/catalog/dairy-data.php',
        'wineData' => __DIR__ . '/catalog/wine-data.php',
        'industrialData' => __DIR__ . '/catalog/industrial-data.php',
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
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
<title>Подбор оборудования — 3 шага</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style-original.css">
<style>
*,*::before,*::after{box-sizing:border-box}
body{margin:0;font-family:'Source Sans Pro',sans-serif;color:#2c3e50;background:#f5f6f8;-webkit-font-smoothing:antialiased;padding:0}
.wrap{max-width:800px;margin:0 auto;padding:48px 24px 60px}
h1{font-size:32px;font-weight:800;text-align:center;color:#1a1a26;margin:0 0 4px;letter-spacing:-.5px}
.sub{font-size:15px;color:#888;text-align:center;margin:0 0 32px;line-height:1.6}
/* Steps indicator */
.steps{display:flex;justify-content:center;align-items:center;gap:0;margin-bottom:40px}
.step{display:flex;align-items:center;gap:8px;font-size:13px;color:#ccc;font-weight:600;padding:8px 16px;border-radius:20px;transition:.3s}
.step .num{width:28px;height:28px;border-radius:50%;background:#e8e8e8;color:#bbb;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;transition:.3s;flex-shrink:0}
.step.active{color:#F77C2A}
.step.active .num{background:#F77C2A;color:#fff}
.step.done{color:#27ae60}
.step.done .num{background:#27ae60;color:#fff}
.step-line{width:40px;height:2px;background:#e8e8e8;flex-shrink:0}
.step.done + .step-line{background:#27ae60}
/* Card */
.card{background:#fff;border-radius:16px;padding:40px;border:1px solid #eee;box-shadow:0 2px 16px rgba(0,0,0,.05);margin-bottom:24px;display:none}
.card.active{display:block}
.card h2{font-size:20px;font-weight:800;color:#1a1a26;margin:0 0 4px}
.card .card-hint{font-size:13px;color:#999;margin:0 0 24px;line-height:1.5}
/* Step 1 - Search */
.search-box{position:relative;background:#fff;border:2px solid #e0e0e0;border-radius:12px;display:flex;align-items:center;padding:4px;transition:border-color .25s}
.search-box:focus-within{border-color:#F77C2A}
.search-box svg{flex-shrink:0;margin:0 8px 0 14px;color:#bbb}
.search-box input{flex:1;border:none;background:transparent;padding:14px 8px;font-size:16px;outline:none;font-family:inherit;color:#333}
.search-box input::placeholder{color:#bbb}
.suggestions{background:#fff;border:1px solid #e8e8e8;border-radius:16px;z-index:10;display:none;box-shadow:0 8px 32px rgba(0,0,0,.1);padding:8px;overflow-y:auto;max-height:520px}
.suggestions.show{display:block}
.suggestions .sr-item{display:flex;gap:20px;padding:16px;cursor:pointer;border-radius:12px;transition:background .15s;margin-bottom:4px}
.suggestions .sr-item:last-child{margin-bottom:0}
.suggestions .sr-item:hover{background:#fff8f0}
.suggestions .sr-item img{width:140px;height:140px;border-radius:12px;object-fit:contain;background:#f8f9fb;border:1px solid #eee;flex-shrink:0}
.suggestions .sr-item .sr-info{flex:1;min-width:0;display:flex;flex-direction:column;justify-content:center}
.suggestions .sr-item .sr-info strong{font-size:18px;color:#1a1a26;display:block;margin-bottom:6px}
.suggestions .sr-item .sr-info .sr-desc{font-size:13px;color:#666;line-height:1.5;margin-bottom:10px}
.suggestions .sr-item .sr-info .sr-feat{font-size:12px;color:#888;line-height:1.6;padding:0;margin:0;list-style:none}
.suggestions .sr-item .sr-info .sr-feat li{padding:2px 0 2px 16px;position:relative}
.suggestions .sr-item .sr-info .sr-feat li::before{content:'✓';position:absolute;left:0;color:#F77C2A;font-weight:700}
.suggestions .sr-item .sr-btn{align-self:flex-end;padding:10px 24px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;flex-shrink:0;transition:opacity .2s}
.suggestions .sr-item .sr-btn:hover{opacity:.9}
.suggestions .empty{color:#999;padding:20px;text-align:center;font-size:14px}
/* Step 2 - Volume grid */
.vol-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px}
.vol-btn{position:relative;padding:16px 10px;border:2px solid #e8e8e8;border-radius:12px;background:#fff;cursor:pointer;text-align:center;transition:all .25s;font-family:inherit}
.vol-btn:hover{border-color:#F77C2A;transform:translateY(-2px);box-shadow:0 4px 16px rgba(247,124,42,.1)}
.vol-btn .v-vol{font-size:18px;font-weight:800;color:#1a1a26;display:block}
.vol-btn .v-unit{font-size:12px;color:#999}
.vol-btn.sel{background:#F77C2A;border-color:#F77C2A;transform:translateY(-2px);box-shadow:0 6px 20px rgba(247,124,42,.25)}
.vol-btn.sel .v-vol,.vol-btn.sel .v-unit{color:#fff}
.vol-hint{text-align:center;font-size:13px;color:#999;margin-top:16px;line-height:1.5}
.vol-hint a{color:#F77C2A;text-decoration:none;font-weight:600;cursor:pointer}
.vol-hint a:hover{text-decoration:underline}
/* Step 3 - Result */
.result-header{display:flex;align-items:center;gap:16px;padding:20px;background:#f8f9fb;border-radius:12px;margin-bottom:20px}
.result-header .rh-icon{width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#F77C2A,#e06a15);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0}
.result-header .rh-info{flex:1;min-width:0}
.result-header .rh-info .rh-name{font-size:16px;font-weight:700;color:#1a1a26;display:block}
.result-header .rh-info .rh-vol{font-size:13px;color:#888}
.price-show{text-align:center;padding:28px;background:linear-gradient(135deg,#fff8f0,#fff);border:2px solid #F77C2A;border-radius:14px;margin-bottom:24px}
.price-show .ps-label{font-size:14px;color:#888;margin-bottom:6px}
.price-show .ps-val{font-size:42px;font-weight:900;color:#F77C2A;letter-spacing:-1px;line-height:1}
.price-show .ps-hint{font-size:13px;color:#bbb;margin-top:6px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-grid .full{grid-column:1/-1}
.form-grid input,.form-grid textarea{width:100%;padding:12px 16px;border:2px solid #e8e8e8;border-radius:10px;font-size:14px;font-family:inherit;outline:none;transition:border-color .25s;background:#fff}
.form-grid input:focus,.form-grid textarea:focus{border-color:#F77C2A}
.form-grid textarea{min-height:70px;resize:vertical}
.submit-btn{width:100%;padding:16px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border:none;border-radius:12px;font-size:16px;font-weight:700;cursor:pointer;transition:opacity .2s,transform .2s;font-family:inherit}
.submit-btn:hover{opacity:.92;transform:translateY(-1px)}
.submit-btn:active{transform:translateY(0)}
.loading{text-align:center;padding:40px;color:#bbb}
.loading .spin{display:inline-block;width:32px;height:32px;border:3px solid #eee;border-top-color:#F77C2A;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.reselect{color:#F77C2A;cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;margin-left:8px}
.reselect:hover{text-decoration:underline}
@media(max-width:600px){.wrap{padding:24px 16px 40px}h1{font-size:24px}.card{padding:24px}.vol-grid{grid-template-columns:repeat(2,1fr)}.form-grid{grid-template-columns:1fr}.steps{gap:4px}.step{padding:6px 10px;font-size:11px}.step-line{width:20px}.price-show .ps-val{font-size:32px}}
</style>
</head>
<body>
<div class="wrap">
<h1>🔧 Подбор оборудования</h1>
<p class="sub">Ответьте на 3 вопроса — получите точную цену и коммерческое предложение <strong>за 2 минуты</strong></p>

<div style="text-align:center;margin:-20px 0 32px">
<span style="display:inline-block;padding:8px 24px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border-radius:20px;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(247,124,42,.25)">🔥 Без звонков и ожидания — цена сразу</span>
</div>

<div class="steps">
<div class="step active" id="step1ind"><span class="num">1</span> Поиск</div>
<div class="step-line"></div>
<div class="step" id="step2ind"><span class="num">2</span> Объём</div>
<div class="step-line"></div>
<div class="step" id="step3ind"><span class="num">3</span> Расчёт</div>
</div>

<div class="card active" id="step1">
<h2>🔎 Шаг 1. Выберите оборудование</h2>
<p class="card-hint">Начните вводить название — мы покажем цену и характеристики</p>
<div class="search-box">
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
<input type="text" id="searchInput" placeholder="ЦКТ, БГВ, ферментатор, сыроизготовитель..." autocomplete="off">
</div>
<div class="suggestions" id="suggestions"></div>
</div>

<div class="card" id="step2">
<div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
<img id="selImg2" src="" alt="" style="width:72px;height:72px;object-fit:contain;border-radius:10px;background:#f8f9fb;border:1px solid #eee;flex-shrink:0;display:none">
<div>
<h2 style="margin:0">📏 Шаг 2. Какой объём?</h2>
<p class="card-hint" style="margin:4px 0 0">Оборудование: <strong id="selName2" style="color:#F77C2A"></strong> <a class="reselect" onclick="backToStep1()">(изменить)</a></p>
</div>
</div>
<div class="vol-grid" id="volGrid"></div>
<p class="vol-hint">Не нашли нужный объём? <a onclick="document.getElementById('customVolInput').style.display='block';this.style.display='none'">Укажите свой</a></p>
<div id="customVolInput" style="display:none;margin-top:12px">
<div style="display:flex;gap:10px">
<input type="number" id="customVolVal" placeholder="Ваш объём, л" style="flex:1;padding:12px 14px;border:2px solid #e0e0e0;border-radius:10px;font-size:14px;font-family:inherit;outline:none" onfocus="this.style.borderColor='#F77C2A'" onblur="this.style.borderColor='#e0e0e0'">
<button class="vol-btn" style="flex:0 0 auto;padding:12px 20px" onclick="selectCustomVol()">Выбрать</button>
</div>
</div>
</div>

<div class="card" id="step3">
<h2>📩 Шаг 3. Цена и заявка</h2>
<p class="card-hint">Всё готово! Цена ниже, укажите контакты — получите КП на почту</p>
<div class="result-header" id="resultHeader">
<img id="resImg" src="" alt="" style="width:56px;height:56px;border-radius:10px;object-fit:contain;background:#f8f9fb;border:1px solid #eee;flex-shrink:0">
<div class="rh-info">
<span class="rh-name" id="resName"></span>
<span class="rh-vol" id="resVol"></span>
</div>
</div>
<div class="price-show" id="priceShow" style="display:none">
<div class="ps-label" style="font-size:12px;color:#888;margin-bottom:6px">💰 Ориентировочная цена с НДС</div>
<div class="ps-val" id="priceVal"></div>
<div class="ps-hint" style="font-size:12px;color:#bbb;margin-top:6px">Точная цена — после расчёта инженером с учётом ваших требований</div>
</div>
<form method="post" action="/php/send.php" id="orderForm">
<input type="hidden" name="form_type" value="item">
<input type="hidden" name="product" id="formProduct" value="">
<div class="form-grid">
<div><input type="text" name="name" placeholder="Имя" required></div>
<div><input type="tel" name="phone" placeholder="Телефон" required></div>
<div class="full"><input type="email" name="email" placeholder="Email для КП"></div>
<div class="full"><textarea name="comment" placeholder="Дополнительные требования, сроки..."></textarea></div>
</div>
<button type="submit" class="submit-btn">📩 Отправить — получить КП с точной ценой и сроками</button>
</form>
<div style="display:flex;justify-content:center;gap:20px;margin-top:16px;flex-wrap:wrap;font-size:12px;color:#bbb">
<span>🔒 Конфиденциально</span>
<span>⚡ Ответ за 2 часа</span>
<span>📋 Бесплатный расчёт</span>
</div>
</div>
</div>

<script>
var step = 1;
var selected = null;
var searchTimer = null;

// Category → image mapping for search results
var CAT_IMAGES = {
    'cct': 'cct-tank.jpg',
    'mash-tun': 'mash-tun.jpg',
    'combined-kettle': 'combined-kettle.jpg',
    'lauter-tun': 'lauter-tun.jpg',
    'brew-kettle': 'brew-kettle.jpg',
    'whirlpool': 'beer-whirlpool.jpg',
    'wort-receiver': 'wort-receiver.jpg',
    'hot-water-tank': 'hot-water-tank.jpg',
    'grain-mill': 'grain-mill.jpg',
    'steam-generator': 'steam-generator.jpg',
    'chiller': 'chiller.jpg',
    'unitank': 'unitank.jpg',
    'heat-exchanger': 'heat-exchanger.jpg',
    'reception': 'dairy-reception.jpg',
    'cooler': 'dairy-cooler.jpg',
    'storage': 'dairy-storage.jpg',
    'vdp': 'dairy-vdp.jpg',
    'fermentation': 'dairy-fermentation.jpg',
    'cheese-maker': 'dairy-cheese-maker.jpg',
    'cottage-cheese': 'dairy-cottage-cheese.jpg',
    'yeast': 'dairy-yeast.jpg',
    'brine': 'dairy-brine.jpg',
    'cheese-shelves': 'dairy-brine.jpg',
    'red-fermentation': 'wine-red-fermentation.jpg',
    'white-fermentation': 'wine-white-fermentation.jpg',
    'storage-aging': 'wine-storage-aging.jpg',
    'cold-stabilization': 'wine-cold-stabilization.jpg',
    'blending': 'wine-blending.png',
    'sulfitation': 'wine-sulfitation.jpg',
    'universal-tank': 'wine-universal-tank.jpg',
    'mixing': 'industrial-mixing.jpg',
    'thermal': 'industrial-thermal.jpg',
    'pressure': 'industrial-pressure.jpg',
    'cip': 'industrial-cip.jpg',
    'heat-exchanger-ind': 'industrial-heat-exchanger.jpg',
};
var CAT_DEFAULTS = { beer: 'cct-tank.jpg', dairy: 'dairy-reception.jpg', wine: 'wine-red-fermentation.jpg', industrial: 'industrial-cip.jpg' };

// Short descriptions for equipment cards
var CAT_DESCS = {
    'cct': 'Цилиндро-конические танки для брожения и дображивания пива. Полная герметичность, рубашка охлаждения, предохранительный клапан.',
    'hot-water-tank': 'Баки горячей воды из нержавейки с паровой рубашкой и термоизоляцией ППУ для пивоварен.',
    'grain-mill': 'Промышленные вальцовые дробилки солода производительностью от 100 до 1000 кг/ч.',
    'steam-generator': 'Электрические и газовые парогенераторы для пивоварен и пищевых производств.',
    'chiller': 'Холодильные агрегаты для охлаждения сусла, пива, молока. Пропиленгликоль.',
    'unitank': 'Форфасы для дображивания, карбонизации и хранения пива под давлением.',
    'heat-exchanger': 'Пластинчатые теплообменники AISI 304/316 для нагрева и охлаждения жидкостей.',
    'mash-tun': 'Заторные аппараты с двумя раздельными рубашками нагрева и лопастной мешалкой.',
    'combined-kettle': 'Комбинированные заторно-сусловарочные аппараты 2-в-1, экономят место и бюджет.',
    'lauter-tun': 'Фильтрационные аппараты для отделения дробины от пивного сусла.',
    'brew-kettle': 'Сусловарочные котлы для кипячения сусла с хмелем.',
    'whirlpool': 'Гидроциклонные аппараты для осветления сусла перед охлаждением.',
    'wort-receiver': 'Сборники горячего сусла из нержавеющей стали.',
    'reception': 'Ёмкости для приёмки, фильтрации и учёта сырого молока.',
    'cooler': 'Резервуары-охладители молока с рубашкой пропиленгликоля.',
    'storage': 'Резервуары для хранения молока и пищевых жидкостей, вертикальные и горизонтальные.',
    'vdp': 'Ванны длительной пастеризации молока с двустенной рубашкой и мешалкой.',
    'fermentation': 'Танки для ферментации кефира, йогурта, сметаны и других кисломолочных продуктов.',
    'cheese-maker': 'Сыроизготовители с лазерной резкой сырного зерна для любых видов сыра.',
    'cottage-cheese': 'Творогоизготовители с нагревом до 95°C и дренажем сыворотки.',
    'yeast': 'Заквасочники для подготовки молочных заквасок, компактные, от 50 до 500 л.',
    'brine': 'Контейнеры для посола сыра из AISI 316, корзина для погружения.',
    'cheese-shelves': 'Модульные стеллажи для созревания сыра, нагрузка до 200 кг/м².',
    'red-fermentation': 'Танки для ферментации красных вин с мешалкой и рубашкой.',
    'white-fermentation': 'Танки для ферментации белых вин с точным контролем температуры.',
    'storage-aging': 'Ёмкости для выдержки и хранения вина в идеальных условиях.',
    'cold-stabilization': 'Танки для холодной стабилизации вина до −5°C.',
    'blending': 'Купажные аппараты для смешивания виноматериалов с мешалкой.',
    'sulfitation': 'Установки для сульфитации вина, точная дозировка.',
    'universal-tank': 'Универсальные терморегулируемые танки-винификаторы для красных и белых вин.',
    'mixing': 'Ёмкости с лопастными и якорными мешалками для пищевых производств.',
    'thermal': 'Ёмкости с рубашкой нагрева/охлаждения и терморегуляцией.',
    'pressure': 'Сосуды под давлением до 6 бар, полный пакет документов.',
    'cip': 'Станции безразборной мойки CIP с программируемым циклом.',
};

var CAT_FEATS = {
    'cct': ['AISI 304 / 316', 'Рубашка охлаждения', 'Термоизоляция ППУ', 'Предохранительный клапан', 'CIP-мойка'],
    'hot-water-tank': ['Паровой нагрев', 'Термоизоляция 50–150 мм', 'Люк-лаз DN400', 'Датчик Pt100'],
    'reception': ['Фильтр грубой очистки', 'Люк-лаз DN400', 'CIP-мойка', 'Уровнемер'],
    'storage': ['AISI 304 / 316', 'Термоизоляция 50–200 мм', 'Люк-лаз DN500', 'CIP-мойка'],
    'vdp': ['Двустенная рубашка', 'Лопастная мешалка', 'PID-терморегулятор', 'До 95°C'],
    'universal-tank': ['Рубашка нагрев + охлаждение', 'Термоизоляция 80 мм', 'Решётка для мезги', 'PID-регулятор'],
};

function catFeats(catKey) {
    return CAT_FEATS[catKey] || ['Лазерная сварка AISI 304', 'Гарантия 12 мес', 'Доставка по РФ'];
}

function catImg(catKey, sid) {
    return '/' + (CAT_IMAGES[catKey] || CAT_DEFAULTS[sid] || 'cct-tank.jpg');
}

function nextStep() {
    document.getElementById('step' + step).classList.remove('active');
    document.getElementById('step' + step + 'ind').classList.remove('active');
    document.getElementById('step' + step + 'ind').classList.add('done');
    step++;
    var el = document.getElementById('step' + step);
    if (el) el.classList.add('active');
    var ind = document.getElementById('step' + step + 'ind');
    if (ind) ind.classList.add('active');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function backToStep1() {
    document.getElementById('step3').classList.remove('active');
    document.getElementById('step3ind').classList.remove('active');
    document.getElementById('step3ind').classList.remove('done');
    document.getElementById('step2ind').classList.remove('done');
    document.getElementById('step1ind').classList.remove('done');
    document.getElementById('step1ind').classList.add('active');
    document.getElementById('step1').classList.add('active');
    document.getElementById('step2').classList.remove('active');
    step = 1;
    selected = null;
    document.getElementById('searchInput').value = '';
    document.getElementById('suggestions').classList.remove('show');
    document.getElementById('searchInput').focus();
}

function fmtPrice(p) {
    return p >= 1000000 ? (p/1000000).toFixed(1) + ' млн ₽' : (p >= 1000 ? Math.round(p/1000) + ' тыс ₽' : p + ' ₽');
}

function selectCustomVol() {
    var v = parseInt(document.getElementById('customVolVal').value);
    if (!v || v <= 0) { document.getElementById('customVolVal').focus(); return; }
    showStep3(selected.priceData, v, 0);
}

document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimer);
    var q = this.value.trim();
    var sug = document.getElementById('suggestions');
    if (!q) { sug.classList.remove('show'); return; }
    searchTimer = setTimeout(function() {
        fetch('/php/search.php?q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(d) {
            // Filter out individual volume entries, keep only category cards
            var cats = d.results.filter(function(r) { return !r.u.match(/\/(\d+)l?\/?$/); });
            // Deduplicate by URL
            var seen = {}; cats = cats.filter(function(r) { var k = r.u; if (seen[k]) return false; seen[k] = true; return true; });
            
            sug.innerHTML = '';
            if (!cats.length) {
                sug.innerHTML = '<div class="empty">Ничего не найдено. Попробуйте другое название</div>';
                sug.classList.add('show'); return;
            }
            cats.forEach(function(r) {
                var catKey = r.u.split('/').filter(Boolean).pop();
                var imgSrc = catImg(catKey, r.si);
                var desc = CAT_DESCS[catKey] || r.s;
                var feats = catFeats(catKey);
                var featHtml = '<ul class="sr-feat">';
                feats.slice(0, 4).forEach(function(f) { featHtml += '<li>' + f + '</li>'; });
                featHtml += '</ul>';
                var div = document.createElement('div');
                div.className = 'sr-item';
                div.innerHTML = '<img src="' + imgSrc + '" alt="" onerror="this.style.display=\'none\'"><div class="sr-info"><strong>' + r.n + '</strong><div class="sr-desc">' + desc + '</div>' + featHtml + '</div><button class="sr-btn">Выбрать →</button>';
                div.querySelector('.sr-btn').addEventListener('click', function(e) { e.stopPropagation(); selectItem(r); });
                div.addEventListener('click', function() { selectItem(r); });
                sug.appendChild(div);
            });
            sug.classList.add('show');
        }).catch(function() {});
    }, 300);
});

document.getElementById('searchInput').addEventListener('blur', function() {
    setTimeout(function() { document.getElementById('suggestions').classList.remove('show'); }, 250);
});

function selectItem(r) {
    document.getElementById('searchInput').value = r.n;
    document.getElementById('suggestions').classList.remove('show');
    selected = { name: r.n, url: r.u, si: r.si };
    
    document.getElementById('selName2').textContent = r.n;
    var imgSrc = catImg(r.u.split('/').filter(Boolean).pop(), r.si);
    var imgEl = document.getElementById('selImg2');
    imgEl.src = imgSrc;
    imgEl.onerror = function(){ this.style.display = 'none'; };
    imgEl.style.display = 'block';
    document.getElementById('volGrid').innerHTML = '<div class="loading"><div class="spin"></div><br>Загружаем цены...</div>';
    nextStep();
    
    var u = new URL(r.u, location.origin);
    var pathParts = u.pathname.split('/').filter(Boolean);
    // Find category key: second-to-last segment if last is a volume, otherwise last
    var last = pathParts[pathParts.length - 1];
    var catKey = last.match(/^\d+l?$/) ? pathParts[pathParts.length - 2] : last;
    var srcMap = { beer: 'beerExtra', dairy: 'dairyData', wine: 'wineData', industrial: 'industrialData' };
    var src = srcMap[r.si] || '';
    if (pathParts.includes('brew-house')) src = 'brewData';
    if (pathParts.includes('cct')) src = 'cctData';
    
    fetch('?get_prices=' + encodeURIComponent(catKey) + '&src=' + src)
    .then(function(r) { return r.json(); })
    .then(function(d) {
        selected.priceData = d;
        var grid = document.getElementById('volGrid');
        grid.innerHTML = '';
        if (!d.prices || !d.prices.length) {
            grid.innerHTML = '<div style="text-align:center;padding:24px;color:#999">Нет данных о ценах</div>';
            return;
        }
            d.prices.sort(function(a,b) { return a.vol - b.vol; });
            d.prices.forEach(function(p) {
                var btn = document.createElement('button');
                btn.className = 'vol-btn';
                btn.innerHTML = '<span class="v-vol">' + p.vol + '</span><span class="v-unit">л</span>';
                btn.addEventListener('click', function() {
                    grid.querySelectorAll('.vol-btn').forEach(function(b) { b.classList.remove('sel'); });
                    btn.classList.add('sel');
                    selected.vol = p.vol;
                    selected.price = p.price;
                    showStep3(d, p.vol, p.price);
                });
                grid.appendChild(btn);
            });
            // Custom volume button
            var cust = document.createElement('button');
            cust.className = 'vol-btn';
            cust.style.borderStyle = 'dashed';
            cust.innerHTML = '<span class="v-vol" style="font-size:14px">Свой</span><span class="v-unit">объём</span>';
            cust.addEventListener('click', function() {
                grid.querySelectorAll('.vol-btn').forEach(function(b) { b.classList.remove('sel'); });
                cust.classList.add('sel');
                showStep3(d, 0, 0);
            });
            grid.appendChild(cust);
            }).catch(function() {
        document.getElementById('volGrid').innerHTML = '<div style="text-align:center;padding:24px;color:#e74c3c">Ошибка загрузки</div>';
    });
}

function showStep3(d, vol, price) {
    var volStr = vol > 0 ? vol + ' л' : 'нестандартный объём';
    var nm = d.name + ' ' + volStr;
    document.getElementById('formProduct').value = nm;
    document.getElementById('resName').textContent = d.name;
    document.getElementById('resVol').textContent = volStr;
    // Set image from step 2
    var img = document.getElementById('resImg');
    var img2 = document.getElementById('selImg2');
    if (img2 && img2.style.display !== 'none') { img.src = img2.src; img.style.display = 'block'; }
    else { img.style.display = 'none'; }
    if (price > 0) {
        document.getElementById('priceVal').textContent = 'от ' + fmtPrice(price);
        document.getElementById('priceShow').style.display = 'block';
    } else {
        document.getElementById('priceShow').style.display = 'block';
        document.getElementById('priceVal').textContent = 'По запросу';
    }
    nextStep();
}
</script>
</body>
</html>