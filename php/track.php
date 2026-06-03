<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

$dd = __DIR__ . '/../tracking_data';
if (!is_dir($dd)) {
    @mkdir($dd, 0755, true);
    @file_put_contents($dd . '/.htaccess', "Require all denied\n");
}

$action = $_POST['action'] ?? '';
$sid = $_POST['sid'] ?? '';
if (!$sid) { echo '{"ok":false}'; exit; }

$now = time();
$date = date('Y-m-d');

switch ($action) {
    case 'visit':
        $f = "$dd/v_$date.json";
        $d = [];
        if (file_exists($f)) $d = json_decode(file_get_contents($f), true) ?: [];
        $d[] = ['sid' => $sid, 'page' => substr($_POST['page'] ?? '', 0, 200), 'ref' => substr($_POST['ref'] ?? '', 0, 500), 'title' => substr($_POST['title'] ?? '', 0, 200), 'screen' => substr($_POST['screen'] ?? '', 0, 30), 't' => $now];
        $d = array_slice($d, -10000);
        file_put_contents($f, json_encode($d, JSON_UNESCAPED_UNICODE), LOCK_EX);

        $uf = "$dd/uv_$date.json";
        $uv = [];
        if (file_exists($uf)) $uv = json_decode(file_get_contents($uf), true) ?: [];
        if (!in_array($sid, $uv)) { $uv[] = $sid; file_put_contents($uf, json_encode($uv), LOCK_EX); }
        echo '{"ok":true}';
        break;

    case 'field':
        $f = "$dd/f_$date.json";
        $d = [];
        if (file_exists($f)) $d = json_decode(file_get_contents($f), true) ?: [];
        $d[] = ['sid' => $sid, 'page' => substr($_POST['page'] ?? '', 0, 200), 'form' => substr($_POST['form'] ?? '', 0, 100), 'field' => substr($_POST['field'] ?? '', 0, 100), 'val' => mb_substr($_POST['value'] ?? '', 0, 1000), 't' => $now];
        $d = array_slice($d, -20000);
        file_put_contents($f, json_encode($d, JSON_UNESCAPED_UNICODE), LOCK_EX);
        echo '{"ok":true}';
        break;

    case 'click':
        $f = "$dd/c_$date.json";
        $d = [];
        if (file_exists($f)) $d = json_decode(file_get_contents($f), true) ?: [];
        $d[] = ['sid' => $sid, 'page' => substr($_POST['page'] ?? '', 0, 200), 'tag' => substr($_POST['tag'] ?? '', 0, 20), 'txt' => mb_substr($_POST['text'] ?? '', 0, 200), 'cls' => substr($_POST['cls'] ?? '', 0, 100), 'href' => substr($_POST['href'] ?? '', 0, 300), 't' => $now];
        $d = array_slice($d, -10000);
        file_put_contents($f, json_encode($d, JSON_UNESCAPED_UNICODE), LOCK_EX);
        echo '{"ok":true}';
        break;

    case 'heartbeat':
        $of = "$dd/online.json";
        $o = [];
        if (file_exists($of)) $o = json_decode(file_get_contents($of), true) ?: [];
        $o[$sid] = $now;
        $cut = $now - 300;
        foreach ($o as $s => $t) { if ($t < $cut) unset($o[$s]); }
        if (count($o) > 1000) $o = array_slice($o, -1000, 1000, true);
        file_put_contents($of, json_encode($o), LOCK_EX);
        echo '{"ok":true}';
        break;

    case 'submit':
        $sf = "$dd/s_$date.json";
        $sd = [];
        if (file_exists($sf)) $sd = json_decode(file_get_contents($sf), true) ?: [];
        $sd[] = ['sid' => $sid, 'form' => substr($_POST['form'] ?? '', 0, 100), 'page' => substr($_POST['page'] ?? '', 0, 200), 't' => $now];
        $sd = array_slice($sd, -5000);
        file_put_contents($sf, json_encode($sd, JSON_UNESCAPED_UNICODE), LOCK_EX);
        echo '{"ok":true}';
        break;

    case 'leave':
        echo '{"ok":true}';
        break;

    default:
        echo '{"ok":false}';
}
