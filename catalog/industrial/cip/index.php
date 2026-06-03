<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/../../industrial-data.php';

$data = $industrialCip;

$pageTitle = $data['title'];
$pageDesc = $data['desc'];
$h1 = $data['h1'];
$image = '/' . $data['image'];
$features = $data['features'];

header('Content-Type: text/html; charset=utf-8');

$schema = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $data['name'],
    'description' => $data['desc'],
    'image' => 'https://ob-kub.ru' . $image,
    'brand' => ['@type' => 'Brand', 'name' => 'ОБОРУДОВАНИЕ КУБАНИ'],
    'category' => 'Промышленное оборудование',
    'material' => 'Нержавеющая сталь AISI 304',
], JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
<title><?= htmlspecialchars($pageTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
<link rel="canonical" href="https://ob-kub.ru/catalog/industrial/cip/">
<script type="application/ld+json"><?= $schema ?></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style-original.css">
<link rel="stylesheet" href="/css/catalog-mobile.css">
<link rel="icon" href="/favicon.png">
<style>*,*::before,*::after{box-sizing:border-box}.cct-page{font-family:'Source Sans Pro',sans-serif;color:#2c3e50;background:#f5f6f8}.cct-page .container{max-width:1100px;margin:0 auto;padding:0 24px}.product-hero{background:linear-gradient(135deg,#2b2b39 0%,#1a1a26 100%);position:relative;overflow:hidden;padding:60px 0 50px}.product-hero::before{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background:url(data:image/vg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+) repeat;pointer-events:none}.product-hero::after{content:'';position:absolute;top:-40%;left:-20%;width:70%;height:200%;background:radial-gradient(ellipse at center,rgba(247,124,42,0.12) 0%,transparent 70%);pointer-events:none}.product-hero .breadcrumbs{padding:0 0 12px;font-size:12px;color:rgba(255,255,255,.3)}.product-hero .breadcrumbs a{color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s}.product-hero .breadcrumbs a:hover{color:#F77C2A}.product-hero .breadcrumbs .ep{margin:0 5px;color:rgba(255,255,255,.12)}.product-hero .breadcrumbs .current{color:rgba(255,255,255,.55)}.hero-flex{display:flex;gap:40px;align-items:center;position:relative;z-index:1}.hero-info{flex:1;min-width:0}.hero-info h1{font-size:28px;font-weight:800;color:#fff;margin:0 0 12px;text-transform:uppercase;letter-spacing:.5px}.hero-info .hero-ub{font-size:15px;color:rgba(255,255,255,.65);line-height:1.6;margin-bottom:16px}.hero-info .hero-price{font-size:26px;font-weight:700;color:#F77C2A;margin-bottom:4px}.hero-info .hero-hint{font-size:12px;color:rgba(255,255,255,.4)}.hero-img{flex:0 0 320px;height:260px;border-radius:12px;overflow:hidden;background:#f0f2f5;display:flex;align-items:center;justify-content:center}.hero-img img{max-width:100%;max-height:100%;display:block}.specs-section{padding:36px 0}.specs-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px}.pec-card{background:#fff;border-radius:10px;padding:16px 18px;box-shadow:0 2px 8px rgba(0,0,0,.04);border-top:3px solid #F77C2A}.pec-card .c-label{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.4px;margin-bottom:2px}.pec-card .c-value{font-size:18px;font-weight:700;color:#1a1a26}.pec-card .c-unit{font-size:12px;color:#888;font-weight:400}.about-section{padding:0 0 36px}.about-card{background:#fff;border-radius:12px;padding:28px;box-shadow:0 2px 8px rgba(0,0,0,.04)}.about-card h2{font-size:22px;font-weight:700;color:#1a1a26;margin:0 0 14px}.about-card p{font-size:14px;color:#555;line-height:1.7;margin:0 0 12px}.about-card ul{list-style:none;padding:0;display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:8px}.about-card ul li{padding:8px 12px;background:#f9f9fb;border-radius:6px;font-size:13px;color:#444;border-left:3px solid #F77C2A}.faq-section{padding:0 0 48px}.faq-card{background:#fff;border-radius:12px;padding:28px;box-shadow:0 2px 8px rgba(0,0,0,.04)}.faq-card h2{font-size:22px;font-weight:700;color:#1a1a26;margin:0 0 16px}.faq-item{border-bottom:1px solid #eee;padding:14px 0}.faq-item:last-child{border-bottom:none}.faq-q{display:flex;justify-content:pace-between;align-items:center;cursor:pointer;font-size:14px;font-weight:600;color:#1a1a26;user-elect:none}.faq-q:hover{color:#F77C2A}.faq-arrow{transition:transform .2s;font-size:10px;color:#F77C2A}.faq-q.open .faq-arrow{transform:rotate(180deg)}.faq-a{max-height:0;overflow:hidden;transition:max-height .3s ease,padding .3s ease;padding:0 12px 0 0;font-size:14px;color:#555;line-height:1.6}.faq-a.how{max-height:500px;padding-top:10px}.form-section{padding:0 0 48px}.form-card{background:linear-gradient(135deg,#2b2b39 0%,#1a1a26 100%);border-radius:12px;padding:36px;position:relative;overflow:hidden}.form-card::before{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background:url(data:image/vg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+) repeat;pointer-events:none}.form-card>*{position:relative;z-index:1}.form-card h2{font-size:22px;font-weight:700;color:#fff;margin:0 0 6px}.form-card .form-ub{font-size:13px;color:rgba(255,255,255,.55);margin-bottom:20px}.form-row{display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap}.form-row input,.form-row textarea{flex:1;min-width:200px;padding:10px 12px;border:none;border-radius:6px;font-size:14px;font-family:inherit;background:rgba(255,255,255,.12);color:#fff;outline:none;transition:background .2s}.form-row input::placeholder,.form-row textarea::placeholder{color:rgba(255,255,255,.4)}.form-row input:focus,.form-row textarea:focus{background:rgba(255,255,255,.2)}.form-row textarea{min-height:70px;resize:vertical}.form-row .checkbox-wrap{display:flex;align-items:center;gap:6px;font-size:12px;color:rgba(255,255,255,.6)}.form-row .checkbox-wrap input{width:15px;height:15px;min-width:15px;accent-color:#F77C2A}.form-row .submit-btn{padding:10px 28px;background:#F77C2A;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:700;cursor:pointer;transition:background .2s;min-width:180px}.form-row .submit-btn:hover{background:#e06a1a}/* Mega Menu */.mega-menu-wrap{position:relative;display:inline-block}.nav .mega-menu-link{display:inline-flex;align-items:center;gap:4px}.nav .mega-menu-link:hover{color:#F77C2A}.mega-arrow{font-size:9px;transition:transform .25s;display:inline-block;margin-left:2px}.mega-menu-wrap:hover .mega-arrow,.mega-menu-wrap.active .mega-arrow{transform:rotate(180deg)}.mega-menu{position:absolute;top:100%;left:50%;transform:translateX(-50%) translateY(10px);background:#fff;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,.18),0 4px 16px rgba(0,0,0,.08);padding:24px;display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;min-width:820px;z-index:1000;opacity:0;visibility:hidden;transition:all .25s ease;margin-top:8px;border:1px solid rgba(0,0,0,.04)}.mega-menu::before{content:'';position:absolute;top:-8px;left:50%;transform:translateX(-50%);border:8px solid transparent;border-bottom-color:#fff}.mega-menu::after{content:'';position:absolute;top:0;left:24px;right:24px;height:3px;background:linear-gradient(90deg,#F77C2A,#FF8C42);border-radius:0 0 3px 3px}.mega-menu-wrap:hover .mega-menu,.mega-menu-wrap.active .mega-menu{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0)}.mega-col h3{font-size:11px;font-weight:700;color:#2b2b39;margin:0 0 10px;padding-bottom:7px;border-bottom:2px solid #F77C2A;text-transform:uppercase;letter-spacing:.4px}.mega-col a{display:block;padding:4px 0;font-size:13px;color:#555;text-decoration:none;transition:color .2s;line-height:1.4;white-space:normal}.mega-col a:hover{color:#F77C2A}.mega-aux{grid-column:1 / -1;margin-top:4px;padding-top:14px;border-top:1px solid #eee;text-align:center}.mega-aux a{display:inline-block;padding:8px 18px;font-size:13px;color:#F77C2A;text-decoration:none;font-weight:600;border-radius:8px;transition:background .2s}.mega-aux a:hover{background:#fff8f0}.cct-page .header{overflow:visible !important}@media.mega-col-link{display:block;text-decoration:none;color:inherit;padding:0;font-size:inherit;line-height:inherit}.mega-col-link:hover h3{color:#F77C2A}@media</style>
</head>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/header.php'; ?>
<main>
<section class="product-hero">
<div class="container">
<div class="breadcrumbs">
<a href="/">Главная</a><span class="ep">/</span>
<a href="/catalog/">Каталог</a><span class="ep">/</span>
<a href="/catalog/industrial/">Промышленное оборудование</a><span class="ep">/</span>
<span class="current"><?= htmlspecialchars($h1) ?></span>
</div>
<div class="hero-flex">
<div class="hero-info">
<h1><?= htmlspecialchars($h1) ?></h1>
<div class="hero-ub"><?= htmlspecialchars($data['desc']) ?></div>
<div class="hero-price">от 450 000 ₽</div>
<div class="hero-hint">* Цена зависит от конфигурации</div>
</div>
<div class="hero-img"><img 
src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($data['name']) ?>"></div>
</div>
</div>
</section>

<section class="specs-section">
<div class="container">
<div class="specs-grid">
<div class="pec-card"><div class="c-label">Материал</div><div class="c-value">AISI 304</div></div>
<div class="pec-card"><div class="c-label">Контуры</div><div class="c-value">1–3 <span class="c-unit">шт</span></div></div>
<div class="pec-card"><div class="c-label">Нагрев</div><div class="c-value">Пар / электричество</div></div>
<div class="pec-card"><div class="c-label">Управление</div><div class="c-value">ПЛК + сенсорная панель</div></div>
<div class="pec-card"><div class="c-label">Исполнение</div><div class="c-value">модульное</div></div>
</div>
</div>
</section>

<section class="about-section">
<div class="container">
<div class="about-card">
<h2>Об оборудовании</h2>
<p><?= htmlspecialchars($data['desc']) ?></p>
<p>CIP-станции (Clean-In-Place) предназначены для автоматизированной безразборной мойки технологического оборудования: танков, трубопроводов, теплообменников, розлива. Обеспечивают циркуляцию моющего раствора (щёлочь, кислота) и воды ополаскивания по замкнутому контуру.</p>
<p>Станции изготавливаются в модульном исполнении на раме из AISI 304. Возможно исполнение с 1, 2 или 3 моечными контурами, с нагревом от пара или ТЭНов. Автоматика на базе ПЛК обеспечивает полный контроль процесса мойки по заданным программам.</p>
<h3 
style="margin-top:16px;margin-bottom:10px;font-size:16px;font-weight:600;color:#1a1a26">Особенности:</h3>
<ul>
<?php foreach ($features as $f): ?>
<li><?= htmlspecialchars($f) ?></li>
<?php endforeach; ?>
</ul>
</div>
</div>
</section>

<section class="faq-section">
<div class="container">
<div class="faq-card">
<h2>Часто задаваемые вопросы</h2>
<div class="faq-item">
<div class="faq-q" onclick="toggleFaq(this)">Сколько контуров нужно для мойки?<span class="faq-arrow">▼</span></div>
<div class="faq-a">1 контур — для небольших производств (мойка одним раствором). 2 контура — для раздельной подачи щёлочи и кислоты. 3 контура — для крупных производств с параллельной мойкой разных групп оборудования.</div>
</div>
<div class="faq-item">
<div class="faq-q" onclick="toggleFaq(this)">Какой нагрев используется?<span class="faq-arrow">▼</span></div>
<div class="faq-a">Доступны два варианта: нагрев паром (при наличии паровой котельной) или электрический нагрев (ТЭНы). Мощность подбирается под объём и температуру мойки.</div>
</div>
<div class="faq-item">
<div class="faq-q" onclick="toggleFaq(this)">Какие программы мойки доступны?<span class="faq-arrow">▼</span></div>
<div class="faq-a">Стандартные программы: предополаскивание, щелочная мойка, промежуточное ополаскивание, кислотная мойка, финальное ополаскивание. Все параметры (время, температура, концентрация) настраиваются.</div>
</div>
<div class="faq-item">
<div class="faq-q" onclick="toggleFaq(this)">Какие сроки изготовления?<span class="faq-arrow">▼</span></div>
<div class="faq-a">Стандартные CIP-станции — от 10-14 рабочих дней. Сложные проекты с автоматикой — от 20 дней.</div>
</div>
</div>
</div>
</section>

<section class="form-section">
<div class="container">
<div class="form-card">
<h2>Получить расчёт стоимости</h2>
<div class="form-ub">Оставьте заявку — мы подберём конфигурацию CIP-станции и пришлём КП</div>
<form method="post" class="quick-form" action="/php/send.php">
<input type="hidden" name="form_type" value="industrial-cip">
<input type="hidden" name="product" value="CIP-станция">
<input type="hidden" name="quantity" value="1">
<div class="form-row">
<input type="text" name="name" placeholder="Ваше имя *" required>
<input type="tel" name="phone" placeholder="Телефон *" required class="phone-mask">
</div>
<div class="form-row">
<input type="email" name="email" placeholder="Email">
<input type="text" name="quantity_display" placeholder="Количество контуров (1-3)">
</div>
<div class="form-row">
<textarea name="comment" placeholder="Тип нагрева, количество танков, автоматика..."></textarea>
</div>
<div class="form-row">
<div class="checkbox-wrap">
<input type="checkbox" name="agreement" value="1" required>
<span>Согласен на обработку персональных данных</span>
</div>
<input type="hidden" id="csrfToken" name="csrf" value="">
				<button type="submit" class="ssubmit-btn">Отправить →</button>
</div>
<div class="form-success-message"></div>
</form>
</div>
</div>
</section>
</main>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/footer.php'; ?>
</html>
