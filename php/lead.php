<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

session_start();
$configFile = __DIR__ . '/../admin/config.php';
if (!file_exists($configFile) || empty($_SESSION['admin'])) {
    echo json_encode(['ok' => false, 'error' => 'auth']);
    exit;
}

$dataDir = __DIR__ . '/../tracking_data';
$leadsFile = "$dataDir/leads.json";
$remindersFile = "$dataDir/reminders.json";

$configRaw = file_exists($configFile) ? (include $configFile) : [];
// Автоконвертация старого строкового конфига в массив
if (is_string($configRaw)) {
    $configRaw = ['hash' => $configRaw, 'from_email' => '', 'imap_host' => '', 'imap_port' => '993', 'imap_user' => '', 'imap_pass' => '', 'smtp_host' => '', 'smtp_port' => '465', 'smtp_user' => '', 'smtp_pass' => ''];
    @file_put_contents($configFile, '<?php return ' . var_export($configRaw, true) . ';', LOCK_EX);
}
// Добавляем недостающие ключи в память (без записи в файл)
if (is_array($configRaw)) {
    $configRaw += ['from_email' => '', 'imap_host' => '', 'imap_port' => '993', 'imap_user' => '', 'imap_pass' => '', 'smtp_host' => '', 'smtp_port' => '465', 'smtp_user' => '', 'smtp_pass' => ''];
}
$fromEmail = '';
$imapHost = '';
$imapPort = '993';
$imapUser = '';
$imapPass = '';
$smtpHost = '';
$smtpPort = '465';
$smtpUser = '';
$smtpPass = '';
if (is_array($configRaw)) {
    $fromEmail = trim($configRaw['from_email'] ?? '');
    $imapHost = trim($configRaw['imap_host'] ?? '');
    $imapPort = trim($configRaw['imap_port'] ?? '993');
    $imapUser = trim($configRaw['imap_user'] ?? '');
    $imapPass = $configRaw['imap_pass'] ?? '';
    $smtpHost = trim($configRaw['smtp_host'] ?? '');
    $smtpPort = trim($configRaw['smtp_port'] ?? '465');
    $smtpUser = trim($configRaw['smtp_user'] ?? '');
    $smtpPass = $configRaw['smtp_pass'] ?? '';
}
if (!$fromEmail) $fromEmail = 'hello@ob-kub.ru';

function loadLeads() {
    global $leadsFile;
    if (!file_exists($leadsFile)) return [];
    return json_decode(file_get_contents($leadsFile), true) ?: [];
}

function saveLeads($leads) {
    global $leadsFile;
    file_put_contents($leadsFile, json_encode($leads, JSON_UNESCAPED_UNICODE), LOCK_EX);
}

$statusLabels = ['new' => 'Новый', 'processing' => 'В обработке', 'contacted' => 'Созвон', 'offer' => 'КП отправлено', 'negotiation' => 'Торг', 'deal' => 'Сделка', 'lost' => 'Отказ'];

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$sid = $_POST['sid'] ?? $_GET['sid'] ?? '';

switch ($action) {
    case 'update_status':
        $status = $_POST['status'] ?? '';
        $allowed = ['new', 'processing', 'contacted', 'offer', 'negotiation', 'deal', 'lost'];
        if (!in_array($status, $allowed)) {
            echo json_encode(['ok' => false, 'error' => 'invalid_status']);
            exit;
        }
        $leads = loadLeads();
        if (!isset($leads[$sid])) $leads[$sid] = [];
        $oldStatus = $leads[$sid]['status'] ?? 'new';
        $leads[$sid]['status'] = $status;
        if ($oldStatus !== $status) {
            if (!isset($leads[$sid]['notes'])) $leads[$sid]['notes'] = [];
            $label = $statusLabels[$status] ?? $status;
            $leads[$sid]['notes'][] = ['text' => "Статус: {$label}", 't' => time(), 'type' => 'system'];
        }
        saveLeads($leads);
        echo json_encode(['ok' => true]);
        break;

    case 'add_note':
        $text = trim($_POST['text'] ?? '');
        if (!$text) {
            echo json_encode(['ok' => false, 'error' => 'empty_note']);
            exit;
        }
        $type = $_POST['type'] ?? 'note';
        if (!in_array($type, ['note', 'call', 'system'])) $type = 'note';
        $leads = loadLeads();
        if (!isset($leads[$sid])) $leads[$sid] = [];
        if (!isset($leads[$sid]['notes'])) $leads[$sid]['notes'] = [];
        $leads[$sid]['notes'][] = ['text' => mb_substr($text, 0, 2000), 't' => time(), 'type' => $type];
        saveLeads($leads);
        echo json_encode(['ok' => true]);
        break;

    case 'get':
        $leads = loadLeads();
        $data = $leads[$sid] ?? ['status' => 'new', 'notes' => []];
        echo json_encode(['ok' => true, 'data' => $data]);
        break;

    case 'add_reminder':
        $text = trim($_POST['text'] ?? '');
        $leadSid = $_POST['lead_sid'] ?? '';
        $due = (int)($_POST['due'] ?? 0);
        if (!$text) {
            echo json_encode(['ok' => false, 'error' => 'empty']);
            exit;
        }
        $list = file_exists($remindersFile) ? (json_decode(file_get_contents($remindersFile), true) ?: []) : [];
        $list[] = ['id' => uniqid(), 'text' => mb_substr($text, 0, 500), 'lead_sid' => $leadSid, 'due' => max($due, time()), 'done' => false, 't' => time()];
        file_put_contents($remindersFile, json_encode($list, JSON_UNESCAPED_UNICODE), LOCK_EX);
        echo json_encode(['ok' => true]);
        break;

    case 'complete_reminder':
        $rid = $_POST['rid'] ?? '';
        $list = file_exists($remindersFile) ? (json_decode(file_get_contents($remindersFile), true) ?: []) : [];
        foreach ($list as &$r) { if (($r['id'] ?? '') === $rid) { $r['done'] = true; break; } }
        file_put_contents($remindersFile, json_encode($list, JSON_UNESCAPED_UNICODE), LOCK_EX);
        echo json_encode(['ok' => true]);
        break;

    case 'send_email':
        $to = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if (!$to || !$subject || !$message || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['ok' => false, 'error' => 'empty']);
            exit;
        }
        $fromName = 'ob-kub.ru';
        $messageHtml = '<html><body style="font-family:system-ui,sans-serif;padding:20px;color:#333">'
            . '<div style="max-width:600px;margin:0 auto;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden">'
            . '<div style="background:#F77C2A;padding:14px 20px;color:#fff;font-size:18px;font-weight:600">ob-kub.ru</div>'
            . '<div style="padding:20px;line-height:1.6">'
            . nl2br(htmlspecialchars($message))
            . '</div>'
            . '<div style="background:#f5f5f5;padding:10px 20px;font-size:11px;color:#888;border-top:1px solid #e0e0e0">'
            . '© ob-kub.ru — <a href="https://ob-kub.ru" style="color:#F77C2A;text-decoration:none">ob-kub.ru</a>'
            . '</div></div></body></html>';
        $boundaryAlt = '=_b' . md5(uniqid('', true));
        $plainText = strip_tags($message);
        $altBody = "--{$boundaryAlt}\r\n"
            . "Content-Type: text/plain; charset=utf-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n"
            . $plainText . "\r\n\r\n"
            . "--{$boundaryAlt}\r\n"
            . "Content-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n"
            . $messageHtml . "\r\n\r\n"
            . "--{$boundaryAlt}--\r\n";
        // Проверяем вложение
        $fileData = null;
        $fileName = '';
        if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK && $_FILES['file']['size'] <= 20971520) {
            $fileData = file_get_contents($_FILES['file']['tmp_name']);
            $fileName = basename($_FILES['file']['name']);
        }
        if ($fileData) {
            $boundaryMix = '=_mix' . md5(uniqid('', true));
            $headers = "From: {$fromName} <{$fromEmail}>\r\nReply-To: {$fromEmail}\r\nMIME-Version: 1.0\r\n"
                . "Message-ID: <" . uniqid('mail', true) . "@ob-kub.ru>\r\n"
                . "Date: " . date('r') . "\r\n"
                . "X-Mailer: PHP/" . phpversion() . "\r\n"
                . "Content-Type: multipart/mixed; boundary=\"{$boundaryMix}\"\r\n";
            $body = "--{$boundaryMix}\r\n"
                . "Content-Type: multipart/alternative; boundary=\"{$boundaryAlt}\"\r\n\r\n"
                . $altBody . "\r\n"
                . "--{$boundaryMix}\r\n"
                . "Content-Type: application/octet-stream; name=\"{$fileName}\"\r\n"
                . "Content-Disposition: attachment; filename=\"{$fileName}\"\r\n"
                . "Content-Transfer-Encoding: base64\r\n\r\n"
                . chunk_split(base64_encode($fileData)) . "\r\n"
                . "--{$boundaryMix}--\r\n";
        } else {
            $headers = "From: {$fromName} <{$fromEmail}>\r\nReply-To: {$fromEmail}\r\nMIME-Version: 1.0\r\n"
                . "Message-ID: <" . uniqid('mail', true) . "@ob-kub.ru>\r\n"
                . "Date: " . date('r') . "\r\n"
                . "X-Mailer: PHP/" . phpversion() . "\r\n"
                . "Content-Type: multipart/alternative; boundary=\"{$boundaryAlt}\"\r\n";
            $body = $altBody;
        }
        // SMTP-отправка приоритетнее mail()
        if ($smtpHost && $smtpUser && $smtpPass) {
            $ok = smtpMail($to, $subject, $body, $headers, $fromEmail, $smtpHost, $smtpPort, $smtpUser, $smtpPass);
        } else {
            $ok = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers, "-f {$fromEmail}");
        }
        if ($ok) {
            $sid2 = $_POST['sid'] ?? '';
            if ($sid2) {
                $leads2 = loadLeads();
                if (!isset($leads2[$sid2])) $leads2[$sid2] = [];
                if (!isset($leads2[$sid2]['notes'])) $leads2[$sid2]['notes'] = [];
                if (!isset($leads2[$sid2]['sent_emails'])) $leads2[$sid2]['sent_emails'] = [];
                $leads2[$sid2]['email'] = $to;
                $leads2[$sid2]['notes'][] = ['text' => "✉️ Отправлено письмо: {$subject}", 't' => time(), 'type' => 'system'];
                $entry = ['subject' => $subject, 'body' => $message, 'to' => $to, 'time' => time()];
                if ($fileName) $entry['files'] = [['name' => $fileName]];
                $leads2[$sid2]['sent_emails'][] = $entry;
                saveLeads($leads2);
            }
        }
        echo json_encode(['ok' => (bool)$ok, 'error' => $ok ? '' : 'mail_failed', 'sid' => $sid2, 'detail_id' => $sid2 ? 'detail-' . md5($sid2) : '']);
        break;

    case 'imap_test':
        set_time_limit(15);
        // Используем значения из POST (из формы) или из конфига
        $testHost = trim($_POST['mhost'] ?? $_POST['mail_host'] ?? $_POST['imap_host'] ?? $imapHost);
        $testPort = trim($_POST['mport'] ?? $_POST['mail_port'] ?? $_POST['imap_port'] ?? $imapPort);
        $testUser = trim($_POST['muser'] ?? $_POST['mail_user'] ?? $_POST['imap_user'] ?? $imapUser);
        $testPass = $_POST['mpass'] ?? $_POST['mail_pass'] ?? $_POST['imap_pass'] ?? $imapPass;
        $info = [];
        $info['has_imap_ext'] = function_exists('imap_open');
        $info['imap_host'] = $testHost ?: '(пусто)';
        $info['imap_port'] = $testPort;
        $info['imap_user'] = $testUser ? substr($testUser, 0, 3) . '***' : '(пусто)';
        $info['imap_configured'] = !empty($testHost) && !empty($testUser) && !empty($testPass);
        $info['from_form'] = isset($_POST['imap_host']);
        // Читаем сырой файл
        $rawFile = file_exists($configFile) ? file_get_contents($configFile) : 'NO FILE';
        $info['raw_config_preview'] = substr($rawFile, 0, 300) . '...';
        // Также читаем через include
        $rawInclude = file_exists($configFile) ? include $configFile : [];
        $info['include_type'] = gettype($rawInclude);
        if (is_array($rawInclude)) {
            $info['file_has_imap_host'] = isset($rawInclude['imap_host']);
            $info['file_imap_host'] = $rawInclude['imap_host'] ?? '(нет)';
        }
        if (!function_exists('imap_open')) {
            echo json_encode(['ok' => false, 'error' => 'no_imap_ext', 'info' => $info]);
            exit;
        }
        if (!$testHost || !$testUser || !$testPass) {
            echo json_encode(['ok' => false, 'error' => 'no_imap_config', 'info' => $info]);
            exit;
        }
        session_write_close();
        // Пробуем разные варианты подключения
        $attempts = [
            "{{$testHost}:{$testPort}/imap/ssl}INBOX",
            "{{$testHost}:{$testPort}/imap/ssl/novalidate-cert}INBOX",
            "{{$testHost}:{$testPort}/imap/notls}INBOX",
            "{{$testHost}:143/imap/notls}INBOX",
        ];
        $mbox = false;
        $lastErr = '';
        foreach ($attempts as $mailbox) {
            $mbox = @imap_open($mailbox, $testUser, $testPass, OP_SILENT, 1);
            if ($mbox) break;
            $lastErr = imap_last_error();
        }
        if (!$mbox) {
            $err = $lastErr ?: 'все варианты не ответили';
            echo json_encode(['ok' => false, 'error' => 'imap_connect_failed', 'detail' => $err, 'info' => $info]);
            exit;
        }
        $status = @imap_status($mbox, $mailbox, SA_ALL);
        $info['messages'] = $status ? $status->messages : 0;
        $info['unseen'] = $status ? $status->unseen : 0;
        imap_close($mbox);
        echo json_encode(['ok' => true, 'info' => $info]);
        break;

    case 'smtp_test':
        $testHost = trim($_POST['smtp_host'] ?? $smtpHost);
        $testPort = trim($_POST['smtp_port'] ?? $smtpPort);
        $testUser = trim($_POST['smtp_user'] ?? $smtpUser);
        $testPass = $_POST['smtp_pass'] ?? $smtpPass;
        if (!$testHost || !$testUser || !$testPass) {
            echo json_encode(['ok' => false, 'error' => 'Заполните SMTP-поля']);
            exit;
        }
        try {
            $errNo = 0; $errStr = '';
            $ssl = (strpos($testPort, '465') !== false);
            $remote = $ssl ? "ssl://{$testHost}:{$testPort}" : "{$testHost}:{$testPort}";
            $s = @stream_socket_client($remote, $errNo, $errStr, 10);
            if (!$s) {
                echo json_encode(['ok' => false, 'error' => "Не удалось подключиться к {$remote}: {$errStr}"]);
                exit;
            }
            // SMTP handshake
            $resp = fgets($s, 512);
            fwrite($s, "EHLO ob-kub.ru\r\n");
            while ($line = fgets($s, 512)) {
                if (substr($line, 3, 1) === ' ') break;
            }
            fwrite($s, "AUTH LOGIN\r\n");
            fgets($s, 512);
            fwrite($s, base64_encode($testUser) . "\r\n");
            fgets($s, 512);
            fwrite($s, base64_encode($testPass) . "\r\n");
            $authResp = fgets($s, 512);
            fwrite($s, "QUIT\r\n");
            fclose($s);
            if (strpos($authResp, '235') !== false) {
                echo json_encode(['ok' => true]);
            } else {
                echo json_encode(['ok' => false, 'error' => "SMTP-авторизация не прошла: " . trim($authResp)]);
            }
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
        exit;

    case 'fetch_all_emails':
        set_time_limit(30);
        $leadSid = $_POST['sid'] ?? '';
        if (!$leadSid) { echo json_encode(['ok' => false, 'error' => 'no_sid']); exit; }
        if (!$imapHost || !$imapUser || !$imapPass) {
            echo json_encode(['ok' => false, 'error' => 'no_imap_config']);
            exit;
        }
        if (!function_exists('imap_open')) {
            echo json_encode(['ok' => false, 'error' => 'no_imap_ext']);
            exit;
        }
        $leads = loadLeads();
        $leadEmail = strtolower(trim($leads[$leadSid]['email'] ?? ''));
        if (!$leadEmail && !empty($leads[$leadSid]['sent_emails'])) {
            $leadEmail = strtolower(trim($leads[$leadSid]['sent_emails'][0]['to'] ?? ''));
        }
        if (!$leadEmail) {
            $leadEmail = strtolower(trim($_POST['email'] ?? ''));
        }
        if (!$leadEmail) { echo json_encode(['ok' => false, 'error' => 'no_email']); exit; }
        // Создаём entry в leads.json, если его нет
        if (!isset($leads[$leadSid])) $leads[$leadSid] = [];
        $leads[$leadSid]['email'] = $leadEmail;
        session_write_close();
        try {
            $foundEmails = [];
            $debugAll = ['lead_email_search' => $leadEmail];
            // Пробуем разные варианты подключения
            $mbox = false;
            $attempts = [
                "{{$imapHost}:{$imapPort}/imap/ssl/novalidate-cert}INBOX",
                "{{$imapHost}:{$imapPort}/imap/ssl}INBOX",
                "{{$imapHost}:{$imapPort}/imap/notls}INBOX",
                "{{$imapHost}:143/imap/notls}INBOX",
            ];
            foreach ($attempts as $mbStr) {
                $mbox = @imap_open($mbStr, $imapUser, $imapPass, OP_SILENT, 1);
                if ($mbox) break;
            }
            if (!$mbox) { echo json_encode(['ok' => false, 'error' => 'imap_connect_failed', 'debug' => $debugAll]); exit; }
            $totalInFolder = @imap_num_msg($mbox);
            $debugAll['total_msgs'] = $totalInFolder;
            if ($totalInFolder > 0) {
                $limit = min($totalInFolder, 200);
                $start = max(1, $totalInFolder - $limit + 1);
                $overview = @imap_fetch_overview($mbox, "$start:$totalInFolder");
                if ($overview && is_array($overview)) {
                    foreach ($overview as $h) {
                        $from = '';
                        if (!empty($h->from)) {
                            if (preg_match('/<([^>]+)>/', $h->from, $m)) $from = strtolower(trim($m[1]));
                            else $from = strtolower(trim($h->from));
                        }
                        if ($from !== $leadEmail) continue;
                        $msgNum = $h->msgno;
                        $subject = $h->subject ?? '';
                        if ($subject) {
                            $enc = @imap_mime_header_decode($subject);
                            $subject = '';
                            foreach ($enc as $e) $subject .= $e->text;
                        }
                        $partNum = 1;
                        $body = @imap_fetchbody($mbox, $msgNum, $partNum);
                        if ($body === '' || $body === '0') { $partNum = '1.1'; $body = @imap_fetchbody($mbox, $msgNum, $partNum); }
                        if ($body === '' || $body === '0') { $partNum = 0; $body = @imap_fetchbody($mbox, $msgNum, $partNum); }
                        $struct = @imap_fetchstructure($mbox, $msgNum);
                        $enc = $struct->encoding ?? 0;
                        if (!empty($struct->parts)) {
                            if ($partNum === 1 && !empty($struct->parts[0]->encoding)) $enc = $struct->parts[0]->encoding;
                            elseif ($partNum === '1.1' && !empty($struct->parts[0]->parts[0]->encoding)) $enc = $struct->parts[0]->parts[0]->encoding;
                        }
                        $map = [0 => '7bit', 1 => '8bit', 2 => 'binary', 3 => 'base64', 4 => 'quoted-printable', 5 => 'other'];
                        $encType = $map[$enc] ?? '7bit';
                        if ($encType === 'base64') $body = base64_decode($body);
                        elseif ($encType === 'quoted-printable') $body = quoted_printable_decode($body);
                        $body = trim(mb_strcut($body ?: '', 0, 10000));
                        $files = [];
                        if (!empty($struct->parts)) {
                            foreach ($struct->parts as $pid => $part) {
                                if (!empty($part->disposition) && strtolower($part->disposition) === 'attachment') {
                                    $fname = $part->dparameters[0]->value ?? $part->parameters[0]->value ?? "file{$pid}";
                                    $files[] = ['name' => @imap_utf8($fname) ?: $fname];
                                }
                            }
                        }
                        $udate = $h->udate ?? time();
                        $key = md5($subject . $body);
                        if (!isset($foundEmails[$key])) {
                            $foundEmails[$key] = [
                                '_dir' => 'in',
                                'subject' => mb_substr($subject ?: '(без темы)', 0, 200),
                                'body' => $body,
                                'time' => $udate,
                                'time_fmt' => date('d.m H:i', $udate),
                                'files' => $files,
                            ];
                        }
                    }
                }
            }
            @imap_close($mbox);
            // Сохраняем найденные письма в lead data
            if (!empty($foundEmails)) {
                if (!isset($leads[$leadSid]['incoming_emails'])) $leads[$leadSid]['incoming_emails'] = [];
                $existingKeys = [];
                foreach ($leads[$leadSid]['incoming_emails'] as $old) {
                    $existingKeys[md5(($old['subject']??'') . ($old['body']??''))] = true;
                }
                foreach ($foundEmails as $e) {
                    $key = md5($e['subject'] . ($e['body'] ?? ''));
                    if (!isset($existingKeys[$key])) {
                        $leads[$leadSid]['incoming_emails'][] = $e;
                        $existingKeys[$key] = true;
                    }
                }
                usort($leads[$leadSid]['incoming_emails'], fn($a, $b) => ($a['time'] ?? 0) - ($b['time'] ?? 0));
                saveLeads($leads);
            }
            // Собираем все письма для показа
            $allForDisplay = [];
            if (!empty($leads[$leadSid]['sent_emails'])) {
                foreach ($leads[$leadSid]['sent_emails'] as $e) {
                    $e['_dir'] = 'out';
                    $e['time_fmt'] = date('d.m H:i', $e['time'] ?? 0);
                    $allForDisplay[] = $e;
                }
            }
            foreach ($foundEmails as $e) { $allForDisplay[] = $e; }
            usort($allForDisplay, fn($a, $b) => ($a['time'] ?? 0) - ($b['time'] ?? 0));
            echo json_encode([
                'ok' => true,
                'emails' => $allForDisplay,
                'count' => count($foundEmails),
                'total' => count($allForDisplay),
                'refresh_sid' => $leadSid,
                'detail_id' => 'detail-' . md5($leadSid),
                'debug' => $debugAll,
            ]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'php_error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()]);
        }
        exit;

    case 'check_mail':
        set_time_limit(20);
        $leadSid = $_POST['sid'] ?? '';
        if (!$imapHost || !$imapUser || !$imapPass) {
            echo json_encode(['ok' => false, 'error' => 'no_imap_config']);
            exit;
        }
        if (!function_exists('imap_open')) {
            echo json_encode(['ok' => false, 'error' => 'no_imap_ext']);
            exit;
        }
        session_write_close();
        try {
            $newCount = 0;
            $foundEmailsCheck = 0;
            $leads = loadLeads();
            $emailToSid = [];
            foreach ($leads as $sid => $ld) {
                if (!empty($ld['email'])) $emailToSid[strtolower(trim($ld['email']))] = $sid;
            }
            $leadEmail = '';
            if ($leadSid && isset($leads[$leadSid]['email'])) {
                $leadEmail = strtolower(trim($leads[$leadSid]['email']));
            }
            if (!$leadEmail && $leadSid && !empty($leads[$leadSid]['sent_emails'])) {
                $leadEmail = strtolower(trim($leads[$leadSid]['sent_emails'][0]['to'] ?? ''));
            }
            if (!$leadEmail) {
                $leadEmail = strtolower(trim($_POST['email'] ?? ''));
            }
            if ($leadSid && $leadEmail) {
                if (!isset($leads[$leadSid])) $leads[$leadSid] = [];
                $leads[$leadSid]['email'] = $leadEmail;
            }
            $debug = ['lead_email' => $leadEmail, 'folders_ok' => [], 'from_examples' => []];
            $searchFolders = ['INBOX'];
            // Пробуем подключиться разными способами
            $connAttempts = [];
            foreach ($searchFolders as $folder) {
                $connAttempts[] = "{{$imapHost}:{$imapPort}/imap/ssl/novalidate-cert}{$folder}";
                $connAttempts[] = "{{$imapHost}:{$imapPort}/imap/ssl}{$folder}";
                $connAttempts[] = "{{$imapHost}:{$imapPort}/imap/notls}{$folder}";
            }
            $connected = false;
            foreach ($connAttempts as $mbStr) {
                $mbox = @imap_open($mbStr, $imapUser, $imapPass, OP_SILENT, 1);
                if ($mbox) { $connected = true; break; }
            }
            if (!$connected) {
                echo json_encode(['ok' => false, 'error' => 'imap_connect_failed', 'debug' => $debug]);
                exit;
            }
            $totalInFolder = @imap_num_msg($mbox);
            $debug['total_in_inbox'] = $totalInFolder;
            if ($totalInFolder > 0) {
                $limit = min($totalInFolder, 200);
                $start = max(1, $totalInFolder - $limit + 1);
                $overview = @imap_fetch_overview($mbox, "$start:$totalInFolder");
                if ($overview && is_array($overview)) {
                    $unseen = 0;
                    foreach ($overview as $h) {
                        if (!$h->seen) $unseen++;
                    }
                    $debug['unseen'] = $unseen;
                    foreach ($overview as $h) {
                        $from = '';
                        if (!empty($h->from)) {
                            if (preg_match('/<([^>]+)>/', $h->from, $m)) $from = strtolower(trim($m[1]));
                            else $from = strtolower(trim($h->from));
                        }
                        if (count($debug['from_examples']) < 20) $debug['from_examples'][] = $from ?: '(empty)';
                        $subject = '';
                        if ($h->subject) {
                            $enc = @imap_mime_header_decode($h->subject);
                            foreach ($enc as $e) $subject .= $e->text;
                        }
                        if (!$subject) $subject = '(без темы)';
                        $fromLower = strtolower(trim($from));
                        $matchedSid = null;
                        if ($leadSid && $leadEmail && $fromLower === $leadEmail) {
                            $matchedSid = $leadSid;
                        } elseif (!$leadSid) {
                            foreach ($emailToSid as $eSid => $eAddr) {
                                if ($fromLower === $eAddr) { $matchedSid = $eSid; break; }
                            }
                        }
                        if ($matchedSid) {
                            $msgNum = $h->msgno;
                            $partNum = 1;
                            $body = @imap_fetchbody($mbox, $msgNum, $partNum);
                            if ($body === '' || $body === '0') { $partNum = '1.1'; $body = @imap_fetchbody($mbox, $msgNum, $partNum); }
                            if ($body === '' || $body === '0') { $partNum = 0; $body = @imap_fetchbody($mbox, $msgNum, $partNum); }
                            if (!empty($body)) {
                                $struct = @imap_fetchstructure($mbox, $msgNum);
                                $enc = $struct->encoding ?? 0;
                                if (!empty($struct->parts)) {
                                    if ($partNum === 1 && !empty($struct->parts[0]->encoding)) $enc = $struct->parts[0]->encoding;
                                    elseif ($partNum === '1.1' && !empty($struct->parts[0]->parts[0]->encoding)) $enc = $struct->parts[0]->parts[0]->encoding;
                                }
                                $map = [0 => '7bit', 1 => '8bit', 2 => 'binary', 3 => 'base64', 4 => 'quoted-printable', 5 => 'other'];
                                $encType = $map[$enc] ?? '7bit';
                                if ($encType === 'base64') $body = base64_decode($body);
                                elseif ($encType === 'quoted-printable') $body = quoted_printable_decode($body);
                                $body = trim(mb_strcut($body ?: '', 0, 5000));
                            }
                            if (!isset($leads[$matchedSid]['incoming_emails'])) $leads[$matchedSid]['incoming_emails'] = [];
                            $emailKey = md5($subject . ($body ?? ''));
                            $isDup = false;
                            foreach ($leads[$matchedSid]['incoming_emails'] as $old) {
                                if (md5(($old['subject']??'') . ($old['body']??'')) === $emailKey) { $isDup = true; break; }
                            }
                            if (!$isDup) {
                                $leads[$matchedSid]['incoming_emails'][] = ['from' => $from, 'subject' => mb_substr($subject, 0, 200), 'body' => $body, 'time' => ($h->udate ?? time())];
                                if (!isset($leads[$matchedSid]['notes'])) $leads[$matchedSid]['notes'] = [];
                                $leads[$matchedSid]['notes'][] = ['text' => "📨 Ответ от {$from}: {$subject}", 't' => time(), 'type' => 'system'];
                                $newCount++;
                            }
                        }
                        $foundEmailsCheck++;
                    }
                }
            }
            @imap_close($mbox);
            if ($newCount > 0) saveLeads($leads);
            echo json_encode(['ok' => true, 'found' => $foundEmailsCheck, 'new' => $newCount, 'sid' => $leadSid, 'detail_id' => $leadSid ? 'detail-' . md5($leadSid) : '', 'debug' => $debug]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'php_error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()]);
        }
        break;

    case 'check_new':
        $fLeadsFile = "$dataDir/leads.json";
        $leads = file_exists($fLeadsFile) ? (json_decode(file_get_contents($fLeadsFile), true) ?: []) : [];
        $cnt = count($leads);
        $prev = (int)($_SESSION['_lcp'] ?? 0);
        $_SESSION['_lcp'] = $cnt;
        echo json_encode(['ok' => true, 'count' => $cnt, 'new' => ($prev > 0 && $cnt > $prev)]);
        break;

    case 'check_reminders':
        $now = time();
        $list = file_exists($remindersFile) ? (json_decode(file_get_contents($remindersFile), true) ?: []) : [];
        $due = [];
        foreach ($list as $r) {
            if (!($r['done'] ?? false) && ($r['due'] ?? 0) <= $now) $due[] = ['id' => $r['id'], 'text' => $r['text']];
        }
        echo json_encode(['ok' => true, 'due' => $due]);
        break;

    default:
        echo json_encode(['ok' => false, 'error' => 'unknown_action']);
}

// === SMTP-ОТПРАВКА ===
function smtpMail($to, $subject, $body, $headers, $fromEmail, $host, $port, $user, $pass) {
    $errNo = 0; $errStr = '';
    $ssl = (strpos($port, '465') !== false);
    $remote = $ssl ? "ssl://{$host}:{$port}" : "{$host}:{$port}";
    $s = @stream_socket_client($remote, $errNo, $errStr, 15);
    if (!$s) return false;
    $buf = fread($s, 512);

    fwrite($s, "EHLO ob-kub.ru\r\n");
    $buf = '';
    while ($line = fgets($s, 512)) {
        $buf .= $line;
        if (substr($line, 3, 1) === ' ') break;
    }

    // STARTTLS для 587
    if (strpos($port, '587') !== false && stripos($buf, 'STARTTLS') !== false) {
        fwrite($s, "STARTTLS\r\n");
        fgets($s, 512);
        stream_socket_enable_crypto($s, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        fwrite($s, "EHLO ob-kub.ru\r\n");
        while ($line = fgets($s, 512)) {
            if (substr($line, 3, 1) === ' ') break;
        }
    }

    fwrite($s, "AUTH LOGIN\r\n");
    fgets($s, 512);
    fwrite($s, base64_encode($user) . "\r\n");
    fgets($s, 512);
    fwrite($s, base64_encode($pass) . "\r\n");
    $r = fgets($s, 512);
    if (strpos($r, '235') === false) { fclose($s); return false; }

    fwrite($s, "MAIL FROM:<{$fromEmail}>\r\n");
    fgets($s, 512);
    fwrite($s, "RCPT TO:<{$to}>\r\n");
    fgets($s, 512);

    fwrite($s, "DATA\r\n");
    fgets($s, 512);
    fwrite($s, "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n");
    fwrite($s, $headers);
    fwrite($s, "\r\n");
    fwrite($s, $body);
    fwrite($s, "\r\n.\r\n");
    fgets($s, 512);

    fwrite($s, "QUIT\r\n");
    fclose($s);
    return true;
}
