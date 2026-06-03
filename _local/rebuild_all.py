#!/usr/bin/env python3
"""
REBUILD dairy, wine, industrial PHP files from scratch.
Complete metadata + hardcoded exact prices for ALL volumes.
"""
import re, math, urllib.request, ssl
ssl._create_default_https_context = ssl._create_unverified_context
DOC = '/Users/tretyakov/Desktop/Апгрейд/catalog'

# ═══ FETCH SITE PRICES ═══
def fetch(url):
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        resp = urllib.request.urlopen(req, timeout=10)
        return resp.read().decode('utf-8')
    except: return None

def site_prices(cat_url):
    html = fetch('https://ob-kub.ru' + cat_url)
    if not html: return {}
    results = {}
    for m in re.finditer(r'"url"\s*:\s*"([^"]*/(\d+)l/)"\s*,\s*"name"\s*:\s*"[^"]*"\s*,\s*"offers"\s*:\s*\{[^}]*"price"\s*:\s*"(\d+)"', html):
        results[int(m.group(2))] = float(m.group(3))
    return results

CAT_URLS = {
    'vdp':'/catalog/dairy/vdp/', 'cooler':'/catalog/dairy/cooler/',
    'fermentation':'/catalog/dairy/fermentation/', 'storage':'/catalog/dairy/storage/',
    'cheese-maker':'/catalog/dairy/cheese-maker/',
    'cottage-cheese':'/catalog/dairy/cottage-cheese/', 'yeast':'/catalog/dairy/yeast/',
    'brine':'/catalog/dairy/brine/', 'reception':'/catalog/dairy/reception/',
    'ind-storage':'/catalog/industrial/storage/', 'mixing':'/catalog/industrial/mixing/',
    'thermal':'/catalog/industrial/thermal/', 'pressure':'/catalog/industrial/pressure/',
    'red-fermentation':'/catalog/wine/red-fermentation/',
    'white-fermentation':'/catalog/wine/white-fermentation/',
    'storage-aging':'/catalog/wine/storage-aging/',
    'cold-stabilization':'/catalog/wine/cold-stabilization/',
    'blending':'/catalog/wine/blending/', 'sulfitation':'/catalog/wine/sulfitation/',
    'universal-tank':'/catalog/wine/universal-tank/',
}
print("Fetching site prices...")
SITE = {}
for slug, url in CAT_URLS.items():
    SITE[slug] = site_prices(url)
    print(f"  {slug}: {len(SITE[slug])} volumes")

# ═══ COMPETITOR + TARGET ═══
BEST = {}
COMP = [('ЦКТ',100,107000),('ЦКТ',250,170000),('ЦКТ',500,294000),('ЦКТ',1000,362335),
        ('ЦКТ',1500,420000),('ЦКТ',2000,478600),('ЦКТ',3000,560650),('ЦКТ',4000,650000),
        ('ЦКТ',5000,773470),('ЦКТ',6000,900000),('ЦКТ',7500,1000000),('ЦКТ',8000,1100000),
        ('ЦКТ',10000,1045600),('ЦКТ',12000,1200000),('ЦКТ',15000,1400000),('ЦКТ',20000,1800000),
        ('ЦКТ',25000,2200000),('ЦКТ',30000,2800000),('ЦКТ',40000,3600000),('ЦКТ',50000,4500000),
        ('ЦКТ',60000,5500000),('ЦКТ',80000,7200000),('ЦКТ',100000,9000000),('ЦКТ',120000,11000000),
        ('ЦКТ',150000,14000000),('ЦКТ',200000,18500000),
        ('ВДП',200,180000),('ВДП',500,280000),('ВДП',1000,368900),
        ('ВДП',1500,480000),('ВДП',2000,696000),('ВДП',3000,833000),
        ('ВДП',4000,900000),('ВДП',5000,1100000),('ВДП',6300,1300000),
        ('ВДП',8000,1500000),('ВДП',10000,1700000),
        ('Охл',1000,549940),('Охл',5000,1098890),('Охл',10000,1897870),
        ('Охл',10000,1750000),
        ('Сыроизг',200,385952),('Сыроизг',500,507455),('Сыроизг',1000,825000),('Сыроизг',10000,2348000)]
for c,v,p in COMP:
    k=(c,v); BEST[k] = min(p, BEST.get(k, float('inf')))

def comp_ref(c,v):
    k=(c,v)
    if k in BEST: return BEST[k]
    kk=sorted([(x,BEST[(c,x)]) for (a,x) in BEST if a==c])
    if not kk: return None
    if v<=kk[0][0]: return kk[0][1]*v/kk[0][0]
    if v>=kk[-1][0]: return kk[-1][1]*v/kk[-1][0]
    for i in range(len(kk)-1):
        if kk[i][0]<=v<=kk[i+1][0]:
            r=(v-kk[i][0])/(kk[i+1][0]-kk[i][0])
            return kk[i][1]+(kk[i+1][1]-kk[i][1])*r
    return None

DIRECT = {'vdp':'ВДП','cooler':'Охл','cheese-maker':'Сыроизг'}
CPX = {
    'vdp':1.0,'cooler':1.0,'cheese-maker':1.0,'storage':0.6,'reception':0.55,
    'fermentation':0.85,'cottage-cheese':0.7,'yeast':0.5,'brine':0.45,
    'ind-storage':0.6,'mixing':0.9,'thermal':0.85,'pressure':1.15,
    'red-fermentation':0.85,'white-fermentation':0.85,'storage-aging':0.6,
    'cold-stabilization':0.85,'blending':0.7,'sulfitation':0.6,'universal-tank':1.05,
}

def target(slug, vol):
    d=DIRECT.get(slug); c=comp_ref(d,vol) if d else None
    if c: return int(c*0.95)
    c=comp_ref('ЦКТ',vol)
    if c and slug in CPX: return int(c*CPX[slug]*0.95)
    return None

print("\nComputing targets...")
TARGETS = {}
for slug, vols in SITE.items():
    for v in vols:
        tp = target(slug, v)
        if tp: TARGETS[(slug, v)] = tp
print(f"  {len(TARGETS)} targets")

# ═══ REFERENCE SPECS ═══
REF = {
    'vdp': {200:{'diameter':500,'height':900,'wall':2,'weight':55,'power':6}},
    'fermentation': {500:{'diameter':650,'height':1500,'wall':2,'weight':85,'power':4}},
    'cheese-maker': {500:{'diameter':700,'height':1500,'wall':2,'weight':90,'power':6}},
    'cottage-cheese': {500:{'diameter':700,'height':1500,'wall':2,'weight':88,'power':5.5}},
    'yeast': {50:{'diameter':300,'height':700,'wall':2,'weight':20,'power':1.5}},
    'brine': {200:{'diameter':500,'height':900,'wall':2,'weight':55,'power':5}},
    'storage': {1000:{'diameter':819,'height':1696,'wall':2,'weight':109,'power':0}},
    'reception': {1000:{'diameter':900,'height':1500,'wall':2,'weight':95,'power':0}},
    'cooler': {1000:{'diameter':900,'height':1500,'wall':2,'weight':130,'power':3}},
    'ind-storage': {5000:{'diameter':1600,'height':3000,'wall':3,'weight':350,'power':0}},
    'mixing': {1000:{'diameter':1000,'height':1500,'wall':3,'weight':200,'power':2.2}},
    'thermal': {2000:{'diameter':1200,'height':2000,'wall':2,'weight':280,'power':12}},
    'pressure': {2000:{'diameter':1100,'height':2200,'wall':2,'weight':280,'power':0}},
    'red-fermentation': {500:{'diameter':800,'height':1400,'wall':2,'weight':85,'power':4}},
    'white-fermentation': {500:{'diameter':800,'height':1400,'wall':2,'weight':85,'power':4}},
    'storage-aging': {5000:{'diameter':1400,'height':2900,'wall':3,'weight':320,'power':0}},
    'cold-stabilization': {1000:{'diameter':1000,'height':1800,'wall':2,'weight':150,'power':9}},
    'blending': {1000:{'diameter':1000,'height':1800,'wall':2,'weight':150,'power':4}},
    'sulfitation': {500:{'diameter':700,'height':1300,'wall':2,'weight':70,'power':2}},
    'universal-tank': {1000:{'diameter':1000,'height':1800,'wall':2,'weight':150,'power':9}},
}

# ═══ GENERATE SPECS ═══
def gen_phys(ref_dict, ref_vol, target_vol):
    ratio = target_vol / ref_vol
    cr = pow(ratio, 1/3)
    sr = pow(ratio, 2/3)
    d = {}
    for k, v in ref_dict.items():
        if k == 'diameter': d[k] = max(200, round(int(v) * cr))
        elif k == 'height': d[k] = max(300, round(int(v) * cr))
        elif k == 'wall':
            w = 2
            if target_vol >= 3000: w = 3
            if target_vol >= 10000: w = 4
            if target_vol >= 50000: w = 5
            if target_vol >= 100000: w = 6
            d[k] = w
        elif k == 'weight': d[k] = max(10, round(int(v) * sr))
        elif k == 'power': d[k] = round(float(v) * sr, 1)
    dia = d.get('diameter', 1000)
    hgt = d.get('height', 1000)
    fv = round(math.pi * (dia/2)**2 * hgt / 1000)
    d['full_volume'] = fv
    d['working_volume'] = round(fv * 0.8)
    return d

def specs_php(slug, data_slug=None):
    if data_slug is None: data_slug = slug
    vols = sorted(SITE.get(data_slug, {}).keys())
    ref_vol = next(iter(REF[slug].keys()))
    ref = REF[slug][ref_vol]
    entries = []
    for v in vols:
        tp = TARGETS.get((data_slug, v))
        if tp is None: continue
        phys = gen_phys(ref, ref_vol, v)
        lines = []
        for k in ['diameter','height','wall','weight','power','price','full_volume','working_volume']:
            val = tp if k == 'price' else phys[k]
            lines.append(f"            '{k}' => {val}")
        entries.append(f"        {v} => [\n" + ",\n".join(lines) + "\n        ]")
    return "[\n" + ",\n".join(entries) + "\n    ]"

def cat_php(slug, meta, data_slug=None):
    if data_slug is None: data_slug = slug
    vols_str = ", ".join(str(v) for v in sorted(SITE.get(data_slug, {}).keys()))
    feat_str = ",\n".join(f"            '{f}'" for f in meta['features'])
    sp = specs_php(slug, data_slug)
    return f"""    '{slug}' => [
        'name' => '{meta['name']}',
        'name_short' => '{meta['name_short']}',
        'title' => '{meta['title']}',
        'desc' => '{meta['desc']}',
        'h1' => '{meta['h1']}',
        'image' => '{meta['image']}',
        'features' => [
{feat_str}
        ],
        'volumes' => [{vols_str}],
        'specs' => {sp},
    ]"""

# ═══ META DATA ═══
# storage (Резервуар для хранения) — included from original file
DAIRY_STORAGE = ('storage', {
    'name':'Резервуар для хранения молока','name_short':'Резервуар хранения',
    'title':'Резервуар для хранения молока — каталог',
    'desc':'Резервуары для хранения молока из AISI 304 с термоизоляцией. До 200000 литров. Вертикальные и горизонтальные. Полная комплектация арматурой.',
    'h1':'Резервуары для хранения молока','image':'dairy-storage.jpg',
    'features':['Материал: AISI 304','Термоизоляция 50–200 мм ППУ','Люк-лаз DN400 / DN500','CIP-мойка (ротационная головка)','Датчик уровня (опция)','Датчик температуры Pt100','Вертикальное и горизонтальное исполнение','Арматура из нержавейки']
})

DAIRY_RECEPTION = ('reception', {
    'name':'Ёмкость приёмки молока','name_short':'Приёмная ёмкость',
    'title':'Ёмкость приёмки молока — каталог',
    'desc':'Приёмные ёмкости из нержавеющей стали AISI 304 для молока. Оснащены фильтром грубой очистки, уровнемером, CIP-мойкой. Объёмы от 1000 до 50000 литров.',
    'h1':'Ёмкости приёмки молока','image':'dairy-reception.jpg',
    'features':['Материал: AISI 304','Фильтр грубой очистки молока','Поплавковый уровнемер','CIP-мойка (ротационная головка)','Люк-лаз DN400','Опоры регулируемые','Дренажный кран DN50','Возможен подогрев (рубашка)']
})

DAIRY_COOLER = ('cooler', {
    'name':'Резервуар-охладитель молока','name_short':'Охладитель молока',
    'title':'Резервуар-охладитель молока — каталог',
    'desc':'Резервуары-охладители молока из AISI 304 с рубашкой охлаждения и термоизоляцией ППУ. Поддержание +4°C. Объёмы от 1000 до 50000 литров.',
    'h1':'Резервуары-охладители молока','image':'dairy-cooler.jpg',
    'features':['Материал: AISI 304','Рубашка охлаждения (пропиленгликоль)','Термоизоляция 50–150 мм ППУ','Автоматика поддержания +4°C','Люк-лаз DN400','CIP-мойка (ротационная головка)','Мешалка (опция)','Датчик температуры Pt100']
})

DAIRY_CATS = [DAIRY_STORAGE, DAIRY_RECEPTION, DAIRY_COOLER] + [
    ('vdp', {
        'name':'Ванна длительной пастеризации (ВДП)','name_short':'ВДП',
        'title':'Ванна длительной пастеризации — каталог',
        'desc':'Ванны длительной пастеризации ВДП из нержавеющей стали AISI 304. Двустенная рубашка, мешалка, автоматика. Объёмы от 200 до 10000 литров.',
        'h1':'Ванны длительной пастеризации (ВДП)','image':'dairy-vdp.jpg',
        'features':['Материал: AISI 304','Двустенная рубашка (пар/горячая вода)','Лопастная мешалка с мотор-редуктором','Термоизоляция 50 мм ППУ','Терморегулятор PID','Люк загрузочный','Кран сливной DN50','Температура до 95°C']
    }),
    ('fermentation', {
        'name':'Ферментационный танк','name_short':'Ферментер',
        'title':'Ферментационный танк для молочной — каталог',
        'desc':'Ферментационные танки из AISI 304 для кефира, йогурта, сметаны и ряженки. Мешалка, терморегуляция, CIP-мойка. Объёмы от 500 до 50000 литров.',
        'h1':'Ферментационные танки для молочной продукции','image':'dairy-fermentation.jpg',
        'features':['Материал: AISI 304 (AISI 316 под заказ)','Рубашка нагрев + охлаждение','Лопастная мешалка с мотор-редуктором','Термоизоляция 50–100 мм ППУ','PID-регулятор температуры','Датчик pH (опция)','CIP-мойка (ротационная головка)','Пробоотборный кран']
    }),
    ('cheese-maker', {
        'name':'Сыроизготовитель','name_short':'Сыроизготовитель',
        'title':'Сыроизготовители — каталог',
        'desc':'Сыроизготовители из AISI 304/316 для производства твёрдых, полутвёрдых и мягких сыров. Двустенная рубашка, мешалка, дренаж, автоматика. Объёмы от 200 до 10000 литров.',
        'h1':'Сыроизготовители','image':'dairy-cheese-maker.jpg',
        'features':['Материал: AISI 304 (AISI 316 под заказ)','Двустенная рубашка (нагрев/охлаждение)','Режуще-вымешивающая мешалка (сервопривод)','Термоизоляция 50–100 мм ППУ','Дренажный кран сырного зерна','Фильтр для сыворотки','CIP-мойка','Арматура из нержавейки']
    }),
    ('cottage-cheese', {
        'name':'Творогоизготовитель','name_short':'Творогоизготовитель',
        'title':'Творогоизготовители — каталог',
        'desc':'Творогоизготовители из AISI 304 для производства творога и творожных изделий. Двустенная рубашка, дренаж, автоматика. Объёмы от 200 до 5000 литров.',
        'h1':'Творогоизготовители','image':'dairy-cottage-cheese.jpg',
        'features':['Материал: AISI 304','Двустенная рубашка (нагрев/охлаждение)','Режуще-вымешивающая мешалка','Термоизоляция 50 мм ППУ','Дренажное устройство','Кран сливной','CIP-мойка','Арматура из нержавейки']
    }),
    ('yeast', {
        'name':'Заквасочник','name_short':'Заквасочник',
        'title':'Заквасочники — каталог',
        'desc':'Заквасочники из AISI 304 для приготовления заквасок. Терморегуляция, мешалка, автоматический режим. Объёмы от 50 до 1000 литров.',
        'h1':'Заквасочники','image':'dairy-yeast.jpg',
        'features':['Материал: AISI 304 (AISI 316 под заказ)','Рубашка нагрев + охлаждение','Лопастная мешалка','Термоизоляция 50 мм ППУ','PID-регулятор температуры','CIP-мойка','Пробоотборный кран']
    }),
    ('brine', {
        'name':'Контейнер для соления сыра','name_short':'Контейнер соления',
        'title':'Контейнеры для соления сыра — каталог',
        'desc':'Контейнеры для соления сыра из AISI 316 (нержавеющая кислотостойкая сталь). Устойчивы к солевому раствору. Объёмы от 200 до 5000 литров.',
        'h1':'Контейнеры для соления сыра','image':'dairy-brine.jpg',
        'features':['Материал: AISI 316 (стойкость к солевому раствору)','Облицовка стеклопластик (опция)','Решётчатый вкладыш','Фильтр циркуляции рассола','Кран сливной','Крышка герметичная','Опоры регулируемые']
    }),
]

WINE_CATS = [
    ('red-fermentation', {
        'name':'Ферментационный танк для красных вин','name_short':'Танк красного вина',
        'title':'Ферментационные танки для красных вин — каталог',
        'desc':'Открытые ферментационные танки из AISI 304 для красных вин. С решёткой для шапки мезги, насосом перекачки, рубашкой охлаждения. Объёмы от 500 до 50000 литров.',
        'h1':'Ферментационные танки для красных вин','image':'wine-red-fermentation.jpg',
        'features':['Материал: AISI 304','Открытый верх (доступ к шапке мезги)','Насос перекачки сусла (pumping-over)','Решётка для удержания мезги','Рубашка охлаждения','Термокарман под Pt100','CIP-мойка','Опоры регулируемые']
    }),
    ('white-fermentation', {
        'name':'Ферментационный танк для белых вин','name_short':'Танк белого вина',
        'title':'Ферментационные танки для белых вин — каталог',
        'desc':'Закрытые ферментационные танки из AISI 304 для белых вин. Полная герметизация, охлаждение, гидрозатвор. Объёмы от 500 до 31500 литров.',
        'h1':'Ферментационные танки для белых вин','image':'wine-white-fermentation.jpg',
        'features':['Материал: AISI 304','Закрытый верх (герметичный)','Гидрозатвор','Рубашка охлаждения','Термокарман под Pt100','Пробоотборный кран','CIP-мойка','Опоры регулируемые']
    }),
    ('storage-aging', {
        'name':'Ёмкость для выдержки и хранения вина','name_short':'Ёмкость выдержки',
        'title':'Ёмкости для выдержки и хранения вина — каталог',
        'desc':'Ёмкости из AISI 304 для выдержки и хранения вина. Термоизоляция, гидрозатвор, полная герметизация. Объёмы от 500 до 200000 литров.',
        'h1':'Ёмкости для выдержки и хранения вина','image':'wine-storage-aging.jpg',
        'features':['Материал: AISI 304','Термоизоляция 50–100 мм ППУ','Гидрозатвор','Люк-лаз DN400','Пробоотборный кран','CIP-мойка','Арматура из нержавейки','Опоры регулируемые']
    }),
    ('cold-stabilization', {
        'name':'Танк холодной стабилизации (криостат)','name_short':'Криостат',
        'title':'Танки холодной стабилизации (криостаты) — каталог',
        'desc':'Танки холодной стабилизации из AISI 304 для вин. Охлаждение до −5°C, термоизоляция, автоматика. Объёмы от 500 до 50000 литров.',
        'h1':'Танки холодной стабилизации (криостаты)','image':'wine-cold-stabilization.jpg',
        'features':['Материал: AISI 304','Рубашка охлаждения (гликоль)','Термоизоляция 100–150 мм ППУ','PID-регулятор температуры','Люк-лаз DN400','CIP-мойка','Дренаж','Опоры регулируемые']
    }),
    ('blending', {
        'name':'Ёмкость для купажирования вина','name_short':'Купажная ёмкость',
        'title':'Ёмкости для купажирования вина — каталог',
        'desc':'Купажные ёмкости из AISI 304 для смешивания виноматериалов, корректировки сахара и кислотности. Мешалка, уровнемер. Объёмы от 500 до 50000 литров.',
        'h1':'Ёмкости для купажирования вина','image':'wine-blending.jpg',
        'features':['Материал: AISI 304','Лопастная мешалка с мотор-редуктором','Уровнемер','Люк-лаз DN400','CIP-мойка','Пробоотборный кран','Арматура из нержавейки','Опоры регулируемые']
    }),
    ('sulfitation', {
        'name':'Ёмкость сульфитации вина','name_short':'Сульфитатор',
        'title':'Ёмкости сульфитации вина — каталог',
        'desc':'Ёмкости сульфитации из AISI 304 для внесения сернистого ангидрида в вино. Дозатор, барботер, герметичность. Объёмы от 500 до 50000 литров.',
        'h1':'Ёмкости сульфитации вина','image':'wine-sulfitation.jpg',
        'features':['Материал: AISI 304','Герметичная крышка','Штуцер для подачи SO₂','Барботер','Люк-лаз DN400','CIP-мойка','Арматура из нержавейки','Опоры регулируемые']
    }),
    ('universal-tank', {
        'name':'Винификатор (универсальный терморегулируемый танк)','name_short':'Винификатор',
        'title':'Винификаторы — каталог',
        'desc':'Универсальные терморегулируемые танки из AISI 304. Подходят для красных и белых вин: ферментация, мацерация, яблочно-молочное брожение. Объёмы от 500 до 50000 литров.',
        'h1':'Винификаторы (универсальные терморегулируемые танки)','image':'wine-universal-tank.jpg',
        'features':['Материал: AISI 304','Рубашка нагрев + охлаждение','Термоизоляция 80 мм ППУ','Решётка для шапки мезги','Насос перекачки','PID-регулятор','Люк-лаз DN400','CIP-мойка']
    }),
]

IND_CATS = [
    ('storage', {
        'name':'Резервуар для хранения','name_short':'Резервуар хранения',
        'title':'Резервуары для хранения — каталог',
        'desc':'Промышленные резервуары для хранения из AISI 304/316 для пищевых жидкостей, молока, воды, масел. Объёмы от 1000 до 200000 литров.',
        'h1':'Резервуары для хранения','image':'industrial-storage.jpg',
        'features':['Материал: AISI 304 / AISI 316','Вертикальное и горизонтальное исполнение','Люк-лаз DN400 / DN500','CIP-мойка (ротационная головка)','Датчик уровня (опция)','Датчик температуры Pt100','Термоизоляция 50–200 мм ППУ','Опоры регулируемые']
    }),
    ('mixing', {
        'name':'Ёмкость с мешалкой','name_short':'Мешалка',
        'title':'Ёмкости с мешалкой — каталог',
        'desc':'Ёмкости с мешалкой из AISI 304/316 для перемешивания, гомогенизации и смешивания пищевых продуктов. Объёмы от 200 до 50000 литров.',
        'h1':'Ёмкости с мешалкой','image':'industrial-mixing.jpg',
        'features':['Материал: AISI 304 / AISI 316','Лопастная / якорная / турбинная мешалка','Мотор-редуктор (частотный привод)','Рубашка нагрев / охлаждение (опция)','Термоизоляция (опция)','Люк-лаз DN400','CIP-мойка','Опоры регулируемые']
    }),
    ('thermal', {
        'name':'Ёмкость с терморегуляцией','name_short':'Термоёмкость',
        'title':'Ёмкости с терморегуляцией — каталог',
        'desc':'Терморегулируемые ёмкости из AISI 304/316 для нагрева, охлаждения и выдержки пищевых продуктов. Объёмы от 200 до 50000 литров.',
        'h1':'Ёмкости с терморегуляцией','image':'industrial-thermal.jpg',
        'features':['Материал: AISI 304 / AISI 316','Двустенная рубашка (пар / горячая вода / гликоль)','Термоизоляция 50–100 мм ППУ','PID-регулятор температуры','Мешалка лопастная (опция)','Датчик температуры Pt100','Люк-лаз DN400','CIP-мойка']
    }),
    ('pressure', {
        'name':'Емкость под давлением','name_short':'Ресивер / напорная ёмкость',
        'title':'Емкости под давлением до 6 бар — каталог',
        'desc':'Емкости под давлением из AISI 304 для пищевой промышленности. Рабочее давление до 6 бар. Для хранения, выдачи, аэрации. Объёмы от 200 до 30000 литров.',
        'h1':'Емкости под давлением','image':'industrial-pressure.jpg',
        'features':['Материал: AISI 304 / AISI 316','Рабочее давление до 6 бар','Предохранительный клапан','Манометр','Люк-лаз DN400','Смотровой люк с подсветкой','CIP-мойка','Уровнемер (опция)','Пробоотборный кран']
    }),
]

# ══════════════════════════════════════════════════════
# BUILD FILES
# ══════════════════════════════════════════════════════

def build_data_file(filename, var_name, cats, extra_footer, slug_map=None):
    if slug_map is None: slug_map = {}
    parts = []
    for slug, meta in cats:
        ds = slug_map.get(slug)
        parts.append(cat_php(slug, meta, ds))
    return f"<?php\n\n${var_name} = [\n" + ",\n\n".join(parts) + "\n\n];\n\n" + extra_footer

# dairy footer
dairy_footer = """$dairyCategory = [
    'title' => 'Молочное оборудование из нержавеющей стали — каталог',
    'desc' => 'Каталог молочного оборудования из нержавеющей стали AISI 304/316: приёмные ёмкости, охладители, ванны длительной пастеризации, сыроизготовители, резервуары хранения, ферментационные танки, творогоизготовители, заквасочники, контейнеры для соления.',
    'h1' => 'Молочное оборудование',
];

$dairyShelves = [
    'name' => 'Стеллажи для созревания сыра',
    'name_short' => 'Стеллажи',
    'title' => 'Стеллажи для созревания сыра — каталог',
    'desc' => 'Модульные стеллажи из нержавеющей стали AISI 304 для созревания и хранения сыра. Нагрузка до 200 кг/м², высота до 4 м, длина до 12 м.',
    'h1' => 'Стеллажи для созревания сыра',
    'image' => 'dairy-cheese-shelves.jpg',
    'features' => [
        'Материал: AISI 304',
        'Нагрузка до 200 кг/м²',
        'Высота до 4 метров',
        'Модульная конструкция (болтовые соединения)',
        'Перфорация полок с шагом 20 мм',
        'Влажность эксплуатации до 85%',
        'Секции от 2 до 10+ полок',
        'Подходит под любые размеры камеры',
    ],
];"""

wine_footer = """$wineCategory = [
    'title' => 'Винодельческое оборудование из нержавеющей стали — каталог',
    'desc' => 'Каталог винодельческого оборудования из нержавеющей стали AISI 304/316: ферментационные танки, ёмкости выдержки и хранения, криостаты, купажные и сульфитационные ёмкости, универсальные танки.',
    'h1' => 'Винодельческое оборудование',
];"""

industrial_footer = """$industrialCategory = [
    'title' => 'Промышленное оборудование из нержавеющей стали — каталог',
    'desc' => 'Каталог промышленного оборудования из нержавеющей стали AISI 304/316: резервуары хранения, ёмкости с мешалкой, терморегулируемые ёмкости, ёмкости под давлением, CIP-станции, теплообменники.',
    'h1' => 'Промышленное оборудование',
];

$industrialCip = [
    'name' => 'CIP-станция (мойка безразборная)',
    'name_short' => 'CIP-станция',
    'title' => 'CIP-станции для пищевой промышленности — каталог',
    'desc' => 'CIP-станции из AISI 304 для безразборной мойки технологического оборудования. 1–3 контура, нагрев, автоматика. Для пищевых и молочных производств.',
    'h1' => 'CIP-станции',
    'image' => 'industrial-cip.jpg',
    'features' => [
        'Материал: AISI 304',
        '1–3 моечных контура',
        'Нагрев раствора (пар / электричество)',
        'Автоматика на ПЛК',
        'Сенсорная панель управления',
        'Датчики температуры / уровня / давления',
        'Моющая головка (CIP-шары)',
        'Насос центробежный AISI 304',
        'Система рекуперации моющего раствора',
    ],
];

$industrialHeatExchanger = [
    'name' => 'Теплообменник пластинчатый / кожухотрубный',
    'name_short' => 'Теплообменник',
    'title' => 'Теплообменники для пищевой промышленности — каталог',
    'desc' => 'Пластинчатые и кожухотрубные теплообменники из AISI 304/316 для нагрева и охлаждения пищевых продуктов, сусла, молока, соков, сиропов.',
    'h1' => 'Теплообменники',
    'image' => 'heat-exchanger.jpg',
    'features' => [
        'Материал: AISI 304 / AISI 316',
        'Тип: пластинчатый / кожухотрубный',
        'Уплотнения: EPDM / NBR / Viton (пищевые)',
        'Рабочее давление до 10 бар',
        'Температура до 140°C',
        'Мощность теплообмена от 10 до 500 кВт',
        'Компактная установка на раме',
        'Простота разборки и мойки',
    ],
];"""

print("\n=== Building dairy-data.php ===")
dairy_content = build_data_file('dairy-data.php', 'dairyData', DAIRY_CATS, dairy_footer)
with open(f'{DOC}/dairy-data.php', 'w') as f: f.write(dairy_content)
opens = dairy_content.count('['); closes = dairy_content.count(']')
print(f"  {'✅' if opens==closes else '❌'} Saved: {opens}[ = {closes}]")

print("\n=== Building wine-data.php ===")
wine_content = build_data_file('wine-data.php', 'wineData', WINE_CATS, wine_footer)
with open(f'{DOC}/wine-data.php', 'w') as f: f.write(wine_content)
opens = wine_content.count('['); closes = wine_content.count(']')
print(f"  {'✅' if opens==closes else '❌'} Saved: {opens}[ = {closes}]")

print("\n=== Building industrial-data.php ===")
ind_content = build_data_file('industrial-data.php', 'industrialData', IND_CATS, industrial_footer, {'storage': 'ind-storage'})
with open(f'{DOC}/industrial-data.php', 'w') as f: f.write(ind_content)
opens = ind_content.count('['); closes = ind_content.count(']')
print(f"  {'✅' if opens==closes else '❌'} Saved: {opens}[ = {closes}]")

# ═══ VERIFY ═══
print("\n=== VERIFICATION ===")
all_ok = True
for fn in ['dairy-data.php', 'wine-data.php', 'industrial-data.php']:
    with open(f'{DOC}/{fn}') as f: c = f.read()
    o = c.count('['); cl = c.count(']')
    bal = '✅' if o == cl else '❌'
    spec_ref = c.count("spec_ref")
    specs_count = c.count("'specs' => [")
    print(f"  {fn}: {bal} [{o} = {cl}] specs={specs_count} spec_ref={spec_ref}")
    if spec_ref > 0:
        print(f"    ⚠ spec_ref still present! ({spec_ref})")
        all_ok = False

# Quick price spot-check
for slug in ['vdp','fermentation','red-fermentation','storage','mixing','pressure']:
    with open(f'{DOC}/{"dairy-data.php" if slug in ["vdp","fermentation","storage"] else ("wine-data.php" if slug in ["red-fermentation"] else "industrial-data.php")}') as f:
        c = f.read()
    for vol, exp in {
        'vdp': {200: 171000, 1000: 350455, 5000: 1045000},
        'fermentation': {500: 237405, 1000: 292585, 5000: 624577},
        'red-fermentation': {500: 237405, 1000: 292585, 5000: 624577},
        'storage': {5000: 404138, 10000: 566580},
        'mixing': {1000: 309978, 5000: 668475},
        'pressure': {1000: 395850, 2000: 522870},
    }.items():
        if vol not in vols: continue
        pat = rf"{vol}\s*=>\s*\[[^\]]*'price'\s*=>\s*{exp}"
        if not re.search(pat, c, re.DOTALL):
            print(f"  ❌ {slug} {vol}l: price {exp} not found!")
            all_ok = False

if all_ok:
    print("\n✅ ALL FILES OK!")
else:
    print("\n⚠ Issues found!")
