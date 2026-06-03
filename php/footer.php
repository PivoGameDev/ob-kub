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
<a href="tel:+79935940107">8 (993) 594-01-07</a>
<a href="mailto:oborudovanie-kubani@yandex.ru">oborudovanie-kubani@yandex.ru</a>
<a href="/#order-form" style="display:inline-block;margin-top:4px;padding:8px 14px;background:linear-gradient(135deg,#F77C2A,#e06a15);color:#fff;border-radius:6px;font-size:12px;font-weight:700;text-decoration:none;transition:opacity .2s" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">📩 Получить КП</a>
<a href="/privacy.html">Политика конфиденциальности</a>
</div>
</div>
<div class="db-footer-bot">© ОБОРУДОВАНИЕ КУБАНИ · 2008–2026 · Все права защищены</div>
</div>
</footer>





<script src="/js/forms.js"></script>
<script src="/js/tracker.js"></script>
<script>document.getElementById("csrfToken")&&(document.getElementById("csrfToken").value=btoa(String(Math.floor(Date.now()/1e3))));</script>