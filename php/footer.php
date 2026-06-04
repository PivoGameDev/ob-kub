<?php require __DIR__ . '/metrika.php'; ?>
<script>
// Mobile drawer
(function(){var h=document.getElementById('hamburger'),d=document.getElementById('mobileDrawer'),o=document.getElementById('drawerOverlay'),c=document.getElementById('drawerClose');function openD(){d.classList.add('active');o.classList.add('active');document.body.style.overflow='hidden'}function closeD(){d.classList.remove('active');o.classList.remove('active');document.body.style.overflow=''}if(!h)return;h.addEventListener('click',function(e){e.stopPropagation();if(d.classList.contains('active')){closeD()}else{openD()}});if(c)c.addEventListener('click',closeD);if(o)o.addEventListener('click',closeD);document.addEventListener('keydown',function(e){if(e.key==='Escape')closeD()});document.querySelectorAll('.drawer-group-toggle').forEach(function(b){b.addEventListener('click',function(){this.closest('.drawer-group').classList.toggle('open')})})})();

// Mobile search trigger
(function(){var b=document.getElementById('mobileSearchBtn'),s=document.getElementById('searchTrigger');if(b&&s)b.addEventListener('click',function(e){e.preventDefault();s.click()})})();
</script>

<footer class="db-footer" style="position:relative;overflow:hidden">
<div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#F77C2A,transparent)"></div>
<div class="db-wrap" style="padding-top:44px">
<div style="display:grid;grid-template-columns:2fr 1fr 1fr 2fr;gap:30px">
<div>
<div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:14px">Оборудование Кубани</div>
<p style="font-size:13px;color:rgba(255,255,255,.45);line-height:1.7;margin:0 0 16px">Производим резервуары из нержавеющей стали AISI 304/316 с 2008 года. Собственное производство 2000 м² в Краснодаре.</p>
<div style="display:flex;gap:8px;flex-wrap:wrap">
<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:rgba(247,124,42,.1);border:1px solid rgba(247,124,42,.15);border-radius:5px;font-size:11px;font-weight:600;color:#F77C2A">17 лет</span>
<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:rgba(247,124,42,.1);border:1px solid rgba(247,124,42,.15);border-radius:5px;font-size:11px;font-weight:600;color:#F77C2A">2000 м²</span>
<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:rgba(247,124,42,.1);border:1px solid rgba(247,124,42,.15);border-radius:5px;font-size:11px;font-weight:600;color:#F77C2A">500+ резервуаров</span>
</div>
</div>
<div>
<h3 style="font-size:12px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.6px;margin:0 0 14px">Навигация</h3>
<a href="/#about" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">О нас</a>
<a href="/catalog/" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">Каталог</a>
<a href="/#projects" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">Проекты</a>
<a href="/#contacts" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">Контакты</a>
<a href="/articles.html" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">Статьи</a>
<a href="/certificates.html" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">Сертификаты</a>
<a href="/payment-delivery.html" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">Оплата и доставка</a>
</div>
<div>
<h3 style="font-size:12px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.6px;margin:0 0 14px">Оборудование</h3>
<a href="/#equipment" onclick="event.preventDefault();document.getElementById('equipment').scrollIntoView({behavior:'smooth'});setTimeout(function(){if(window.sw)sw(0)},400)" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">🥛 Молочное</a>
<a href="/#equipment" onclick="event.preventDefault();document.getElementById('equipment').scrollIntoView({behavior:'smooth'});setTimeout(function(){if(window.sw)sw(1)},400)" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">🍷 Винодельческое</a>
<a href="/#equipment" onclick="event.preventDefault();document.getElementById('equipment').scrollIntoView({behavior:'smooth'});setTimeout(function(){if(window.sw)sw(2)},400)" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">🍺 Пивоваренное</a>
<a href="/#equipment" onclick="event.preventDefault();document.getElementById('equipment').scrollIntoView({behavior:'smooth'});setTimeout(function(){if(window.sw)sw(3)},400)" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">💧 Вода</a>
<a href="/#equipment" onclick="event.preventDefault();document.getElementById('equipment').scrollIntoView({behavior:'smooth'});setTimeout(function(){if(window.sw)sw(4)},400)" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">🫒 Масло</a>
<a href="/#equipment" onclick="event.preventDefault();document.getElementById('equipment').scrollIntoView({behavior:'smooth'});setTimeout(function(){if(window.sw)sw(5)},400)" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:5px 0;transition:color .2s">🍯 Кондитерская</a>
</div>
<div>
<h3 style="font-size:12px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.6px;margin:0 0 14px">Контакты</h3>
<a href="tel:+79935940107" style="display:block;font-size:16px;font-weight:700;color:#F77C2A;text-decoration:none;padding:4px 0">8 (993) 594-01-07</a>
<a href="mailto:oborudovanie-kubani@yandex.ru" style="display:block;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;padding:4px 0">oborudovanie-kubani@yandex.ru</a>
<a href="https://t.me/oborudovanie_kubani" target="_blank" rel="noopener" style="display:block;font-size:13px;color:#27a6e5;font-weight:600;text-decoration:none;padding:4px 0">✈️ Telegram</a>
<div style="margin-top:8px">
<a href="/#order-form" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">📩 Получить КП</a>
</div>
<a href="/privacy.html" style="display:block;font-size:12px;color:rgba(255,255,255,.3);text-decoration:none;padding:4px 0;margin-top:8px">Политика конфиденциальности</a>
</div>
</div>
<div style="padding:24px 0;margin-top:36px;border-top:1px solid rgba(255,255,255,.06);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px">
<div style="font-size:12px;color:rgba(255,255,255,.3)">© ОБОРУДОВАНИЕ КУБАНИ · 2008–2026 · Все права защищены</div>
</div>
</div>
</footer>

<script src="/js/forms.js"></script>
<script src="/js/tracker.js"></script>
<script>document.getElementById("csrfToken")&&(document.getElementById("csrfToken").value=btoa(String(Math.floor(Date.now()/1e3))));</script>