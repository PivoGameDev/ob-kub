#!/usr/bin/env python3
"""
Replace dynamic spec_ref + formula with hardcoded specs.
Set EXACT target prices (competitor -5% or ЦКТ×коэфф×0.95) for EVERY volume.
"""
import re, math, urllib.request, ssl
ssl._create_default_https_context = ssl._create_unverified_context
DOC = '/Users/tretyakov/Desktop/Апгрейд/catalog'

# ═══ FETCH ALL PRICES FROM SITE ═══
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
    'vdp': '/catalog/dairy/vdp/', 'cooler': '/catalog/dairy/cooler/',
    'cheese-maker': '/catalog/dairy/cheese-maker/', 'storage': '/catalog/dairy/storage/',
    'fermentation': '/catalog/dairy/fermentation/', 'cottage-cheese': '/catalog/dairy/cottage-cheese/',
    'yeast': '/catalog/dairy/yeast/', 'brine': '/catalog/dairy/brine/',
    'reception': '/catalog/dairy/reception/',
    'red-fermentation': '/catalog/wine/red-fermentation/', 'white-fermentation': '/catalog/wine/white-fermentation/',
    'storage-aging': '/catalog/wine/storage-aging/', 'cold-stabilization': '/catalog/wine/cold-stabilization/',
    'blending': '/catalog/wine/blending/', 'sulfitation': '/catalog/wine/sulfitation/',
    'universal-tank': '/catalog/wine/universal-tank/',
    'mixing': '/catalog/industrial/mixing/', 'thermal': '/catalog/industrial/thermal/',
    'pressure': '/catalog/industrial/pressure/',
    'ind-storage': '/catalog/industrial/storage/',
}
print("Fetching site prices...")
SITE = {}
for slug, url in CAT_URLS.items():
    SITE[slug] = site_prices(url)
    print(f"  {slug}: {len(SITE[slug])} volumes")

# ═══ COMPETITOR DATA ═══
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
        ('Охл',10000,1750000),  # АгроДеталь cheaper
        ('Сыроизг',200,385952),('Сыроизг',500,507455),('Сыроизг',1000,825000),('Сыроизг',10000,2348000)]
BEST={}
for c,v,p in COMP:
    k=(c,v); BEST[k] = min(p, BEST.get(k, float('inf')))
def comp(c,v):
    k=(c,v)
    if k in BEST: return BEST[k]
    kk=sorted([x for (a,x) in BEST if a==c])
    kk=[(x,BEST[(c,x)]) for x in kk]
    if not kk: return None
    if v<=kk[0][0]: return kk[0][1]*v/kk[0][0]
    if v>=kk[-1][0]: return kk[-1][1]*v/kk[-1][0]
    for i in range(len(kk)-1):
        if kk[i][0]<=v<=kk[i+1][0]:
            r=(v-kk[i][0])/(kk[i+1][0]-kk[i][0])
            return kk[i][1]+(kk[i+1][1]-kk[i][1])*r
    return None

CPX = {
    'ВДП':1.0,'Охладители молока':1.0,'Сыроизготовители':1.0,'Резервуары хранения':0.6,
    'Ферментационные танки':0.85,'Творогоизготовители':0.7,'Заквасочники':0.5,
    'Контейнеры для соления':0.45,'Ёмкости приёмки':0.55,'Красная ферментация':0.85,
    'Белая ферментация':0.85,'Выдержка и хранение':0.6,'Холодная стабилизация':0.85,
    'Купажирование':0.7,'Сульфитация':0.6,'Винификатор':1.05,'Ёмкости с мешалкой':0.9,
    'Ёмкости с терморегуляцией':0.85,'Ёмкости под давлением':1.15,
}
DIRECT = {'ВДП':'ВДП','Охладители молока':'Охл','Сыроизготовители':'Сыроизг'}

def target(cat, vol):
    d=DIRECT.get(cat); c=comp(d,vol) if d else None
    if c: return int(c*0.95)
    c=comp('ЦКТ',vol)
    if c: return int(c*CPX.get(cat,1.0)*0.95)
    return None

# ═══ COMPUTE ALL TARGETS ═══
SLUG_CAT = {
    'vdp':'ВДП','cooler':'Охладители молока','cheese-maker':'Сыроизготовители',
    'reception':'Ёмкости приёмки','storage':'Резервуары хранения',
    'fermentation':'Ферментационные танки','cottage-cheese':'Творогоизготовители',
    'yeast':'Заквасочники','brine':'Контейнеры для соления',
    'ind-storage':'Резервуары хранения',
    'mixing':'Ёмкости с мешалкой','thermal':'Ёмкости с терморегуляцией',
    'pressure':'Ёмкости под давлением',
    'red-fermentation':'Красная ферментация','white-fermentation':'Белая ферментация',
    'storage-aging':'Выдержка и хранение','cold-stabilization':'Холодная стабилизация',
    'blending':'Купажирование','sulfitation':'Сульфитация','universal-tank':'Винификатор',
}

TARGETS = {}
for slug, vols in SITE.items():
    cat_name = SLUG_CAT.get(slug, slug)
    for v in vols:
        tp = target(cat_name, v)
        if tp: TARGETS[(slug, v)] = tp

print(f"\nComputed {len(TARGETS)} exact targets")

# ═══ GENERATE HARDCODED SPECS ═══
def gen_specs_php(slug, vols, ref_dict, ref_vol):
    """Generate PHP array string for hardcoded specs with exact target prices"""
    entries = []
    for vol in sorted(vols):
        tp = TARGETS.get((slug, vol))
        if tp is None:
            print(f"  ⚠ {slug} {vol}l: no target, using site price {vols.get(vol,'?')}")
            tp = vols.get(vol, 0)
        
        ratio = vol / ref_vol
        cr = pow(ratio, 1/3)
        sr = pow(ratio, 2/3)
        
        items = {}
        for k, v in ref_dict.items():
            if k == 'price':
                items[k] = str(int(tp))
            elif k == 'diameter':
                items[k] = str(max(200, round(int(v) * cr)))
            elif k == 'height':
                items[k] = str(max(300, round(int(v) * cr)))
            elif k == 'wall':
                w = 2
                if vol >= 3000: w = 3
                if vol >= 10000: w = 4
                if vol >= 50000: w = 5
                if vol >= 100000: w = 6
                items[k] = str(w)
            elif k == 'weight':
                try: items[k] = str(max(10, round(int(v) * sr)))
                except: items[k] = v
            elif k == 'power':
                try: items[k] = str(round(float(v) * sr, 1))
                except: items[k] = v
            else:
                items[k] = v
        
        # full_volume, working_volume
        dia = int(ref_dict.get('diameter', 1000)) * cr
        hgt = int(ref_dict.get('height', 1000)) * cr
        fv = round(math.pi * (dia/2)**2 * hgt / 1000)
        items['full_volume'] = str(fv)
        items['working_volume'] = str(round(fv * 0.8))
        
        lines = ",\n".join(f"            '{k}' => {v}" for k, v in items.items())
        entries.append(f"        {vol} => [\n{lines}\n        ]")
    
    return "[\n" + ",\n".join(entries) + "\n    ]"

# ═══ UPDATE DAIRY, INDUSTRIAL, WINE ═══
FILES = [
    ('dairy-data.php', ['reception','cooler','vdp','fermentation','cheese-maker',
                        'cottage-cheese','yeast','brine','storage']),
    ('industrial-data.php', ['ind-storage','mixing','thermal','pressure']),
    ('wine-data.php', ['red-fermentation','white-fermentation','storage-aging',
                       'cold-stabilization','blending','sulfitation','universal-tank']),
]

for filename, slugs in FILES:
    print(f"\n=== {filename} ===")
    with open(f'{DOC}/{filename}') as f: content = f.read()
    
    for slug in slugs:
        slug_key = 'storage' if filename != 'industrial-data.php' else slug
        if slug == 'ind-storage': slug_key = 'storage'
        
        vols = SITE.get(slug, {})
        if not vols and slug != 'ind-storage': 
            print(f"  ⚠ {slug}: no site data")
            continue
        if slug == 'ind-storage':
            vols = SITE.get('ind-storage', {})
            slug_key = 'storage'
        
        # Find spec_ref and extract ref vol + specs
        cat_start = content.find(f"'{slug_key}'")
        if cat_start < 0:
            print(f"  ⚠ {slug}: not found in file")
            continue
        
        search_zone = content[cat_start:cat_start+3000]
        # Find spec_ref content
        ref_m = re.search(r"'spec_ref'\s*=>\s*\[(\d+)\s*=>\s*\[(.*?)\]", search_zone, re.DOTALL)
        if not ref_m:
            print(f"  ⚠ {slug}: spec_ref not found")
            continue
        
        ref_vol = int(ref_m.group(1))
        ref_spec_str = ref_m.group(2)
        ref_pairs = re.findall(r"'(\w+)'\s*=>\s*([^,]+)(?:,|$)", ref_spec_str)
        ref_dict = {k: v.strip() for k, v in ref_pairs}
        
        # Generate hardcoded specs
        specs_php = gen_specs_php(slug if slug != 'ind-storage' else 'storage', vols, ref_dict, ref_vol)
        
        # Replace 'spec_ref' => [...] with 'specs' => [...]
        old_spec_ref = re.search(
            rf"('{slug_key}'\s*=>\s*\[.*?)'spec_ref'\s*=>\s*\[.*?\]\s*,",
            content, re.DOTALL
        )
        if not old_spec_ref:
            print(f"  ⚠ {slug}: cannot locate spec_ref block")
            continue
        
        before_spec_ref = old_spec_ref.group(1)
        new_section = before_spec_ref + f"'specs' => {specs_php},"
        
        # Find what comes after spec_ref (rest of the block)
        after_ref = content[old_spec_ref.end():]
        # Find the next ], or end of the slug block
        content = new_section + after_ref
        
        print(f"  {slug}: {len(vols)} volumes hardcoded (ref vol {ref_vol}l)")
    
    # Remove function call _*Volumes($*Data);
    content = re.sub(r'\n_(dairy|industrial|wine)Volumes\(\$(dairy|industrial|wine)Data\);\n', '\n', content)
    
    with open(f'{DOC}/{filename}', 'w') as f: f.write(content)
    print(f"  Saved {filename}")

print("\n✅ All files updated with exact target prices!")
