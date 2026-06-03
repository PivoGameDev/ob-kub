<?php
header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? mb_strtolower(trim($_GET['q'])) : '';
$q = str_replace('ё', 'е', $q); // ё→е для поиска

// No query — return empty
if (!$q) {
    echo '{"results":[]}';
    exit;
}

// Minimal SVG icon for each section
$icons = [
    'beer' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F77C2A" stroke-width="2"><path d="M6 20h12M8 16V4a2 2 0 012-2h4a2 2 0 012 2v12M4 16h16M4 20h16"/></svg>',
    'dairy' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F77C2A" stroke-width="2"><path d="M8 2l4 4v14a2 2 0 002 2h4a2 2 0 002-2V6l-4-4"/></svg>',
    'wine' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F77C2A" stroke-width="2"><path d="M12 2l-3 8v8a3 3 0 006 0v-8z"/></svg>',
    'industrial' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F77C2A" stroke-width="2"><rect x="4" y="8" width="16" height="12" rx="1"/><path d="M8 8V4h8v4"/></svg>',
];

$results = [];

function matchItem($q, $name) {
    $nl = mb_strtolower($name);
    $nl = str_replace('ё', 'е', $nl);
    return mb_strpos($nl, $q) !== false;
}

function add(&$r, $q, $name, $section, $sectionId, $cat, $url, $icon) {
    if (matchItem($q, $name)) {
        $r[] = ['n' => $name, 's' => $section, 'si' => $sectionId, 'c' => $cat, 'u' => $url, 'i' => $icon];
    }
}

// ===== SYNONYM MAP (жаргон → каноническое название) =====
$synonymIndex = [
    // ── Пиво ──
    ['syns' => ['цилиндроконический танк', 'конусный танк', 'конический танк', 'танк для пива', 'пивной танк', 'танк брожения', 'танк дображивания', 'танк цкт'], 'name' => 'ЦКТ (Цилиндро-конические танки)', 'sec' => 'Пивоваренное оборудование', 'sid' => 'beer', 'url' => '/catalog/beer/cct/', 'ico' => $icons['beer']],
    ['syns' => ['маш тюн', 'заторный чан', 'заторный бак', 'заторник', 'mash tun'], 'name' => 'Варочные порядки — Заторный аппарат', 'sec' => 'Пивоваренное оборудование', 'sid' => 'beer', 'url' => '/catalog/beer/brew-house/mash-tun/', 'ico' => $icons['beer']],
    ['syns' => ['зса', 'заторно-сусловарочный котел'], 'name' => 'Варочные порядки — Заторно-сусловарочный аппарат', 'sec' => 'Пивоваренное оборудование', 'sid' => 'beer', 'url' => '/catalog/beer/brew-house/combined-kettle/', 'ico' => $icons['beer']],
    ['syns' => ['лаутер тюн', 'лаутер', 'lauter tun', 'фильтрчан'], 'name' => 'Варочные порядки — Фильтрационный аппарат', 'sec' => 'Пивоваренное оборудование', 'sid' => 'beer', 'url' => '/catalog/beer/brew-house/lauter-tun/', 'ico' => $icons['beer']],
    ['syns' => ['сусловарка', 'сусловарочный котел', 'варочный котел', 'котел для сусла', 'котел для варки сусла'], 'name' => 'Варочные порядки — Сусловарочный аппарат', 'sec' => 'Пивоваренное оборудование', 'sid' => 'beer', 'url' => '/catalog/beer/brew-house/brew-kettle/', 'ico' => $icons['beer']],
    ['syns' => ['гидроциклон', 'whirlpool', 'вирпул'], 'name' => 'Варочные порядки — Гидроциклонный аппарат (Вирпул)', 'sec' => 'Пивоваренное оборудование', 'sid' => 'beer', 'url' => '/catalog/beer/brew-house/whirlpool/', 'ico' => $icons['beer']],
    ['syns' => ['суслоприемник', 'сборник горячего сусла', 'сборник сусла'], 'name' => 'Варочные порядки — Суслосборник', 'sec' => 'Пивоваренное оборудование', 'sid' => 'beer', 'url' => '/catalog/beer/brew-house/wort-receiver/', 'ico' => $icons['beer']],
    ['syns' => ['форфаc', 'лагерный танк', 'лагерник', 'юнит', 'unitank', 'bbt'], 'name' => 'Форфас (BBT)', 'sec' => 'Пивоваренное оборудование', 'sid' => 'beer', 'url' => '/catalog/beer/unitank/', 'ico' => $icons['beer']],
    ['syns' => ['бак горячего водоснабжения', 'бак гвс', 'бак для воды', 'бгв'], 'name' => 'Бак горячей воды', 'sec' => 'Пивоваренное оборудование', 'sid' => 'beer', 'url' => '/catalog/beer/hot-water-tank/', 'ico' => $icons['beer']],
    // ── Молоко ──
    ['syns' => ['емкость приемки молока', 'приемная емкость', 'молокоприемник', 'емкость для приема молока'], 'name' => 'Ёмкость приёмки молока', 'sec' => 'Молочное оборудование', 'sid' => 'dairy', 'url' => '/catalog/dairy/reception/', 'ico' => $icons['dairy']],
    ['syns' => ['танк-охладитель', 'молокоохладитель', 'охладительный резервуар', 'емкость охладитель молока'], 'name' => 'Резервуар-охладитель молока', 'sec' => 'Молочное оборудование', 'sid' => 'dairy', 'url' => '/catalog/dairy/cooler/', 'ico' => $icons['dairy']],
    ['syns' => ['танк для молока', 'емкость для молока', 'резервуар молочный'], 'name' => 'Резервуар для хранения молока', 'sec' => 'Молочное оборудование', 'sid' => 'dairy', 'url' => '/catalog/dairy/storage/', 'ico' => $icons['dairy']],
    ['syns' => ['пастеризатор', 'пастеризационная ванна', 'ванна пастеризации', 'вдп пастеризатор'], 'name' => 'Ванна длительной пастеризации (ВДП)', 'sec' => 'Молочное оборудование', 'sid' => 'dairy', 'url' => '/catalog/dairy/vdp/', 'ico' => $icons['dairy']],
    ['syns' => ['сыроделка', 'аппарат для сыра', 'сырная ванна', 'сыродельный аппарат'], 'name' => 'Сыроизготовитель', 'sec' => 'Молочное оборудование', 'sid' => 'dairy', 'url' => '/catalog/dairy/cheese-maker/', 'ico' => $icons['dairy']],
    ['syns' => ['творожница', 'аппарат для творога'], 'name' => 'Творогоизготовитель', 'sec' => 'Молочное оборудование', 'sid' => 'dairy', 'url' => '/catalog/dairy/cottage-cheese/', 'ico' => $icons['dairy']],
    ['syns' => ['ферментер', 'ферментатор', 'танк для йогурта', 'емкость для сметаны', 'йогуртный танк', 'заквасочный танк'], 'name' => 'Ферментационный танк', 'sec' => 'Молочное оборудование', 'sid' => 'dairy', 'url' => '/catalog/dairy/fermentation/', 'ico' => $icons['dairy']],
    ['syns' => ['бассейн для соления', 'контейнер для посола', 'солильный контейнер', 'емкость для соления сыра'], 'name' => 'Контейнер для соления сыра', 'sec' => 'Молочное оборудование', 'sid' => 'dairy', 'url' => '/catalog/dairy/brine/', 'ico' => $icons['dairy']],
    ['syns' => ['заквасочный аппарат', 'ванна для закваски'], 'name' => 'Заквасочник', 'sec' => 'Молочное оборудование', 'sid' => 'dairy', 'url' => '/catalog/dairy/yeast/', 'ico' => $icons['dairy']],
    // ── Вино ──
    ['syns' => ['чаны для красного вина', 'резервуар для красного вина'], 'name' => 'Ферментационный танк для красных вин', 'sec' => 'Винодельческое оборудование', 'sid' => 'wine', 'url' => '/catalog/wine/red-fermentation/', 'ico' => $icons['wine']],
    ['syns' => ['чаны для белого вина', 'резервуар для белого вина'], 'name' => 'Ферментационный танк для белых вин', 'sec' => 'Винодельческое оборудование', 'sid' => 'wine', 'url' => '/catalog/wine/white-fermentation/', 'ico' => $icons['wine']],
    ['syns' => ['винная емкость', 'танк для вина', 'емкость для вина', 'винный резервуар'], 'name' => 'Ёмкость для выдержки и хранения вина', 'sec' => 'Винодельческое оборудование', 'sid' => 'wine', 'url' => '/catalog/wine/storage-aging/', 'ico' => $icons['wine']],
    ['syns' => ['стабилизатор вина', 'криостабилизатор'], 'name' => 'Танк холодной стабилизации (криостат)', 'sec' => 'Винодельческое оборудование', 'sid' => 'wine', 'url' => '/catalog/wine/cold-stabilization/', 'ico' => $icons['wine']],
    ['syns' => ['купажер', 'купажный аппарат'], 'name' => 'Ёмкость для купажирования вина', 'sec' => 'Винодельческое оборудование', 'sid' => 'wine', 'url' => '/catalog/wine/blending/', 'ico' => $icons['wine']],
    ['syns' => ['емкость сульфитации'], 'name' => 'Ёмкость сульфитации вина', 'sec' => 'Винодельческое оборудование', 'sid' => 'wine', 'url' => '/catalog/wine/sulfitation/', 'ico' => $icons['wine']],
    ['syns' => ['утт', 'универсальный танк', 'терморегулируемый танк'], 'name' => 'Винификатор (универсальный терморегулируемый танк)', 'sec' => 'Винодельческое оборудование', 'sid' => 'wine', 'url' => '/catalog/wine/universal-tank/', 'ico' => $icons['wine']],
    // ── Промышленное ──
    ['syns' => ['складской резервуар', 'накопительный резервуар', 'хранилище'], 'name' => 'Резервуар для хранения', 'sec' => 'Промышленное оборудование', 'sid' => 'industrial', 'url' => '/catalog/industrial/storage/', 'ico' => $icons['industrial']],
    ['syns' => ['емкость с перемешиванием', 'бак с мешалкой', 'танк с мешалкой', 'аппарат с мешалкой'], 'name' => 'Ёмкость с мешалкой', 'sec' => 'Промышленное оборудование', 'sid' => 'industrial', 'url' => '/catalog/industrial/mixing/', 'ico' => $icons['industrial']],
    ['syns' => ['емкость с обогревом', 'емкость с охлаждением', 'термостатированная емкость', 'емкость с рубашкой', 'танк с терморегуляцией'], 'name' => 'Ёмкость с терморегуляцией', 'sec' => 'Промышленное оборудование', 'sid' => 'industrial', 'url' => '/catalog/industrial/thermal/', 'ico' => $icons['industrial']],
    ['syns' => ['напорная емкость', 'герметичный сосуд'], 'name' => 'Ёмкость под давлением', 'sec' => 'Промышленное оборудование', 'sid' => 'industrial', 'url' => '/catalog/industrial/pressure/', 'ico' => $icons['industrial']],
];

// ==== SYNONYM MATCHING (прямое добавление, без matchItem) ====
$synQ = str_replace('ё', 'е', $q);
foreach ($synonymIndex as $si) {
    foreach ($si['syns'] as $syn) {
        $sn = str_replace('ё', 'е', mb_strtolower($syn));
        if (mb_strpos($synQ, $sn) !== false || mb_strpos($sn, $synQ) !== false) {
            $exists = false;
            foreach ($results as $r) {
                if ($r['u'] === $si['url']) { $exists = true; break; }
            }
            if (!$exists) {
                $results[] = ['n' => $si['name'], 's' => $si['sec'], 'si' => $si['sid'], 'c' => '', 'u' => $si['url'], 'i' => $si['ico']];
            }
            break;
        }
    }
}

// ===== CATEGORIES & SECTIONS =====
$sections = [
    ['id' => 'beer', 'name' => 'Пивоваренное оборудование', 'url' => '/catalog/beer/'],
    ['id' => 'dairy', 'name' => 'Молочное оборудование', 'url' => '/catalog/dairy/'],
    ['id' => 'wine', 'name' => 'Винодельческое оборудование', 'url' => '/catalog/wine/'],
    ['id' => 'industrial', 'name' => 'Промышленное оборудование', 'url' => '/catalog/industrial/'],
];

// Add sections themselves
foreach ($sections as $sec) {
    add($results, $q, $sec['name'], $sec['name'], $sec['id'], '', $sec['url'], $icons[$sec['id']]);
}

// ===== BEER CATEGORIES =====
$beerCats = [
    ['n' => 'ЦКТ (Цилиндро-конические танки)', 'url' => '/catalog/beer/cct/', 'vols' => [100,250,500,1000,1500,2000,3000,4000,5000,6000,7500,8000,10000,12000,15000,20000,25000,30000,40000,50000,60000,80000,100000,120000,150000,200000], 'unit' => 'л'],
    ['n' => 'Варочные порядки', 'url' => '/catalog/beer/brew-house/', 'subs' => [
        ['n' => 'Заторный аппарат', 'slug' => 'mash-tun', 'vols' => [250,500,1000,2000,3000,5000]],
        ['n' => 'Заторно-сусловарочный аппарат', 'slug' => 'combined-kettle', 'vols' => [250,500,1000,2000,3000,5000]],
        ['n' => 'Фильтрационный аппарат', 'slug' => 'lauter-tun', 'vols' => [250,500,1000,2000,3000,5000]],
        ['n' => 'Сусловарочный аппарат', 'slug' => 'brew-kettle', 'vols' => [250,500,1000,2000,3000,5000]],
        ['n' => 'Гидроциклонный аппарат (Вирпул)', 'slug' => 'whirlpool', 'vols' => [250,500,1000,2000,3000,5000]],
        ['n' => 'Суслосборник', 'slug' => 'wort-receiver', 'vols' => [500,1000,2000,3000,5000]],
    ]],
    ['n' => 'Бак горячей воды', 'url' => '/catalog/beer/hot-water-tank/', 'vols' => [500,1000,1500,2000,3000,4000,5000,6000,8000,10000,15000,20000], 'unit' => 'л'],
    ['n' => 'Дробилка солода', 'url' => '/catalog/beer/grain-mill/', 'vols' => [100,200,300,500,1000], 'unit' => 'кг/ч'],
    ['n' => 'Парогенератор', 'url' => '/catalog/beer/steam-generator/', 'vols' => [20,100,150,300,400,700], 'unit' => 'кг пара/ч'],
    ['n' => 'Чиллер', 'url' => '/catalog/beer/chiller/', 'vols' => [8,12,20,50,150], 'unit' => 'кВт'],
    ['n' => 'Форфас (BBT)', 'url' => '/catalog/beer/unitank/', 'vols' => [250,500,1000,1500,2000,3000,5000], 'unit' => 'л'],
    ['n' => 'Теплообменник пластинчатый', 'url' => '/catalog/beer/heat-exchanger/', 'vols' => [300,600,1000,3000,5000], 'unit' => 'л/ч'],
];

// ===== DAIRY CATEGORIES =====
$dairyCats = [
    ['n' => 'Ёмкость приёмки молока', 'slug' => 'reception', 'vols' => [1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000]],
    ['n' => 'Резервуар-охладитель молока', 'slug' => 'cooler', 'vols' => [1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000]],
    ['n' => 'Резервуар для хранения молока', 'slug' => 'storage', 'vols' => [3000,4000,5000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000,63000,80000,100000,125000,160000,200000]],
    ['n' => 'Ванна длительной пастеризации (ВДП)', 'slug' => 'vdp', 'vols' => [200,300,500,1000,1500,2000,3000,4000,5000,6300,8000,10000]],
    ['n' => 'Ферментационный танк', 'slug' => 'fermentation', 'vols' => [500,1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000]],
    ['n' => 'Сыроизготовитель', 'slug' => 'cheese-maker', 'vols' => [200,300,500,1000,1500,2000,3000,4000,5000,6300,8000,10000]],
    ['n' => 'Творогоизготовитель', 'slug' => 'cottage-cheese', 'vols' => [200,300,500,1000,1500,2000,3000,4000,5000,6300]],
    ['n' => 'Заквасочник', 'slug' => 'yeast', 'vols' => [50,100,200,300,500,630,800,1000]],
    ['n' => 'Контейнер для соления сыра', 'slug' => 'brine', 'vols' => [200,300,500,1000,1500,2000,3000,4000,5000,6300]],
    ['n' => 'Стеллажи для созревания сыра', 'slug' => 'cheese-shelves'],
];

// ===== WINE CATEGORIES =====
$wineCats = [
    ['n' => 'Ферментационный танк для красных вин', 'slug' => 'red-fermentation', 'vols' => [500,1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000]],
    ['n' => 'Ферментационный танк для белых вин', 'slug' => 'white-fermentation', 'vols' => [500,1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,31500]],
    ['n' => 'Ёмкость для выдержки и хранения вина', 'slug' => 'storage-aging', 'vols' => [1000,1500,2000,3000,4000,5000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000,63000,80000,100000,125000,160000,200000]],
    ['n' => 'Танк холодной стабилизации (криостат)', 'slug' => 'cold-stabilization', 'vols' => [500,1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,31500]],
    ['n' => 'Ёмкость для купажирования вина', 'slug' => 'blending', 'vols' => [1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000]],
    ['n' => 'Ёмкость сульфитации вина', 'slug' => 'sulfitation', 'vols' => [500,1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,31500]],
    ['n' => 'Винификатор (универсальный терморегулируемый танк)', 'slug' => 'universal-tank', 'vols' => [500,1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000]],
];

// ===== INDUSTRIAL CATEGORIES =====
$industrialCats = [
    ['n' => 'Резервуар для хранения', 'slug' => 'storage', 'vols' => [1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000,63000,80000,100000]],
    ['n' => 'Ёмкость с мешалкой', 'slug' => 'mixing', 'vols' => [500,1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000]],
    ['n' => 'Ёмкость с терморегуляцией', 'slug' => 'thermal', 'vols' => [500,1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,30000,31500,40000,50000]],
    ['n' => 'Ёмкость под давлением', 'slug' => 'pressure', 'vols' => [200,300,400,500,1000,1500,2000,3000,4000,5000,6000,6300,8000,10000,12500,15000,16000,20000,25000,30000]],
    ['n' => 'CIP-станция', 'slug' => 'cip'],
    ['n' => 'Теплообменник промышленный', 'slug' => 'heat-exchanger'],
];

// Helper: format volume for display
function fmt($v, $unit = 'л') {
    if ($unit !== 'л') return $v . ' ' . $unit;
    if ($v >= 1000) return number_format($v, 0, '.', ' ') . ' л';
    return $v . ' л';
}

// ===== BUILD INDEX =====

// 1. Beer categories (flat and with subs)
foreach ($beerCats as $cat) {
    if (isset($cat['subs'])) {
        add($results, $q, $cat['n'], 'Пивоваренное оборудование', 'beer', '', $cat['url'], $icons['beer']);
        foreach ($cat['subs'] as $sub) {
            $subUrl = $cat['url'] . $sub['slug'] . '/';
            add($results, $q, $sub['n'], 'Пивоваренное оборудование', 'beer', $cat['n'], $subUrl, $icons['beer']);
            if (isset($sub['vols'])) {
                foreach ($sub['vols'] as $v) {
                    add($results, $q, $sub['n'] . ' ' . fmt($v), 'Пивоваренное оборудование', 'beer', $cat['n'] . ' — ' . $sub['n'], $subUrl . $v . 'l/', $icons['beer']);
                }
            }
        }
    } elseif (isset($cat['vols'])) {
        $unit = isset($cat['unit']) ? $cat['unit'] : 'л';
        add($results, $q, $cat['n'], 'Пивоваренное оборудование', 'beer', '', $cat['url'], $icons['beer']);
        if ($cat['n'] === 'ЦКТ (Цилиндро-конические танки)') {
            foreach ($cat['vols'] as $v) {
                add($results, $q, 'ЦКТ ' . fmt($v), 'Пивоваренное оборудование', 'beer', $cat['n'], $cat['url'] . $v . 'l/', $icons['beer']);
            }
        } else {
            foreach ($cat['vols'] as $v) {
                add($results, $q, $cat['n'] . ' ' . fmt($v, $unit), 'Пивоваренное оборудование', 'beer', $cat['n'], $cat['url'] . $v . 'l/', $icons['beer']);
            }
        }
    } else {
        add($results, $q, $cat['n'], 'Пивоваренное оборудование', 'beer', '', $cat['url'], $icons['beer']);
    }
}

// 2. Dairy categories
foreach ($dairyCats as $cat) {
    $url = '/catalog/dairy/' . $cat['slug'] . '/';
    add($results, $q, $cat['n'], 'Молочное оборудование', 'dairy', '', $url, $icons['dairy']);
    if (isset($cat['vols'])) {
        foreach ($cat['vols'] as $v) {
            add($results, $q, $cat['n'] . ' ' . fmt($v), 'Молочное оборудование', 'dairy', $cat['n'], $url . $v . 'l/', $icons['dairy']);
        }
    }
}

// 3. Wine categories
foreach ($wineCats as $cat) {
    $url = '/catalog/wine/' . $cat['slug'] . '/';
    add($results, $q, $cat['n'], 'Винодельческое оборудование', 'wine', '', $url, $icons['wine']);
    if (isset($cat['vols'])) {
        foreach ($cat['vols'] as $v) {
            add($results, $q, $cat['n'] . ' ' . fmt($v), 'Винодельческое оборудование', 'wine', $cat['n'], $url . $v . 'l/', $icons['wine']);
        }
    }
}

// 4. Industrial categories
foreach ($industrialCats as $cat) {
    $url = '/catalog/industrial/' . $cat['slug'] . '/';
    add($results, $q, $cat['n'], 'Промышленное оборудование', 'industrial', '', $url, $icons['industrial']);
    if (isset($cat['vols'])) {
        foreach ($cat['vols'] as $v) {
            add($results, $q, $cat['n'] . ' ' . fmt($v), 'Промышленное оборудование', 'industrial', $cat['n'], $url . $v . 'l/', $icons['industrial']);
        }
    }
}

// Limit results
$capped = array_slice($results, 0, 30);

echo json_encode(['results' => $capped], JSON_UNESCAPED_UNICODE);
