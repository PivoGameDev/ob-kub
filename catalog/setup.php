<?php
// Setup script: создаёт папки для каждого объёма ЦКТ
// Залей в корень сайта, открой в браузере 1 раз, потом удали

require __DIR__ . '/cct-data.php';

$base = __DIR__ . '/beer/cct';
$count = 0;

foreach ($cctData['cct']['specs'] as $vol => $data) {
    $dir = "$base/{$vol}l";
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $file = "$dir/index.php";
    if (!file_exists($file)) {
        $code = '<?php $_SERVER["CATALOG_VOLUME"]=' . $vol . '; require __DIR__ . "/../index.php";';
        file_put_contents($file, $code);
        $count++;
    }
}

echo "✅ Создано $count страниц ЦКТ из " . count($cctData['cct']['specs']) . " объёмов.<br>";
echo "Проверь:<br>";
foreach (array_slice(array_keys($cctData['cct']['specs']), 0, 5) as $v) {
    echo "<a href='/catalog/beer/cct/{$v}l/' target='_blank'>/catalog/beer/cct/{$v}l/</a><br>";
}
