#!/bin/bash
# Конвертирует все PNG/JPG в WebP
# Требует: cwebp (brew install webp) или ImageMagick (brew install imagemagick)
#
# Использование:
#   chmod +x convert-to-webp.sh
#   ./convert-to-webp.sh

echo "Конвертация изображений в WebP..."

# Пропускаем иконки и SVG
for img in *.png *.jpg *.jpeg; do
    [ -f "$img" ] || continue
    [[ "$img" == *-icon* ]] && continue
    [[ "$img" == favicon* ]] && continue
    
    webp="${img}.webp"
    [ -f "$webp" ] && echo "  Пропущено (уже есть): $img" && continue
    
    size=$(stat -f%z "$img")
    
    if command -v cwebp &>/dev/null; then
        cwebp -q 80 "$img" -o "$webp" 2>/dev/null
    elif command -v convert &>/dev/null; then
        convert "$img" -quality 80 "$webp"
    else
        echo "  ОШИБКА: установите cwebp (brew install webp) или ImageMagick"
        exit 1
    fi
    
    webp_size=$(stat -f%z "$webp")
    saved=$(( (size - webp_size) * 100 / size ))
    echo "  ✓ $img: $((size/1024))KB → $((webp_size/1024))KB (−${saved}%)"
done

echo ""
echo "Готово! WebP-файлы созданы."
echo "В .htaccess уже настроено: если браузер поддерживает WebP — отдаётся .webp, иначе оригинал."
