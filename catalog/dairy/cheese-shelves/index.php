<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/../../dairy-data.php';
require __DIR__ . '/../../catalog-styles.php';

$data = $dairyData['cheese-shelves'];
$sorted = $data['volumes'];
$specUnit = $data['spec_unit'] ?? 'шт';
$specLabel = $data['spec_label'] ?? 'Количество';
$h1 = $data['h1'];
$metaTitle = $data['title'];
$metaDesc = $data['desc'];
$baseUrl = '/catalog/dairy/cheese-shelves/';
$canonical = 'https://ob-kub.ru' . $baseUrl;
$bodyClass = 'brewery-page cct-page';

require __DIR__ . '/../../layout-start.php';
?>
<section class="list-hero">
<div class="container">
<div class="breadcrumbs">
<a href="/">Главная</a><span class="ep">/</span>
<a href="/catalog/">Каталог</a><span class="ep">/</span>
<a href="/catalog/dairy/">Молочное оборудование</a><span class="ep">/</span>
<span class="current"><?= htmlspecialchars($h1) ?></span>
</div>
<div class="list-hero-inner">
<div class="list-hero-img"><img src="/<?= htmlspecialchars($data['image']) ?>" alt="<?= htmlspecialchars($data['name']) ?>"></div>
<div class="list-hero-text">
<h1><?= htmlspecialchars($h1) ?></h1>
<p class="hero-sub"><?= htmlspecialchars($metaDesc) ?></p>
<div class="hero-tags"><?php 
$shuffled = $data['features']; shuffle($shuffled); $c = 0;
foreach ($shuffled as $f): if ($c >= 3) break; ?><span><?= htmlspecialchars($f) ?></span><?php $c++; endforeach; ?>
</div>
<div class="hero-trust">AISI 304/316 · 18+ лет на рынке · Доставка по РФ</div>
</div>
</div>
</div>
</section>
<section class="container">
<div class="section-head">
<h2 class="section-title">Выберите количество полок</h2>
<p class="section-desc">Нажмите на карточку для выбора</p>
</div>
<div class="volumes-grid">
<?php foreach ($sorted as $vol): $s = $data['specs'][$vol]; $price = $s['price']; $priceStr = $price >= 1000000 ? number_format($price/1000000,1,'.','').' млн ₽' : ($price >= 1000 ? number_format($price/1000,0,'.','').' тыс ₽' : number_format($price,0,'.',' ').' ₽'); ?>
<div class="vol-card" style="cursor:default">
<div class="vol-card-body">
<div class="vol-label"><?= htmlspecialchars($specLabel) ?></div>
<div class="vol-value"><?= $vol ?><span class="vol-unit"> <?= $specUnit ?></span></div>
<div class="price">от <?= $priceStr ?></div>
</div>
<div class="vol-card-footer"><span class="btn-elect" style="cursor:default">Выбрать</span></div>
</div>
<?php endforeach; ?>
</div>
</section>
<section class="cct-adv">
<div class="cct-adv-item"><div class="cct-adv-icon">🏭</div><div class="cct-adv-title">18+ лет на рынке</div><div class="cct-adv-text">с 2008 года</div></div>
<div class="cct-adv-item"><div class="cct-adv-icon">⚙️</div><div class="cct-adv-title">Свой цех</div><div class="cct-adv-text">2000 м² в Краснодаре</div></div>
<div class="cct-adv-item"><div class="cct-adv-icon">📦</div><div class="cct-adv-title">Доставка по РФ</div><div class="cct-adv-text">любой ТК или нашим транспортом</div></div>
<div class="cct-adv-item"><div class="cct-adv-icon">✅</div><div class="cct-adv-title">Гарантия 12 мес</div><div class="cct-adv-text">сертификат ТР ЕАЭС</div></div>
</section>
<section class="container" style="max-width:600px;margin:0 auto 48px">
<div class="cct-form">
<h2>📩 Получить расчёт стеллажей для созревания сыра</h2>
<p class="cft-sub">Укажите контакты — подготовим КП с точной стоимостью и сроками</p>
<form method="post" action="/php/send.php">
<input type="hidden" name="form_type" value="item">
<input type="hidden" name="product" value="Стеллажи для созревания сыра">
<div class="row">
<div><label>Ваше имя</label><input type="text" name="name" required></div>
<div><label>Телефон</label><input type="tel" name="phone" required></div>
</div>
<div class="full"><label>Email</label><input type="email" name="email" required></div>
<div class="full"><label>Количество полок</label>
<select name="quantity">
<?php foreach ($sorted as $vol): ?>
<option value="<?= $vol ?> секций"><?= $vol ?> секций (от <?= number_format($data['specs'][$vol]['price'], 0, '.', ' ') ?> ₽)</option>
<?php endforeach; ?>
</select>
</div>
<div class="full"><label>Комментарий</label><textarea name="comment" rows="3" placeholder="Дополнительные требования, размеры камеры, материал..."></textarea></div>
<button type="submit" class="submit-btn">📩 Получить расчёт</button>
</form>
</div>
</section>
<?php require __DIR__ . '/../../layout-end.php'; ?>
