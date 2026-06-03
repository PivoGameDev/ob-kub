#!/usr/bin/env python3
"""
Update ONLY prices in PHP data files. No structural changes.
Keeps spec_ref, keeps _*Volumes() calls, keeps everything as-is.
"""
import re, math
DOC = '/Users/tretyakov/Desktop/Апгрейд/catalog'

# ═══════════════════════════════════════════════════
# TARGET PRICES (copied from build_full.py computation)
# ═══════════════════════════════════════════════════
# Directly computed values for each category × volume

# ЦКТ (26 volumes) — target = competitor -5%
CCT = {
    100: 101650, 250: 161500, 500: 279300, 1000: 344218,
    1500: 399000, 2000: 454670, 2500: 0, 3000: 532618,
    4000: 617500, 5000: 734797, 6000: 855000, 7500: 950000,
    8000: 1045000, 10000: 993320, 12000: 1140000, 15000: 1330000,
    20000: 1710000, 25000: 2090000, 30000: 2660000, 40000: 3420000,
    50000: 4275000, 60000: 5225000, 80000: 6840000, 100000: 8550000,
    120000: 10450000, 150000: 13300000, 200000: 17575000,
}

# Заторные — ЦКТ×0.85×0.95
MASH = {250: 137275, 500: 237405, 1000: 292585, 2000: 386469, 3000: 452724, 5000: 624577}
# Фильтрационные — ЦКТ×0.80×0.95
LAUTER = {250: 129200, 500: 223440, 1000: 275374, 2000: 363736, 3000: 426094, 5000: 587837}
# Сусловарочные — ЦКТ×0.90×0.95
KETTLE = {250: 145350, 500: 251370, 1000: 309796, 2000: 409203, 3000: 479355, 5000: 661316}
# Вильпули — ЦКТ×0.70×0.95
WHIRLPOOL = {250: 113049, 500: 195510, 1000: 240952, 2000: 318269, 3000: 372832, 5000: 514357}
# Сборники сусла — ЦКТ×0.55×0.95
WORT = {500: 153615, 1000: 189320, 2000: 250068, 3000: 292939, 5000: 404138}
# БГВ — ЦКТ×0.45×0.95
BGV = {500: 125685, 1000: 154898, 1500: 179550, 2000: 204601, 3000: 239677,
       4000: 277875, 5000: 330658, 6000: 384750, 8000: 470250, 10000: 446994,
       15000: 598500, 20000: 769500}
# Форфасы — ЦКТ×0.85×0.95
UNITANK = {250: 137275, 500: 237405, 1000: 292585, 1500: 339150, 2000: 386469, 3000: 452724, 5000: 624577}

# Helper to find and replace ONE price in PHP
def update_price(content, vol, new_price, key_name='price'):
    """Replace first occurrence of '{vol} => [...'price' => OLD]' with new_price"""
    old = re.search(
        rf"({vol}\s*=>\s*\[.*?'{key_name}'\s*=>\s*)(\d+)([\s,])",
        content, re.DOTALL
    )
    if not old:
        return content, False
    content = content[:old.start(2)] + str(new_price) + content[old.end(2):]
    old_val = int(old.group(2))
    if old_val != new_price:
        print(f"    {vol}: {old_val:,} → {new_price:,} ₽".replace(',', ' '))
    return content, True

def update_spec_ref_price(content, slug, new_ref_price):
    """Replace price inside spec_ref for a given category slug"""
    old = re.search(
        rf"('{slug}'.*?spec_ref.*?price\s*=>\s*)(\d+)",
        content, re.DOTALL
    )
    if not old:
        return content, False
    content = content[:old.start(2)] + str(new_ref_price) + content[old.end(2):]
    return content, True

# ═══════════════════════════════════════════════════
# 1. cct-data.php
# ═══════════════════════════════════════════════════
print("1. cct-data.php (ЦКТ)...")
with open(f'{DOC}/cct-data.php') as f: c = f.read()
changed = 0
for vol, price in sorted(CCT.items()):
    c, ok = update_price(c, vol, price, 'from_price')
    if ok: changed += 1
    else: print(f"  ⚠ ЦКТ {vol} not found")
with open(f'{DOC}/cct-data.php', 'w') as f: f.write(c)
print(f"  Updated {changed}/{len(CCT)} volumes")

# ═══════════════════════════════════════════════════
# 2. brew-house-data.php
# ═══════════════════════════════════════════════════
print("\n2. brew-house-data.php...")
with open(f'{DOC}/brew-house-data.php') as f: b = f.read()
total = 0
for name, data in [('Заторные', MASH), ('Фильтрационные', LAUTER), ('Сусловарочные', KETTLE),
                    ('Вильпули', WHIRLPOOL), ('Суслосборники', WORT)]:
    n = 0
    for vol, price in sorted(data.items()):
        b, ok = update_price(b, vol, price)
        if ok: n += 1
    print(f"  {name}: {n}/{len(data)}")
    total += n
with open(f'{DOC}/brew-house-data.php', 'w') as f: f.write(b)
print(f"  Total: {total}")

# ═══════════════════════════════════════════════════
# 3. beer-extra-data.php
# ═══════════════════════════════════════════════════
print("\n3. beer-extra-data.php...")
with open(f'{DOC}/beer-extra-data.php') as f: e = f.read()
total = 0
for name, data in [('БГВ', BGV), ('Форфасы', UNITANK)]:
    n = 0
    for vol, price in sorted(data.items()):
        e, ok = update_price(e, vol, price)
        if ok: n += 1
    print(f"  {name}: {n}/{len(data)}")
    total += n
with open(f'{DOC}/beer-extra-data.php', 'w') as f: f.write(e)
print(f"  Total: {total}")

# ═══════════════════════════════════════════════════
# 4-6. dairy / industrial / wine — adjust spec_ref price
# ═══════════════════════════════════════════════════
# The formula in _*Specs is:
#   price = max(round($ref['price'] * pow(ratio, 0.78) / 500) * 500, 5000)
#   price = round($price * 1.3)
# So for reference volume: final = round(round(ref_price / 500) * 500 * 1.3)
# => ref_price = round(target / 1.3 / 500) * 500

def calc_ref_price(target):
    """Given target price for spec_ref's reference volume, compute spec_ref price"""
    return round(round(target / 1.3 / 500) * 500)

# Dairy categories (reference volume → target price)
DAIRY_REFS = {
    'reception':   (1000, 163400),  # ЦКТ1000×0.55×0.95
    'cooler':      (1000, 297160),  # Охл1000×0.95
    'storage':     (5000, 418950),  # ЦКТ×0.60×0.95
    'vdp':         (200,  171000),  # ВДП200×0.95
    'fermentation':(500,  293929),  # ЦКТ500×0.85×0.95
    'cheese-maker':(500,  481582),  # Сыроизг500×0.95
    'cottage-cheese':(500, 0),      # ЦКТ500×0.70×0.95 = 185711
    'yeast':       (50,   0),       # ЦКТ50 прокси
    'brine':       (200,  0),       # ЦКТ200×0.45×0.95
}

# Compute targets for all dairy volumes programmatically
# Since the dairy formula auto-generates all volumes from one spec_ref,
# I only need to set the spec_ref price so ref volume = target
# Other volumes will scale proportionally via the formula

# Actually for dairy/industrial/wine, the formula has a ×1.3 markup.
# I need to set spec_ref price such that the OUTPUT for ref volume = target.
# OUTPUT = round(round(ref_price * 1 / 500) * 500 * 1.3) = round(ref_price / 500) * 500 * 1.3 (approx)

DAIRY_REF_PRICES = {
    'reception':   round(163400 / 1.3 / 500) * 500,      # ~125500
    'cooler':      round(297160 / 1.3 / 500) * 500,      # ~228500
    'storage':     round(418950 / 1.3 / 500) * 500,      # ~322000
    'vdp':         round(171000 / 1.3 / 500) * 500,      # ~131500
    'fermentation':round(293929 / 1.3 / 500) * 500,      # ~226000
    'cheese-maker':round(481582 / 1.3 / 500) * 500,      # ~370000
    'cottage-cheese': round(0 / 1.3 / 500) * 500,        # TBD
    'yeast':       round(0 / 1.3 / 500) * 500,           # TBD
    'brine':       round(0 / 1.3 / 500) * 500,           # TBD
}

IND_REF_PRICES = {
    'storage':     round(0 / 1.3 / 500) * 500,
    'mixing':      round(0 / 1.3 / 500) * 500,
    'thermal':     round(0 / 1.3 / 500) * 500,
    'pressure':    round(0 / 1.3 / 500) * 500,
}

WINE_REF_PRICES = {
    'red-fermentation':  round(0 / 1.3 / 500) * 500,
    'white-fermentation':round(0 / 1.3 / 500) * 500,
    'storage-aging':     round(0 / 1.3 / 500) * 500,
    'cold-stabilization':round(0 / 1.3 / 500) * 500,
    'blending':          round(0 / 1.3 / 500) * 500,
    'sulfitation':       round(0 / 1.3 / 500) * 500,
    'universal-tank':    round(0 / 1.3 / 500) * 500,
}

# Actually compute all targets properly in a loop
print("\n4. Computing dairy targets...")
import urllib.request, ssl, json
ssl._create_default_https_context = ssl._create_unverified_context

def get_site_price(cat_url):
    """Fetch current price for a specific product page"""
    try:
        req = urllib.request.Request(cat_url, headers={'User-Agent': 'Mozilla/5.0'})
        html = urllib.request.urlopen(req, timeout=10).read().decode('utf-8')
        for m in re.finditer(r'"url"\s*:\s*"([^"]*/(\d+)l/)"\s*,\s*"name"\s*:\s*"[^"]*"\s*,\s*"offers"\s*:\s*\{[^}]*"price"\s*:\s*"(\d+)"', html):
            vol = int(m.group(2))
            pr = float(m.group(3))
            if pr > 0: return {vol: pr}
        return {}
    except: return {}

# ═══ Compute target for ANY (category, volume) ═══
COMP = [
    ('ЦКТ', 100, 107000), ('ЦКТ', 250, 170000), ('ЦКТ', 500, 294000), ('ЦКТ', 1000, 362335),
    ('ЦКТ', 1500, 420000), ('ЦКТ', 2000, 478600), ('ЦКТ', 3000, 560650), ('ЦКТ', 4000, 650000),
    ('ЦКТ', 5000, 773470), ('ЦКТ', 6000, 900000), ('ЦКТ', 7500, 1000000), ('ЦКТ', 8000, 1100000),
    ('ЦКТ', 10000, 1045600), ('ЦКТ', 12000, 1200000), ('ЦКТ', 15000, 1400000), ('ЦКТ', 20000, 1800000),
    ('ЦКТ', 25000, 2200000), ('ЦКТ', 30000, 2800000), ('ЦКТ', 40000, 3600000), ('ЦКТ', 50000, 4500000),
    ('ЦКТ', 60000, 5500000), ('ЦКТ', 80000, 7200000), ('ЦКТ', 100000, 9000000), ('ЦКТ', 120000, 11000000),
    ('ЦКТ', 150000, 14000000), ('ЦКТ', 200000, 18500000),
    ('ВДП', 200, 180000), ('ВДП', 500, 280000), ('ВДП', 1000, 368900), ('ВДП', 1500, 480000),
    ('ВДП', 2000, 696000), ('ВДП', 3000, 833000), ('ВДП', 4000, 900000), ('ВДП', 5000, 1100000),
    ('ВДП', 6300, 1300000), ('ВДП', 8000, 1500000), ('ВДП', 10000, 1700000),
    ('Сыроизг', 200, 385952), ('Сыроизг', 500, 507455), ('Сыроизг', 1000, 825000), ('Сыроизг', 10000, 2348000),
    ('Охл', 1000, 549940), ('Охл', 5000, 1098890), ('Охл', 10000, 1897870),
]

BEST = {}
for cat, vol, price in COMP:
    k = (cat, vol)
    if k not in BEST or price < BEST[k]: BEST[k] = price

def comp_price(cat, vol):
    k = (cat, vol)
    if k in BEST: return BEST[k]
    known = sorted([(v, p) for (c, v), p in BEST.items() if c == cat])
    if not known: return None
    if vol <= known[0][0]: return known[0][1] * vol / known[0][0]
    if vol >= known[-1][0]: return known[-1][1] * vol / known[-1][0]
    for i in range(len(known)-1):
        if known[i][0] <= vol <= known[i+1][0]:
            v1, p1 = known[i]; v2, p2 = known[i+1]
            r = (vol - v1) / (v2 - v1) if v2 != v1 else 0
            return p1 + (p2 - p1) * r
    return None

# Target categories and their calculation rules
CPX = {
    'ВДП': 1.0, 'Охладители молока': 1.0, 'Сыроизготовители': 1.0,
    'Резервуары хранения': 0.6, 'Ферментационные танки': 0.85,
    'Творогоизготовители': 0.7, 'Заквасочники': 0.5,
    'Контейнеры для соления': 0.45, 'Ёмкости приёмки': 0.55,
    'Красная ферментация': 0.85, 'Белая ферментация': 0.85,
    'Выдержка и хранение': 0.6, 'Холодная стабилизация': 0.85,
    'Купажирование': 0.7, 'Сульфитация': 0.6, 'Винификатор': 1.05,
    'Ёмкости с мешалкой': 0.9, 'Ёмкости с терморегуляцией': 0.85,
    'Ёмкости под давлением': 1.15,
}

DIRECT = {'ВДП': 'ВДП', 'Охладители молока': 'Охл', 'Сыроизготовители': 'Сыроизг'}

def get_target(cat, vol):
    d = DIRECT.get(cat)
    if d:
        p = comp_price(d, vol)
        if p: return int(p * 0.95)
    cpx = CPX.get(cat, 1.0)
    p = comp_price('ЦКТ', vol)
    if p: return int(p * cpx * 0.95)
    return None

# Compute ALL target prices
ALL_TARGETS = {}
site_cats = [
    ('Молоко', 'ВДП'), ('Молоко', 'Охладители молока'), ('Молоко', 'Сыроизготовители'),
    ('Молоко', 'Резервуары хранения'), ('Молоко', 'Ферментационные танки'),
    ('Молоко', 'Творогоизготовители'), ('Молоко', 'Заквасочники'),
    ('Молоко', 'Контейнеры для соления'), ('Молоко', 'Ёмкости приёмки'),
    ('Вино', 'Красная ферментация'), ('Вино', 'Белая ферментация'),
    ('Вино', 'Выдержка и хранение'), ('Вино', 'Холодная стабилизация'),
    ('Вино', 'Купажирование'), ('Вино', 'Сульфитация'), ('Вино', 'Винификатор'),
    ('Пром', 'Резервуары хранения'), ('Пром', 'Ёмкости с мешалкой'),
    ('Пром', 'Ёмкости с терморегуляцией'), ('Пром', 'Ёмкости под давлением'),
]

print("  Fetching all category prices...")
for section, cat in site_cats:
    # Map category to URL
    url_map = {
        'ВДП': '/catalog/dairy/vdp/', 'Охладители молока': '/catalog/dairy/cooler/',
        'Сыроизготовители': '/catalog/dairy/cheese-maker/', 'Резервуары хранения': '/catalog/dairy/storage/',
        'Ферментационные танки': '/catalog/dairy/fermentation/', 'Творогоизготовители': '/catalog/dairy/cottage-cheese/',
        'Заквасочники': '/catalog/dairy/yeast/', 'Контейнеры для соления': '/catalog/dairy/brine/',
        'Ёмкости приёмки': '/catalog/dairy/reception/',
        'Красная ферментация': '/catalog/wine/red-fermentation/', 'Белая ферментация': '/catalog/wine/white-fermentation/',
        'Выдержка и хранение': '/catalog/wine/storage-aging/', 'Холодная стабилизация': '/catalog/wine/cold-stabilization/',
        'Купажирование': '/catalog/wine/blending/', 'Сульфитация': '/catalog/wine/sulfitation/', 'Винификатор': '/catalog/wine/universal-tank/',
        'Ёмкости с мешалкой': '/catalog/industrial/mixing/', 'Ёмкости с терморегуляцией': '/catalog/industrial/thermal/',
        'Ёмкости под давлением': '/catalog/industrial/pressure/',
    }
    url = 'https://ob-kub.ru' + url_map.get(cat, '')
    if not url: continue
    site_prices = get_site_price(url)
    for vol, cur_price in site_prices.items():
        tp = get_target(cat, vol)
        if tp:
            ALL_TARGETS[(cat, vol)] = tp

print(f"  Computed {len(ALL_TARGETS)} targets")

# ═══ Update files ═══
# For dairy/industrial/wine with spec_ref: adjust the base price so ref volume gets correct target
# Formula: final = round(round(ref_price * pow(ratio, 0.78) / 500) * 500 * 1.3)
# For ref volume (ratio=1): final = round(round(ref_price / 500) * 500 * 1.3)
# ref_price needed = round(final_target / 1.3 / 500) * 500
def adjust_spec_ref(content, slug, ref_vol, cat_name):
    """Adjust spec_ref price so that reference volume gets the target price"""
    target = ALL_TARGETS.get((cat_name, ref_vol))
    if not target: return content, False
    # Reverse formula: ref_price = round(target / 1.3 / 500) * 500
    ref_price = round(target / 1.3 / 500) * 500
    # Verify: final = round(round(ref_price / 500) * 500 * 1.3)
    final = round(round(ref_price / 500) * 500 * 1.3)
    diff = abs(final - target)
    
    # Find and replace price inside spec_ref
    m = re.search(rf"('{slug}'.*?spec_ref\s*=>\s*\[.*?price\s*=>\s*)(\d+)", content, re.DOTALL)
    if not m:
        # Try without spec_ref (if already converted)
        m = re.search(rf"('{slug}'.*?specs\s*=>\s*\[.*?{ref_vol}\s*=>\s*\[.*?price\s*=>\s*)(\d+)", content, re.DOTALL)
    if not m:
        return content, False
    old_val = int(m.group(2))
    content = content[:m.start(2)] + str(ref_price) + content[m.end(2):]
    if old_val != ref_price:
        print(f"  {cat_name}: spec_ref {old_val:,} → {ref_price:,} ₽ (ref vol {ref_vol}l → ~{final:,} ₽, ±{diff} ₽)".replace(',', ' '))
    return content, True

# Dairy
print("\n4. dairy-data.php (spec_ref prices)...")
with open(f'{DOC}/dairy-data.php') as f: d = f.read()
dairy_slugs = [('vdp', 200, 'ВДП'), ('cooler', 1000, 'Охладители молока'),
               ('cheese-maker', 500, 'Сыроизготовители'), ('storage', 5000, 'Резервуары хранения'),
               ('fermentation', 500, 'Ферментационные танки'), ('cottage-cheese', 500, 'Творогоизготовители'),
               ('yeast', 50, 'Заквасочники'), ('brine', 200, 'Контейнеры для соления'),
               ('reception', 1000, 'Ёмкости приёмки')]
for slug, ref_vol, cat_name in dairy_slugs:
    d, ok = adjust_spec_ref(d, slug, ref_vol, cat_name)
    if not ok: print(f"  ⚠ {cat_name}: not found")
with open(f'{DOC}/dairy-data.php', 'w') as f: f.write(d)
print("  Done")

# Industrial
print("\n5. industrial-data.php (spec_ref prices)...")
with open(f'{DOC}/industrial-data.php') as f: i = f.read()
ind_slugs = [('storage', 5000, 'Резервуары хранения'), ('mixing', 1000, 'Ёмкости с мешалкой'),
             ('thermal', 2000, 'Ёмкости с терморегуляцией'), ('pressure', 2000, 'Ёмкости под давлением')]
for slug, ref_vol, cat_name in ind_slugs:
    i, ok = adjust_spec_ref(i, slug, ref_vol, cat_name)
    if not ok: print(f"  ⚠ {cat_name}: not found")
with open(f'{DOC}/industrial-data.php', 'w') as f: f.write(i)
print("  Done")

# Wine
print("\n6. wine-data.php (spec_ref prices)...")
with open(f'{DOC}/wine-data.php') as f: w = f.read()
wine_slugs = [('red-fermentation', 500, 'Красная ферментация'), ('white-fermentation', 500, 'Белая ферментация'),
              ('storage-aging', 5000, 'Выдержка и хранение'), ('cold-stabilization', 1000, 'Холодная стабилизация'),
              ('blending', 1000, 'Купажирование'), ('sulfitation', 500, 'Сульфитация'),
              ('universal-tank', 1000, 'Винификатор')]
for slug, ref_vol, cat_name in wine_slugs:
    w, ok = adjust_spec_ref(w, slug, ref_vol, cat_name)
    if not ok: print(f"  ⚠ {cat_name}: not found")
with open(f'{DOC}/wine-data.php', 'w') as f: f.write(w)
print("  Done")

print("\n✅ All done! Only prices were changed, file structure preserved.")
