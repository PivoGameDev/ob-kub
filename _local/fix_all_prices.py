#!/usr/bin/env python3
"""
HARDCODE ALL PRICES — FINAL CORRECT VERSION.

For dairy/wine: replace spec_ref with hardcoded specs (exact target prices).
For industrial: reconstruct lost categories + hardcode all specs.
Preserves content before/after match (fixes previous bug).
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
    'reception':'/catalog/dairy/reception/', 'cooler':'/catalog/dairy/cooler/',
    'vdp':'/catalog/dairy/vdp/', 'fermentation':'/catalog/dairy/fermentation/',
    'cheese-maker':'/catalog/dairy/cheese-maker/', 'storage':'/catalog/dairy/storage/',
    'cottage-cheese':'/catalog/dairy/cottage-cheese/', 'yeast':'/catalog/dairy/yeast/',
    'brine':'/catalog/dairy/brine/',
    'ind-storage':'/catalog/industrial/storage/', 'mixing':'/catalog/industrial/mixing/',
    'thermal':'/catalog/industrial/thermal/', 'pressure':'/catalog/industrial/pressure/',
    'red-fermentation':'/catalog/wine/red-fermentation/', 'white-fermentation':'/catalog/wine/white-fermentation/',
    'storage-aging':'/catalog/wine/storage-aging/', 'cold-stabilization':'/catalog/wine/cold-stabilization/',
    'blending':'/catalog/wine/blending/', 'sulfitation':'/catalog/wine/sulfitation/',
    'universal-tank':'/catalog/wine/universal-tank/',
}
print("Fetching site prices...")
SITE = {}
for slug, url in CAT_URLS.items():
    SITE[slug] = site_prices(url)
    print(f"  {slug}: {len(SITE[slug])} volumes")

# ═══ COMPETITOR DATA ═══
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

def comp(c,v):
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
    'vdp':1.0, 'cooler':1.0, 'cheese-maker':1.0, 'storage':0.6, 'reception':0.55,
    'fermentation':0.85, 'cottage-cheese':0.7, 'yeast':0.5, 'brine':0.45,
    'ind-storage':0.6, 'mixing':0.9, 'thermal':0.85, 'pressure':1.15,
    'red-fermentation':0.85, 'white-fermentation':0.85, 'storage-aging':0.6,
    'cold-stabilization':0.85, 'blending':0.7, 'sulfitation':0.6, 'universal-tank':1.05,
}

def target(slug, vol):
    d=DIRECT.get(slug); c=comp(d,vol) if d else None
    if c: return int(c*0.95)
    c=comp('ЦКТ',vol)
    if c and slug in CPX: return int(c*CPX[slug]*0.95)
    return None

# ═══ REFERENCE SPECS (diameter, height, wall, weight, power) for categories with no spec_ref ═══
# Format: {slug: {ref_vol: {diameter, height, wall, weight, power}}}
REF_SPECS = {
    'ind-storage': {5000: {'diameter': 1600, 'height': 3000, 'wall': 3, 'weight': 350, 'power': 0}},
    'mixing': {1000: {'diameter': 1000, 'height': 1500, 'wall': 3, 'weight': 200, 'power': 2.2}},
    'thermal': {2000: {'diameter': 1200, 'height': 2000, 'wall': 2, 'weight': 280, 'power': 12}},
}

print("\nComputing target prices...")
TARGETS = {}
for slug, vols in SITE.items():
    for v in vols:
        tp = target(slug, v)
        if tp:
            TARGETS[(slug, v)] = tp
print(f"  {len(TARGETS)} exact targets")

# ═══ GENERATE HARDCODED SPECS ARRAY ═══
def gen_specs(slug, vols, ref_dict, ref_vol):
    entries = []
    for vol in sorted(vols):
        tp = TARGETS.get((slug, vol))
        if tp is None:
            print(f"  ⚠ {slug} {vol}l: no target, skipped")
            continue
        ratio = vol / ref_vol
        cr = pow(ratio, 1/3)
        sr = pow(ratio, 2/3)
        items = {}
        for k, v in ref_dict.items():
            if k == 'price':
                items['price'] = str(int(tp))
            elif k == 'diameter':
                items['diameter'] = str(max(200, round(int(v) * cr)))
            elif k == 'height':
                items['height'] = str(max(300, round(int(v) * cr)))
            elif k == 'wall':
                w = 2
                if vol >= 3000: w = 3
                if vol >= 10000: w = 4
                if vol >= 50000: w = 5
                if vol >= 100000: w = 6
                items['wall'] = str(w)
            elif k == 'weight':
                try: items['weight'] = str(max(10, round(int(v) * sr)))
                except: items['weight'] = str(v)
            elif k == 'power':
                try: items['power'] = str(round(float(v) * sr, 1))
                except: items['power'] = str(v)
            else:
                items[k] = str(v)
        dia = int(items.get('diameter', '1000'))
        hgt = int(items.get('height', '1000'))
        fv = round(math.pi * (dia/2)**2 * hgt / 1000)
        items['full_volume'] = str(fv)
        items['working_volume'] = str(round(fv * 0.8))
        lines = ",\n".join(f"            '{k}' => {v}" for k, v in items.items())
        entries.append(f"        {vol} => [\n{lines}\n        ]")
    return "[\n" + ",\n".join(entries) + "\n    ]"

def gen_specs_from_specs(slug, vols, ref_specs_dict, ref_vol):
    """Generate specs from an existing specs dict {vol: {key: val}}"""
    ref_dict = ref_specs_dict[ref_vol]
    return gen_specs(slug, vols, ref_dict, ref_vol)

# ═══ EXTRACT ref spec from spec_ref block ═══
def parse_ref_spec(spec_ref_text):
    pairs = re.findall(r"'(\w+)'\s*=>\s*([^,\]]+)", spec_ref_text)
    return {k: v.strip() for k, v in pairs}

# ════════════════════════════════════════════════════
# 1. DAIRY & WINE — replace spec_ref with specs
# ════════════════════════════════════════════════════
FILES_DIRECT = [
    ('dairy-data.php', ['vdp','fermentation','cheese-maker','cottage-cheese','yeast','brine']),
    ('wine-data.php', ['red-fermentation','white-fermentation','storage-aging',
                       'cold-stabilization','blending','sulfitation','universal-tank']),
]

for filename, slugs in FILES_DIRECT:
    print(f"\n=== {filename} ===")
    with open(f'{DOC}/{filename}') as f: content = f.read()
    changed = False
    
    for slug in slugs:
        cat_start = content.find(f"'{slug}'")
        if cat_start < 0:
            print(f"  ⚠ {slug}: not found")
            continue
        
        # Find spec_ref within the category
        spec_ref_m = re.search(rf"('{re.escape(slug)}'\s*=>\s*\[.*?)'spec_ref'\s*=>\s*\[", content, re.DOTALL)
        if not spec_ref_m:
            print(f"  ⚠ {slug}: spec_ref not found")
            continue
        
        before_spec_ref = spec_ref_m.group(1)  # from slug opener to just before 'spec_ref'
        spec_ref_start = spec_ref_m.end() - 1  # position of the '[' after 'spec_ref' =>
        
        # Now find the matching closing bracket of spec_ref's value
        # The value is: [vol => ['...']]
        # We need to find the outer closing ] followed by ,
        rest = content[spec_ref_start:]
        # Find the outer ] that closes the spec_ref value, followed by ,
        depth = 0
        end_idx = -1
        for i, ch in enumerate(rest):
            if ch == '[': depth += 1
            elif ch == ']': 
                depth -= 1
                if depth == 0:
                    end_idx = i + 1  # after the ]
                    break
        if end_idx < 0 or end_idx >= len(rest):
            print(f"  ⚠ {slug}: cannot find closing bracket of spec_ref")
            continue
        
        # rest[:end_idx] = [vol => [...]]
        # The full spec_ref match ends at spec_ref_start + end_idx
        spec_ref_end = spec_ref_start + end_idx
        # After spec_ref's closing ], there should be a comma
        if content[spec_ref_end:spec_ref_end+1] == ',':
            spec_ref_end += 1
        
        # Extract ref vol and specs from the spec_ref value
        inner = rest[:end_idx]  # [vol => [...]]
        ref_m = re.search(r'(\d+)\s*=>\s*\[(.*?)\]', inner, re.DOTALL)
        if not ref_m:
            print(f"  ⚠ {slug}: cannot parse spec_ref content")
            continue
        
        ref_vol = int(ref_m.group(1))
        ref_dict = parse_ref_spec(ref_m.group(2))
        
        # Get volumes from the slug's volumes array
        vol_m = re.search(rf"'{slug}'[^[]*\[.*?'volumes'\s*=>\s*\[([^\]]+)\]", content[cat_start:], re.DOTALL)
        if not vol_m:
            print(f"  ⚠ {slug}: cannot find volumes array")
            continue
        volumes = [int(v.strip()) for v in vol_m.group(1).split(',') if v.strip()]
        
        # Build vols dict from SITE and targets
        vols = SITE.get(slug, {})
        
        # Generate new specs PHP
        specs_php = gen_specs(slug, vols, ref_dict, ref_vol)
        new_part = f"'specs' => {specs_php},"
        
        # Replace spec_ref block with specs block (preserving content before/after)
        full_old = content[spec_ref_m.start():spec_ref_end]
        content = content[:spec_ref_m.start()] + new_part + content[spec_ref_end:]
        changed = True
        print(f"  {slug}: {len(volumes)} volumes hardcoded (ref {ref_vol}l)")
    
    if changed:
        with open(f'{DOC}/{filename}', 'w') as f: f.write(content)
        print(f"  ✅ Saved {filename}")
    else:
        print(f"  No changes to {filename}")

# ════════════════════════════════════════════════════
# 2. INDUSTRIAL — reconstruct + hardcode all specs
# ════════════════════════════════════════════════════
print(f"\n=== industrial-data.php ===")
with open(f'{DOC}/industrial-data.php') as f: orig = f.read()

# The file has: (contents before $industrialData), pressure specs, then rest of file
# We need to insert storage, mixing, thermal before pressure
# Since the categories were lost, build the entire $industrialData section

# Reconstruct category metadata
def build_category(slug, name, name_short, title, desc, h1, image, features, volumes, ref_vol, ref_spec):
    vols = SITE.get(slug, {})
    specs_php = gen_specs(slug, vols, ref_spec, ref_vol)
    feat_php = ",\n".join(f"            '{f}'" for f in features)
    vol_php = ", ".join(str(v) for v in volumes)
    return f"""    '{slug}' => [
        'name' => '{name}',
        'name_short' => '{name_short}',
        'title' => '{title}',
        'desc' => '{desc}',
        'h1' => '{h1}',
        'image' => '{image}',
        'features' => [
{feat_php}
        ],
        'volumes' => [{vol_php}],
        'specs' => {specs_php},
    ]"""

# Industrial categories
ind_cats = []
for slug, (name, name_short, title, h1, image, ref_vol, ref_spec, features, vol_info) in {
    'storage': (
        'Резервуар для хранения', 'Резервуар хранения',
        'Резервуары для хранения — каталог', 'Резервуары для хранения',
        'industrial-storage.jpg', 5000, REF_SPECS['ind-storage'][5000],
        ['Материал: AISI 304 / AISI 316',
         'Вертикальное и горизонтальное исполнение',
         'Люк-лаз DN400 / DN500',
         'CIP-мойка (ротационная головка)',
         'Датчик уровня (опция)',
         'Датчик температуры Pt100',
         'Термоизоляция 50–200 мм ППУ',
         'Опоры регулируемые'],
        {'desc': 'Промышленные резервуары для хранения из AISI 304/316 для пищевых жидкостей, молока, воды, масел. Объёмы от 1000 до 200000 литров.'}
    ),
    'mixing': (
        'Ёмкость с мешалкой', 'Мешалка',
        'Ёмкости с мешалкой — каталог', 'Ёмкости с мешалкой',
        'industrial-mixing.jpg', 1000, REF_SPECS['mixing'][1000],
        ['Материал: AISI 304 / AISI 316',
         'Лопастная / якорная / турбинная мешалка',
         'Мотор-редуктор (частотный привод)',
         'Рубашка нагрев / охлаждение (опция)',
         'Термоизоляция (опция)',
         'Люк-лаз DN400',
         'CIP-мойка',
         'Опоры регулируемые'],
        {'desc': 'Ёмкости с мешалкой из AISI 304/316 для перемешивания, гомогенизации и смешивания пищевых продуктов. Объёмы от 200 до 50000 литров.'}
    ),
    'thermal': (
        'Ёмкость с терморегуляцией', 'Термоёмкость',
        'Ёмкости с терморегуляцией — каталог', 'Ёмкости с терморегуляцией',
        'industrial-thermal.jpg', 2000, REF_SPECS['thermal'][2000],
        ['Материал: AISI 304 / AISI 316',
         'Двустенная рубашка (пар / горячая вода / гликоль)',
         'Термоизоляция 50–100 мм ППУ',
         'PID-регулятор температуры',
         'Мешалка лопастная (опция)',
         'Датчик температуры Pt100',
         'Люк-лаз DN400',
         'CIP-мойка'],
        {'desc': 'Терморегулируемые ёмкости из AISI 304/316 для нагрева, охлаждения и выдержки пищевых продуктов. Объёмы от 200 до 50000 литров.'}
    ),
}.items():
    vol_list = sorted(SITE.get(slug, {}).keys())
    if not vol_list:
        print(f"  ⚠ {slug}: no site data, skipping")
        continue
    cat_php = build_category(slug, name, name_short, title, vol_info['desc'], h1, image, features, vol_list, ref_vol, ref_spec)
    ind_cats.append(cat_php)

# Extract pressure's existing specs from current file
pressure_start = orig.find("'pressure' => [")
pressure_end = orig.find("\n    ],", pressure_start)
# Find the next section after the core data array
next_section_start = orig.find("];\n", pressure_end)
if next_section_start < 0:
    next_section_start = len(orig)

# Extract pressure category content (already has hardcoded specs)
pressure_content = orig[pressure_start:pressure_end]

# Build complete file
header = "$industrialData = [\n\n"
footer = "\n];\n\n" + orig[next_section_start+2:]

new_content = header + ",\n\n".join(ind_cats) + ",\n\n" + pressure_content + footer

with open(f'{DOC}/industrial-data.php', 'w') as f:
    f.write(new_content)
print(f"  ✅ Reconstructed and saved industrial-data.php")

# ════════════════════════════════════════════════════
# VERIFICATION
# ════════════════════════════════════════════════════
print("\n\n=== VERIFICATION ===")
all_ok = True
for fn in ['dairy-data.php', 'wine-data.php', 'industrial-data.php']:
    with open(f'{DOC}/{fn}') as f: c = f.read()
    opens = c.count('[')
    closes = c.count(']')
    bal = '✅' if opens == closes else '❌'
    specs_count = c.count("'specs' => [")
    spec_ref_count = c.count("'spec_ref'")
    if spec_ref_count > 0:
        print(f"  {fn}: {bal} spec_ref still present: {spec_ref_count}, specs: {specs_count}")
        all_ok = False
    else:
        print(f"  {fn}: {bal} brackets balanced, {specs_count} specs sections, {opens} opens = {closes} closes")

if all_ok:
    print("\n✅ All files updated successfully!")
else:
    print("\n⚠ Some issues remain!")
