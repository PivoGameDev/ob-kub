<?php
error_reporting(0);
ini_set('display_errors', 0);
header_remove('X-Powered-By');
header('Content-Type: application/json; charset=utf-8');

// === НАСТРОЙКИ ===
$to = 'ok@cl121464.tw1.ru';
$to2 = 'oborudovanie-kubani@yandex.ru';

// === SMTP из конфига ===
$smtpHost = ''; $smtpPort = '465'; $smtpUser = ''; $smtpPass = '';
$cfgFile = __DIR__ . '/../admin/config.php';
if (file_exists($cfgFile)) {
    $cfg = include $cfgFile;
    if (is_array($cfg)) {
        $smtpHost = trim($cfg['smtp_host'] ?? '');
        $smtpPort = trim($cfg['smtp_port'] ?? '465');
        $smtpUser = trim($cfg['smtp_user'] ?? '');
        $smtpPass = $cfg['smtp_pass'] ?? '';
    }
}
$allowed_domains = ['ob-kub.ru', 'cl121464.tw1.ru'];
$max_file_size = 5 * 1024 * 1024; // 5 MB
$allowed_mime = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
$allowed_ext = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'txt'];
$rate_limit_file = __DIR__ . '/rate_limit_cache.json';
$rate_limit_window = 300; // 5 минут
$rate_limit_max = 10; // макс 10 отправок с одного IP за window
$debugLogFile = __DIR__ . '/mail_debug.txt';
@touch($debugLogFile);
@chmod($debugLogFile, 0644);

// === ПЕРЕВОДЫ ===
$form_names = [
    'quick' => 'Быстрая заявка',
    'detailed' => 'Подробный расчёт',
    'brewery' => 'Расчёт пивоварни',
    'dairy' => 'Расчёт молочного оборудования',
    'winery' => 'Расчёт винодельческого оборудования',
    'tour' => 'Запись на экскурсию',
    'main-draft' => 'Расчёт оборудования (главная)',
    'beer-draft' => 'Расчёт пивоваренного оборудования',
    'milk-draft' => 'Расчёт молочного оборудования',
    'wine-draft' => 'Расчёт винодельческого оборудования',
    'industrial-draft' => 'Расчёт промышленного оборудования',
    'item' => 'Расчёт товара из каталога',
    'beer-item' => 'Расчёт пивоваренного оборудования',
    'brew-house-product' => 'Расчёт пивоварни под ключ',
    'industrial-cip' => 'Расчёт CIP-станции',
    'catalog-request' => 'Запрос каталога с ценами',
    'industrial-heat-exchanger' => 'Расчёт теплообменника',
    'dairy-shelves' => 'Расчёт стеллажей для сыра',
    'dairy-item' => 'Расчёт молочного оборудования (карточка)',
    'wine-item' => 'Расчёт винодельческого оборудования (карточка)',
    'industrial-item' => 'Расчёт промышленного оборудования (карточка)',
];
$form_name = $form_names[$_POST['form_type'] ?? ''] ?? 'Неизвестная форма';

$tank_types = [
    'storage' => 'Резервуар для хранения',
    'mixing' => 'Ёмкость с мешалкой',
    'thermal' => 'Ёмкость с терморегуляцией',
    'pressure' => 'Ёмкость под давлением',
    'sip' => 'Ёмкость для CIP-станции',
    'heat-exchanger' => 'Теплообменник',
    'beer' => 'Пивоваренное оборудование',
    'kvas' => 'Квасное оборудование',
    'milk' => 'Молочное оборудование',
    'wine' => 'Винодельческое оборудование',
    'oil' => 'Масложировое оборудование',
    'chemical' => 'Химическое оборудование',
    'water' => 'Водоподготовка',
    'other' => 'Другое',
];

$option_names = [
    'mixer' => 'Мешалка',
    'pump' => 'Насос',
    'hatch' => 'Люк',
    'cip_head' => 'CIP-головка',
    'thermo_couple' => 'Термопара',
    'safety_valve' => 'Предохранительный клапан',
    'schunt' => 'Шунт',
    'insulation' => 'Термоизоляция',
];

$hc_types = [
    'one_jacket' => '1 рубашка',
    'two_jackets' => '2 раздельные рубашки',
    'three_jackets' => '3 раздельные рубашки',
];

$materials = [
    'aisi304' => 'AISI 304',
    'aisi316' => 'AISI 316',
    'aisi316l' => 'AISI 316L',
];

// === ФУНКЦИИ ===
function jsonExit($success, $message) {
    if (!$success) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
    header('Location: /thanks.html');
    exit;
}

function smtpMailSend($to, $subject, $body, $headers, $fromEmail, $host, $port, $user, $pass, $logFile = '') {
    $log = function($msg) use ($logFile) {
        if ($logFile) @file_put_contents($logFile, '[' . date('H:i:s') . "] $msg\n", FILE_APPEND);
    };
    if (!$host || !$user || !$pass) {
        $log("SMTP: no config, fallback to mail()");
        $r = @mail($to, $subject, $body, $headers);
        $log("mail() returned: " . ($r ? 'true' : 'false'));
        return $r;
    }
    $errNo = 0; $errStr = '';
    $ssl = (strpos($port, '465') !== false);
    $remote = $ssl ? "ssl://{$host}:{$port}" : "{$host}:{$port}";
    $log("SMTP: connecting to $remote");
    $s = @stream_socket_client($remote, $errNo, $errStr, 15);
    if (!$s) {
        $log("SMTP: connect failed ($errNo) $errStr — fallback to mail()");
        $r = @mail($to, $subject, $body, $headers);
        $log("mail() returned: " . ($r ? 'true' : 'false'));
        return $r;
    }
    $buf = fread($s, 512);
    $log("SMTP: banner: " . trim($buf));
    fwrite($s, "EHLO ob-kub.ru\r\n");
    $buf = '';
    while ($line = fgets($s, 512)) {
        $buf .= $line;
        if (substr($line, 3, 1) === ' ') break;
    }
    $log("SMTP: EHLO response: " . trim(str_replace("\r\n", ' | ', $buf)));
    if (strpos($port, '587') !== false && stripos($buf, 'STARTTLS') !== false) {
        fwrite($s, "STARTTLS\r\n");
        fgets($s, 512);
        stream_socket_enable_crypto($s, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        fwrite($s, "EHLO ob-kub.ru\r\n");
        while ($line = fgets($s, 512)) { if (substr($line, 3, 1) === ' ') break; }
        $log("SMTP: STARTTLS done");
    }
    fwrite($s, "AUTH LOGIN\r\n");
    fgets($s, 512);
    fwrite($s, base64_encode($user) . "\r\n");
    fgets($s, 512);
    fwrite($s, base64_encode($pass) . "\r\n");
    $r = fgets($s, 512);
    if (strpos($r, '235') === false) {
        $log("SMTP: AUTH failed: " . trim($r) . " — fallback to mail()");
        fclose($s);
        $r2 = @mail($to, $subject, $body, $headers);
        $log("mail() returned: " . ($r2 ? 'true' : 'false'));
        return $r2;
    }
    $log("SMTP: AUTH OK");
    // Use SMTP user as both MAIL FROM and From header (Yandex requires match)
    $envFrom = $fromEmail;
    $smtpHeaders = $headers;
    if (strpos($user, '@') !== false) {
        $envFrom = $user;
        $smtpHeaders = preg_replace('/^From:\s*.+$/im', "From: $user", $headers);
    }
    fwrite($s, "MAIL FROM:<{$envFrom}>\r\n");
    $mr = fgets($s, 512);
    $log("SMTP: MAIL FROM ($envFrom): " . trim($mr));
    fwrite($s, "RCPT TO:<{$to}>\r\n");
    $rr = fgets($s, 512);
    $log("SMTP: RCPT TO ($to): " . trim($rr));
    if (strpos($mr, '250') === false || strpos($rr, '250') === false) {
        $log("SMTP: MAIL FROM or RCPT TO rejected — fallback to mail()");
        fclose($s);
        $r2 = @mail($to, $subject, $body, $headers);
        $log("mail() returned: " . ($r2 ? 'true' : 'false'));
        return $r2;
    }
    fwrite($s, "DATA\r\n");
    $dr = fgets($s, 512);
    $log("SMTP: DATA: " . trim($dr));
    fwrite($s, "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n");
    fwrite($s, $smtpHeaders);
    fwrite($s, "\r\n");
    fwrite($s, $body);
    fwrite($s, "\r\n.\r\n");
    $datR = fgets($s, 512);
    $log("SMTP: DATA result: " . trim($datR));
    fwrite($s, "QUIT\r\n");
    fclose($s);
    $ok = strpos($datR, '250') !== false;
    $log("SMTP: done, success=" . ($ok ? 'true' : 'false'));
    return $ok;
}

function sanitize($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}

function getClientIP() {
    $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR', 'HTTP_CLIENT_IP'];
    foreach ($headers as $h) {
        if (!empty($_SERVER[$h])) {
            $ips = explode(',', $_SERVER[$h]);
            return trim($ips[0]);
        }
    }
    return 'unknown';
}

// === 1. REFERER CHECK (CSRF) ===
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$referer_allowed = false;
foreach ($allowed_domains as $domain) {
    if (stripos($referer, $domain) !== false) { $referer_allowed = true; break; }
    if (stripos($origin, $domain) !== false) { $referer_allowed = true; break; }
}
if (!$referer_allowed) {
    jsonExit(false, 'Ошибка безопасности: неверный источник запроса');
}

// === 2. RATE LIMITING ===
$client_ip = getClientIP();
$ip_hash = 'ip_' . md5($client_ip);
$rate_data = [];
if (file_exists($rate_limit_file)) {
    $rate_data = json_decode(file_get_contents($rate_limit_file), true) ?? [];
}
$now = time();
$rate_data[$ip_hash] = array_values(array_filter($rate_data[$ip_hash] ?? [], function($t) use ($now, $rate_limit_window) {
    return $t > ($now - $rate_limit_window);
}));
if (count($rate_data[$ip_hash]) >= $rate_limit_max) {
    jsonExit(false, 'Слишком много запросов. Попробуйте через 5 минут.');
}
$rate_data[$ip_hash][] = $now;
file_put_contents($rate_limit_file, json_encode($rate_data), LOCK_EX);

// === 3. HONEYPOT CHECK (ANTI-BOT) ===
if (!empty($_POST['_website'])) {
    jsonExit(false, 'Обнаружен бот');
}

// === 4. CSRF TIMESTAMP CHECK ===
$csrf = $_POST['_csrf'] ?? $_POST['csrf'] ?? '';
$csrf_time = base64_decode($csrf, true);
if (!$csrf || $csrf_time === false || abs($now - (int)$csrf_time) > 1800) {
    jsonExit(false, 'Ошибка безопасности: повторите отправку');
}

// === 5. СБОР ДАННЫХ ===
$form_type = sanitize($_POST['form_type'] ?? 'unknown');
$name = sanitize($_POST['name'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$email_raw = trim($_POST['email'] ?? '');
$email = validateEmail($email_raw) ? $email_raw : '';
$user_message_raw = $_POST['message'] ?? $_POST['comment'] ?? $_POST['user_message'] ?? '';
$user_message = sanitize($user_message_raw);

if (empty($name) || empty($phone)) {
    jsonExit(false, 'Заполните имя и телефон');
}

// === 6. EMAIL HEADER INJECTION GUARD ===
$safe_email = $email ?: 'no-reply@ob-kub.ru';
$safe_email = str_replace(["\r", "\n"], '', $safe_email);

// === 7. СБОРКА ПИСЬМА ===
$subject = "=$form_name= $name";

$body = str_repeat('=', 60) . "\n";
$body .= "  НОВАЯ ЗАЯВКА С САЙТА\n";
$body .= str_repeat('=', 60) . "\n\n";

// --- КОНТАКТНЫЕ ДАННЫЕ ---
$body .= "--- КОНТАКТНЫЕ ДАННЫЕ ---\n";
$body .= "Форма: $form_name\n";
$body .= "Имя: $name\n";
$body .= "Телефон: $phone\n";
if ($email) $body .= "E-mail: $email\n";
$body .= "\n";

// --- ДАННЫЕ КОНФИГУРАТОРА ---
$quickLikeTypes = ['quick', 'item', 'beer-item', 'brew-house-product', 'industrial-cip', 'industrial-heat-exchanger', 'dairy-shelves', 'dairy-item', 'wine-item', 'industrial-item'];
if (in_array($form_type, $quickLikeTypes) && $user_message) {
    $body .= "--- СООБЩЕНИЕ ---\n";
    if (!empty($_POST['product'])) $body .= "Товар: " . sanitize($_POST['product']) . "\n";
    if (!empty($_POST['quantity'])) $body .= "Количество: " . sanitize($_POST['quantity']) . "\n";
    $body .= "$user_message\n\n";
}

if ($form_type === 'detailed') {
    $tankIndex = 1;
    while (isset($_POST["tank_type_$tankIndex"])) {
        $typeKey = sanitize($_POST["tank_type_$tankIndex"]);
        $typeName = $tank_types[$typeKey] ?? $typeKey;
        $volume = sanitize($_POST["volume_$tankIndex"] ?? '');

        $body .= "--- Ёмкость $tankIndex ---\n";
        $body .= "  Тип: $typeName\n";
        $body .= "  Объём: $volume л\n";

        if (!empty($_POST["heating_$tankIndex"])) {
            $htKey = $_POST["heating_type_$tankIndex"] ?? '';
            $htName = $hc_types[$htKey] ?? $htKey;
            $body .= "  Нагрев: да ($htName)\n";
        }
        if (!empty($_POST["cooling_$tankIndex"])) {
            $ctKey = $_POST["cooling_type_$tankIndex"] ?? '';
            $ctName = $hc_types[$ctKey] ?? $ctKey;
            $body .= "  Охлаждение: да ($ctName)\n";
        }

        foreach ($option_names as $key => $label) {
            if (!empty($_POST["{$key}_$tankIndex"])) {
                $body .= "  ✓ $label\n";
            }
        }

        if (!empty($_POST["comment_$tankIndex"])) {
            $body .= "  Комментарий: " . sanitize($_POST["comment_$tankIndex"]) . "\n";
        }
        $body .= "\n";
        $tankIndex++;
    }
    if ($user_message) {
        $body .= "--- КОММЕНТАРИЙ ---\n";
        $body .= "$user_message\n\n";
    }
}

if ($form_type === 'dairy') {
    if (isset($_POST['dairy_equipment_type']) && is_array($_POST['dairy_equipment_type'])) {
        $idx = 1;
        foreach ($_POST['dairy_equipment_type'] as $i => $typeVal) {
            if (empty($typeVal)) continue;
            $body .= "--- Оборудование $idx ---\n";
            $body .= "  Тип: " . sanitize($typeVal) . "\n";
            $nameVal = $_POST['dairy_equipment_type_name'][$i] ?? '';
            if ($nameVal) $body .= "  Название: " . sanitize($nameVal) . "\n";
            $vol = $_POST['dairy_volume'][$i] ?? '';
            if ($vol) $body .= "  Объём/Кол-во: " . sanitize($vol) . "\n";
            $matKey = $_POST['dairy_material'][$i] ?? '';
            $matName = $materials[$matKey] ?? $matKey;
            if ($matKey) $body .= "  Материал: $matName\n";
            if (!empty($_POST['dairy_heating'][$i])) $body .= "  ✓ Система нагрева\n";
            if (!empty($_POST['dairy_cooling'][$i])) $body .= "  ✓ Система охлаждения\n";
            if (!empty($_POST['dairy_mixing'][$i])) $body .= "  ✓ Автоматическая мешалка\n";
            if (!empty($_POST['dairy_cip'][$i])) $body .= "  ✓ CIP-мойка\n";
            if (!empty($_POST['dairy_insulation'][$i])) $body .= "  ✓ Термоизоляция\n";
            $comm = $_POST['dairy_comment'][$i] ?? '';
            if ($comm) $body .= "  Комментарий: " . sanitize($comm) . "\n";
            $body .= "\n";
            $idx++;
        }
    }
    if ($user_message) {
        $body .= "--- КОММЕНТАРИЙ ---\n$user_message\n\n";
    }
}

if ($form_type === 'winery') {
    if (isset($_POST['winery_equipment_type']) && is_array($_POST['winery_equipment_type'])) {
        $idx = 1;
        foreach ($_POST['winery_equipment_type'] as $i => $typeVal) {
            if (empty($typeVal)) continue;
            $body .= "--- Ёмкость $idx ---\n";
            $body .= "  Тип: " . sanitize($typeVal) . "\n";
            $nameVal = $_POST['winery_equipment_type_name'][$i] ?? '';
            if ($nameVal) $body .= "  Название: " . sanitize($nameVal) . "\n";
            $vol = $_POST['winery_volume'][$i] ?? '';
            if ($vol) $body .= "  Объём: " . sanitize($vol) . " л\n";
            $matKey = $_POST['winery_material'][$i] ?? '';
            $matName = $materials[$matKey] ?? $matKey;
            if ($matKey) $body .= "  Материал: $matName\n";
            if (!empty($_POST['winery_heating'][$i])) $body .= "  ✓ Система нагрева\n";
            if (!empty($_POST['winery_cooling'][$i])) $body .= "  ✓ Система охлаждения\n";
            if (!empty($_POST['winery_mixing'][$i])) $body .= "  ✓ Автоматическая мешалка\n";
            if (!empty($_POST['winery_cip'][$i])) $body .= "  ✓ CIP-мойка\n";
            if (!empty($_POST['winery_insulation'][$i])) $body .= "  ✓ Термоизоляция\n";
            if (!empty($_POST['winery_inertization'][$i])) $body .= "  ✓ Система инертизации\n";
            $comm = $_POST['winery_comment'][$i] ?? '';
            if ($comm) $body .= "  Комментарий: " . sanitize($comm) . "\n";
            $body .= "\n";
            $idx++;
        }
    }
    if ($user_message) {
        $body .= "--- КОММЕНТАРИЙ ---\n$user_message\n\n";
    }
}

if ($form_type === 'brewery') {
    $lotLabels = [
        'lot1' => '250 л/варка',
        'lot2' => '500 л/варка',
        'lot3' => '1000 л/варка',
        'lot4' => '1000+ л/варка',
        'lot5' => '3000 л/варка',
        'lot6' => '3000+ л/варка',
        'lot7' => '5000 л/варка',
    ];
    $lot = sanitize($_POST['brewery_lot'] ?? '');
    $lotLabel = $lotLabels[$lot] ?? $lot;
    $body .= "--- КОМПЛЕКТАЦИЯ ---\n";
    $body .= "  Объём: $lotLabel\n";
    $body .= "\n--- ВЫБРАННОЕ ОБОРУДОВАНИЕ ---\n";
    $eqCount = 0;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'equipment_') === 0) {
            $body .= "  ✓ " . sanitize($value) . "\n";
            $eqCount++;
        }
    }
    if ($eqCount === 0) $body .= "  (не выбрано)\n";
    if ($user_message) {
        $body .= "\n--- КОММЕНТАРИЙ ---\n$user_message\n";
    }
}

if ($form_type === 'tour') {
    if ($user_message) {
        $body .= "--- ПOЖЕЛАНИЯ ---\n$user_message\n\n";
    }
}

// --- DRAFT ФОРМЫ (главная, пиво, молоко, вино, промышленное) ---
if (in_array($form_type, ['main-draft', 'beer-draft', 'milk-draft', 'wine-draft', 'industrial-draft'])) {
    $body .= "--- ПАРАМЕТРЫ ---\n";
    if (!empty($_POST['industry'])) $body .= "Отрасль: " . sanitize($_POST['industry']) . "\n";
    if (!empty($_POST['equipment_type'])) {
        $typeVal = $_POST['equipment_type'];
        $typeLabels = [
            'mash_tun' => 'Заторный аппарат',
            'combined_kettle' => 'Заторно-сусловарочный аппарат',
            'lauter_tun' => 'Фильтрационный аппарат (Фильтрчан)',
            'brew_kettle' => 'Сусловарочный аппарат',
            'whirlpool' => 'Гидроциклонный аппарат',
            'hot_water' => 'Бак горячей воды',
            'wort_receiver' => 'Суслосборник',
            'ckt' => 'ЦКТ (Цилиндро-конический танк)',
            'forfas' => 'Форфасы',
            'mill' => 'Дробилка солода',
            'steam' => 'Парогенератор',
            'chiller' => 'Чиллер',
            'heatex' => 'Теплообменник',
        ];
        $typeName = $typeLabels[$typeVal] ?? $typeVal;
        $body .= "Тип оборудования: $typeName\n";
    }
    if (!empty($_POST['brew_config'])) {
        $cfgLabels = [
            'brew250' => 'Пилотная 250 л',
            'brew500' => 'Профессиональная 500 л',
            'brew1000' => 'Базовая 1 000 л',
            'brew1500' => 'Средняя 1 500 л',
            'brew3000' => 'Промышленная 3 000 л',
            'brew5000' => 'Заводская 5 000 л',
            'brew10000' => 'Промышленная 10 000 л',
        ];
        $cfgName = $cfgLabels[$_POST['brew_config']] ?? $_POST['brew_config'];
        $body .= "Комплектация: $cfgName\n";
    }
    if (!empty($_POST['volume'])) $body .= "Объём: " . sanitize($_POST['volume']) . " л\n";
    if ($user_message) $body .= "Комментарий: $user_message\n";
    $body .= "\n";
}

if (!empty($_POST['agreement'])) {
    $body .= "Согласие на обработку: да\n\n";
}

// --- ИСТОЧНИК ЛИДА ---
$pagePath = parse_url($referer, PHP_URL_PATH) ?? '';
if ($pagePath === '' || $pagePath === '/') {
    $pageTitle = 'Главная';
} else {
    $pageName = basename($pagePath, '.html');
    $pageTitles = [
        'index' => 'Главная',
        'beer' => 'Пивоварни',
        'dairy' => 'Молочное оборудование',
        'winery' => 'Винодельни',
        'articles' => 'Статьи',
    ];
    $pageTitle = $pageTitles[$pageName] ?? $pageName;
}
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

$body .= str_repeat('-', 60) . "\n";
$body .= "--- ИСТОЧНИК ЛИДА ---\n";
    $body .= "Страница: $pageTitle\n";
    $body .= "URL: $referer\n";
    $body .= "IP: $client_ip\n";
    if (!empty($_POST['product'])) $body .= "Товар: " . sanitize($_POST['product']) . "\n";
    if (!empty($_POST['quantity'])) $body .= "Количество: " . sanitize($_POST['quantity']) . "\n";
    $body .= "Дата: " . date('Y-m-d H:i:s') . "\n";
$body .= "Браузер: $userAgent\n";
$body .= str_repeat('=', 60) . "\n";

// === 8. ФАЙЛЫ ===
$attachments = [];
$file_fields = ['attachment', 'brewery-file'];
foreach ($file_fields as $field) {
    if (empty($_FILES[$field]['tmp_name']) || !is_uploaded_file($_FILES[$field]['tmp_name'])) continue;

    $file = $_FILES[$field];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['size'] > $max_file_size) continue;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_mime) && !in_array($ext, $allowed_ext)) continue;

    $safe_name = preg_replace('/[^a-zA-Zа-яА-Я0-9._-]/u', '_', $file['name']);
    $safe_name = basename($safe_name);

    $attachments[] = [
        'data' => file_get_contents($file['tmp_name']),
        'name' => $safe_name,
        'type' => $mime
    ];
}

// === 9. ОТПРАВКА ===
$headers = "From: no-reply@ob-kub.ru\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
$headers .= "Reply-To: $safe_email\r\n";

$success = false;
if (!empty($attachments)) {
    $boundary = md5(uniqid(time(), true));
    $headers = "From: no-reply@ob-kub.ru\r\n";
    $headers .= "Reply-To: $safe_email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $multipart = "--$boundary\r\n";
    $multipart .= "Content-Type: text/plain; charset=utf-8\r\n\r\n";
    $multipart .= "$body\r\n\r\n";

    foreach ($attachments as $file) {
        $multipart .= "--$boundary\r\n";
        $multipart .= "Content-Type: {$file['type']}; name=\"{$file['name']}\"\r\n";
        $multipart .= "Content-Disposition: attachment; filename=\"{$file['name']}\"\r\n";
        $multipart .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $multipart .= chunk_split(base64_encode($file['data'])) . "\r\n";
    }
    $multipart .= "--$boundary--";

    $success = smtpMailSend($to, $subject, $multipart, $headers, 'no-reply@ob-kub.ru', $smtpHost, $smtpPort, $smtpUser, $smtpPass, $debugLogFile);
    smtpMailSend($to2, $subject, $multipart, $headers, 'no-reply@ob-kub.ru', $smtpHost, $smtpPort, $smtpUser, $smtpPass, $debugLogFile);
} else {
    $success = smtpMailSend($to, $subject, $body, $headers, 'no-reply@ob-kub.ru', $smtpHost, $smtpPort, $smtpUser, $smtpPass, $debugLogFile);
    smtpMailSend($to2, $subject, $body, $headers, 'no-reply@ob-kub.ru', $smtpHost, $smtpPort, $smtpUser, $smtpPass, $debugLogFile);
}

if ($success) {
    // === 10. СОХРАНЕНИЕ ЛИДА ===
    $tdDir = __DIR__ . '/../tracking_data';
    if (!is_dir($tdDir)) @mkdir($tdDir, 0755, true);

    // Get or generate session ID
    $leadSid = trim($_POST['_sid'] ?? '');
    if (!$leadSid) $leadSid = 's_' . dechex($now) . '_' . substr(md5(uniqid()), 0, 9);

    $pagePath = parse_url($referer, PHP_URL_PATH) ?? '';
    $leadDate = date('Y-m-d');
    $leadFile = "$tdDir/f_$leadDate.json";

    // Load existing leads for today
    $leadData = [];
    if (file_exists($leadFile)) {
        $raw = file_get_contents($leadFile);
        $leadData = json_decode($raw, true) ?: [];
    }

    // Build field entries (same format as track.php action=field)
    $leadFields = [
        ['sid' => $leadSid, 'page' => $pagePath, 'form' => $form_type, 'field' => 'name', 'val' => $name, 't' => $now],
        ['sid' => $leadSid, 'page' => $pagePath, 'form' => $form_type, 'field' => 'phone', 'val' => $phone, 't' => $now],
    ];
    if ($email) $leadFields[] = ['sid' => $leadSid, 'page' => $pagePath, 'form' => $form_type, 'field' => 'email', 'val' => $email, 't' => $now];
    if ($user_message) $leadFields[] = ['sid' => $leadSid, 'page' => $pagePath, 'form' => $form_type, 'field' => 'comment', 'val' => $user_message, 't' => $now];
    if (!empty($_POST['product'])) $leadFields[] = ['sid' => $leadSid, 'page' => $pagePath, 'form' => $form_type, 'field' => 'product', 'val' => sanitize($_POST['product']), 't' => $now];
    if (!empty($_POST['quantity'])) $leadFields[] = ['sid' => $leadSid, 'page' => $pagePath, 'form' => $form_type, 'field' => 'quantity', 'val' => sanitize($_POST['quantity']), 't' => $now];

    $leadData = array_merge($leadData, $leadFields);
    $leadData = array_slice($leadData, -20000);

    $written = file_put_contents($leadFile, json_encode($leadData, JSON_UNESCAPED_UNICODE), LOCK_EX);
    if ($written === false) {
        @file_put_contents($debugLogFile, '[' . date('H:i:s') . "] LEAD SAVE FAILED to $leadFile\n", FILE_APPEND);
    } else {
        @file_put_contents($debugLogFile, '[' . date('H:i:s') . "] LEAD SAVED: sid=$leadSid name=$name phone=$phone file=$leadFile\n", FILE_APPEND);
    }

    // Also save a compact entry to php/leads_archive.json in case f_*.json has issues
    $archiveFile = __DIR__ . '/leads_archive.json';
    $archive = [];
    if (file_exists($archiveFile)) $archive = json_decode(file_get_contents($archiveFile), true) ?: [];
    $archive[] = ['sid' => $leadSid, 'name' => $name, 'phone' => $phone, 'email' => $email, 'form' => $form_type, 'page' => $pagePath, 'product' => $_POST['product'] ?? '', 'msg' => $user_message, 't' => $now];
    $archive = array_slice($archive, -5000);
    file_put_contents($archiveFile, json_encode($archive, JSON_UNESCAPED_UNICODE), LOCK_EX);

    // Telegram-уведомление
    $tgConfig = __DIR__ . '/../admin/config.php';
    if (file_exists($tgConfig)) {
        $tgSettings = include $tgConfig;
        if (is_array($tgSettings) && !empty($tgSettings['bot_token']) && !empty($tgSettings['chat_id'])) {
            $tgMsg = "<b>📩 Новая заявка</b>\n";
            $tgMsg .= "<b>Форма:</b> $form_name\n";
            $tgMsg .= "<b>Имя:</b> $name\n";
            $tgMsg .= "<b>Телефон:</b> $phone\n";
            if ($email) $tgMsg .= "<b>E-mail:</b> $email\n";
            $pageTitle = '';
            $pagePath = parse_url($referer, PHP_URL_PATH) ?? '';
            if ($pagePath === '' || $pagePath === '/') $pageTitle = 'Главная';
            else {
                $pageName = basename($pagePath, '.html');
                $pageTitles = ['index' => 'Главная', 'beer' => 'Пивоварни', 'dairy' => 'Молочное', 'winery' => 'Винодельни'];
                $pageTitle = $pageTitles[$pageName] ?? $pageName;
            }
            $tgMsg .= "<b>Страница:</b> $pageTitle\n";
            $tgMsg .= "<b>Время:</b> " . date('Y-m-d H:i:s');
            $ch = curl_init('https://api.telegram.org/bot' . $tgSettings['bot_token'] . '/sendMessage');
            curl_setopt_array($ch, [CURLOPT_POST => 1, CURLOPT_POSTFIELDS => ['chat_id' => $tgSettings['chat_id'], 'text' => $tgMsg, 'parse_mode' => 'HTML'], CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => 5]);
            curl_exec($ch);
            curl_close($ch);
        }
    }
    jsonExit(true, 'Спасибо! Ваша заявка отправлена.');
} else {
    jsonExit(false, 'Ошибка при отправке. Попробуйте позже.');
}
