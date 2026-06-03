<header class="header">
<div class="container">
<div class="header-top">
<div class="logo-section">
<a href="/" class="logo-link">
<img src="/logo.png" alt="ОБОРУДОВАНИЕ КУБАНИ" class="logo-img" onerror="this.style.display='none'">
</a>
</div>
<nav class="nav">
<a href="/#about">О нас</a>
    <span class="cat-trigger" id="catTrigger">Каталог</span>
    
    <a href="/#projects">Проекты</a>
    <a href="/#contacts">Контакты</a>
    <span class="search-trigger" id="searchTrigger"><svg class="srch-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg> Поиск</span>
    </nav>
    <div class="header-right">
    <div class="phone"><a href="tel:+79935940107">8 (993) 594-01-07</a></div>
    <button class="consult-btn" onclick="var e=document.getElementById('order-form');if(e)e.scrollIntoView({behavior:'smooth'});else location.href='/#order-form';">Получить КП</button>
    </div>
    </div>
    <div class="search-dropdown" id="searchDropdown">
    <div class="search-inner">
    <div class="search-field-wrap">
    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
    <input type="text" class="search-input" id="searchInput" placeholder="Поиск оборудования..." autocomplete="off" autocorrect="off" spellcheck="false">
    <span class="search-clear" id="searchClear">&times;</span>
    </div>
    <div class="search-results" id="searchResults"></div>
    <div class="search-empty" id="searchEmpty">Введите название оборудования, категорию или объём</div>
    </div>
    </div>
    <div class="catalog-dropdown" id="catalogDropdown">
<div class="cat-grid">
<div class="cat-col">
<h3>🍺 Пивоваренное</h3>
<a href="/catalog/beer/cct/">ЦКТ</a>
<a href="/catalog/beer/brew-house/">Варочные порядки</a>
<a href="/catalog/beer/grain-mill/">Дробилки солода</a>
<a href="/catalog/beer/hot-water-tank/">Баки горячей воды</a>
<a href="/catalog/beer/steam-generator/">Парогенераторы</a>
<a href="/catalog/beer/chiller/">Чиллеры</a>
<a href="/catalog/beer/unitank/">Форфасы</a>
<a href="/catalog/beer/heat-exchanger/">Теплообменники</a>
</div>
<div class="cat-col">
<h3>🥛 Молочное</h3>
<a href="/catalog/dairy/reception/">Ёмкости приёмки молока</a>
<a href="/catalog/dairy/cooler/">Резервуары-охладители</a>
<a href="/catalog/dairy/storage/">Резервуары хранения</a>
<a href="/catalog/dairy/vdp/">Ванны длительной пастеризации</a>
<a href="/catalog/dairy/fermentation/">Ферментационные танки</a>
<a href="/catalog/dairy/cheese-maker/">Сыроизготовители</a>
<a href="/catalog/dairy/cottage-cheese/">Творогоизготовители</a>
<a href="/catalog/dairy/yeast/">Заквасочники</a>
<a href="/catalog/dairy/cheese-shelves/">Стеллажи для созревания сыра</a>
</div>
<div class="cat-col">
<h3>🍷 Винодельческое</h3>
<a href="/catalog/wine/red-fermentation/">Ферментация красных вин</a>
<a href="/catalog/wine/white-fermentation/">Ферментация белых вин</a>
<a href="/catalog/wine/storage-aging/">Выдержка и хранение</a>
<a href="/catalog/wine/cold-stabilization/">Холодная стабилизация</a>
<a href="/catalog/wine/blending/">Купажирование</a>
<a href="/catalog/wine/sulfitation/">Сульфитация</a>
<a href="/catalog/wine/universal-tank/">Винификатор</a>
</div>
<div class="cat-col">
<h3>🏭 Промышленное</h3>
<a href="/catalog/industrial/storage/">Резервуары для хранения</a>
<a href="/catalog/industrial/mixing/">Ёмкости с мешалкой</a>
<a href="/catalog/industrial/thermal/">Ёмкости с терморегуляцией</a>
<a href="/catalog/industrial/pressure/">Ёмкости под давлением</a>
</div>
</div>
</div>
</div>

<!-- Mobile header -->
<div class="mobile-header">
  <div class="mobile-logo-wrap">
    <a href="/"><img src="/logo.png" alt="ОБОРУДОВАНИЕ КУБАНИ" onerror="this.style.display='none'"></a>
  </div>
  <div class="mobile-actions">
    <a href="tel:+79935940107" class="mobile-action-btn phone-btn" aria-label="Позвонить">
      <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
    </a>
    <button class="mobile-action-btn" id="mobileSearchBtn" aria-label="Поиск">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
    </button>
    <button class="hamburger" id="hamburger" aria-label="Меню">
      <span></span><span></span><span></span>
    </button>
  </div>
</div>

</header>


<!-- Drawer overlay -->
<div class="mobile-drawer-overlay" id="drawerOverlay"></div>

<!-- Mobile drawer -->
<div class="mobile-drawer" id="mobileDrawer">
<div class="drawer-header">
<div class="drawer-brand">ОБОРУДОВАНИЕ КУБАНИ</div>
<button class="drawer-close" id="drawerClose">&times;</button>
</div>
<nav class="drawer-nav">
<a href="/#about" class="drawer-link" onclick="event.preventDefault();closeDrawer();window.location.href=this.href">О нас</a>
<a href="/#projects" class="drawer-link" onclick="event.preventDefault();closeDrawer();window.location.href=this.href">Проекты</a>

<div class="drawer-group">
<button class="drawer-group-toggle" data-group="catalog">
Каталог <span class="drawer-arrow">▼</span>
</button>
<div class="drawer-submenu">
<div class="drawer-subgroup-title">🍺 Пивоваренное</div>
<a href="/catalog/beer/cct/">ЦКТ</a>
<a href="/catalog/beer/brew-house/">Варочные порядки</a>
<a href="/catalog/beer/grain-mill/">Дробилки солода</a>
<a href="/catalog/beer/hot-water-tank/">Баки горячей воды</a>
<a href="/catalog/beer/steam-generator/">Парогенераторы</a>
<a href="/catalog/beer/chiller/">Чиллеры</a>
<a href="/catalog/beer/unitank/">Форфасы</a>
<a href="/catalog/beer/heat-exchanger/">Теплообменники</a>
<div class="drawer-subgroup-title">🥛 Молочное</div>
<a href="/catalog/dairy/reception/">Ёмкости приёмки молока</a>
<a href="/catalog/dairy/cooler/">Резервуары-охладители</a>
<a href="/catalog/dairy/storage/">Резервуары хранения</a>
<a href="/catalog/dairy/vdp/">Ванны длительной пастеризации</a>
<a href="/catalog/dairy/fermentation/">Ферментационные танки</a>
<a href="/catalog/dairy/cheese-maker/">Сыроизготовители</a>
<a href="/catalog/dairy/cottage-cheese/">Творогоизготовители</a>
<a href="/catalog/dairy/yeast/">Заквасочники</a>
<a href="/catalog/dairy/cheese-shelves/">Стеллажи для созревания сыра</a>
<div class="drawer-subgroup-title">🍷 Винодельческое</div>
<a href="/catalog/wine/red-fermentation/">Ферментация красных вин</a>
<a href="/catalog/wine/white-fermentation/">Ферментация белых вин</a>
<a href="/catalog/wine/storage-aging/">Выдержка и хранение</a>
<a href="/catalog/wine/cold-stabilization/">Холодная стабилизация</a>
<a href="/catalog/wine/blending/">Купажирование</a>
<a href="/catalog/wine/sulfitation/">Сульфитация</a>
<a href="/catalog/wine/universal-tank/">Винификатор</a>
<div class="drawer-subgroup-title">🏭 Промышленное</div>
<a href="/catalog/industrial/storage/">Резервуары для хранения</a>
<a href="/catalog/industrial/mixing/">Ёмкости с мешалкой</a>
<a href="/catalog/industrial/thermal/">Ёмкости с терморегуляцией</a>
<a href="/catalog/industrial/pressure/">Ёмкости под давлением</a>
</div>
</div>

<a href="/#contacts" class="drawer-link" onclick="event.preventDefault();closeDrawer();window.location.href=this.href">Контакты</a>
<a href="/articles.html" class="drawer-link">Статьи</a>
<a href="/certificates.html" class="drawer-link">Сертификаты</a>
<a href="/payment-delivery.html" class="drawer-link">Оплата и доставка</a>
<a href="/privacy.html" class="drawer-link">Политика конфиденциальности</a>

<div class="drawer-divider"></div>

<div class="drawer-footer">
<button class="drawer-footer-btn primary" onclick="closeDrawer();var e=document.getElementById('order-form');if(e)e.scrollIntoView({behavior:'smooth'});else location.href='/#order-form';">
<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
Получить КП
</button>
<a href="tel:+79935940107" class="drawer-footer-btn secondary">
<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
Звонок
</a>
</div>
<script>function closeDrawer(){var d=document.getElementById('mobileDrawer'),o=document.getElementById('drawerOverlay');if(d)d.classList.remove('active');if(o)o.classList.remove('active');document.body.style.overflow=''}</script>
</div>

<!-- Catalog mobile overlay -->
<div class="catalog-overlay" id="catalogOverlay">
<div class="catalog-overlay-inner">
<div class="catalog-overlay-header">
<h2>Каталог оборудования</h2>
<button class="catalog-overlay-close" id="catalogOverlayClose">&times;</button>
</div>
<div class="catalog-overlay-body" id="catalogOverlayBody"></div>
</div>
</div>

<script>(function(){var t=document.getElementById('catTrigger'),d=document.getElementById('catalogDropdown'),o=document.getElementById('catalogOverlay'),b=document.getElementById('catalogOverlayClose'),ob=document.getElementById('catalogOverlayBody');function closeCatalog(){d.classList.remove('active');t.classList.remove('active');o.classList.remove('active');document.body.style.overflow='';var st=document.getElementById('searchTrigger'),sd=document.getElementById('searchDropdown');st&&st.classList.remove('active');sd&&sd.classList.remove('active')}function openDesktop(){var a=d.classList.contains('active');closeCatalog();if(!a){d.classList.add('active');t.classList.add('active')}}function openMobile(){if(!ob.children.length){var m=document.querySelector('.catalog-dropdown .cat-grid');if(m)ob.innerHTML=m.innerHTML}closeCatalog();o.classList.add('active');document.body.style.overflow='hidden'}if(!t)return;t.addEventListener('click',function(e){e.preventDefault();if(window.innerWidth>768){openDesktop()}else{openMobile()}});document.addEventListener('click',function(e){if(window.innerWidth>768&&d.classList.contains('active')&&!e.target.closest('.header')){closeCatalog()}});document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeCatalog()}});if(b)b.addEventListener('click',closeCatalog);if(o)o.addEventListener('click',function(e){if(e.target===o)closeCatalog()})})();

(function(){var st=document.getElementById('searchTrigger'),sd=document.getElementById('searchDropdown'),si=document.getElementById('searchInput'),sr=document.getElementById('searchResults'),sc=document.getElementById('searchClear');function closeSearch(){sd.classList.remove('active');st.classList.remove('active');document.body.style.overflow='';var ct=document.getElementById('catTrigger'),cd=document.getElementById('catalogDropdown');ct&&ct.classList.remove('active');cd&&cd.classList.remove('active')}window.closeSearch=closeSearch;function openSearch(){var ct=document.getElementById('catTrigger'),cd=document.getElementById('catalogDropdown'),co=document.getElementById('catalogOverlay');ct&&ct.classList.remove('active');cd&&cd.classList.remove('active');co&&co.classList.remove('active');sd.classList.add('active');st.classList.add('active');setTimeout(function(){si&&si.focus()},150)}if(!st)return;st.addEventListener('click',function(e){e.preventDefault();if(sd.classList.contains('active')){closeSearch()}else{openSearch()}});var searchTimer;si.addEventListener('input',function(){var q=this.value.trim();if(q.length>0){sc.classList.add('visible')}else{sc.classList.remove('visible');sr.classList.remove('has-results');sr.innerHTML='';sd.classList.remove('has-query');return}sd.classList.add('has-query');clearTimeout(searchTimer);searchTimer=setTimeout(function(){sd.classList.add('loading');var x=new XMLHttpRequest();x.open('GET','/php/search.php?q='+encodeURIComponent(q),true);x.onload=function(){sd.classList.remove('loading');if(x.status===200){try{var d=JSON.parse(x.responseText);renderResults(d.results,q)}catch(e){}}};x.send()},300)});function renderResults(items,q){if(!items||items.length===0){sr.classList.remove('has-results');sr.innerHTML='<div class="search-empty" style="display:block">Ничего не найдено</div>';return}var html='';for(var i=0;i<items.length;i++){var it=items[i];html+='<a href="'+it.u+'" onclick="closeSearch()">';if(it.i){html+='<div class="sr-icon"><img src="'+it.i+'" alt=""></div>'}else{html+='<div class="sr-icon"/>'}html+='<div class="sr-info"><div class="sr-title">'+it.t+'</div>';if(it.s){html+='<div class="sr-spec">'+it.s+'</div>'}html+='</div><div class="sr-price">'+it.p+'</div></a>'}sr.innerHTML=html;sr.classList.add('has-results')}})();</script>