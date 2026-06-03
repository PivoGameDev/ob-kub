<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
<title>Спасибо за заявку — ОБОРУДОВАНИЕ КУБАНИ</title>
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style-original.css">
<link rel="icon" href="favicon.png">
<style>
*,*::before,*::after{box-sizing:border-box}
body{margin:0;padding:0!important;font-family:'Source Sans Pro',sans-serif;color:#2c3e50;background:#f5f6f8;-webkit-font-smoothing:antialiased;overflow-x:hidden}

.header{position:relative!important;background:#fff!important;border-bottom:none!important;padding:0!important;min-height:64px!important;display:flex!important;align-items:center!important;box-shadow:0 1px 4px rgba(0,0,0,.08)!important;overflow:visible!important}
.header .container{max-width:1200px!important;padding:0 24px!important;margin:0 auto!important;width:100%!important}
.header-top{padding:0!important;display:flex!important;align-items:center!important;justify-content:space-between!important;gap:20px!important}
.header .logo-section{display:flex!important;align-items:center!important;flex-shrink:0!important}
.header .logo-img{height:58px!important;width:auto!important;padding:0!important;margin:0!important;display:block!important}
.nav{position:static!important;transform:none!important;display:flex!important;align-items:center!important;gap:24px!important;flex-wrap:nowrap!important;background:transparent!important;border:none!important}
.nav>a{color:#333!important;font-size:14px!important;font-weight:600!important;padding:6px 0!important;white-space:nowrap!important;text-decoration:none!important;border:none!important;background:transparent!important}
.nav>a:hover,.cat-trigger:hover{color:#F77C2A!important;background:transparent!important}
.cat-trigger{color:#333!important;font-size:14px!important;font-weight:600!important;padding:6px 0!important;text-decoration:none!important;cursor:pointer!important;display:inline-flex!important;align-items:center!important;gap:4px!important;background:none!important;border:none!important;font-family:inherit!important}
.cat-trigger:hover{color:#F77C2A!important}
.cat-trigger.active{color:#F77C2A!important}
.search-trigger{color:#333!important;font-size:14px!important;font-weight:600!important;padding:6px 0!important;cursor:pointer!important;display:inline-flex!important;align-items:center!important;gap:4px!important;background:none!important;border:none!important;font-family:inherit!important;white-space:nowrap!important}
.search-trigger:hover{color:#F77C2A!important}
.search-trigger.active{color:#F77C2A!important}
.srch-ico{width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:2px;position:relative;top:-1px}
.header-right{display:flex!important;align-items:center!important;gap:12px!important;flex-shrink:0!important}
.header .phone{padding:0!important;margin:0!important;font-size:15px!important;font-weight:600!important}
.header .phone a{color:#333!important;text-decoration:none!important;white-space:nowrap!important}
.header .phone a:hover{color:#F77C2A!important}
.header .consult-btn{background:#F77C2A!important;color:#fff!important;border:none!important;font-weight:600!important;font-size:12px!important;letter-spacing:.4px!important;border-radius:6px!important;padding:8px 16px!important;height:36px!important;cursor:pointer!important;white-space:nowrap!important;min-width:auto!important;text-transform:uppercase!important}
.header .consult-btn:hover{background:#e06a15!important;transform:none!important;box-shadow:none!important}
.header-menu-row,.header-back-row{display:none!important}

.catalog-dropdown{border-top:2px solid #F77C2A;overflow:hidden;max-height:0;opacity:0;padding:0;transition:max-height .4s ease,opacity .3s ease,padding .3s ease;position:absolute;left:0;right:0;top:100%;background:#fff;z-index:999;box-shadow:0 8px 30px rgba(0,0,0,.12)}
.catalog-dropdown.active{max-height:420px;opacity:1;padding:20px 24px 24px}
.catalog-dropdown .cat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:28px}
.catalog-dropdown .cat-col h3{font-size:11px;font-weight:700;color:#F77C2A;text-transform:uppercase;letter-spacing:.5px;margin:0 0 10px;padding:0 0 6px;border-bottom:1px solid #f0f0f0}
.catalog-dropdown .cat-col a{display:block;font-size:13px;color:#555;text-decoration:none;padding:4px 0;line-height:1.5;transition:color .2s}
.catalog-dropdown .cat-col a:hover{color:#F77C2A}
.catalog-overlay{position:fixed;top:0;left:0;width:100%;height:100dvh;background:rgba(0,0,0,.55);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);z-index:99999;display:flex;align-items:stretch;justify-content:center;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s}
.catalog-overlay.active{opacity:1;visibility:visible}
.catalog-overlay-inner{background:#fff;width:100%;max-width:520px;display:flex;flex-direction:column;height:100dvh}
.catalog-overlay-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #eee;flex-shrink:0;background:#fff}
.catalog-overlay-header h2{font-size:16px;font-weight:700;color:#1a1a26;margin:0;padding:0;border:none}
.catalog-overlay-close{background:none;border:none;font-size:26px;cursor:pointer;color:#999;line-height:1;padding:0;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:6px;transition:background .2s}
.catalog-overlay-close:hover{background:#f5f5f5;color:#333}
.catalog-overlay-body{flex:1;overflow-y:auto;padding:16px 20px 20px;scrollbar-width:thin}
.catalog-overlay-body .cat-col{margin-bottom:16px}
.catalog-overlay-body .cat-col h3{font-size:13px;font-weight:700;color:#F77C2A;margin:0 0 6px;padding:0;border:none;text-transform:none;letter-spacing:0}
.catalog-overlay-body .cat-col h3 a{color:#F77C2A;text-decoration:none}
.catalog-overlay-body .cat-col a{display:block;font-size:14px;color:#555;text-decoration:none;padding:5px 0;line-height:1.4;border-bottom:1px solid #f5f5f5}
.catalog-overlay-body .cat-col a:last-child{border-bottom:none}
.catalog-overlay-body .cat-col a:hover{color:#F77C2A}

.search-dropdown{display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border-top:2px solid #F77C2A;z-index:9999;box-shadow:0 8px 30px rgba(0,0,0,.12)}
.search-dropdown.active{display:block}
.search-inner{max-width:600px;margin:0 auto;padding:20px 24px}
.search-field-wrap{position:relative;display:flex;align-items:center;background:#f4f5f7;border-radius:10px;padding:0 14px;border:1px solid #e2e4e8;transition:border-color .2s}
.search-field-wrap:focus-within{border-color:#F77C2A;background:#fff}
.search-icon{flex-shrink:0;margin-right:10px}
.search-input{flex:1;border:none;background:transparent;padding:14px 0;font-size:15px;font-family:inherit;outline:none;color:#333}
.search-input::placeholder{color:#aaa}
.search-clear{display:none;font-size:22px;color:#999;cursor:pointer;padding:0 0 0 10px;line-height:1;user-select:none}
.search-clear.visible{display:block}
.search-clear:hover{color:#333}
.search-results{padding:10px 0 0;max-height:340px;overflow-y:auto}
.search-results a{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f0f0f0;text-decoration:none;color:inherit}
.search-results a:hover{background:#fafafa}
.sr-info{flex:1;min-width:0}
.sr-title{font-size:14px;font-weight:600;color:#1a1a26}
.sr-spec{font-size:11px;color:#888}
.sr-price{font-size:14px;font-weight:700;color:#F77C2A;white-space:nowrap;margin-left:8px}

.thanks-section{display:flex;align-items:center;justify-content:center;min-height:calc(100vh - 64px - 200px);padding:60px 24px}
.thanks-card{text-align:center;max-width:520px;width:100%}
.thanks-icon{width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,#2ecc71,#27ae60);display:flex;align-items:center;justify-content:center;margin:0 auto 28px;box-shadow:0 8px 32px rgba(46,204,113,.3)}
.thanks-icon svg{width:40px;height:40px;stroke:#fff;stroke-width:3;fill:none;stroke-linecap:round;stroke-linejoin:round}
.thanks-title{font-size:30px;font-weight:800;color:#1a1a26;margin:0 0 10px}
.thanks-sub{font-size:16px;color:#666;line-height:1.6;margin:0 0 8px}
.thanks-detail{font-size:14px;color:#999;line-height:1.5;margin:0 0 36px}
.thanks-btn{display:inline-flex;align-items:center;gap:8px;background:#F77C2A;color:#fff;border:none;font-weight:700;font-size:14px;letter-spacing:.3px;border-radius:10px;padding:14px 32px;cursor:pointer;text-decoration:none;transition:background .2s,transform .15s}
.thanks-btn:hover{background:#e06a15;transform:translateY(-2px)}
.thanks-btn svg{width:16px;height:16px;stroke:currentColor;stroke-width:2.5;fill:none}
.thanks-timer{font-size:13px;color:#bbb;margin-top:20px}

.db-footer{background:linear-gradient(135deg,#2b2b39,#1a1a26);padding:44px 0 0;border-top:1px solid rgba(255,255,255,.05)}
.db-footer-inner{display:grid;grid-template-columns:repeat(3,1fr);gap:30px}
.db-footer-col h3{font-size:12px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.6px;margin-bottom:14px}
.db-footer-col a{display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s}
.db-footer-col a:hover{color:#F77C2A}
.db-footer-bot{padding:24px 0;margin-top:36px;border-top:1px solid rgba(255,255,255,.06);font-size:12px;color:rgba(255,255,255,.3);text-align:center}

@media(max-width:1024px){
.header{position:relative!important;height:auto!important;min-height:auto!important}
.header .container{padding:0 16px!important}
.header-top{gap:8px!important;padding:4px 0!important}
.header .logo-img{height:48px!important}
.header .phone{font-size:13px!important}
.header .consult-btn{font-size:10px!important;padding:5px 10px!important;height:28px!important}
.nav{gap:10px!important}
.nav>a,.cat-trigger,.search-trigger{font-size:12px!important;padding:3px 0!important}
.header-right{gap:6px!important}
}

@media(max-width:700px){
.header{position:relative!important;height:auto!important;min-height:auto!important;flex-direction:column!important}
.header .container{padding:0 12px!important}
.header-top{flex-wrap:wrap!important;gap:1px!important;padding:2px 0!important}
.header .logo-img{height:36px!important}
.header .phone{font-size:11px!important}
.header .consult-btn{font-size:9px!important;padding:3px 6px!important;height:22px!important}
.nav{order:3!important;width:100%!important;display:flex!important;flex-wrap:wrap!important;justify-content:center!important;gap:2px 8px!important;padding:0!important;border-top:1px solid #eee!important;margin-top:0!important}
.nav>a,.cat-trigger,.search-trigger{font-size:10px!important;padding:0!important;line-height:1.2!important}
.header-right{gap:4px!important}
.db-footer-inner{grid-template-columns:1fr;gap:20px}
}

@media(max-width:768px){
.header-top{display:none!important}
.header .container{max-width:100%!important;width:100%!important;padding:0!important;margin:0!important}
.header{display:block!important;width:100%!important;position:sticky!important;top:0;z-index:1000;background:#fff;min-height:0!important}
.mobile-header{display:flex;align-items:center;justify-content:space-between;height:56px;padding:0 20px;background:#fff;width:100%;box-sizing:border-box}
.mobile-header .mobile-logo-wrap{display:flex;align-items:center;flex-shrink:0}
.mobile-header .mobile-logo-wrap a{display:block}
.mobile-header .mobile-logo-wrap img{height:49px;width:auto;display:block}
.mobile-header .mobile-actions{display:flex;align-items:center;gap:0;flex-shrink:0}
.mobile-action-btn{width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;color:#333;flex-shrink:0}
.mobile-action-btn svg{width:20px;height:20px;stroke:currentColor;stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round}
.mobile-action-btn.phone-btn svg{stroke-width:2.2}
.mobile-header .hamburger{width:44px;height:44px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;background:none;border:none;cursor:pointer}
.mobile-header .hamburger span{display:block;width:20px;height:2.5px;background:#333;border-radius:2px;transition:all .3s}
.mobile-drawer-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:9999;opacity:0;visibility:hidden;transition:all .3s;-webkit-tap-highlight-color:transparent}
.mobile-drawer-overlay.active{opacity:1;visibility:visible}
.mobile-drawer{position:fixed;top:0;right:-300px;width:300px;max-width:85vw;height:100%;background:#fff;z-index:10000;overflow-y:auto;transition:right .3s ease;box-shadow:-2px 0 20px rgba(0,0,0,.12);-webkit-overflow-scrolling:touch}
.mobile-drawer.active{right:0}
.drawer-header{display:flex;align-items:center;justify-content:space-between;padding:16px 16px 16px 20px;border-bottom:1px solid #eee}
.drawer-brand{font-size:13px;font-weight:700;color:#1a1a26;letter-spacing:.3px}
.drawer-close{width:36px;height:36px;background:none;border:none;font-size:24px;color:#999;cursor:pointer;display:flex;align-items:center;justify-content:center;border-radius:6px;margin-right:-6px}
.drawer-close:active{background:#f5f5f5}
.drawer-nav{padding:4px 0}
.drawer-link{display:flex;align-items:center;padding:14px 20px;font-size:15px;color:#333;text-decoration:none;font-weight:500;transition:background .15s;-webkit-tap-highlight-color:transparent}
.drawer-link:active{background:#f5f5f5}
.drawer-group-toggle{display:flex;align-items:center;justify-content:space-between;width:100%;padding:14px 20px;background:none;border:none;font-size:15px;font-weight:500;color:#333;cursor:pointer;font-family:inherit;transition:background .15s;-webkit-tap-highlight-color:transparent}
.drawer-group-toggle:active{background:#f5f5f5}
.drawer-arrow{font-size:9px;color:#999;transition:transform .3s}
.drawer-group.open .drawer-arrow{transform:rotate(180deg)}
.drawer-submenu{display:none;background:#f8f9fa;padding:2px 0}
.drawer-group.open .drawer-submenu{display:block}
.drawer-subgroup-title{padding:12px 20px 4px 32px;font-size:10px;font-weight:700;color:#F77C2A;text-transform:uppercase;letter-spacing:.5px}
.drawer-submenu a{display:block;padding:10px 20px 10px 32px;font-size:14px;color:#555;text-decoration:none;transition:background .15s}
.drawer-submenu a:active{background:#eee}
.drawer-divider{height:1px;background:#eee;margin:8px 20px}
.drawer-footer{display:flex;gap:10px;padding:16px 20px 20px}
.drawer-footer-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:12px 8px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;text-decoration:none;transition:opacity .15s;min-height:44px;border:none}
.drawer-footer-btn:active{opacity:.8}
.drawer-footer-btn.primary{background:#F77C2A;color:#fff}
.drawer-footer-btn.primary svg{stroke:#fff}
.drawer-footer-btn.secondary{background:#f0f0f0;color:#333}
.drawer-footer-btn.secondary svg{stroke:#555}
.catalog-overlay-body{display:grid;grid-template-columns:1fr 1fr;gap:6px 20px}
.thanks-title{font-size:24px}
.thanks-icon{width:72px;height:72px}
.thanks-icon svg{width:32px;height:32px}
}

@media(min-width:769px){
.catalog-overlay{display:none!important}
.mobile-header,.mobile-drawer,.mobile-drawer-overlay{display:none!important}
}
</style>
</head>
<body>

<?php require $_SERVER["DOCUMENT_ROOT"]."/php/header.php"; ?>

<main>
<section class="thanks-section">
<div class="thanks-card">
<div class="thanks-icon">
<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
</div>
<h1 class="thanks-title">Спасибо!</h1>
<p class="thanks-sub">Ваша заявка отправлена.</p>
<p class="thanks-detail">Мы свяжемся с вами в ближайшее время.<br>Обычно отвечаем в течение 2 часов.</p>
<a href="/" class="thanks-btn">
<svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
Вернуться на сайт
</a>
<p class="thanks-timer">Вы будете перенаправлены через <span id="countdown">10</span> секунд</p>
</div>
</section>
</main>

<?php require $_SERVER['DOCUMENT_ROOT'].'/php/footer.php'; ?>

<script>
(function(){
var sec = 10;
var el = document.getElementById('countdown');
var timer = setInterval(function(){
sec--;
if(el) el.textContent = sec;
if(sec <= 0){
clearInterval(timer);
window.location.href = '/';
}
}, 1000);
})();
</script>

</body>
</html>
