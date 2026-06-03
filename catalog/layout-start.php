<?php
header('Content-Type: text/html; charset=utf-8');
$bodyClass = $bodyClass ?? 'brewery-page catalog-page';
$inlineStyles = $inlineStyles ?? '';
$extraHead = $extraHead ?? '';
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
<title><?= htmlspecialchars($metaTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
<link rel="canonical" href="<?= $canonical ?>">
<?= $extraHead ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style-original.css">
<link rel="stylesheet" href="/css/catalog-mobile.css">
<link rel="stylesheet" href="/css/header.css">
<link rel="icon" href="/favicon.png">
<style><?= $inlineStyles ?></style>
</head>
<body class="<?= htmlspecialchars($bodyClass) ?>">
<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; ?>
<main>
