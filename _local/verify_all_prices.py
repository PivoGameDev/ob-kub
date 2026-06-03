#!/usr/bin/env python3
"""
VERIFY EVERY PRICE: compute targets from scratch, read all PHP files, compare.
Reports any mismatch.
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
    'cct':'/catalog/beer/cct/',
    'mash-tun':'/catalog/beer/brew-house/mash-tun/', 'lauter-tun':'/catalog/beer/brew-house/lauter-tun/',
    'brew-kettle':'/catalog/beer/brew-house/brew-kettle/', 'whirlpool':'/catalog/beer/brew-house/whirlpool/',
    'wort-receiver':'/catalog/beer/brew-house/wort-receiver/',
    'hot-water-tank':'/catalog/beer/hot-water-tank/', 'unitank':'/catalog/beer/unitank/',
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

print("Fetching current site prices...")
SITE = {}
for slug, url in CAT_URLS.items():
    SITE[slug] = site_prices(url)
    print(f"  {slug}: {len(SITE[slug])} volumes")

# ═══ COMPETITOR TARGETS ═══
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
# For ЦКТ: direct competitor 'ЦКТ', no CPX factor (no formula)
def target(slug, vol):
    if slug == 'cct':
        c = comp_ref('ЦКТ', vol)
        return int(c * 0.95) if c else None
    d = DIRECT.get(slug); c = comp_ref(d, vol) if d else None
    if c: return int(c * 0.95)
    CPX = {'mash-tun':0.85,'lauter-tun':0.8,'brew-kettle':0.9,'whirlpool':0.7,
           'wort-receiver':0.55,'hot-water-tank':0.45,'unitank':0.85,
           'storage':0.6,'reception':0.55,'fermentation':0.85,'cottage-cheese':0.7,
           'yeast':0.5,'brine':0.45,'vdp':1.0,'cooler':1.0,'cheese-maker':1.0,
           'ind-storage':0.6,'mixing':0.9,'thermal':0.85,'pressure':1.15,
           'red-fermentation':0.85,'white-fermentation':0.85,'storage-aging':0.6,
           'cold-stabilization':0.85,'blending':0.7,'sulfitation':0.6,'universal-tank':1.05}
    c = comp_ref('ЦКТ', vol)
    if c and slug in CPX: return int(c * CPX[slug] * 0.95)
    return None

# ═══ MAP SLUGS → FILES ═══
FILE_MAP = {
    'cct': ('cct-data.php', 'from_price'),
    'mash-tun': ('brew-house-data.php', 'price'),
    'lauter-tun': ('brew-house-data.php', 'price'),
    'brew-kettle': ('brew-house-data.php', 'price'),
    'whirlpool': ('brew-house-data.php', 'price'),
    'wort-receiver': ('brew-house-data.php', 'price'),
    'hot-water-tank': ('beer-extra-data.php', 'price'),
    'unitank': ('beer-extra-data.php', 'price'),
    'storage': ('dairy-data.php', 'price'),
    'reception': ('dairy-data.php', 'price'),
    'cooler': ('dairy-data.php', 'price'),
    'vdp': ('dairy-data.php', 'price'),
    'fermentation': ('dairy-data.php', 'price'),
    'cheese-maker': ('dairy-data.php', 'price'),
    'cottage-cheese': ('dairy-data.php', 'price'),
    'yeast': ('dairy-data.php', 'price'),
    'brine': ('dairy-data.php', 'price'),
    'red-fermentation': ('wine-data.php', 'price'),
    'white-fermentation': ('wine-data.php', 'price'),
    'storage-aging': ('wine-data.php', 'price'),
    'cold-stabilization': ('wine-data.php', 'price'),
    'blending': ('wine-data.php', 'price'),
    'sulfitation': ('wine-data.php', 'price'),
    'universal-tank': ('wine-data.php', 'price'),
    'ind-storage': ('industrial-data.php', 'price'),
    'mixing': ('industrial-data.php', 'price'),
    'thermal': ('industrial-data.php', 'price'),
    'pressure': ('industrial-data.php', 'price'),
}

# ═══ VERIFY ═══
print("\n\nVerifying every price...\n")

# Pre-load all files
file_contents = {}
for fn in set(f for f, _ in FILE_MAP.values()):
    with open(f'{DOC}/{fn}') as f: file_contents[fn] = f.read()

errors = []
total = 0
found_total = 0

for slug, vols in sorted(SITE.items()):
    fn, price_key = FILE_MAP[slug]
    content = file_contents[fn]
    
    for vol in sorted(vols):
        total += 1
        expected = target(slug, vol)
        if expected is None:
            errors.append(f"  {slug} {vol}l: CANNOT COMPUTE TARGET")
            continue
        
        # Find price in file — search within the slug's section
        slug_start = content.find(f"'{slug}' => [") if slug != 'cct' else 0
        if slug_start < 0 and slug != 'cct':
            errors.append(f"  {slug}: NOT FOUND in {fn}")
            continue
        
        if slug == 'cct':
            search_zone = content
        else:
            slug_end = content.find("\n    '", slug_start + 50)
            if slug_end < 0: slug_end = slug_start + 10000
            search_zone = content[slug_start:slug_end]
        
        # Find the price for this volume
        pat = r"\b" + re.escape(str(vol)) + r"\b[^]]*'" + re.escape(price_key) + r"'\s*=>\s*(\d+)"
        m = re.search(pat, search_zone)
        if m:
            found_price = int(m.group(1))
            if found_price == expected:
                found_total += 1
            else:
                errors.append(f"  ❌ {slug} {vol}l ({fn}): expected {expected:,}, found {found_price:,}")
        else:
            if slug == 'cct':
                errors.append(f"  ❌ {slug} {vol}l ({fn}): price entry NOT FOUND in file")
            else:
                # Try broader search
                m2 = re.search(r"\b" + re.escape(str(vol)) + r"\b[^]]*'" + re.escape(price_key) + r"'\s*=>\s*(\d+)", content)
                if m2:
                    found_price = int(m2.group(1))
                    if found_price == expected:
                        found_total += 1
                    else:
                        errors.append(f"  ❌ {slug} {vol}l ({fn}): found in wrong section! price={found_price:,}")
                else:
                    errors.append(f"  ❌ {slug} {vol}l ({fn}): price NOT FOUND anywhere")

print(f"  Total SKUs checked: {total}")
print(f"  Correct: {found_total}")
print(f"  Errors: {len(errors)}")
print()

if errors:
    print("ERRORS:")
    for e in errors:
        print(e)
else:
    print("✅ ALL PRICES CORRECT — every volume in every category verified!")

# Summary by category
print("\nSummary by file:")
by_file = {}
for slug, (fn, price_key) in FILE_MAP.items():
    by_file.setdefault(fn, []).append(slug)

for fn, slugs in sorted(by_file.items()):
    with open(f'{DOC}/{fn}') as f: c = f.read()
    # Count volume entries (not header/footer lines)
    vol_entries = len(re.findall(r'^\s+\d+\s*=>\s*\[', c, re.MULTILINE))
    spec_ref_count = c.count('spec_ref')
    print(f"  {fn}: {vol_entries} specs, spec_ref={spec_ref_count}")
