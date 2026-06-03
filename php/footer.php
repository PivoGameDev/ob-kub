<?php require __DIR__ . '/metrika.php'; ?>
<script>
// Mobile drawer
(function(){var h=document.getElementById('hamburger'),d=document.getElementById('mobileDrawer'),o=document.getElementById('drawerOverlay'),c=document.getElementById('drawerClose');function openD(){d.classList.add('active');o.classList.add('active');document.body.style.overflow='hidden'}function closeD(){d.classList.remove('active');o.classList.remove('active');document.body.style.overflow=''}if(!h)return;h.addEventListener('click',function(e){e.stopPropagation();if(d.classList.contains('active')){closeD()}else{openD()}});if(c)c.addEventListener('click',closeD);if(o)o.addEventListener('click',closeD);document.addEventListener('keydown',function(e){if(e.key==='Escape')closeD()});document.querySelectorAll('.drawer-group-toggle').forEach(function(b){b.addEventListener('click',function(){this.closest('.drawer-group').classList.toggle('open')})})})();

// Mobile search trigger
(function(){var b=document.getElementById('mobileSearchBtn'),s=document.getElementById('searchTrigger');if(b&&s)b.addEventListener('click',function(e){e.preventDefault();s.click()})})();
</script>

<footer class="db-footer">
<div class="db-wrap">
<div class="db-footer-inner">
<div class="db-footer-col">
<h3>Навигация</h3>
<a href="/#about">О нас</a>
<a href="/catalog/">Каталог</a>
<a href="/#projects">Проекты</a>
<a href="/#contacts">Контакты</a>
<a href="/articles.html">Статьи</a>
<a href="/certificates.html">Сертификаты</a>
<a href="/payment-delivery.html">Оплата и доставка</a>
</div>
<div class="db-footer-col">
<h3>Оборудование</h3>
<a href="/beer.html">Пивоваренное</a>
<a href="/dairy.html">Молочное</a>
<a href="/winery.html">Винодельческое</a>
<a href="/industrial.html">Промышленное</a>
</div>
<div class="db-footer-col">
<h3>Контакты</h3>
<a href="tel:+79935940107" style="font-size:16px;font-weight:700;color:#F77C2A">8 (993) 594-01-07</a>
<a href="mailto:oborudovanie-kubani@yandex.ru">oborudovanie-kubani@yandex.ru</a>
<a href="https://t.me/oborudovanie_kubani" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:6px;margin:4px 0;color:#27a6e5;font-weight:600;font-size:13px;text-decoration:none"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg> Написать в Telegram</a>
<a href="/#order-form" style="display:inline-flex;align-items:center;gap:6px;margin-top:6px;padding:10px 20px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">📩 Получить КП</a>
<a href="/privacy.html" style="margin-top:8px;display:block">Политика конфиденциальности</a>
</div>
</div>
<div class="db-footer-bot">© ОБОРУДОВАНИЕ КУБАНИ · 2008–2026 · Все права защищены</div>
</div>
</footer>





<script src="/js/forms.js"></script>
<script src="/js/tracker.js"></script>
<script>document.getElementById("csrfToken")&&(document.getElementById("csrfToken").value=btoa(String(Math.floor(Date.now()/1e3))));</script>