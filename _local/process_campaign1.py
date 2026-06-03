#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Извлекает фразы из Кампания_1_704573285.xlsx, распределяет по категориям
и генерирует 6 XLSX-файлов через build_campaigns.py."""
import zipfile, xml.etree.ElementTree as ET, csv, os
from collections import defaultdict

SRC = "хлам/XLSX-кампании/Кампания_1_704573285.xlsx"
CSV_OUT = "keywords_generated.csv"

ET.register_namespace("", "http://schemas.openxmlformats.org/spreadsheetml/2006/main")
NS = "{http://schemas.openxmlformats.org/spreadsheetml/2006/main}"

# Маппинг: тип оборудования → категория
# Ключ — последнее слово из названия группы (без цифр)
TYPE_TO_CAT = {
    # BEER
    "заторный": "BEER", "заторник": "BEER",
    "сусловарочный": "BEER", "сусловарочник": "BEER",
    "гидроциклонный": "BEER", "вирпул": "BEER",
    "накопитель": "BEER",  # накопитель сусла
    "фильтрационный": "BEER", "фильтрчан": "BEER",
    "водогрейка": "BEER", "чан": "BEER",
    # WINE
    "винификатор": "WINE",
    # DAIRY
    "пастеризатор": "DAIRY",
    # INDUSTRIAL
    "термоемкость": "INDUSTRIAL",
    "промышленная": "INDUSTRIAL", "технологическая": "INDUSTRIAL",
    "ферментер": "INDUSTRIAL", "ферментатор": "INDUSTRIAL",
    # GENERAL
    "емкость": "GENERAL", "резервуар": "GENERAL",
    "бак": "GENERAL", "танк": "GENERAL",
    "оборудование": "GENERAL",  # емкостное оборудование
}

def detect_category(group_name):
    """Определяет категорию по названию группы."""
    words = group_name.strip().split()
    if not words:
        return "GENERAL"
    # Ищем последнее слово-тип (после цифр объёма)
    for w in reversed(words):
        key = w.lower().strip('"')
        if key in TYPE_TO_CAT:
            return TYPE_TO_CAT[key]
    # Если только цифры (plain volume)
    if words[0].isdigit():
        return "GENERAL"
    return "GENERAL"

def main():
    print("Читаем Кампания_1...")
    with zipfile.ZipFile(SRC) as z:
        content = z.read("xl/worksheets/sheet1.xml")
    root = ET.fromstring(content)
    rows = root.findall(f".//{NS}row")

    # Собираем фразы по категориям
    cat_phrases = defaultdict(list)

    for row in rows:
        rn = row.get("r")
        if not rn or not rn.isdigit() or int(rn) < 12:
            continue
        cells = {}
        for c in row:
            t = c.find(f"{NS}is/{NS}t")
            v = c.find(f"{NS}v")
            val = (t.text if t is not None else v.text if v is not None else "")
            cells[c.get("r")] = val
        group = cells.get(f"D{rn}", "")
        phrase = cells.get(f"G{rn}", "")
        if not phrase:
            continue
        cat = detect_category(group)
        cat_phrases[cat].append(phrase)

    total = sum(len(v) for v in cat_phrases.values())
    print(f"Всего фраз: {total}")
    for cat in ["DAIRY", "BEER", "WINE", "INDUSTRIAL", "GENERAL"]:
        n = len(cat_phrases.get(cat, []))
        print(f"  {cat}: {n}")

    # Пишем в CSV (тот же формат, что ждёт build_campaigns.py)
    with open(CSV_OUT, "w", encoding="utf-8", newline="") as f:
        w = csv.writer(f, delimiter=";")
        w.writerow(["Group", "Phrase"])
        for cat in ["DAIRY", "BEER", "WINE", "INDUSTRIAL", "GENERAL"]:
            for phr in cat_phrases.get(cat, []):
                w.writerow([f"{cat}-ALL", phr])

    print(f"\nЗаписано в {CSV_OUT}")
    print("Запускаем build_campaigns.py...")
    import build_campaigns
    build_campaigns.main()

if __name__ == "__main__":
    main()
