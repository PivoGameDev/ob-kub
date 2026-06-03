<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();

$configFile = __DIR__ . '/config.php';
$dataDir = __DIR__ . '/../tracking_data';
$leadsFile = "$dataDir/leads.json";

// === ЗАГРУЗКА КОНФИГА (с обратной совместимостью) ===
function loadConfig($file) {
    if (!file_exists($file)) return null;
    $raw = include $file;
    if (is_string($raw)) return ['hash' => $raw, 'bot_token' => '', 'chat_id' => '', 'from_email' => '', 'imap_host' => '', 'imap_port' => '993', 'imap_user' => '', 'imap_pass' => '', 'smtp_host' => '', 'smtp_port' => '465', 'smtp_user' => '', 'smtp_pass' => ''];
    return $raw + ['hash' => '', 'bot_token' => '', 'chat_id' => '', 'from_email' => '', 'imap_host' => '', 'imap_port' => '993', 'imap_user' => '', 'imap_pass' => '', 'smtp_host' => '', 'smtp_port' => '465', 'smtp_user' => '', 'smtp_pass' => ''];
}

function saveConfig($file, $data) {
    $code = '<?php return ' . var_export($data, true) . ';';
    file_put_contents($file, $code, LOCK_EX);
}

$config = loadConfig($configFile);

// === УСТАНОВКА ===
if ($config === null) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_pass'])) {
        $pass = $_POST['setup_pass'];
        if (strlen($pass) < 4) { $error = 'Минимум 4 символа'; }
        else {
            saveConfig($configFile, ['hash' => password_hash($pass, PASSWORD_DEFAULT), 'bot_token' => '', 'chat_id' => '', 'from_email' => '', 'imap_host' => '', 'imap_port' => '993', 'imap_user' => '', 'imap_pass' => '', 'smtp_host' => '', 'smtp_port' => '465', 'smtp_user' => '', 'smtp_pass' => '']);
            $_SESSION['admin'] = true;
            header('Location: ?');
            exit;
        }
    }
    ?><!DOCTYPE html><meta charset="utf-8"><title>Установка пароля</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>body{font-family:system-ui,sans-serif;background:#f0f2f5;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;color:#333}
    .box{background:#fff;padding:40px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);width:340px}
    h1{font-size:20px;margin:0 0 20px;color:#F77C2A;text-align:center}
    label{display:block;margin-bottom:6px;font-size:13px;color:#666}
    input{width:100%;padding:10px 12px;border:1px solid #d0d0d0;border-radius:6px;background:#fff;color:#333;font-size:14px;box-sizing:border-box}
    .btn{width:100%;padding:10px;background:#F77C2A;color:#fff;border:none;border-radius:6px;font-size:15px;cursor:pointer;margin-top:16px}
    .btn:hover{background:#e06a1a}
    .err{color:#e74c3c;font-size:13px;margin-top:8px;text-align:center}</style>
    <div class="box"><h1>🔐 Установка пароля</h1>
    <form method="post">
    <label>Придумайте пароль для входа в админ-панель</label>
    <input type="password" name="setup_pass" required minlength="4" autofocus>
    <?php if (isset($error)) echo '<div class="err">'.$error.'</div>'; ?>
    <button class="btn">Установить и войти</button>
    </form></div></html>
    <?php exit;
}

// === ВХОД ===
$loggedIn = !empty($_SESSION['admin']);
if (!$loggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_pass'])) {
    if (password_verify($_POST['login_pass'], $config['hash'])) {
        $_SESSION['admin'] = true;
        $loggedIn = true;
    } else {
        $loginError = 'Неверный пароль';
    }
}
if (!$loggedIn) {
    ?><!DOCTYPE html><meta charset="utf-8"><title>Вход в админ-панель</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>body{font-family:system-ui,sans-serif;background:#f0f2f5;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;color:#333}
    .box{background:#fff;padding:40px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);width:340px}
    h1{font-size:20px;margin:0 0 20px;color:#F77C2A;text-align:center}
    label{display:block;margin-bottom:6px;font-size:13px;color:#666}
    input{width:100%;padding:10px 12px;border:1px solid #d0d0d0;border-radius:6px;background:#fff;color:#333;font-size:14px;box-sizing:border-box}
    .btn{width:100%;padding:10px;background:#F77C2A;color:#fff;border:none;border-radius:6px;font-size:15px;cursor:pointer;margin-top:16px}
    .btn:hover{background:#e06a1a}
    .err{color:#e74c3c;font-size:13px;margin-top:8px;text-align:center}
    .hint{font-size:12px;color:#888;text-align:center;margin-top:12px}</style>
    <div class="box"><h1>🔐 Админ-панель</h1>
    <form method="post">
    <label>Пароль</label>
    <input type="password" name="login_pass" required autofocus>
    <?php if (isset($loginError)) echo '<div class="err">'.$loginError.'</div>'; ?>
    <button class="btn">Войти</button>
    </form></div></html>
    <?php exit;
}

// === СОХРАНЕНИЕ НАСТРОЕК (Telegram) ===
if (isset($_POST['save_settings'])) {
    $config['bot_token'] = trim($_POST['bot_token'] ?? '');
    $config['chat_id'] = trim($_POST['chat_id'] ?? '');
    $config['from_email'] = trim($_POST['from_email'] ?? '');
    $config['imap_host'] = trim($_POST['mhost'] ?? $_POST['mail_host'] ?? $_POST['imap_host'] ?? '');
    $config['imap_port'] = trim($_POST['mport'] ?? $_POST['mail_port'] ?? $_POST['imap_port'] ?? '993');
    $config['imap_user'] = trim($_POST['muser'] ?? $_POST['mail_user'] ?? $_POST['imap_user'] ?? '');
    $config['imap_pass'] = $_POST['mpass'] ?? $_POST['mail_pass'] ?? $_POST['imap_pass'] ?? '';
    $config['smtp_host'] = trim($_POST['smtp_host'] ?? $config['smtp_host'] ?? '');
    $config['smtp_port'] = trim($_POST['smtp_port'] ?? $config['smtp_port'] ?? '465');
    $smtpUserPost = trim($_POST['smtp_user'] ?? '');
    $smtpPassPost = $_POST['smtp_pass'] ?? '';
    $config['smtp_user'] = $smtpUserPost ?: ($config['smtp_user'] ?: $config['imap_user']);
    $config['smtp_pass'] = $smtpPassPost ?: ($config['smtp_pass'] ?: $config['imap_pass']);
    if (isset($_POST['new_pass']) && strlen($_POST['new_pass']) >= 4) {
        $config['hash'] = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
    }
    saveConfig($configFile, $config);
    $saved = true;
    // Отладка: показываем что пришло из формы
    $debugSaved = [
        '_post_mhost' => "'" . ($_POST['mhost'] ?? 'NULL') . "'",
        '_post_mport' => "'" . ($_POST['mport'] ?? 'NULL') . "'",
        '_post_muser' => "'" . ($_POST['muser'] ?? 'NULL') . "'",
        '_post_from_email' => "'" . ($_POST['from_email'] ?? 'NULL') . "'",
        '_post_save_settings' => isset($_POST['save_settings']) ? 'yes' : 'NO',
        'saved_imap_host' => "'" . $config['imap_host'] . "'",
        'smtp_host' => "'" . ($_POST['smtp_host'] ?? 'NULL') . "'",
        'smtp_user' => "'" . ($_POST['smtp_user'] ?? 'NULL') . "'",
        'smtp_pass_set' => isset($_POST['smtp_pass']) && $_POST['smtp_pass'] !== '' ? 'YES (saved from IMAP fallback)' : 'EMPTY (will use IMAP)',
    ];
}

// === ОБРАБОТКА ДЕЙСТВИЙ С ЛИДАМИ ===
if (isset($_POST['lead_action'])) {
    $leads = [];
    if (file_exists($leadsFile)) $leads = json_decode(file_get_contents($leadsFile), true) ?: [];
    $sid = $_POST['sid'] ?? '';
    if ($_POST['lead_action'] === 'status' && $sid) {
        $leads[$sid]['status'] = $_POST['status'] ?? 'new';
    }
    if ($_POST['lead_action'] === 'note' && $sid && trim($_POST['note_text'] ?? '')) {
        if (!isset($leads[$sid]['notes'])) $leads[$sid]['notes'] = [];
        $leads[$sid]['notes'][] = ['text' => mb_substr(trim($_POST['note_text']), 0, 2000), 't' => time()];
    }
    file_put_contents($leadsFile, json_encode($leads, JSON_UNESCAPED_UNICODE), LOCK_EX);
    header('Location: ?date=' . urlencode($_GET['date'] ?? ''));
    exit;
}

// === ПАРАМЕТРЫ ===
$selectedDate = $_GET['date'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) $selectedDate = date('Y-m-d');
$isToday = ($selectedDate === date('Y-m-d'));

// === СБОР СТАТИСТИКИ ===
function loadJson($path) { return file_exists($path) ? (json_decode(file_get_contents($path), true) ?: []) : []; }

$online = loadJson("$dataDir/online.json");
$onlineCount = count($online);
$visitCount = count(loadJson("$dataDir/v_$selectedDate.json"));
$uvCount = count(loadJson("$dataDir/uv_$selectedDate.json"));
$todayFields = loadJson("$dataDir/f_$selectedDate.json");
$fieldCount = count($todayFields);
$todayClicks = loadJson("$dataDir/c_$selectedDate.json");
$clickCount = count($todayClicks);

$submits = loadJson("$dataDir/s_$selectedDate.json");
$submitCount = count($submits);

$leadsData = [];
if (file_exists($leadsFile)) $leadsData = json_decode(file_get_contents($leadsFile), true) ?: [];

// === СТРАНИЦЫ ===
$pages = [];
foreach (loadJson("$dataDir/v_$selectedDate.json") as $v) { $p = $v['page'] ?: '/'; $pages[$p] = ($pages[$p] ?? 0) + 1; }
arsort($pages);

// === ИСТОЧНИКИ ===
$refs = [];
foreach (loadJson("$dataDir/v_$selectedDate.json") as $v) {
    if ($v['ref']) { $h = parse_url($v['ref'], PHP_URL_HOST) ?: $v['ref']; $refs[$h] = ($refs[$h] ?? 0) + 1; }
}
arsort($refs);

// === НЕДЕЛЬНАЯ СТАТИСТИКА ===
$weekLabels = []; $weekVisits = []; $weekUV = []; $weekSubmits = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $weekLabels[] = date('d.m', strtotime("-$i days"));
    $weekVisits[] = count(loadJson("$dataDir/v_$d.json"));
    $weekUV[] = count(loadJson("$dataDir/uv_$d.json"));
    $weekSubmits[] = count(loadJson("$dataDir/s_$d.json"));
}

// === ПО ЧАСАМ ===
$hourly = array_fill(0, 24, 0);
foreach (loadJson("$dataDir/v_$selectedDate.json") as $v) $hourly[(int)date('G', $v['t'])]++;

// === СТАТУСЫ ЛИДОВ ===
$statusLabels = ['new' => 'Новый', 'processing' => 'В обработке', 'contacted' => 'Созвон', 'offer' => 'КП отправлено', 'negotiation' => 'Торг', 'deal' => 'Сделка', 'lost' => 'Отказ'];
$statusColors = ['new' => '#3498db', 'processing' => '#f39c12', 'contacted' => '#F77C2A', 'offer' => '#9b59b6', 'negotiation' => '#e67e22', 'deal' => '#27ae60', 'lost' => '#e74c3c'];

// === ГРУППИРОВКА ЛИДОВ (из ВСЕХ дат) ===
$allLeadFields = [];
$fieldFiles = glob("$dataDir/f_*.json");
if ($fieldFiles) {
    foreach ($fieldFiles as $ff) {
        $d = loadJson($ff);
        if ($d) foreach ($d as $item) $allLeadFields[] = $item;
    }
}
$sessions = [];
foreach ($allLeadFields as $f) {
    $s = $f['sid'];
    if (!isset($sessions[$s])) $sessions[$s] = ['fields' => [], 'page' => $f['page'], 'last' => $f['t'], 'first' => $f['t']];
    $sessions[$s]['fields'][] = $f;
    $sessions[$s]['last'] = max($sessions[$s]['last'], $f['t']);
    $sessions[$s]['first'] = min($sessions[$s]['first'], $f['t']);
}
uasort($sessions, function($a, $b) { return $b['last'] - $a['last']; });

$leads = [];
foreach ($sessions as $sid => $s) {
    $name = ''; $phone = ''; $email = ''; $allVals = [];
    foreach ($s['fields'] as $f) {
        $v = trim($f['val']); if (!$v) continue;
        $fn = strtolower($f['field']);
        if (strpos($fn, 'name') !== false && !$name) $name = $v;
        elseif (strpos($fn, 'phone') !== false && !$phone) $phone = $v;
        elseif (strpos($fn, 'email') !== false && !$email) $email = $v;
        $allVals[] = $f;
    }
    if ($name || $phone) {
        $lk = $name . '|' . $phone;
        $ld = $leadsData[$sid] ?? ['status' => 'new', 'notes' => []];
        $leads[$lk] = [
            'sid' => $sid,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'page' => $s['page'],
            'last' => $s['last'],
            'first' => $s['first'],
            'vals' => $allVals,
            'status' => $ld['status'] ?? 'new',
            'notes' => $ld['notes'] ?? [],
            'sent_emails' => $ld['sent_emails'] ?? [],
            'incoming_emails' => $ld['incoming_emails'] ?? [],
        ];
    }
}

// === ПОПУЛЯРНОСТЬ В КОНФИГУРАТОРЕ ===
$eqStats = [];
foreach ($todayFields as $f) {
    $fn = strtolower($f['field']);
    $fv = trim($f['val']);
    if (!$fv) continue;
    // tank type, volume, options
    if (strpos($fn, 'tank_type') !== false || strpos($fn, 'volume') !== false || strpos($fn, 'dairy_equipment_type') !== false || strpos($fn, 'winery_equipment_type') !== false || strpos($fn, 'brewery_lot') !== false || strpos($fn, 'heating') !== false || strpos($fn, 'cooling') !== false || strpos($fn, 'material') !== false) {
        $key = $f['field'];
        $eqStats[$key] = ($eqStats[$key] ?? []);
        $eqStats[$key][$fv] = ($eqStats[$key][$fv] ?? 0) + 1;
    }
}
foreach ($eqStats as $k => $v) { arsort($eqStats[$k]); }

// === ПОСЕТИТЕЛИ (все сессии) ===
$todayVisits = loadJson("$dataDir/v_$selectedDate.json");
$allFields = loadJson("$dataDir/f_$selectedDate.json");
$allClicks = loadJson("$dataDir/c_$selectedDate.json");
$allSubmits = loadJson("$dataDir/s_$selectedDate.json");

$fiSids = []; foreach ($allFields as $f) $fiSids[$f['sid']] = true;
$clSids = []; foreach ($allClicks as $c) $clSids[$c['sid']] = true;
$suSids = []; foreach ($allSubmits as $s) $suSids[$s['sid']] = true;

$visitors = [];
$intDomains = ['ob-kub.ru','cl121464.tw1.ru','www.ob-kub.ru'];
foreach ($todayVisits as $v) {
    $sid = $v['sid'];
    $page = $v['page'] ?: '/';
    if (!isset($visitors[$sid])) {
        $ref = $v['ref'] ?? '';
        $refHost = $ref ? (parse_url($ref, PHP_URL_HOST) ?: $ref) : '';
        $isInt = false;
        foreach ($intDomains as $d) { if (strpos($refHost, $d) !== false) { $isInt = true; break; } }
        $visitors[$sid] = [
            'entry_page' => $page, 'entry_time' => $v['t'],
            'last_time' => $v['t'], 'pages' => [$page],
            'ref' => $ref, 'ref_host' => $refHost,
            'ref_type' => !$ref ? 'direct' : ($isInt ? 'internal' : 'external'),
            'screen' => $v['screen'] ?? '',
        ];
    } else {
        $visitors[$sid]['last_time'] = max($visitors[$sid]['last_time'], $v['t']);
        if (!in_array($page, $visitors[$sid]['pages'])) $visitors[$sid]['pages'][] = $page;
    }
}
foreach ($online as $oSid => $oTs) {
    if (isset($visitors[$oSid])) $visitors[$oSid]['last_time'] = max($visitors[$oSid]['last_time'], $oTs);
}
$visitorCount = 0;
foreach ($visitors as $sid => &$vv) {
    $vv['has_fields'] = isset($fiSids[$sid]);
    $vv['has_clicks'] = isset($clSids[$sid]);
    $vv['has_submit'] = isset($suSids[$sid]);
    $vv['duration'] = $vv['last_time'] - $vv['entry_time'];
    $vv['page_count'] = count($vv['pages']);
    $vv['interacted'] = $vv['has_fields'] || $vv['has_clicks'] || $vv['has_submit'];
    $refHost = $vv['ref_host'];
    if ($vv['ref_type'] !== 'external') {
        $vv['source_class'] = $vv['ref_type'] === 'direct' ? 'vsrc-direct' : '';
    } elseif (preg_match('/(yandex|ya\.ru)/i', $refHost)) {
        $vv['source_class'] = 'vsrc-yandex';
    } elseif (preg_match('/(google|googlesyndication)/i', $refHost)) {
        $vv['source_class'] = 'vsrc-google';
    } elseif (preg_match('/(vk\.com|vk\.ru|instagram|facebook|fb\.com|t\.me|telegram|ok\.ru)/i', $refHost)) {
        $vv['source_class'] = 'vsrc-social';
    } else {
        $vv['source_class'] = 'vsrc-other';
    }
    $visitorCount++;
}
unset($vv);
uasort($visitors, function($a, $b) { return $b['last_time'] - $a['last_time']; });

// === ВОРОНКА КОНВЕРСИИ ===
$funnelVisitors = $uvCount;
$funnelFieldFillers = count($fiSids);
$funnelSubmitters = count($suSids);
$funnelLeads = count($leads);
$funnelDeals = 0;
foreach ($leadsData as $ld) { if (($ld['status'] ?? '') === 'deal') $funnelDeals++; }

// === НОВЫЕ ЛИДЫ (для звука) ===
$prevLeadCount = (int)($_SESSION['_lc'] ?? 0);
$hasNewLeads = $prevLeadCount > 0 && $funnelLeads > $prevLeadCount;
$_SESSION['_lc'] = $funnelLeads;

// === НАПОМИНАНИЯ ===
$remindersFile = "$dataDir/reminders.json";
$reminders = file_exists($remindersFile) ? (json_decode(file_get_contents($remindersFile), true) ?: []) : [];
$pendingReminders = [];
$dueReminders = [];
$now = time();
foreach ($reminders as $r) {
    if (!($r['done'] ?? false)) {
        $pendingReminders[] = $r;
        if (($r['due'] ?? 0) <= $now) $dueReminders[] = $r;
    }
}

// === ТЕСТ TELEGRAM ===
$tgTestResult = '';
if (isset($_GET['tg_test']) && $config['bot_token'] && $config['chat_id']) {
    $ch = curl_init('https://api.telegram.org/bot' . $config['bot_token'] . '/sendMessage');
    curl_setopt_array($ch, [CURLOPT_POST => 1, CURLOPT_POSTFIELDS => ['chat_id' => $config['chat_id'], 'text' => '✅ Тестовое сообщение из админ-панели ob-kub.ru', 'parse_mode' => 'HTML'], CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => 5]);
    $res = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $tgTestResult = $http === 200 ? 'ok' : 'error';
}

// === CSV-ЭКСПОРТ ===
if (isset($_GET['csv'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="lids_' . $selectedDate . '.csv"');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
    fputcsv($out, ['Имя', 'Телефон', 'Email', 'Страница', 'Статус', 'Время', 'Заметки']);
    foreach ($leads as $l) {
        $notes = implode(' | ', array_map(function($n) { return $n['text']; }, $l['notes']));
        fputcsv($out, [$l['name'], $l['phone'], $l['email'], $l['page'], $statusLabels[$l['status']] ?? 'Новый', date('H:i', $l['last']), $notes]);
    }
    fclose($out);
    exit;
}

?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Админ-панель</title>
<script>(function(){var s=sessionStorage.getItem('admin_scroll');if(s){sessionStorage.removeItem('admin_scroll');window.scrollTo(0,parseInt(s))}})();</script>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui,sans-serif;background:#f0f2f5;color:#333;font-size:14px;line-height:1.5}
.header{background:#fff;padding:14px 20px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #e0e0e0;position:sticky;top:0;z-index:100;flex-wrap:wrap;gap:8px}
.header h1{font-size:17px;color:#F77C2A}
.header .hdr-right{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.header a{color:#e74c3c;text-decoration:none;font-size:13px}
.header a:hover{text-decoration:underline}
.wrap{max-width:1400px;margin:0 auto;padding:16px}
.metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px}
.m-card{background:#fff;border-radius:10px;padding:14px;text-align:center;border:1px solid #e8e8e8;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.m-card .num{font-size:26px;font-weight:700;color:#F77C2A}
.m-card .lbl{font-size:12px;color:#888;margin-top:3px}
.cols{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}
@media(max-width:900px){.cols{grid-template-columns:1fr}}
.graph{background:#fff;border-radius:10px;padding:14px;border:1px solid #e8e8e8;overflow-x:auto}
.graph h3{font-size:13px;color:#888;margin-bottom:10px}
.bars{display:flex;align-items:flex-end;gap:4px;height:80px}
.bar-wrap{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px}
.bar{width:100%;background:#F77C2A;border-radius:3px 3px 0 0;min-height:2px;transition:height .3s}
.bar.green{background:#27ae60}
.bar.blue{background:#3498db}
.bar-label{font-size:10px;color:#888;text-align:center;line-height:1.2}
.bar-label strong{color:#333;font-size:11px}
table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;padding:7px 8px;color:#999;font-weight:600;border-bottom:1px solid #e0e0e0;font-size:11px;text-transform:uppercase;letter-spacing:.4px}
td{padding:7px 8px;border-bottom:1px solid #eee;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
tr:hover td{background:#f5f7fa;cursor:default}
.panel{background:#fff;border-radius:10px;padding:14px;border:1px solid #e8e8e8;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.panel h3{font-size:13px;color:#888;margin-bottom:10px}
.badge{display:inline-block;padding:2px 7px;border-radius:4px;font-size:11px;font-weight:600;white-space:nowrap}
.badge-orange{background:#F77C2A20;color:#F77C2A}
.badge-blue{background:#3498db20;color:#3498db}
.badge-green{background:#27ae6020;color:#27ae60}
.tab-nav{display:flex;gap:4px;margin-bottom:12px;flex-wrap:wrap}
.tab-btn{background:#e0e0e0;color:#666;border:none;padding:7px 14px;border-radius:6px;cursor:pointer;font-size:13px;transition:.15s}
.tab-btn.active{background:#F77C2A;color:#fff}
.tab-btn:hover:not(.active){background:#d0d0d0}
.tab-content{display:none}
.tab-content.active{display:block}
.fval{color:#27ae60;font-size:12px}
.flbl{color:#888}
.ctxt{color:#F77C2A}
.src-internal{color:#3498db}
.src-external{color:#27ae60}
.empty{color:#999;text-align:center;padding:24px;font-size:13px}
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:#e8e8e8}
::-webkit-scrollbar-thumb{background:#ccc;border-radius:3px}
.online-dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#27ae60;margin-right:5px;animation:pulse 2s infinite;vertical-align:middle}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.sbox{width:100%;padding:7px 10px;border:1px solid #d0d0d0;border-radius:6px;background:#fff;color:#333;font-size:13px;margin-bottom:10px}
select.sbox{appearance:auto;cursor:pointer}
.date-nav{display:flex;gap:8px;align-items:center;margin-bottom:14px;flex-wrap:wrap}
.date-nav input[type=date]{background:#fff;border:1px solid #d0d0d0;color:#333;padding:6px 10px;border-radius:6px;font-size:13px}
.date-nav .btn-sm{background:#F77C2A;color:#fff;border:none;padding:6px 14px;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none}
.date-nav .btn-sm:hover{background:#e06a1a}
.status-select{font-size:12px;padding:3px 6px;border-radius:4px;border:1px solid #d0d0d0;background:#fff;color:#333;cursor:pointer}
.note-input{display:flex;gap:6px;margin-top:4px}
.note-input input{flex:1;padding:5px 8px;border:1px solid #d0d0d0;border-radius:4px;background:#fff;color:#333;font-size:12px}
.note-input button{padding:5px 10px;background:#F77C2A;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:12px}
.notes-list{font-size:12px;color:#666;margin-top:4px;line-height:1.5}
.notes-list .n-time{color:#aaa;font-size:10px}
.settings-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
@media(max-width:600px){.settings-grid{grid-template-columns:1fr}}
.settings-grid label{font-size:12px;color:#666;display:block;margin-bottom:3px}
.settings-grid input{width:100%;padding:8px 10px;border:1px solid #d0d0d0;border-radius:6px;background:#fff;color:#333;font-size:13px}
.help-text{font-size:12px;color:#888;margin-top:4px;line-height:1.4}
fieldset{border:1px solid #e0e0e0;border-radius:8px;padding:14px;margin-bottom:12px}
legend{font-size:12px;color:#F77C2A;padding:0 6px}
.tg-ok{color:#27ae60;font-size:12px;margin-top:6px}
.tg-err{color:#e74c3c;font-size:12px;margin-top:6px}
.submit-btn-save{background:#F77C2A;color:#fff;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:14px;margin-top:10px}
.submit-btn-save:hover{background:#e06a1a}
.vsrc-yandex{background:#fff3cd;color:#856404}
.vsrc-google{background:#e8f0fe;color:#1967d2}
.vsrc-social{background:#fce4ec;color:#c62828}
.vsrc-direct{background:#e3f2fd;color:#1565c0}
.vsrc-other{background:#e8f5e9;color:#2e7d32}
/* CRM */
.lead-row{cursor:pointer}
.lead-row.active td{background:#eef2ff}
.lead-detail{display:none;background:#fafbfc}
.lead-detail.open{display:table-row}
.lead-detail td{padding:14px;white-space:normal}
.lead-detail .dt-col{display:grid;grid-template-columns:1fr 1fr;gap:10px}
@media(max-width:700px){.lead-detail .dt-col{grid-template-columns:1fr}}
.lead-detail .dt-block{background:#f0f2f5;border-radius:8px;padding:12px}
.lead-detail .dt-block h4{font-size:12px;color:#888;margin-bottom:8px;text-transform:uppercase;letter-spacing:.4px}
.lead-detail .dt-val{font-size:13px;color:#333;margin-bottom:4px}
.lead-detail .dt-val .lbl{color:#888;font-size:11px;display:inline-block;min-width:70px}
.note-row{padding:5px 0;border-bottom:1px solid #e8e8e8;font-size:13px;line-height:1.5}
.note-row:last-child{border:0}
.note-row .n-time{color:#aaa;font-size:11px}
.note-row .n-type{display:inline-block;padding:1px 5px;border-radius:3px;font-size:10px;font-weight:600;margin-right:4px}
.note-type-call{background:#27ae6020;color:#27ae60}
.note-type-system{background:#eee;color:#999}
.note-type-note{background:#3498db20;color:#3498db}
.note-input select{width:auto;min-width:80px}
/* Воронка */
.funnel{display:flex;align-items:center;gap:6px;margin-bottom:16px;flex-wrap:nowrap}
.funnel-item{background:#fff;border-radius:10px;padding:10px 12px;text-align:center;border:1px solid #e8e8e8;flex:1;min-width:0;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.funnel-item .fn-num{font-size:20px;font-weight:700}
.funnel-item .fn-lbl{font-size:10px;color:#888;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.funnel-item .fn-pct{font-size:10px;color:#bbb;margin-top:1px}
.fn-visitors .fn-num{color:#3498db}
.fn-fields .fn-num{color:#F77C2A}
.fn-submits .fn-num{color:#9b59b6}
.fn-leads .fn-num{color:#27ae60}
.fn-deals .fn-num{color:#e74c3c}
.funnel-arrow{display:flex;align-items:center;color:#ddd;font-size:18px;justify-content:center}
@media(max-width:700px){.funnel{flex-wrap:wrap;gap:4px}.funnel-item{flex:1 1 45%;padding:8px 10px}.funnel-arrow{flex:0 0 auto;font-size:14px}}
/* Быстрые контакты */
.contact-btns{display:flex;gap:6px;flex-wrap:wrap;margin-top:6px}
.contact-btns a{display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;transition:.15s}
.contact-btns .cb-call{background:#e8f5e9;color:#2e7d32}
.contact-btns .cb-call:hover{background:#c8e6c9}
.contact-btns .cb-wa{background:#e8f5e9;color:#128C7E}
.contact-btns .cb-wa:hover{background:#c8e6c9}
.contact-btns .cb-tg{background:#e3f2fd;color:#1565c0}
.contact-btns .cb-tg:hover{background:#bbdefb}
/* Напоминания */
.reminder-bell{position:relative;cursor:pointer;font-size:18px}
.reminder-bell .rb-count{position:absolute;top:-6px;right:-8px;background:#e74c3c;color:#fff;font-size:9px;font-weight:700;padding:1px 4px;border-radius:8px;min-width:16px;text-align:center}
.reminder-popup{display:none;position:absolute;top:100%;right:0;background:#fff;border:1px solid #e0e0e0;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.1);width:300px;z-index:200;margin-top:8px}
.reminder-popup.open{display:block}
.reminder-popup .rp-h{font-size:12px;color:#888;padding:10px 12px;border-bottom:1px solid #eee}
.reminder-popup .rp-item{padding:8px 12px;border-bottom:1px solid #f0f0f0;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px}
.reminder-popup .rp-item:hover{background:#f5f7fa}
.reminder-popup .rp-item .rp-done{color:#27ae60;font-weight:700;cursor:pointer}
.reminder-popup .rp-empty{color:#999;padding:12px;text-align:center;font-size:13px}
/* Мобильная адаптация */
@media(max-width:600px){
  .wrap{padding:8px}
  .header{padding:10px 12px}
  .header h1{font-size:14px}
  .header h1 img{height:20px!important}
  .metrics{grid-template-columns:repeat(2,1fr);gap:6px}
  .m-card{padding:10px}
  .m-card .num{font-size:20px}
  .date-nav{flex-direction:column;align-items:stretch}
  .date-nav form{flex-direction:column}
  table{font-size:12px}
  th,td{padding:5px 6px}
  .panel{padding:10px}
  .tab-btn{padding:5px 10px;font-size:12px}
  .reminder-popup{width:auto;left:0;right:0;margin:8px 4px 0}
}
</style>
</head>
<body>

<div class="header">
    <h1><img src="/logoblack.png" height="28" style="vertical-align:middle;margin-right:8px" alt="">Админ-панель</h1>
    <div class="hdr-right">
        <span style="font-size:12px;color:#888"><span class="online-dot"></span><?= $onlineCount ?> онлайн</span>
        <span class="reminder-bell" id="reminderBell" onclick="toggleReminders()">🔔<?php if (count($pendingReminders) > 0): ?><span class="rb-count"><?= count($pendingReminders) ?></span><?php endif; ?></span>
        <div class="reminder-popup" id="reminderPopup">
            <div class="rp-h">⏰ Напоминания</div>
            <div id="reminderList">
            <?php if (empty($pendingReminders)): ?>
                <div class="rp-empty">Нет активных напоминаний</div>
            <?php else: ?>
                <?php foreach ($pendingReminders as $r): ?>
                <div class="rp-item" onclick="completeReminder('<?= htmlspecialchars($r['id'], ENT_QUOTES) ?>')">
                    <span class="rp-done">✓</span>
                    <span><?= htmlspecialchars($r['text']) ?></span>
                    <span style="font-size:10px;color:#aaa;margin-left:auto"><?= date('d.m H:i', $r['due']) ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
        <a href="?logout=1">Выйти</a>
    </div>
</div>

<?php if (isset($_GET['logout'])) { session_destroy(); header('Location: ?'); exit; } ?>

<div class="wrap">

<!-- Дата -->
<div class="date-nav">
    <form method="get" style="display:flex;gap:8px;align-items:center">
        <input type="date" name="date" value="<?= $selectedDate ?>" onchange="this.form.submit()">
        <?php if (!$isToday): ?>
            <a href="?" class="btn-sm">Сегодня</a>
        <?php endif; ?>
        <span style="font-size:12px;color:#888">
            <?= $visitCount ?> просмотров · <?= $uvCount ?> уникальных
            <?php if (!$isToday): ?>· <a href="?csv=1&date=<?= urlencode($selectedDate) ?>" style="color:#27ae60;text-decoration:none">CSV</a><?php endif; ?>
        </span>
    </form>
</div>

<!-- Метрики -->
<div class="metrics">
    <div class="m-card"><div class="num"><?= $onlineCount ?></div><div class="lbl">Сейчас на сайте</div></div>
    <div class="m-card"><div class="num"><?= $uvCount ?></div><div class="lbl">Уникальных</div></div>
    <div class="m-card"><div class="num"><?= $visitCount ?></div><div class="lbl">Просмотров</div></div>
    <div class="m-card"><div class="num"><?= $fieldCount ?></div><div class="lbl">Вводов в поля</div></div>
    <div class="m-card"><div class="num"><?= $submitCount ?></div><div class="lbl">Отправлено форм</div></div>
    <div class="m-card"><div class="num"><?= count($leads) ?></div><div class="lbl">Лидов</div></div>
</div>

<!-- Воронка -->
<div class="funnel">
    <div class="funnel-item fn-visitors">
        <div class="fn-num"><?= $funnelVisitors ?></div>
        <div class="fn-lbl">Посетители</div>
        <div class="fn-pct">100%</div>
    </div>
    <div class="funnel-arrow">→</div>
    <div class="funnel-item fn-fields">
        <div class="fn-num"><?= $funnelFieldFillers ?></div>
        <div class="fn-lbl">Заполнили поля</div>
        <div class="fn-pct"><?= $funnelVisitors > 0 ? round($funnelFieldFillers / $funnelVisitors * 100) : 0 ?>%</div>
    </div>
    <div class="funnel-arrow">→</div>
    <div class="funnel-item fn-submits">
        <div class="fn-num"><?= $funnelSubmitters ?></div>
        <div class="fn-lbl">Отправили</div>
        <div class="fn-pct"><?= $funnelVisitors > 0 ? round($funnelSubmitters / $funnelVisitors * 100) : 0 ?>%</div>
    </div>
    <div class="funnel-arrow">→</div>
    <div class="funnel-item fn-leads">
        <div class="fn-num"><?= $funnelLeads ?></div>
        <div class="fn-lbl">Лиды</div>
        <div class="fn-pct"><?= $funnelVisitors > 0 ? round($funnelLeads / $funnelVisitors * 100) : 0 ?>%</div>
    </div>
    <div class="funnel-arrow">→</div>
    <div class="funnel-item fn-deals">
        <div class="fn-num"><?= $funnelDeals ?></div>
        <div class="fn-lbl">Сделки</div>
        <div class="fn-pct"><?= $funnelLeads > 0 ? round($funnelDeals / $funnelLeads * 100) : 0 ?>%</div>
    </div>
</div>

<!-- Графики -->
<div class="cols">
    <div class="graph">
        <h3>Посещаемость за 7 дней</h3>
        <div class="bars">
            <?php $mx = max(max($weekVisits),1); foreach ($weekVisits as $i => $v): ?>
            <div class="bar-wrap"><div class="bar" style="height:<?= max(2,round($v/$mx*80)) ?>px"></div><div class="bar-label"><?= $weekLabels[$i] ?><br><strong><?= $v ?></strong></div></div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="graph">
        <h3>Конверсия за 7 дней (заявки)</h3>
        <div class="bars">
            <?php $mx2 = max(max($weekSubmits),1); foreach ($weekSubmits as $i => $v): ?>
            <div class="bar-wrap"><div class="bar green" style="height:<?= max(2,round($v/$mx2*80)) ?>px"></div><div class="bar-label"><?= $weekLabels[$i] ?><br><strong><?= $v ?></strong></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Часы -->
<div class="panel">
    <h3>Активность по часам <?= $isToday ? 'сегодня' : $selectedDate ?></h3>
    <div style="display:flex;align-items:flex-end;gap:2px;height:50px">
        <?php $mh = max(max($hourly),1); foreach ($hourly as $h => $c): ?>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px" title="<?= $h ?>:00 — <?= $c ?>">
            <div style="width:100%;background:#F77C2A;border-radius:2px;height:<?= max(2,round($c/$mh*50)) ?>px;min-height:2px"></div>
            <span style="font-size:8px;color:#555"><?= $h ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php $activeTab = $_GET['tab'] ?? 'leads'; ?>
<!-- Вкладки -->
<div class="tab-nav">
    <button class="tab-btn <?= $activeTab === 'leads' ? 'active' : '' ?>" data-tab="leads">📋 Лиды (<?= count($leads) ?>)</button>
    <button class="tab-btn <?= $activeTab === 'fields' ? 'active' : '' ?>" data-tab="fields">✏️ Поля</button>
    <button class="tab-btn <?= $activeTab === 'pages' ? 'active' : '' ?>" data-tab="pages">📄 Страницы</button>
    <button class="tab-btn <?= $activeTab === 'clicks' ? 'active' : '' ?>" data-tab="clicks">🖱 Клики</button>
    <button class="tab-btn <?= $activeTab === 'sources' ? 'active' : '' ?>" data-tab="sources">🔗 Источники</button>
    <button class="tab-btn <?= $activeTab === 'popular' ? 'active' : '' ?>" data-tab="popular">📈 Популярность</button>
    <button class="tab-btn <?= $activeTab === 'visitors' ? 'active' : '' ?>" data-tab="visitors">👥 Посетители (<?= $visitorCount ?>)</button>
    <button class="tab-btn <?= $activeTab === 'settings' ? 'active' : '' ?>" data-tab="settings">⚙️ Настройки</button>
    <button class="tab-btn <?= $activeTab === 'prices' ? 'active' : '' ?>" data-tab="prices">💲 Цены</button>
</div>
<script>
// Restore active tab from hash
if (location.hash) {
    var tab = location.hash.replace('#', '');
    document.querySelectorAll('.tab-btn').forEach(function(b){
        if (b.dataset.tab === tab) {
            setTimeout(function(){ b.click(); }, 50);
        }
    });
}
document.querySelectorAll('.tab-btn[data-tab="prices"]').forEach(function(b){
    b.addEventListener('click', function(){ location.hash = 'tab-prices'; });
});
document.querySelectorAll('.tab-btn:not([data-tab="prices"])').forEach(function(b){
    b.addEventListener('click', function(){ location.hash = ''; });
});
</script>

<!-- === ЛИДЫ === -->
<div class="tab-content <?= $activeTab === 'leads' ? 'active' : '' ?>" id="tab-leads">
    <?php if (empty($leads)): ?>
        <div class="empty">Нет лидов за выбранный период.</div>
    <?php else: ?>
    <div style="display:flex;gap:8px;margin-bottom:10px;flex-wrap:wrap">
        <input class="sbox" id="lead-search" placeholder="🔍 Поиск по имени, телефону, странице..." style="flex:1;min-width:200px" oninput="filterTable('lead-table',this.value)">
        <select class="sbox" id="lead-status-filter" style="width:auto;min-width:140px" onchange="filterByStatus()">
            <option value="">Все статусы</option>
            <?php foreach ($statusLabels as $k => $l): ?>
            <option value="<?= $k ?>"><?= $l ?></option>
            <?php endforeach; ?>
        </select>
        <a href="?csv=1&date=<?= urlencode($selectedDate) ?>" class="btn-sm" style="background:#27ae60">Скачать CSV</a>
    </div>
    <div class="panel" style="overflow-x:auto">
        <table id="lead-table">
            <thead><tr><th>Статус</th><th>Имя</th><th>Телефон</th><th>Источник</th><th>Время</th><th>Заметки</th></tr></thead>
            <?php foreach ($leads as $l):
                $noteTypes = ['call' => '📞 Звонок', 'note' => '📝 Заметка'];
            ?>
            <tbody class="lead-group">
            <tr class="lead-row" data-status="<?= $l['status'] ?>" onclick="toggleLeadDetail('detail-<?= md5($l['sid']) ?>')">
                <td>
                    <span class="badge" style="background:<?= $statusColors[$l['status']] ?? '#888' ?>20;color:<?= $statusColors[$l['status']] ?? '#888' ?>"><?= $statusLabels[$l['status']] ?? 'Новый' ?></span>
                </td>
                <td><strong><?= htmlspecialchars($l['name'] ?: '—') ?></strong></td>
                <td><a href="tel:<?= htmlspecialchars($l['phone']) ?>" style="color:#333;text-decoration:none"><?= htmlspecialchars($l['phone'] ?: '—') ?></a></td>
                <td><span class="badge badge-blue"><?= htmlspecialchars($l['page'] ?: '/') ?></span></td>
                <td style="font-size:11px;color:#888"><?= date('H:i:s', $l['last']) ?></td>
                <td>
                    <?php $lastNotes = array_slice($l['notes'], -2); foreach (array_reverse($lastNotes) as $n): ?>
                        <div style="font-size:12px;color:#999;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            <?php if (isset($n['type']) && $n['type'] === 'call'): ?>📞<?php elseif (isset($n['type']) && $n['type'] === 'system'): ?>⚙️<?php endif; ?>
                            <?= htmlspecialchars(mb_substr($n['text'], 0, 40)) ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($l['notes'])): ?><span style="color:#555;font-size:12px">—</span><?php endif; ?>
                </td>
            </tr>
            <tr class="lead-detail" id="detail-<?= md5($l['sid']) ?>">
                <td colspan="6">
                    <div class="dt-col">
                        <div>
                            <div class="dt-block">
                                <h4>Контакт</h4>
                                <div class="dt-val"><span class="lbl">Имя:</span> <?= htmlspecialchars($l['name'] ?: '—') ?></div>
                                <div class="dt-val"><span class="lbl">Телефон:</span> <a href="tel:<?= htmlspecialchars($l['phone']) ?>" style="color:#27ae60"><?= htmlspecialchars($l['phone'] ?: '—') ?></a></div>
                                <div class="dt-val"><span class="lbl">Email:</span> <?= htmlspecialchars($l['email'] ?: '—') ?></div>
                                <div class="dt-val"><span class="lbl">Страница:</span> <?= htmlspecialchars($l['page'] ?: '/') ?></div>
                                <div class="dt-val"><span class="lbl">Время:</span> <?= date('d.m H:i:s', $l['last']) ?></div>
                                <div class="contact-btns">
                                    <a href="tel:<?= htmlspecialchars($l['phone']) ?>" class="cb-call">📞 Позвонить</a>
                                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $l['phone']) ?>" target="_blank" class="cb-wa">💬 WhatsApp</a>
                                    <a href="#" onclick="copyPhone('<?= htmlspecialchars($l['phone']) ?>');return false" class="cb-tg">📋 Копировать</a>
                                    <?php if ($l['email']): ?>
                                    <a href="#" onclick="toggleEmailForm('<?= md5($l['sid']) ?>');return false" class="cb-tg" style="background:#e3f2fd;color:#1565c0">✉️ Email</a>
                                    <?php endif; ?>
                                </div>
                                <?php if ($l['email']): ?>
                                <div id="ef-<?= md5($l['sid']) ?>" style="display:none;margin-top:6px">
                                    <input type="text" id="esub-<?= md5($l['sid']) ?>" value="Ответ на заявку с ob-kub.ru" style="width:100%;padding:5px 8px;border:1px solid #d0d0d0;border-radius:4px;font-size:12px;margin-bottom:4px;box-sizing:border-box">
                                    <textarea id="emsg-<?= md5($l['sid']) ?>" rows="3" style="width:100%;padding:5px 8px;border:1px solid #d0d0d0;border-radius:4px;font-size:12px;margin-bottom:4px;box-sizing:border-box;resize:vertical" placeholder="Текст письма..."></textarea>
                                    <div style="display:flex;gap:4px;align-items:center">
                                        <input type="file" id="efile-<?= md5($l['sid']) ?>" style="flex:1;font-size:11px">
                                        <button onclick="sendLeadEmail('<?= htmlspecialchars($l['sid'], ENT_QUOTES) ?>','<?= htmlspecialchars($l['email'], ENT_QUOTES) ?>','<?= md5($l['sid']) ?>')" style="padding:5px 12px;background:#F77C2A;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:12px">📤 Отправить</button>
                                    </div>
                                    <span id="eres-<?= md5($l['sid']) ?>" style="font-size:11px"></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="dt-block">
                                <h4>Статус</h4>
                                <select class="sbox" style="margin-bottom:0;width:auto;min-width:160px" onchange="updateLeadStatusAjax('<?= htmlspecialchars($l['sid'], ENT_QUOTES) ?>',this.value)">
                                    <?php foreach ($statusLabels as $k => $lb): ?>
                                    <option value="<?= $k ?>" <?= $l['status'] === $k ? 'selected' : '' ?>><?= $lb ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div style="margin-top:8px">
                                    <input type="text" id="rm-text-<?= md5($l['sid']) ?>" placeholder="Напомнить через..." style="width:100%;padding:5px 8px;border:1px solid #d0d0d0;border-radius:4px;background:#fff;color:#333;font-size:12px;box-sizing:border-box;margin-bottom:4px">
                                    <div style="display:flex;gap:4px">
                                        <button onclick="setReminder('<?= htmlspecialchars($l['sid'], ENT_QUOTES) ?>','<?= md5($l['sid']) ?>',30)" style="flex:1;padding:4px;background:#e8e8e8;border:none;border-radius:4px;cursor:pointer;font-size:11px">30 мин</button>
                                        <button onclick="setReminder('<?= htmlspecialchars($l['sid'], ENT_QUOTES) ?>','<?= md5($l['sid']) ?>',60)" style="flex:1;padding:4px;background:#e8e8e8;border:none;border-radius:4px;cursor:pointer;font-size:11px">1 час</button>
                                        <button onclick="setReminder('<?= htmlspecialchars($l['sid'], ENT_QUOTES) ?>','<?= md5($l['sid']) ?>',1440)" style="flex:1;padding:4px;background:#e8e8e8;border:none;border-radius:4px;cursor:pointer;font-size:11px">Завтра</button>
                                    </div>
                                </div>
                            </div>
                            <div class="dt-block">
                                <h4>Данные формы</h4>
                                <?php foreach ($l['vals'] as $fv): ?>
                                    <div class="dt-val"><span class="lbl"><?= htmlspecialchars($fv['field']) ?>:</span> <?= htmlspecialchars($fv['val']) ?></div>
                                <?php endforeach; ?>
                                <?php if (empty($l['vals'])): ?><div style="color:#555">Нет дополнительных данных</div><?php endif; ?>
                            </div>
                            <?php
                            $allEmails = [];
                            if (!empty($l['sent_emails'])) foreach ($l['sent_emails'] as $e) { $e['_dir'] = 'out'; $allEmails[] = $e; }
                            if (!empty($l['incoming_emails'])) foreach ($l['incoming_emails'] as $e) { $e['_dir'] = 'in'; $allEmails[] = $e; }
                            usort($allEmails, fn($a,$b) => ($a['time']??0)-($b['time']??0));
                            ?>
                            <div class="dt-block">
                                <h4>✉️ Переписка
                                    <?php if ($l['email']): ?>
                                    <button onclick="checkLeadMail('<?= md5($l['sid']) ?>','<?= htmlspecialchars($l['sid'], ENT_QUOTES) ?>','<?= htmlspecialchars($l['email'] ?? '', ENT_QUOTES) ?>')" style="padding:2px 8px;background:#e3f2fd;color:#1565c0;border:none;border-radius:4px;cursor:pointer;font-size:10px">📬 Новые ответы</button>
                                    <button onclick="fetchAllLeadMails('<?= md5($l['sid']) ?>','<?= htmlspecialchars($l['sid'], ENT_QUOTES) ?>','<?= htmlspecialchars($l['email'] ?? '', ENT_QUOTES) ?>')" style="padding:2px 8px;background:#fff;color:#888;border:1px solid #d0d0d0;border-radius:4px;cursor:pointer;font-size:10px">🔄 Вся переписка</button>
                                    <?php endif; ?>
                                </h4>
                                <div id="mail-thread-<?= md5($l['sid']) ?>">
                                <?php if (empty($allEmails)): ?>
                                    <div style="color:#555;font-size:13px">Нет писем</div>
                                <?php else: ?>
                                    <?php foreach ($allEmails as $em): ?>
                                    <div style="margin-bottom:5px;padding:5px 8px;border-radius:5px;font-size:12px;<?= $em['_dir']==='out' ? 'background:#e3f2fd;border:1px solid #bbdefb' : 'background:#fff;border:1px solid #e0e0e0' ?>">
                                        <div style="font-weight:600;font-size:10px;color:#888"><?= $em['_dir']==='out' ? '→ Отправлено' : '← Получено' ?> · <?= date('d.m H:i', $em['time']??0) ?></div>
                                        <div style="margin:2px 0"><strong>Тема:</strong> <?= htmlspecialchars($em['subject']??'') ?></div>
                                        <div style="color:#555;white-space:pre-wrap"><?= htmlspecialchars(mb_substr($em['body']??'',0,5000)) ?></div>
                                        <?php if (!empty($em['files'])): foreach ($em['files'] as $f): ?>
                                        <div style="font-size:11px;margin-top:2px">📎 <a href="#" onclick="downloadAttach('<?= md5($l['sid']) ?>','<?= htmlspecialchars(addslashes($f['name'] ?? $f)) ?>');return false" style="color:#1565c0"><?= htmlspecialchars($f['name'] ?? $f) ?></a></div>
                                        <?php endforeach; endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </div>
                                <div id="mail-result-<?= md5($l['sid']) ?>" style="font-size:11px;margin-top:4px;color:#888"></div>
                            </div>
                        </div>
                        <div>
                            <div class="dt-block">
                                <h4>История действий</h4>
                                <div id="notes-<?= md5($l['sid']) ?>">
                                <?php if (empty($l['notes'])): ?>
                                    <div style="color:#555;font-size:13px">Нет записей</div>
                                <?php else: ?>
                                    <?php foreach (array_reverse($l['notes']) as $n):
                                        $nt = $n['type'] ?? 'note';
                                        $typeLabel = $noteTypes[$nt] ?? '📝 Заметка';
                                    ?>
                                    <div class="note-row">
                                        <span class="n-time"><?= date('d.m H:i', $n['t']) ?></span>
                                        <span class="note-type-<?= $nt ?>"><?= $typeLabel ?></span>
                                        <?= htmlspecialchars($n['text']) ?>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </div>
                            </div>
                            <div class="dt-block">
                                <h4>Добавить запись</h4>
                                <div style="display:flex;gap:6px;flex-wrap:wrap">
                                    <select id="ntype-<?= md5($l['sid']) ?>" style="width:auto;min-width:120px;padding:6px;border:1px solid #d0d0d0;border-radius:4px;background:#fff;color:#333;font-size:13px">
                                        <option value="note">📝 Заметка</option>
                                        <option value="call">📞 Звонок</option>
                                    </select>
                                    <input type="text" id="ntext-<?= md5($l['sid']) ?>" placeholder="Текст..." style="flex:1;min-width:150px;padding:6px 8px;border:1px solid #d0d0d0;border-radius:4px;background:#fff;color:#333;font-size:13px">
                                    <button onclick="addLeadNote('<?= htmlspecialchars($l['sid'], ENT_QUOTES) ?>','<?= md5($l['sid']) ?>')" style="padding:6px 12px;background:#F77C2A;color:#fff;border:none;border-radius:4px;cursor:pointer">➕</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- === ПОЛЯ === -->
<div class="tab-content" id="tab-fields">
    <?php $rFields = array_slice(array_reverse($todayFields), 0, 100); if (empty($rFields)): ?>
        <div class="empty">Нет данных о вводе полей.</div>
    <?php else: ?>
    <div class="panel" style="overflow-x:auto">
        <table><thead><tr><th>Время</th><th>Страница</th><th>Форма</th><th>Поле</th><th>Значение</th></tr></thead>
        <tbody><?php foreach ($rFields as $f): ?>
            <tr><td style="font-size:11px;color:#888"><?= date('H:i:s', $f['t']) ?></td>
            <td><span class="badge badge-blue"><?= htmlspecialchars($f['page']) ?></span></td>
            <td><?= htmlspecialchars($f['form']) ?></td>
            <td><?= htmlspecialchars($f['field']) ?></td>
            <td class="fval"><?= htmlspecialchars($f['val']) ?></td></tr>
        <?php endforeach; ?></tbody></table>
    </div>
    <?php endif; ?>
</div>

<!-- === СТРАНИЦЫ === -->
<div class="tab-content" id="tab-pages">
    <div class="panel" style="overflow-x:auto">
        <?php $tp = array_sum($pages); if ($tp === 0): ?><div class="empty">Нет данных.</div>
        <?php else: ?>
        <table><thead><tr><th>Страница</th><th>Просмотров</th></tr></thead>
        <tbody><?php foreach ($pages as $p => $c): ?>
            <tr><td><?= htmlspecialchars($p) ?></td><td><strong><?= $c ?></strong> <span style="color:#888;font-size:11px">(<?= round($c/$tp*100) ?>%)</span></td></tr>
        <?php endforeach; ?></tbody></table>
        <?php endif; ?>
    </div>
</div>

<!-- === КЛИКИ === -->
<div class="tab-content" id="tab-clicks">
    <?php $rClicks = array_slice(array_reverse($todayClicks), 0, 50); if (empty($rClicks)): ?>
        <div class="empty">Нет данных о кликах.</div>
    <?php else: ?>
    <div class="panel" style="overflow-x:auto">
        <table><thead><tr><th>Время</th><th>Страница</th><th>Элемент</th><th>Текст/Ссылка</th></tr></thead>
        <tbody><?php foreach ($rClicks as $c): ?>
            <tr><td style="font-size:11px;color:#888"><?= date('H:i:s', $c['t']) ?></td>
            <td><span class="badge badge-blue"><?= htmlspecialchars($c['page']) ?></span></td>
            <td><span class="badge badge-orange"><?= htmlspecialchars($c['tag']) ?></span> <span class="badge badge-green"><?= htmlspecialchars($c['cls']) ?></span></td>
            <td class="ctxt"><?php if ($c['txt']): ?>"<?= htmlspecialchars($c['txt']) ?>"<?php endif; ?>
                <?php if ($c['href'] && $c['txt'] !== $c['href']): ?><br><span style="color:#888;font-size:11px"><?= htmlspecialchars($c['href']) ?></span><?php endif; ?>
            </td></tr>
        <?php endforeach; ?></tbody></table>
    </div>
    <?php endif; ?>
</div>

<!-- === ИСТОЧНИКИ === -->
<div class="tab-content" id="tab-sources">
    <div class="panel" style="overflow-x:auto">
        <?php $tr = array_sum($refs); if ($tr === 0): ?><div class="empty">Нет данных о переходах.</div>
        <?php else: ?>
        <table><thead><tr><th>Источник</th><th>Переходов</th></tr></thead>
        <tbody><?php foreach ($refs as $src => $c):
            $int = false; foreach (['ob-kub.ru','cl121464.tw1.ru'] as $d) { if (strpos($src,$d)!==false) $int=true; }
            ?>
            <tr><td class="<?= $int?'src-internal':'src-external' ?>"><?= htmlspecialchars($src) ?> <?= $int?'(внутр)':'(внеш)' ?></td>
            <td><strong><?= $c ?></strong> <span style="color:#888;font-size:11px">(<?= round($c/$tr*100) ?>%)</span></td></tr>
        <?php endforeach; ?></tbody></table>
        <?php endif; ?>
    </div>
</div>

<!-- === ПОПУЛЯРНОСТЬ === -->
<div class="tab-content" id="tab-popular">
    <?php if (empty($eqStats)): ?>
        <div class="empty">Нетданных о выборе оборудования.</div>
    <?php else: ?>
    <div class="panel" style="overflow-x:auto">
        <table><thead><tr><th>Параметр</th><th>Значение</th><th>Раз</th></tr></thead>
        <tbody><?php foreach ($eqStats as $param => $vals): ?>
            <?php $first = true; foreach ($vals as $val => $cnt): ?>
            <tr><td><?= $first ? htmlspecialchars($param) : '' ?></td>
            <td><?= htmlspecialchars($val) ?></td><td><strong><?= $cnt ?></strong></td></tr>
            <?php $first = false; endforeach; ?>
        <?php endforeach; ?></tbody></table>
    </div>
    <?php endif; ?>
</div>

<!-- === ПОСЕТИТЕЛИ === -->
<div class="tab-content" id="tab-visitors">
    <?php if (empty($visitors)): ?>
        <div class="empty">Нет данных о посещениях.</div>
    <?php else: ?>
    <div style="display:flex;gap:8px;margin-bottom:10px;flex-wrap:wrap">
        <input class="sbox" id="visitor-search" placeholder="🔍 Поиск по странице, источнику..." style="flex:1;min-width:200px" oninput="filterTable('visitor-table',this.value)">
        <select class="sbox" id="visitor-source-filter" style="width:auto;min-width:140px" onchange="filterVisitorSource()">
            <option value="">Все источники</option>
            <option value="external">Внешние 🔗</option>
            <option value="direct">Прямой заход 🏠</option>
            <option value="internal">Внутренний 🔄</option>
        </select>
    </div>
    <div class="panel" style="overflow-x:auto">
        <table id="visitor-table">
            <thead><tr><th>Время</th><th>Источник</th><th>Вход</th><th>Страниц</th><th>На сайте</th><th>Экран</th><th>Активность</th></tr></thead>
            <tbody>
            <?php $vi = 0; foreach ($visitors as $sid => $vv): if (++$vi > 500) break; ?>
            <tr class="visitor-row" data-ref-type="<?= $vv['ref_type'] ?>" data-interacted="<?= $vv['interacted'] ? 1 : 0 ?>">
                <td style="font-size:11px;color:#888"><?= date('H:i:s', $vv['entry_time']) ?></td>
                <td>
                    <?php if ($vv['ref_type'] === 'direct'): ?>
                        <span class="badge badge-blue">Прямой</span>
                    <?php elseif ($vv['ref_type'] === 'internal'): ?>
                        <span class="badge badge-orange">Внутр.</span>
                    <?php else: ?>
                        <span class="badge <?= $vv['source_class'] ?>" title="<?= htmlspecialchars($vv['ref']) ?>"><?= htmlspecialchars($vv['ref_host'] ?: 'Внешний') ?></span>
                    <?php endif; ?>
                </td>
                <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= htmlspecialchars($vv['entry_page']) ?>">
                    <?= htmlspecialchars($vv['entry_page']) ?>
                </td>
                <td><strong><?= $vv['page_count'] ?></strong>
                    <?php if ($vv['page_count'] > 1): ?>
                        <span style="font-size:10px;color:#555;cursor:pointer" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'':'none'">📄</span>
                        <span style="display:none;font-size:11px;color:#888;white-space:normal"><?php foreach ($vv['pages'] as $pp): ?><br><?= htmlspecialchars($pp) ?><?php endforeach; ?></span>
                    <?php endif; ?>
                </td>
                <td style="font-size:12px">
                    <?php if ($vv['duration'] < 5): ?>
                        <span style="color:#555">—</span>
                    <?php elseif ($vv['duration'] < 60): ?>
                        <?= $vv['duration'] ?>с
                    <?php else: ?>
                        <?= floor($vv['duration'] / 60) ?>м <?= $vv['duration'] % 60 ?>с
                    <?php endif; ?>
                </td>
                <td style="font-size:11px;color:#888"><?= htmlspecialchars($vv['screen'] ?: '—') ?></td>
                <td>
                    <?php if ($vv['has_submit']): ?><span class="badge badge-green" title="Отправил форму">📩</span><?php endif; ?>
                    <?php if ($vv['has_fields']): ?><span class="badge badge-orange" title="Заполнял поля">✏️</span><?php endif; ?>
                    <?php if ($vv['has_clicks']): ?><span class="badge badge-blue" title="Кликал">🖱</span><?php endif; ?>
                    <?php if (!$vv['interacted']): ?><span style="color:#555;font-size:11px">—</span><?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- === НАСТРОЙКИ === -->
<div class="tab-content" id="tab-settings">
    <form method="post">
    <fieldset><legend>🔐 Смена пароля</legend>
        <label>Новый пароль (оставьте пустым, чтобы не менять)</label>
        <input type="password" name="new_pass" minlength="4" autocomplete="off">
    </fieldset>

    <fieldset><legend>✉️ Email-отправка</legend>
        <div class="settings-grid">
            <div><label>Email отправителя</label><input type="email" name="from_email" value="<?= htmlspecialchars($config['from_email']) ?>" placeholder="hello@ob-kub.ru"></div>
        </div>
        <div class="help-text">С этого адреса будут приходить письма клиентам из карточки лида.</div>
    </fieldset>

    <fieldset><legend>📨 Проверка почты (IMAP)</legend>
        <div class="settings-grid">
            <div><label>IMAP сервер</label><input type="text" name="mhost" value="<?= htmlspecialchars($config['imap_host']) ?>" placeholder="imap.yandex.ru" autocomplete="off"></div>
            <div><label>Порт</label><input type="text" name="mport" value="<?= htmlspecialchars($config['imap_port'] ?: '993') ?>" placeholder="993" autocomplete="off"></div>
            <div><label>Логин</label><input type="text" name="muser" value="<?= htmlspecialchars($config['imap_user']) ?>" placeholder="oborudovanie-kubani@yandex.ru" autocomplete="off"></div>
            <div><label>Пароль</label><input type="password" name="mpass" value="<?= htmlspecialchars($config['imap_pass']) ?>" autocomplete="new-password"></div>
        </div>
        <div class="help-text">Для Яндекс.Почты: сервер <strong>imap.yandex.ru</strong>, порт <strong>993</strong>. <strong>Пароль приложения</strong> (сгенерированный).</div>
        <div style="margin-top:8px"><a href="#" onclick="return testImap()" style="color:#F77C2A;font-size:12px">🔌 Проверить подключение IMAP</a> <span id="imap-test-result" style="font-size:12px;margin-left:6px"></span></div>
    </fieldset>

    <fieldset><legend>📤 SMTP-отправка (Яндекс)</legend>
        <div class="settings-grid">
            <div><label>SMTP сервер</label><input type="text" name="smtp_host" value="<?= htmlspecialchars($config['smtp_host'] ?: 'smtp.yandex.ru') ?>" placeholder="smtp.yandex.ru"></div>
            <div><label>Порт</label><input type="text" name="smtp_port" value="<?= htmlspecialchars($config['smtp_port'] ?: '465') ?>" placeholder="465"></div>
            <div><label>Логин</label><input type="text" name="smtp_user" value="<?= htmlspecialchars($config['smtp_user'] ?: $config['imap_user']) ?>" placeholder="oborudovanie-kubani@yandex.ru"></div>
            <div><label>Пароль приложения</label><input type="password" name="smtp_pass" value="<?= htmlspecialchars($config['smtp_pass'] ?: $config['imap_pass']) ?>" autocomplete="new-password"></div>
        </div>
        <div class="help-text">Письма будут отправляться через Яндекс — не попадут в спам. <strong>Пароль приложения</strong> (не обычный пароль!).</div>
        <div style="margin-top:8px"><a href="#" onclick="return testSmtp()" style="color:#F77C2A;font-size:12px">📤 Проверить SMTP</a> <span id="smtp-test-result" style="font-size:12px;margin-left:6px"></span></div>
    </fieldset>

    <fieldset><legend>📬 Telegram-уведомления</legend>
        <div class="settings-grid">
            <div><label>Token бота</label><input type="text" name="bot_token" value="<?= htmlspecialchars($config['bot_token']) ?>" placeholder="1234567890:ABCdef..."></div>
            <div><label>Chat ID</label><input type="text" name="chat_id" value="<?= htmlspecialchars($config['chat_id']) ?>" placeholder="-1001234567890"></div>
        </div>
        <?php if ($tgTestResult === 'ok'): ?><div class="tg-ok">✅ Тест пройден! Сообщение отправлено.</div>
        <?php elseif ($tgTestResult === 'error'): ?><div class="tg-err">❌ Ошибка. Проверьте Token и Chat ID.</div>
        <?php endif; ?>
        <div class="help-text">
            Как настроить:<br>
            1. Напишите <strong>@BotFather</strong> в Telegram, создайте бота, получите токен<br>
            2. Напишите боту любое сообщение<br>
            3. Откройте в браузере:
            <code style="color:#888;word-break:break-all">https://api.telegram.org/botВАШ_ТОКЕН/getUpdates</code><br>
            4. Найдите свой <strong>chat_id</strong> (число) в ответе и вставьте выше<br>
            5. Нажмите «Сохранить», затем <a href="?tg_test=1" style="color:#F77C2A">проверить тест</a>
        </div>
    </fieldset>

    <button type="submit" name="save_settings" class="submit-btn-save">💾 Сохранить настройки</button>
    <?php if (isset($saved)): ?><span style="color:#27ae60;font-size:13px;margin-left:10px">✓ Сохранено</span><?php endif; ?>
    <?php if (isset($debugSaved)): ?>
    <div style="margin-top:8px;padding:8px;background:#f5f5f5;border-radius:6px;font-size:11px;color:#555;line-height:1.6">
        <strong>Отладка сохранения:</strong><br>
        POST save_settings: <?= $debugSaved['_post_save_settings'] ?><br>
        POST mhost: <?= htmlspecialchars($debugSaved['_post_mhost']) ?><br>
        POST mport: <?= htmlspecialchars($debugSaved['_post_mport']) ?><br>
        POST muser: <?= htmlspecialchars($debugSaved['_post_muser']) ?><br>
        POST from_email: <?= htmlspecialchars($debugSaved['_post_from_email']) ?><br>
        POST smtp_host: <?= htmlspecialchars($debugSaved['smtp_host']) ?><br>
        POST smtp_user: <?= htmlspecialchars($debugSaved['smtp_user']) ?><br>
        POST smtp_pass: <?= $debugSaved['smtp_pass_set'] ?><br>
        Сохранено smtp_user: <?= htmlspecialchars($config['smtp_user'] ?: 'EMPTY') ?><br>
        Сохранено imap_host: <?= htmlspecialchars($debugSaved['saved_imap_host']) ?>
    </div>
    <?php endif; ?>
    </form>
</div>

<!-- === ЦЕНЫ === -->
<?php
$priceFiles = [
    'beer' => [
        ['file' => __DIR__ . '/../catalog/beer-extra-data.php', 'var' => 'beerExtra'],
        ['file' => __DIR__ . '/../catalog/brew-house-data.php', 'var' => 'brewData'],
        ['file' => __DIR__ . '/../catalog/cct-data.php', 'var' => 'cctData'],
    ],
    'dairy' => [['file' => __DIR__ . '/../catalog/dairy-data.php', 'var' => 'dairyData']],
    'wine' => [['file' => __DIR__ . '/../catalog/wine-data.php', 'var' => 'wineData']],
    'industrial' => [['file' => __DIR__ . '/../catalog/industrial-data.php', 'var' => 'industrialData']],
];
$priceCategories = [];

// Auto-detect categories from data files
foreach ($priceFiles as $indKey => $fileList) {
    foreach ($fileList as $fi) {
        $fpath = $fi['file'];
        $varName = $fi['var'];
        if (file_exists($fpath)) {
            require $fpath;
            $data = &$$varName;
            if ($data) {
                foreach ($data as $catKey => $catVal) {
                    if (isset($catVal['specs']) && !empty($catVal['specs'])) {
                        $priceCategories[$indKey][$indKey . '/' . $varName . '/' . $catKey] = $catVal['name'] ?? $catKey;
                    }
                }
            }
        }
    }
}

$selIndustry = $_POST['price_industry'] ?? 'beer';
$selCategory = $_POST['price_category'] ?? '';
$selVar = '';
$selCatKey = '';
if ($selCategory && preg_match('#^([^/]+)/([^/]+)/(.+)$#', $selCategory, $m)) {
    $selIndustry = $m[1];
    $selVar = $m[2];
    $selCatKey = $m[3];
}
$priceSaved = false;

// Save prices
if (isset($_POST['save_prices']) && $selIndustry && $selVar && $selCatKey) {
    $fpath = '';
    foreach ($priceFiles[$selIndustry] as $fi) {
        if ($fi['var'] === $selVar) { $fpath = $fi['file']; break; }
    }
    if ($fpath && file_exists($fpath)) {
        require $fpath;
        $allData = &$$selVar;
        if (isset($allData[$selCatKey]['specs'])) {
            foreach ($_POST as $key => $val) {
                if (preg_match('/^price_(\d+)$/', $key, $m)) {
                    $vol = (int)$m[1];
                    $newPrice = (int)preg_replace('/[^0-9]/', '', $val);
                    if ($newPrice > 0 && isset($allData[$selCatKey]['specs'][$vol])) {
                        $allData[$selCatKey]['specs'][$vol]['price'] = $newPrice;
                    }
                }
            }
            $code = '<?php' . "\n\n";
            $code .= '$' . $selVar . ' = ';
            $code .= var_export($allData, true) . ';';
            $code .= "\n";
            file_put_contents($fpath, $code, LOCK_EX);
            $priceSaved = true;
        }
    }
}

// Load current prices
$currentPrices = [];
if ($selIndustry && $selVar && $selCatKey) {
    $fpath = '';
    foreach ($priceFiles[$selIndustry] as $fi) {
        if ($fi['var'] === $selVar) { $fpath = $fi['file']; break; }
    }
    if ($fpath && file_exists($fpath)) {
        require $fpath;
        $allData = &$$selVar;
        if (isset($allData[$selCatKey]['specs'])) {
            foreach ($allData[$selCatKey]['specs'] as $vol => $spec) {
                if (isset($spec['price'])) {
                    $currentPrices[(int)$vol] = (int)$spec['price'];
                }
            }
        }
    }
}
?>
<div class="tab-content <?= $activeTab === 'prices' ? 'active' : '' ?>" id="tab-prices">
    <form method="post" action="?tab=prices#tab-prices">
    <div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
        <label style="font-size:13px;color:#666">Отрасль:</label>
        <select name="price_industry" class="sbox" style="width:auto;min-width:120px" onchange="this.form.submit()">
            <?php foreach (['beer' => 'Пиво', 'dairy' => 'Молочка', 'wine' => 'Вино', 'industrial' => 'Пром'] as $k => $v): ?>
            <option value="<?= $k ?>" <?= $selIndustry === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
        <label style="font-size:13px;color:#666">Категория:</label>
        <select name="price_category" class="sbox" style="width:auto;min-width:160px" onchange="this.form.submit()">
            <option value="">— выберите —</option>
            <?php foreach (($priceCategories[$selIndustry] ?? []) as $k => $v): ?>
            <option value="<?= $k ?>" <?= $selCategory === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($priceSaved): ?><div style="color:#27ae60;font-size:14px;margin-bottom:10px">✅ Цены сохранены</div><?php endif; ?>
    <?php if ($selCategory && !empty($currentPrices)): ?>
    <div style="background:#fff;border-radius:8px;border:1px solid #e8e8e8;overflow:hidden">
    <table>
        <tr><th>Объём</th><th>Текущая цена</th><th>Новая цена</th></tr>
        <?php ksort($currentPrices); foreach ($currentPrices as $vol => $price): 
            $priceStr = $price >= 1000000 ? number_format($price/1000000, 1, '.', '') . ' млн' : ($price >= 1000 ? number_format($price/1000, 0, '.', '') . ' тыс' : $price);
        ?>
        <tr>
            <td style="font-weight:600"><?= number_format($vol, 0, '.', ' ') ?> л</td>
            <td style="color:#888"><?= number_format($price, 0, '.', ' ') ?> ₽</td>
            <td><input type="text" name="price_<?= $vol ?>" value="<?= $price ?>" style="width:140px;padding:6px 8px;border:1px solid #d0d0d0;border-radius:4px;font-size:13px"></td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <button type="submit" name="save_prices" class="submit-btn-save" style="margin-top:12px">💾 Сохранить цены</button>
    <?php elseif ($selCategory && empty($currentPrices)): ?>
    <div class="empty">Не найдено спецификаций для выбранной категории</div>
    <?php endif; ?>
    </form>
</div>

</div><!-- /wrap -->

<script>
// === ВКЛАДКИ ===
document.querySelectorAll('.tab-btn').forEach(function(b){
    b.addEventListener('click',function(){
        document.querySelectorAll('.tab-btn,.tab-content').forEach(function(e){e.classList.remove('active')});
        this.classList.add('active');
        document.getElementById('tab-'+this.dataset.tab).classList.add('active');
    });
});

// === ФИЛЬТР ТАБЛИЦЫ ===
function filterTable(id,q){
    q=q.toLowerCase();
    if(id==='lead-table'){
        document.querySelectorAll('#'+id+' .lead-group').forEach(function(g){
            var txt=g.querySelector('.lead-row').textContent.toLowerCase();
            g.style.display=txt.indexOf(q)>-1?'':'none';
        });
    } else {
        document.querySelectorAll('#'+id+' tbody tr').forEach(function(r){
            r.style.display=r.textContent.toLowerCase().indexOf(q)>-1?'':'none';
        });
    }
}

// === ФИЛЬТР ПО СТАТУСУ ===
function filterByStatus(){
    var s=document.getElementById('lead-status-filter').value;
    document.querySelectorAll('.lead-group').forEach(function(g){
        var row=g.querySelector('.lead-row');
        g.style.display=!s||row.dataset.status===s?'':'none';
    });
}

// === СТАТУС ЛИДА (AJAX) ===
function updateLeadStatusAjax(sid,status){
    var x=new XMLHttpRequest();
    x.open('POST','/php/lead.php',true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.onload=function(){ if(x.status===200) setTimeout(function(){ sessionStorage.setItem('admin_scroll', window.scrollY); location.reload(); },500); };
    x.send('action=update_status&sid='+encodeURIComponent(sid)+'&status='+encodeURIComponent(status));
}

// === ЗАМЕТКА (AJAX) ===
function addLeadNote(sid,hash){
    var inp=document.getElementById('ntext-'+hash);
    var sel=document.getElementById('ntype-'+hash);
    var text=inp.value.trim();
    if(!text) return;
    var x=new XMLHttpRequest();
    x.open('POST','/php/lead.php',true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.onload=function(){
        if(x.status===200){ inp.value=''; sessionStorage.setItem('admin_scroll', window.scrollY); location.reload(); }
    };
    x.send('action=add_note&sid='+encodeURIComponent(sid)+'&text='+encodeURIComponent(text)+'&type='+encodeURIComponent(sel.value));
}

// === ДЕТАЛЬНАЯ КАРТОЧКА ===
function toggleLeadDetail(id){
    var tr=document.getElementById(id);
    if(!tr) return;
    var open=tr.classList.toggle('open');
    var group=tr.closest('.lead-group');
    if(group) group.querySelector('.lead-row').classList.toggle('active',open);
    if(open) sessionStorage.setItem('admin_open_lead', id);
    else sessionStorage.removeItem('admin_open_lead');
}

// === ФИЛЬТР ПОСЕТИТЕЛЕЙ ===
function filterVisitorSource(){
    var s=document.getElementById('visitor-source-filter').value;
    document.querySelectorAll('.visitor-row').forEach(function(r){
        if(!s) r.style.display='';
        else r.style.display=r.dataset.refType===s?'':'none';
    });
}

// === НАПОМИНАНИЯ ===
function toggleReminders(){
    document.getElementById('reminderPopup').classList.toggle('open');
}

function setReminder(sid,hash,minutes){
    var inp=document.getElementById('rm-text-'+hash);
    var text=inp.value.trim();
    if(!text){ inp.focus(); return; }
    var due=Math.floor(Date.now()/1000)+minutes*60;
    var x=new XMLHttpRequest();
    x.open('POST','/php/lead.php',true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.onload=function(){ if(x.status===200){ sessionStorage.setItem('admin_scroll', window.scrollY); location.reload(); } };
    x.send('action=add_reminder&text='+encodeURIComponent(text)+'&lead_sid='+encodeURIComponent(sid)+'&due='+due);
}

function completeReminder(rid){
    var x=new XMLHttpRequest();
    x.open('POST','/php/lead.php',true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.onload=function(){ if(x.status===200) location.reload(); };
    x.send('action=complete_reminder&rid='+encodeURIComponent(rid));
}

// === ЗВУК УВЕДОМЛЕНИЯ ===
function toggleEmailForm(hash){
    var el=document.getElementById('ef-'+hash);
    if(el) el.style.display=el.style.display==='none'?'':'none';
}

function sendLeadEmail(sid,email,hash){
    var sub=document.getElementById('esub-'+hash);
    var msg=document.getElementById('emsg-'+hash);
    var fileInput=document.getElementById('efile-'+hash);
    var res=document.getElementById('eres-'+hash);
    if(!sub||!msg||!res) return;
    if(!msg.value.trim()){res.textContent='❌ Напишите текст';return;}
    res.textContent='⏳...';
    var fd=new FormData();
    fd.append('action','send_email');
    fd.append('sid',sid);
    fd.append('email',email);
    fd.append('subject',sub.value);
    fd.append('message',msg.value);
    if(fileInput && fileInput.files && fileInput.files[0]){
        fd.append('file',fileInput.files[0]);
    }
    var x=new XMLHttpRequest();
    x.open('POST','/php/lead.php',true);
    x.onload=function(){
        if(x.status===200){
            try{
                var r=JSON.parse(x.responseText);
                res.textContent=r.ok?'✅ Отправлено':'❌ Ошибка';
                if(r.ok && r.detail_id) setTimeout(function(){ sessionStorage.setItem('admin_open_lead', r.detail_id); location.reload(); }, 1200);
            }catch(e){ res.textContent='❌ Ошибка'; }
        } else { res.textContent='❌ Ошибка'; }
    };
    x.send(fd);
}

function checkLeadMail(hash,sid,email){
    var res=document.getElementById('mail-result-'+hash);
    if(res) res.textContent='⏳ Проверяю...';
    var x=new XMLHttpRequest();
    x.open('POST','/php/lead.php',true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.onload=function(){
        if(x.status===200){
            try{
                var r=JSON.parse(x.responseText);
                if(r.ok){
                    if(res){
                        if(r.new>0) res.textContent='✅ Найдено новых: '+r.new;
                        else if(r.debug){
                            var dbg='';
                            for(var f in r.debug.folders_ok) dbg+=f+'='+r.debug.folders_ok[f]+' ';
                            res.textContent='ℹ️ Нет ответов. Debug: '+dbg+' from_examples: '+(r.debug.from_examples||[]).join(', ').substring(0,100);
                        } else {
                            res.textContent='✅ Новых ответов нет';
                        }
                    }
                    if(r.new>0 && r.detail_id) setTimeout(function(){ sessionStorage.setItem('admin_open_lead', r.detail_id); location.reload(); },1000);
                } else {
                    if(res) res.textContent='❌ '+(r.error==='no_imap_ext' ? 'IMAP недоступен на хостинге' : r.error==='no_imap_config' ? 'Не настроен IMAP в Настройках' : 'Ошибка подключения');
                }
            }catch(e){ if(res) res.textContent='❌ Ошибка'; }
        } else { if(res) res.textContent='❌ Ошибка'; }
    };
    x.send('action=check_mail&sid='+encodeURIComponent(sid)+'&email='+encodeURIComponent(email||''));
}

function fetchAllLeadMails(hash,sid,email){
    var th=document.getElementById('mail-thread-'+hash);
    var res=document.getElementById('mail-result-'+hash);
    if(res) res.textContent='⏳ Загружаю всю переписку...';
    var x=new XMLHttpRequest();
    x.open('POST','/php/lead.php',true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.timeout=30000;
    x.onload=function(){
        if(x.status===200){
            try{
                var r=JSON.parse(x.responseText);
                if(r.ok && r.emails && r.emails.length){
                    var html='';
                    r.emails.forEach(function(e){
                        var dirCl=e._dir==='out'?'background:#e3f2fd;border:1px solid #bbdefb':'background:#fff;border:1px solid #e0e0e0';
                        var dirLb=e._dir==='out'?'→ Отправлено':'← Получено';
                        html+='<div style="margin-bottom:5px;padding:5px 8px;border-radius:5px;font-size:12px;'+dirCl+'">';
                        html+='<div style="font-weight:600;font-size:10px;color:#888">'+dirLb+' · '+(e.time_fmt||'')+'</div>';
                        html+='<div style="margin:2px 0"><strong>Тема:</strong> '+(e.subject||'')+'</div>';
                        html+='<div style="color:#555;white-space:pre-wrap">'+(e.body||'')+'</div>';
                        if(e.files && e.files.length){
                            e.files.forEach(function(f){
                                var fname = f.name||f;
                                html+='<div style="font-size:11px;margin-top:2px">📎 <a href="#" onclick="return false" style="color:#1565c0">'+fname+'</a></div>';
                            });
                        }
                        html+='</div>';
                    });
                    if(th) th.innerHTML=html;
                    if(res) res.textContent='✅ Загружено: '+(r.total||r.count||0)+' писем';
                    // Показываем отладку если письма не найдены
                    if(!r.total && r.debug){
                        var dbg='';
                        for(var f in r.debug.folders) dbg+=f+': '+r.debug.folders[f]+', ';
                        if(res) res.textContent='ℹ️ Нет писем. Debug: '+dbg+' ищем: '+r.debug.lead_email_search;
                    }
                } else {
                    if(th) th.innerHTML='<div style="color:#555;font-size:13px">Нет писем</div>';
                    if(res) res.textContent=r.ok?'Писем не найдено':'❌ '+(r.error||'ошибка');
                }
                if(r.detail_id) sessionStorage.setItem('admin_open_lead', r.detail_id);
            }catch(e){ if(th) th.innerHTML='<div style="color:#555;font-size:13px">Ошибка</div>'; if(res) res.textContent='❌ Ошибка'; }
        } else { if(res) res.textContent='❌ HTTP '+x.status; }
    };
    x.onerror=function(){ if(res) res.textContent='❌ Сетевая ошибка'; };
    x.ontimeout=function(){ if(res) res.textContent='⏱ Таймаут 30с'; };
    x.send('action=fetch_all_emails&sid='+encodeURIComponent(sid)+'&email='+encodeURIComponent(email||''));
}

function downloadAttach(hash,name){ return false; }

function copyPhone(num){
    var inp=document.createElement('input');
    inp.value=num;
    document.body.appendChild(inp);
    inp.select();
    try{ document.execCommand('copy'); }catch(e){}
    document.body.removeChild(inp);
    alert('Номер скопирован: '+num);
}

function playBeep(){
    try{
        var ctx=new (window.AudioContext||window.webkitAudioContext)();
        var o=ctx.createOscillator();
        var g=ctx.createGain();
        o.connect(g); g.connect(ctx.destination);
        o.type='sine'; o.frequency.value=880;
        g.gain.setValueAtTime(0.3,ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.01,ctx.currentTime+0.5);
        o.start(ctx.currentTime); o.stop(ctx.currentTime+0.5);
        // Второй звук выше
        setTimeout(function(){
            var o2=ctx.createOscillator();
            var g2=ctx.createGain();
            o2.connect(g2); g2.connect(ctx.destination);
            o2.type='sine'; o2.frequency.value=1100;
            g2.gain.setValueAtTime(0.2,ctx.currentTime);
            g2.gain.exponentialRampToValueAtTime(0.01,ctx.currentTime+0.4);
            o2.start(ctx.currentTime); o2.stop(ctx.currentTime+0.4);
        },200);
    }catch(e){}
}

<?php if ($hasNewLeads): ?>
playBeep();
<?php endif; ?>

// === ПРОВЕРКА НОВЫХ ЛИДОВ (каждые 15с) ===
function pollNewLeads(){
    var x=new XMLHttpRequest();
    x.open('GET','/php/lead.php?action=check_new&t='+Date.now(),true);
    x.onload=function(){
        if(x.status===200){
            try{
                var r=JSON.parse(x.responseText);
                if(r.new){ playBeep(); }
            }catch(e){}
        }
    };
    x.send();
}

// === ПРОВЕРКА ПРОСРОЧЕННЫХ НАПОМИНАНИЙ (каждые 30с) ===
function pollReminders(){
    var x=new XMLHttpRequest();
    x.open('GET','/php/lead.php?action=check_reminders&t='+Date.now(),true);
    x.onload=function(){
        if(x.status===200){
            try{
                var r=JSON.parse(x.responseText);
                if(r.due && r.due.length>0){
                    playBeep();
                    // Обновить попап
                    var bell=document.getElementById('reminderBell');
                    if(bell && !bell.querySelector('.rb-count')){
                        var c=document.createElement('span');
                        c.className='rb-count';
                        c.textContent=r.due.length;
                        bell.appendChild(c);
                    }
                }
            }catch(e){}
        }
    };
    x.send();
}

// === ТЕСТ IMAP ===
function testImap(){
    var el=document.getElementById('imap-test-result');
    if(el) el.textContent='⏳...';
    var host=encodeURIComponent(document.querySelector('[name="mhost"]').value);
    var port=encodeURIComponent(document.querySelector('[name="mport"]').value);
    var user=encodeURIComponent(document.querySelector('[name="muser"]').value);
    var pass=encodeURIComponent(document.querySelector('[name="mpass"]').value);
    var x=new XMLHttpRequest();
    x.open('POST','/php/lead.php',true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.timeout=15000;
    x.ontimeout=function(){ if(el) el.innerHTML='⏱ Таймаут — IMAP не отвечает 15с'; };
    x.onerror=function(){ if(el) el.innerHTML='❌ Сетевая ошибка'; };
    x.onload=function(){
        if(x.status===200){
            try{
                var r=JSON.parse(x.responseText);
                if(el){
                    if(r.ok){
                        el.innerHTML='✅ Подключено! Писем: '+r.info.messages+', непрочитано: '+r.info.unseen;
                    } else {
                        var msg=r.error;
                        if(r.error==='no_imap_ext') msg='❌ IMAP-расширение PHP не установлено на хостинге';
                        else if(r.error==='no_imap_config') msg='❌ Не заполнены настройки IMAP';
                        else if(r.error==='imap_connect_failed') msg='❌ Ошибка подключения: '+(r.detail||'неизвестная');
                        el.innerHTML=msg;
                        if(r.info){
                            var extra=' <span style="font-size:10px;color:#888">(сервер: '+r.info.imap_host+', логин: '+r.info.imap_user+', расширение: '+(r.info.has_imap_ext?'да':'нет')+')</span>';
                            if(r.info.file_has_imap_host!==undefined) extra+='<br><span style="font-size:10px;color:#888">файл: imap_host='+r.info.file_imap_host+', тип='+r.info.include_type+'</span>';
                            if(r.info.raw_config_preview) extra+='<br><span style="font-size:9px;color:#aaa">'+r.info.raw_config_preview.replace(/</g,'&lt;')+'</span>';
                            el.innerHTML+=extra;
                        }
                    }
                }
            }catch(e){ if(el) el.textContent='❌ Ошибка'; }
        }
    };
    x.send('action=imap_test&t='+Date.now()+'&imap_host='+host+'&imap_port='+port+'&imap_user='+user+'&imap_pass='+pass);
    return false;
}

// === ТЕСТ SMTP ===
function testSmtp(){
    var el=document.getElementById('smtp-test-result');
    if(el) el.textContent='⏳...';
    var host=encodeURIComponent(document.querySelector('[name="smtp_host"]').value);
    var port=encodeURIComponent(document.querySelector('[name="smtp_port"]').value);
    var user=encodeURIComponent(document.querySelector('[name="smtp_user"]').value);
    var pass=encodeURIComponent(document.querySelector('[name="smtp_pass"]').value);
    var x=new XMLHttpRequest();
    x.open('POST','/php/lead.php',true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.timeout=15000;
    x.ontimeout=function(){ if(el) el.innerHTML='⏱ Таймаут 15с'; };
    x.onerror=function(){ if(el) el.innerHTML='❌ Сетевая ошибка'; };
    x.onload=function(){
        if(x.status===200){
            try{
                var r=JSON.parse(x.responseText);
                if(el) el.innerHTML=r.ok ? '✅ SMTP работает!' : '❌ '+(r.error||'ошибка');
            }catch(e){ if(el) el.textContent='❌ Ошибка'; }
        } else { if(el) el.textContent='❌ HTTP '+x.status; }
    };
    x.send('action=smtp_test&t='+Date.now()+'&smtp_host='+host+'&smtp_port='+port+'&smtp_user='+user+'&smtp_pass='+pass);
    return false;
}

// === ЗАПУСК ПРОВЕРОК ===
setInterval(pollNewLeads,15000);
setInterval(pollReminders,30000);

// Проверка почты каждые 5 минут
setInterval(function(){
    var x=new XMLHttpRequest();
    x.open('POST','/php/lead.php',true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.onload=function(){
        if(x.status===200){
            try{
                var r=JSON.parse(x.responseText);
                if(r.ok && r.new>0) playBeep();
            }catch(e){}
        }
    };
    x.send('action=check_mail');
},300000);

// Перезагрузка раз в 6 часов (чтобы не выдёргивало в начало)
setTimeout(function(){ location.reload(); }, 21600000);
// Восстановление открытой карточки после перезагрузки
window.addEventListener('load', function(){
    var openLead = sessionStorage.getItem('admin_open_lead');
    if (openLead) { toggleLeadDetail(openLead); sessionStorage.removeItem('admin_open_lead'); }
});
</script>

</body>
</html>
