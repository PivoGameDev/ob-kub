# Сайт ob-kub.ru — Критическая информация

## Хостинг (Timeweb)
- FTP/SSH хост: cl121464.tw1.ru
- FTP пользователь: cl121464_obkub
- SSH пользователь: cl121464
- **Web root (корень сайта)**: `/home/c/cl121464/public_html/`
- **Все файлы сайта заливаются в `public_html/`**

## Деплой
- GitHub репозиторий: github.com/PivoGameDev/ob-kub
- Автодеплой через GitHub Actions (SamKirkland/FTP-Deploy-Action@v4.4.0)
- Иногда деплой не срабатывает — тогда заливать вручную через Timeweb File Manager в public_html/
- `dangerous-clean-slate: true` УДАЛЯЕТ ВСЕ ФАЙЛЫ НА СЕРВЕРЕ — не использовать!

## API ключи
- Kie.ai (генерация картинок): 02f0f546718966cb47c1f7d2d786ce58 (~920 токенов)
- Бюджет: до 100 токенов без согласования

## Формы
- Все формы отправляются на /php/send.php
- form_type для лид-магнита: catalog-request
- Почта для заявок: ok@cl121464.tw1.ru, oborudovanie-kubani@yandex.ru

## Каталог
- Data-файлы: catalog/*-data.php
- Шаблон товара: catalog/helpers/product-template.php
- Цены через: /catalog/?get_prices=...&src=...
