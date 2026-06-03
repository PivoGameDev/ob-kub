<?php
error_reporting(0);
ini_set('display_errors', 0);

$vol = isset($_GET['vol']) ? (int)$_GET['vol'] : 0;
if (!$vol) { header('Location: /catalog/beer/cct/'); exit; }

require __DIR__ . '/../../cct-data.php';
require __DIR__ . '/../../helpers/product-template.php';

$cat = $cctData['cct'];
$allData = $cat['specs'];
$d = $allData[$vol] ?? null;
if (!$d) { header('Location: /catalog/beer/cct/'); exit; }

// Build a data structure that renderProductPage() expects
$cctItem = [
    'name'       => 'ЦКТ',
    'name_short' => 'ЦКТ',
    'image'      => 'cct-tank.jpg',
    'desc'       => $d['desc'],
    'features'   => [
        'AISI 304 / AISI 316 (опция)',
        'Давление рабочее до 2.5 бар',
        'Угол конуса 60–70°',
        'CIP-мойка (ротационная головка)',
        'Рубашки охлаждения: ' . $d['jackets_desc'],
        'Теплоизоляция минвата 50–100 мм',
        'Люк-лаз DN400 / DN500',
        'Опоры регулируемые',
    ],
    'specs' => [],
    'volumes' => $cat['volumes'],
];

// Map CCT spec keys → template keys (from_price→price, height_full→height)
foreach ($allData as $v => $spec) {
    $cctItem['specs'][$v] = [
        'price'          => $spec['from_price'],
        'diameter'       => $spec['diameter'],
        'height'         => $spec['height_full'],
        'wall'           => $spec['wall'],
        'weight'         => $spec['weight'],
        'full_volume'    => $spec['full_volume'],
        'working_volume' => $spec['working_volume'],
        'power'          => 0,
    ];
}

$volStr = number_format($vol, 0, '.', ' ');
$canonical = "https://ob-kub.ru/catalog/beer/cct/{$vol}l/";

$opts = [
    'canonical'          => $canonical,
    'baseUrl'            => '/catalog/beer/cct/',
    'catPrefix'          => '/catalog/beer/',
    'categoryName'       => 'Пивоваренное оборудование',
    'categoryUrl'        => '/catalog/beer/',
    'formType'           => 'item',
    'specUnit'           => 'л',
    'specLabel'          => 'Объём',
    'breadcrumbMiddle'   => 'Пивоваренное оборудование',
    'breadcrumbMiddleUrl'=> '/catalog/beer/',
    'advItems'           => [
        ['icon' => '🏭', 'title' => 'Собственное производство', 'text' => 'Полный цикл в Краснодаре'],
        ['icon' => '⭐', 'title' => '50+ пивоварен', 'text' => 'Оборудовали по всей России'],
        ['icon' => '📏', 'title' => 'Любой объём', 'text' => 'От 100 до 200 000 литров'],
        ['icon' => '🔧', 'title' => 'Гарантия 3 года', 'text' => 'Сервис и поддержка'],
    ],
];

renderProductPage('cct', $vol, $cctItem, $opts);
