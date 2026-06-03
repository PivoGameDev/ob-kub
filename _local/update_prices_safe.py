#!/usr/bin/env python3
"""
SAFELY update prices in PHP data files.
Only modifies the 'price'/'from_price' field — never changes structure.
Uses category slug + volume to uniquely identify each price.
"""
import re, math
DOC = '/Users/tretyakov/Desktop/Апгрейд/catalog'

# ═══ TARGET PRICES ═══
# ЦКТ — competitor -5%
CCT = {100:101650,250:161500,500:279300,1000:344218,1500:399000,2000:454670,
       3000:532618,4000:617500,5000:734797,6000:855000,7500:950000,
       8000:1045000,10000:993320,12000:1140000,15000:1330000,20000:1710000,
       25000:2090000,30000:2660000,40000:3420000,50000:4275000,60000:5225000,
       80000:6840000,100000:8550000,120000:10450000,150000:13300000,200000:17575000}

# ПИВО — ЦКТ × complexity × 0.95
brew_specs = {
    'mash-tun':       {250:137275,500:237405,1000:292585,2000:386469,3000:452724,5000:624577},
    'lauter-tun':     {250:129200,500:223440,1000:275374,2000:363736,3000:426094,5000:587837},
    'brew-kettle':    {250:145350,500:251370,1000:309796,2000:409203,3000:479355,5000:661316},
    'whirlpool':      {250:113049,500:195510,1000:240952,2000:318269,3000:372832,5000:514357},
    'wort-receiver':  {500:153615,1000:189320,2000:250068,3000:292939,5000:404138},
    'hot-water-tank': {500:125685,1000:154898,1500:179550,2000:204601,3000:239677,
                       4000:277875,5000:330658,6000:384750,8000:470250,10000:446994,
                       15000:598500,20000:769500},
    'unitank':        {250:137275,500:237405,1000:292585,1500:339150,2000:386469,3000:452724,5000:624577},
}

def update_price(content, slug, vol, new_price, key='price'):
    """Update price within a specific category slug, for a given volume.
    Uses slug to scope the search — unique match guaranteed.
    """
    # Find category block
    cat_start = content.find(f"'{slug}'")
    if cat_start < 0: return content, False
    
    # Search within 4000 chars after slug for the volume+price pattern
    search_zone = content[cat_start:cat_start + 4000]
    m = re.search(
        rf"({vol}\s*=>\s*\[.*?'{key}'\s*=>\s*)(\d+)",
        search_zone, re.DOTALL
    )
    if not m: return content, False
    
    abs_start = cat_start + m.start(2)
    abs_end = cat_start + m.end(2)
    old_val = int(m.group(2))
    if old_val == new_price: return content, True  # already correct, no change
    content = content[:abs_start] + str(new_price) + content[abs_end:]
    print(f"  {slug} {vol}л: {old_val:,} → {new_price:,} ₽".replace(',',' '))
    return content, True

def update_spec_ref(content, slug, new_price):
    """Update price inside spec_ref for a slug"""
    cat_start = content.find(f"'{slug}'")
    if cat_start < 0: return content, False
    search_zone = content[cat_start:cat_start + 3000]
    m = re.search(r"('spec_ref'.*?'price'\s*=>\s*)(\d+)", search_zone, re.DOTALL)
    if not m: return content, False
    abs_start = cat_start + m.start(2)
    abs_end = cat_start + m.end(2)
    content = content[:abs_start] + str(new_price) + content[abs_end:]
    return content, True

def get_current(content, slug, vol, key='price'):
    """Get current price for a slug+vol"""
    cat_start = content.find(f"'{slug}'")
    if cat_start < 0: return None
    search_zone = content[cat_start:cat_start + 4000]
    m = re.search(rf"({vol}\s*=>\s*\[.*?'{key}'\s*=>\s*)(\d+)", search_zone, re.DOTALL)
    return int(m.group(2)) if m else None

# ═══ 1. CCT-DATA.PHP ═══
print("1. cct-data.php...")
with open(f'{DOC}/cct-data.php') as f: c = f.read()
n = 0
for vol, price in sorted(CCT.items()):
    m = re.search(rf"({vol}\s*=>\s*\[.*?'from_price'\s*=>\s*)(\d+)", c, re.DOTALL)
    if m:
        old = int(m.group(2))
        if old != price:
            c = c[:m.start(2)] + str(price) + c[m.end(2):]
            print(f"  ЦКТ {vol}л: {old:,} → {price:,} ₽".replace(',',' '))
            n += 1
    else:
        print(f"  ⚠ ЦКТ {vol}л not found")
with open(f'{DOC}/cct-data.php', 'w') as f: f.write(c)
print(f"  Updated {n}/{len(CCT)}")

# ═══ 2-3. BREW-HOUSE + BEER-EXTRA ═══
for file, slug_list in [
    ('brew-house-data.php', ['mash-tun','lauter-tun','brew-kettle','whirlpool','wort-receiver']),
    ('beer-extra-data.php', ['hot-water-tank','unitank']),
]:
    print(f"\n{['2','3'][file=='beer-extra-data.php']}. {file}...")
    with open(f'{DOC}/{file}') as f: content = f.read()
    n = 0
    for slug in slug_list:
        for vol, price in sorted(brew_specs[slug].items()):
            content, ok = update_price(content, slug, vol, price)
            if ok: n += 1
            else: print(f"  ⚠ {slug} {vol} not found")
    with open(f'{DOC}/{file}', 'w') as f: f.write(content)
    print(f"  Total: {n}")

# ═══ 4-6. DAIRY / INDUSTRIAL / WINE ═══
# These use spec_ref + formula. We adjust spec_ref price so 
# reference volume gets the target price.
# Formula: final = round(round(ref_price / 500) * 500 * 1.3)
# So ref_price ≈ round(target / 1.3 / 500) * 500

def compute_ref_price(target):
    """Compute spec_ref price from target for ref volume"""
    return round(target / 1.3 / 500) * 500

# Compute actual targets from the same logic as build_full.py
COMP = [('ЦКТ',100,107000),('ЦКТ',250,170000),('ЦКТ',500,294000),('ЦКТ',1000,362335),
        ('ЦКТ',1500,420000),('ЦКТ',2000,478600),('ЦКТ',3000,560650),('ЦКТ',4000,650000),
        ('ЦКТ',5000,773470),('ЦКТ',6000,900000),('ЦКТ',7500,1000000),('ЦКТ',8000,1100000),
        ('ЦКТ',10000,1045600),('ЦКТ',12000,1200000),('ЦКТ',15000,1400000),('ЦКТ',20000,1800000),
        ('ЦКТ',25000,2200000),('ЦКТ',30000,2800000),('ЦКТ',40000,3600000),('ЦКТ',50000,4500000),
        ('ЦКТ',60000,5500000),('ЦКТ',80000,7200000),('ЦКТ',100000,9000000),('ЦКТ',120000,11000000),
        ('ЦКТ',150000,14000000),('ЦКТ',200000,18500000),
        ('ВДП',200,180000),('ВДП',500,280000),('ВДП',1000,368900),
        ('Охл',1000,549940),('Охл',5000,1098890),('Охл',10000,1897870),
        ('Сыроизг',200,385952),('Сыроизг',500,507455),('Сыроизг',1000,825000),('Сыроизг',10000,2348000)]
BEST = {}
for c,v,p in COMP:
    k=(c,v)
    if k not in BEST or p < BEST[k]: BEST[k] = p
def comp(c,v):
    k=(c,v)
    if k in BEST: return BEST[k]
    kk=sorted([x for (a,x) in BEST if a==c])
    kk = [(x, BEST[(c,x)]) for x in kk]  # (vol, price) pairs
    if not kk: return None
    if v<=kk[0][0]: return kk[0][1]*v/kk[0][0]
    if v>=kk[-1][0]: return kk[-1][1]*v/kk[-1][0]
    for i in range(len(kk)-1):
        if kk[i][0]<=v<=kk[i+1][0]:
            r=(v-kk[i][0])/(kk[i+1][0]-kk[i][0])
            return kk[i][1]+(kk[i+1][1]-kk[i][1])*r
    return None

CPX = {'ВДП':1.0,'Охладители молока':1.0,'Сыроизготовители':1.0,'Резервуары хранения':0.6,
       'Ферментационные танки':0.85,'Творогоизготовители':0.7,'Заквасочники':0.5,
       'Контейнеры для соления':0.45,'Ёмкости приёмки':0.55,'Красная ферментация':0.85,
       'Белая ферментация':0.85,'Выдержка и хранение':0.6,'Холодная стабилизация':0.85,
       'Купажирование':0.7,'Сульфитация':0.6,'Винификатор':1.05,'Ёмкости с мешалкой':0.9,
       'Ёмкости с терморегуляцией':0.85,'Ёмкости под давлением':1.15,'Резервуары хранения (пром)':0.6}
DIRECT = {'ВДП':'ВДП','Охладители молока':'Охл','Сыроизготовители':'Сыроизг'}

def target(cat, vol):
    d=DIRECT.get(cat); c=comp(d,vol) if d else None
    if c: return int(c*0.95)
    c=comp('ЦКТ',vol)
    if c: return int(c*CPX.get(cat,1.0)*0.95)
    return None

# Build slug → ref_vol → target mapping for spec_ref updates
# (separate for each file to avoid slug collisions)
DAIRY_REFS = {
    'reception': ('Ёмкости приёмки', 1000),
    'cooler': ('Охладители молока', 1000),
    'storage': ('Резервуары хранения', 5000),
    'vdp': ('ВДП', 200),
    'fermentation': ('Ферментационные танки', 500),
    'cheese-maker': ('Сыроизготовители', 500),
    'cottage-cheese': ('Творогоизготовители', 500),
    'yeast': ('Заквасочники', 50),
    'brine': ('Контейнеры для соления', 200),
}
IND_REFS = {
    'storage': ('Резервуары хранения', 5000),
    'mixing': ('Ёмкости с мешалкой', 1000),
    'thermal': ('Ёмкости с терморегуляцией', 2000),
    'pressure': ('Ёмкости под давлением', 2000),
}
WINE_REFS = {
    'red-fermentation': ('Красная ферментация', 500),
    'white-fermentation': ('Белая ферментация', 500),
    'storage-aging': ('Выдержка и хранение', 5000),
    'cold-stabilization': ('Холодная стабилизация', 1000),
    'blending': ('Купажирование', 1000),
    'sulfitation': ('Сульфитация', 500),
    'universal-tank': ('Винификатор', 1000),
}

for file, refs in [
    ('dairy-data.php', DAIRY_REFS),
    ('industrial-data.php', IND_REFS),
    ('wine-data.php', WINE_REFS),
]:
    print(f"\n{['4','5','6'][['dairy','industrial','wine'].index(file.split('-')[0])]}. {file}...")
    with open(f'{DOC}/{file}') as f: content = f.read()
    for slug, (cat_name, ref_vol) in refs.items():
        tp = target(cat_name, ref_vol)
        if not tp: continue
        ref_price = compute_ref_price(tp)
        
        content, ok = update_spec_ref(content, slug, ref_price)
        if ok:
            final_approx = round(round(ref_price / 500) * 500 * 1.3)
            print(f"  {slug}: spec_ref → {ref_price:,} ₽ (ref {ref_vol}л → ≈{final_approx:,} ₽)".replace(',',' '))
        else:
            print(f"  ⚠ {slug}: spec_ref not found")
    
    with open(f'{DOC}/{file}', 'w') as f: f.write(content)

print("\n✅ Done! All prices updated safely.")
