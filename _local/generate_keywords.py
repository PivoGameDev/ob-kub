#!/usr/bin/env python3
"""
Генератор семантического ядра для Яндекс.Директа (ob-kub.ru)
Версия 3 — ПОЛНЫЙ ОХВАТ: все типы, все паттерны из 1-й кампании.
Фразовое соответствие (кавычки).
"""

from collections import defaultdict

# ═══════════════════════════════════════════════════════════
# 1. ВСЕ ТИПЫ ОБОРУДОВАНИЯ (из каталога + 1-й кампании)
# ═══════════════════════════════════════════════════════════

BASE = [
    ("емкость", "f"), ("резервуар", "m"), ("бак", "m"), ("танк", "m"),
]

BEER = [
    ("ЦКТ", "m"), ("форфас", "m"), ("лагерный танк", "m"), ("unitank", "m"),
    ("заторный аппарат", "m"), ("заторный чан", "m"), ("заторник", "m"),
    ("сусловарочный аппарат", "m"), ("сусловарочный котел", "m"), ("сусловарочник", "m"),
    ("фильтрационный аппарат", "m"), ("фильтрчан", "m"),
    ("гидроциклонный аппарат", "m"), ("вирпул", "m"), ("суслосборник", "m"),
    ("бак горячей воды", "m"), ("водогрейка", "f"),
    ("парогенератор", "m"), ("дробилка солода", "f"), ("солододробилка", "f"),
    ("чиллер", "m"), ("холодильная установка", "f"),
    ("пивоваренное оборудование", "n"), ("оборудование для пивоварни", "n"),
    ("емкость для браги", "f"), ("танк для брожения", "m"),
    ("чан", "m"), ("ферментатор", "m"), ("сосуд для брожения", "m"),
]

DAIRY = [
    ("молокоохладитель", "m"), ("охладитель молока", "m"),
    ("резервуар для хранения молока", "m"), ("емкость для приемки молока", "f"),
    ("ванна пастеризации", "f"), ("ВДП", "f"), ("ванна длительной пастеризации", "f"),
    ("пастеризатор", "m"), ("сыроизготовитель", "m"), ("сыроварня", "f"),
    ("творогоизготовитель", "m"), ("заквасочник", "m"),
    ("ферментационный танк", "m"), ("ферментер", "m"), ("ферментатор", "m"),
    ("молочное оборудование", "n"), ("оборудование для сыроварни", "n"),
    ("пастеризационная ванна", "f"), ("накопитель молока", "m"),
    ("контейнер для соления сыра", "m"), ("стеллаж для созревания сыра", "m"),
    ("емкость для созревания сыра", "f"),
]

WINE = [
    ("винификатор", "m"), ("криостат", "m"),
    ("емкость для ферментации вина", "f"), ("емкость для выдержки вина", "f"),
    ("емкость для хранения вина", "f"), ("емкость для купажирования", "f"),
    ("купажер", "m"), ("емкость для сульфитации", "f"), ("сульфитатор", "m"),
    ("винное оборудование", "n"), ("винодельческое оборудование", "n"),
    ("оборудование для винодельни", "n"),
]

IND = [
    ("промышленная емкость", "f"), ("технологическая емкость", "f"),
    ("термоемкость", "f"), ("емкость с мешалкой", "f"),
    ("емкость с терморегуляцией", "f"), ("емкость под давлением", "f"),
    ("накопительная емкость", "f"), ("накопитель", "m"),
    ("емкостное оборудование", "n"), ("промышленное оборудование", "n"),
    ("технологическое оборудование", "n"), ("пищевое оборудование", "n"),
    ("теплообменник", "m"),     ("CIP станция", "f"), ("CIP мойка", "f"),
    ("автоматическая мойка", "f"),
    ("CIP автоматическая", "f"), ("CIP безнасосная", "f"),
]

GENERAL = [
    ("оборудование из нержавейки", "n"), ("оборудование из нержавеющей стали", "n"),
    ("изделия из нержавейки", "n"),
]

# ═══════════════════════════════════════════════════════════
# 1b. REUSABLE GEO TYPES (for geo-modified keywords)
# ═══════════════════════════════════════════════════════════

GEO_TYPES = [
    ("емкость", "f"), ("резервуар", "m"), ("бак", "m"), ("танк", "m"),
    ("оборудование", "n"),
    ("ЦКТ", "m"), ("форфас", "m"), ("лагерный танк", "m"),
    ("заторный аппарат", "m"), ("сусловарочный котел", "m"),
    ("фильтрчан", "m"), ("вирпул", "m"), ("чиллер", "m"),
    ("молокоохладитель", "m"), ("охладитель молока", "m"),
    ("пастеризатор", "m"), ("сыроизготовитель", "m"), ("сыроварня", "f"),
    ("винификатор", "m"), ("декантер", "m"),
    ("ферментер", "m"), ("ферментатор", "m"),
    ("промышленная емкость", "f"), ("технологическая емкость", "f"),
    ("термоемкость", "f"), ("емкость с мешалкой", "f"),
    ("емкость под давлением", "f"), ("накопительная емкость", "f"),
    ("теплообменник", "m"), ("расширительный бак", "m"),
    ("сепаратор", "m"), ("гомогенизатор", "m"),
    ("емкостное оборудование", "n"), ("пищевое оборудование", "n"),
    ("технологическое оборудование", "n"), ("промышленное оборудование", "n"),
    ("оборудование из нержавейки", "n"), ("оборудование из нержавеющей стали", "n"),
    ("изделия из нержавейки", "n"),
]

# ═══════════════════════════════════════════════════════════
# 1c. ГЕО-МОДИФИКАТОРЫ (ЮФО + Краснодарский край)
# ═══════════════════════════════════════════════════════════

GEO_WORDS = ["краснодар", "в краснодаре", "из краснодара", "кубань", "на кубани", "юфо", "южный федеральный"]
GEO_PREFIX = ["изготовление", "производство"]

# ═══════════════════════════════════════════════════════════
# 2. МАТЕРИАЛЫ И ДЕЙСТВИЯ
# ═══════════════════════════════════════════════════════════

MAT_BARE = ["из нержавейки", "из нержавеющей стали", "нерж"]
MAT_GRADE = ["AISI 304", "AISI 316", "AISI", "AISI304", "AISI316"]

# СТАНДАРТНЫЕ ДЕЙСТВИЯ (купить, цена, заказать)
BUY = ["купить", "заказать"]
PRICE = ["цена", "стоимость", "прайс"]
ALL_ACT = BUY + PRICE + ["от производителя"]

# ДОПОЛНИТЕЛЬНЫЕ ПАТТЕРНЫ из 1-й кампании
PREFIX_WHERE    = ["где купить", "где заказать"]
PREFIX_MAKE     = ["изготовление", "производство"]
PREFIX_NEED     = ["нужен", "нужна", "ищу"]
PREFIX_CATALOG  = ["каталог цен на"]
PREFIX_ANALOG   = ["аналог"]
PREFIX_REPLACE  = ["замена", "импортозамещение"]
PREFIX_URGENT   = ["срочно"]
PREFIX_BUDGET   = ["недорого"]

# КОНСТРУКТИВ (для базовых типов)
CONSTR_WITH = ["с мешалкой", "с рубашкой охлаждения", "с терморегуляцией", "с подогревом", "с крышкой"]
CONSTR_PREFIX = ["под давлением"]

# ПРИЛАГАТЕЛЬНЫЕ (для базовых типов)
ADJ_f = "вертикальная горизонтальная цилиндрическая герметичная пищевая промышленная технологическая".split()
ADJ_m = "вертикальный горизонтальный цилиндрический герметичный пищевой промышленный технологический".split()

# ═══════════════════════════════════════════════════════════
# 3. КАТЕГОРИИ
# ═══════════════════════════════════════════════════════════

CATEGORIES = {
    "BEER": {
        "label": "Пиво",
        "types": BEER, "base": BASE,
        "purposes": ["для пива", "для варки пива", "для брожения", "для пивоварни"],
        "minus": "молоко вино сыр творог йогурт кефир масло сок",
    },
    "DAIRY": {
        "label": "Молоко",
        "types": DAIRY, "base": BASE,
        "purposes": ["для молока", "для хранения молока", "для молочной продукции",
                      "для сыра", "для творога", "для йогурта", "для кефира"],
        "minus": "пиво вино брага солод хмель сусло самогон",
    },
    "WINE": {
        "label": "Вино",
        "types": WINE, "base": BASE,
        "purposes": ["для вина", "для виноделия", "для ферментации вина",
                      "для выдержки вина", "для хранения вина", "для купажирования"],
        "minus": "пиво молоко сыр творог брага солод",
    },
    "INDUSTRIAL": {
        "label": "Пром",
        "types": IND, "base": BASE,
        "purposes": ["для хранения", "для производства"],
        "minus": "пиво молоко вино брага самогон",
    },
    "GENERAL": {
        "label": "Общее",
        "types": GENERAL, "base": BASE,
        "purposes": [],
        "minus": "пиво молоко вино брага самогон",
    },
    "GEO": {
        "label": "Гео",
        "types": GEO_TYPES, "base": [],
        "purposes": [],
        "minus": "пиво молоко вино брага самогон",
    },
}

# ═══════════════════════════════════════════════════════════
# 4. МИНУС-СЛОВА (глобальные)
# ═══════════════════════════════════════════════════════════

MINUS_WORDS = """
    самогон самогонщик самогоноварение
    водонагреватель бойлер
    полив огород дача
    котел отопление колонка радиатор
    канализация септик выгребной кнс
    противень кастрюля сковорода мультиварка компост
    буровой боулинг штамповка памятник
    рыбьего флэкси
    гидропоника
    отзыв форум
    болгарка зерно
""".split()

MINUS_PHR = ["в быту", "для рук", "для стендов", "газовый котел", "газовая колонка"]

# ═══════════════════════════════════════════════════════════
# 5. ПОМОЩНИКИ
# ═══════════════════════════════════════════════════════════

def wc(s): return len(s.split()) if s else 0
def mk(p): return " ".join(x for x in p if x and x.strip()).strip()

def is_garbage(phrase, extra_minus):
    p = phrase.lower()
    words = set(p.split())
    for m in MINUS_WORDS:
        if m in words: return True
    for m in MINUS_PHR:
        if m in p: return True
    if extra_minus:
        for m in extra_minus:
            if m.strip().lower() in words: return True
    return False

BAD_STARTS = {"из", "aisi", "нержавеющая", "нержавеющий", "нержавейки",
              "для", "с", "под", "на"}

seen_global = set()
results_global = []
extra_minus_global = []

def set_extra_minus(words):
    global extra_minus_global
    extra_minus_global = words

def add(phrase):
    if not phrase: return
    n = wc(phrase)
    if n < 2 or n > 7: return
    key = phrase.lower()
    if key in seen_global: return
    f = key.split()[0]
    if f in BAD_STARTS: return
    # Проверка минус-слов
    words_set = set(key.split())
    for m in MINUS_WORDS:
        if m in words_set: return
    for m in MINUS_PHR:
        if m in key: return
    for m in extra_minus_global:
        if m in words_set: return
    seen_global.add(key)
    results_global.append({"phrase": f'"{phrase}"'})

# ═══════════════════════════════════════════════════════════
# 6. ГЕНЕРАЦИЯ
# ═══════════════════════════════════════════════════════════

def gen_all(all_types, purposes):
    for t_word, t_gender in all_types:
        is_base = t_word in ("емкость", "резервуар", "бак", "танк")
        is_spec_type = not is_base
        # Если тип уже содержит "из нержавейки" — не добавляем материал повторно
        has_builtin_mat = any(m in t_word.lower() for m in ["нержавейк", "нержавеющ"])
        sp = t_word

        mg = "нержавеющая" if t_gender == "f" else "нержавеющий"
        adj = ADJ_f if t_gender == "f" else ADJ_m

        # ─── А. ПРОСТЫЕ: тип + материал ─────────────────
        if has_builtin_mat:
            # Тип уже содержит материал: только действие
            for a in ALL_ACT:
                add(f"{a} {sp}")
            if is_spec_type:
                add(sp)
        else:
            for mat in MAT_BARE + MAT_GRADE + [mg]:
                add(f"{sp} {mat}")
                for a in ALL_ACT:
                    add(f"{a} {sp} {mat}")
                    if a in (*BUY, *PRICE):
                        add(f"{sp} {mat} {a}")

        # ─── Б. ПРИЛАГАТЕЛЬНЫЕ (только для базовых) ─────
        if is_base:
            for a in adj:
                for m in [mg] + MAT_GRADE:
                    add(f"{sp} {a} {m}")
                    for act in ALL_ACT:
                        add(f"{act} {sp} {a} {m}")
                add(f"{a} {sp}")
                for m in MAT_BARE + MAT_GRADE:
                    add(f"{a} {sp} {m}")

        # Если тип уже содержит материал — только базовые паттерны
        if has_builtin_mat:
            for pre in PREFIX_WHERE:
                add(f"{pre} {sp}")
            for pre in PREFIX_NEED:
                add(f"{pre} {sp}")
            for pre in PREFIX_CATALOG:
                add(f"{pre} {sp}")
            for pre in PREFIX_ANALOG:
                add(f"{pre} {sp}")
            for pre in PREFIX_REPLACE:
                add(f"{pre} {sp}")
            for a in BUY:
                add(f"срочно {a} {sp}")
            add(f"недорого {sp}")
            continue

        # ─── В. НАЗНАЧЕНИЕ (для всего, без тавтологии) ──
        for p in purposes:
            skip = any(part in sp.lower() for part in p.replace("для ", "").split())
            if skip: continue
            for mat in MAT_BARE + MAT_GRADE:
                add(f"{sp} {p} {mat}")
                add(f"{sp} {mat} {p}")
                add(f"купить {sp} {p} {mat}")
            if is_spec_type:
                add(f"{sp} {p}")

        # ─── Г. КОНСТРУКТИВ (только для базовых) ────────
        if is_base:
            for c in CONSTR_WITH:
                for m in MAT_BARE + MAT_GRADE:
                    add(f"{sp} {c} {m}")
                    add(f"{sp} {m} {c}")
                    add(f"купить {sp} {c} {m}")
            for c in CONSTR_PREFIX:
                for m in MAT_BARE + MAT_GRADE:
                    add(f"{sp} {c} {m}")
                    add(f"купить {sp} {c} {m}")

        # ─── Д. ПРЕФИКСЫ ИЗ 1-Й КАМПАНИИ ────────────────
        for pre in PREFIX_WHERE:
            for mat in MAT_BARE + MAT_GRADE + [mg]:
                add(f"{pre} {sp} {mat}")
            if is_spec_type:
                add(f"{pre} {sp}")

        for pre in PREFIX_MAKE:
            for mat in MAT_BARE + MAT_GRADE:
                add(f"{pre} {sp} {mat}")

        for pre in PREFIX_NEED:
            for mat in MAT_BARE + MAT_GRADE:
                add(f"{pre} {sp} {mat}")

        for pre in PREFIX_CATALOG:
            add(f"{pre} {sp}")

        for pre in PREFIX_ANALOG:
            add(f"{pre} {sp}")

        for pre in PREFIX_REPLACE:
            add(f"{pre} {sp}")

        for a in BUY:
            add(f"срочно {a} {sp}")

        for mat in MAT_BARE + MAT_GRADE + [mg]:
            add(f"недорого {sp} {mat}")

# ═══════════════════════════════════════════════════════════
# 6b. ГЕО-ГЕНЕРАЦИЯ
# ═══════════════════════════════════════════════════════════

def gen_geo():
    for t_word, t_gender in GEO_TYPES:
        sp = t_word
        mg = "нержавеющая" if t_gender == "f" else "нержавеющий"
        has_mat = any(m in sp.lower() for m in ["нержавейк", "нержавеющ"])

        # Тип + гео-суффикс
        for g in GEO_WORDS:
            add(f"{sp} {g}")
            if not has_mat:
                add(f"{sp} из нержавейки {g}")
                add(f"{sp} нержавеющий {g}")
            add(f"купить {sp} {g}")
            add(f"заказать {sp} {g}")
            add(f"цена {sp} {g}")

        # Префикс + тип + гео
        for pr in GEO_PREFIX:
            add(f"{pr} {sp}")
            for g in GEO_WORDS:
                add(f"{pr} {sp} {g}")
            if not has_mat:
                add(f"{pr} {sp} из нержавейки")
                for g in GEO_WORDS:
                    add(f"{pr} {sp} из нержавейки {g}")

        # Где купить + гео
        for g in GEO_WORDS:
            add(f"где купить {sp} {g}")
            add(f"где заказать {sp} {g}")


# ═══════════════════════════════════════════════════════════
# 7. MAIN
# ═══════════════════════════════════════════════════════════

def main():
    global seen_global, results_global

    for cat_key in ["BEER", "DAIRY", "WINE", "INDUSTRIAL", "GENERAL", "GEO"]:
        cat = CATEGORIES[cat_key]
        seen_global = set()
        results_global = []
        set_extra_minus(cat["minus"].split())

        if cat_key == "GEO":
            gen_geo()
        else:
            purposes = cat.get("purposes", [])
            all_types = cat["base"] + cat["types"]
            gen_all(all_types, purposes)

        total = len(results_global)
        print(f"{cat_key} ({cat['label']}): {total} фраз")

        # Сохраняем для TXT/CSV
        cat["_results"] = list(results_global)

    # Общий итог
    grand = sum(len(CATEGORIES[c]["_results"]) for c in CATEGORIES)
    print(f"\nИТОГО: {grand} фраз")

    # TXT
    path = "keywords_generated.txt"
    with open(path, "w", encoding="utf-8") as f:
        f.write(f"Сгенерировано: {grand} фраз (фразовое соответствие)\n{'='*70}\n")
        for ck in ["BEER", "DAIRY", "WINE", "INDUSTRIAL", "GENERAL"]:
            cat = CATEGORIES[ck]
            cp = cat["_results"]
            f.write(f"\n{'='*70}\n{ck} — {cat['label']}\nМинус: {cat['minus']}\nФраз: {len(cp)}\n{'='*70}\n")
            for r in sorted(cp, key=lambda x: x["phrase"]):
                f.write(f"{r['phrase']}\n")
    print(f"\nTXT: {path}")

    # CSV
    csv = "keywords_generated.csv"
    with open(csv, "w", encoding="utf-8") as f:
        f.write("Группа;Фраза\n")
        for ck in CATEGORIES:
            for r in sorted(CATEGORIES[ck]["_results"], key=lambda x: x["phrase"]):
                f.write(f"{ck}-ALL;{r['phrase']}\n")
    print(f"CSV: {csv}")

    # Статистика
    by_cat = defaultdict(int)
    for ck, cat in CATEGORIES.items():
        by_cat[ck] = len(cat["_results"])
    print("\nПо категориям:", dict(by_cat))

if __name__ == "__main__":
    main()
