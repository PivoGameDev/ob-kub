import os, re

ROOT = '/Users/tretyakov/Desktop/Ап'
HEADER_PHP = '''<?php require $_SERVER["DOCUMENT_ROOT"]."/php/header.php"; ?>'''
FOOTER_PHP = '''<?php require $_SERVER['DOCUMENT_ROOT'].'/php/footer.php'; ?>'''

def convert(path):
    with open(path, encoding='utf-8') as f:
        html = f.read()

    old_len = len(html)

    # Remove old Yandex.Metrika blocks
    html = re.sub(
        r'<!--\s*Yandex\.Metrika counter\s*-->.*?<!--\s*/Yandex\.Metrika counter\s*-->',
        '',
        html,
        flags=re.DOTALL
    )

    # Remove standalone Metrika <noscript> if any
    html = re.sub(
        r'<noscript>.*?yandex\.ru/watch/\d+.*?</noscript>',
        '',
        html,
        flags=re.DOTALL
    )

    # Remove Metrika from inline ym() calls inside <script> (with surrounding script tag)
    html = re.sub(
        r'<script[^>]*>.*?ym\s*\(\s*\d+\s*,.*?</script>',
        '',
        html,
        flags=re.DOTALL
    )

    # Remove tracker.js (included in footer.php)
    html = re.sub(
        r'\s*<script[^>]*src=["\'].*?tracker\.js["\'][^>]*></script>',
        '',
        html,
        flags=re.DOTALL
    )

    # Remove duplicate mobile drawer/search JS that's now in footer.php
    html = re.sub(
        r'<script>\s*// Mobile drawer.*?closeDrawer\(\)\{\}.*?</script>',
        '',
        html,
        flags=re.DOTALL
    )
    html = re.sub(
        r'<script>\s*\(function.*?closeDrawer.*?</script>',
        '',
        html,
        flags=re.DOTALL
    )

    # Remove duplicate catalog dropdown overlay JS (in footer.php)
    html = re.sub(
        r'<script>\s*\(function.*?catalogOverlay.*?</script>',
        '',
        html,
        flags=re.DOTALL
    )

    # Remove search functionality JS (in footer.php)
    html = re.sub(
        r'<script>\s*\(function.*?searchTrigger.*?closeSearch.*?</script>',
        '',
        html,
        flags=re.DOTALL
    )

    # Remove mega-menu JS (in footer.php via catalog overlay)
    html = re.sub(
        r'<script>document\.querySelectorAll\([\'"]\.mega-menu[\'"]\).*?</script>',
        '',
        html,
        flags=re.DOTALL
    )

    # Remove header height padding JS (in footer.php)
    html = re.sub(
        r'<script>function\s*h\(\)\{var h=.*?offsetHeight.*?</script>',
        '',
        html,
        flags=re.DOTALL
    )

    # Replace <header...>...</header> with PHP require
    header_match = re.search(r'<header[^>]*>.*?</header>', html, re.DOTALL)
    if header_match:
        html = html[:header_match.start()] + HEADER_PHP + html[header_match.end():]

    # Replace <footer...>...</footer> with PHP require
    footer_match = re.search(r'<footer[^>]*>.*?</footer>', html, re.DOTALL)
    if footer_match:
        html = html[:footer_match.start()] + FOOTER_PHP + html[footer_match.end():]

    # Remove duplicate script includes that are now part of header.php / footer.php
    # forms.js — keep only once (it's not in header/footer, it's page-specific)
    # Keep CSRF token script (not in footer)
    # Keep other page-specific scripts

    new_path = path.replace('.html', '.php')
    with open(new_path, 'w', encoding='utf-8') as f:
        f.write(html)

    print(f'  {os.path.basename(path)} → {os.path.basename(new_path)}  ({old_len}→{len(html)} chars)')

# === Root HTML files ===
root_files = ['index.html', '404.html', 'beer.html', 'dairy.html', 'winery.html',
              'industrial.html', 'articles.html', 'certificates.html',
              'payment-delivery.html', 'privacy.html']

print('=== Root pages ===')
for f in root_files:
    fp = os.path.join(ROOT, f)
    if os.path.exists(fp):
        convert(fp)
    else:
        print(f'  SKIP: {f} not found')

# === Article HTML files ===
print('\n=== Articles ===')
articles_dir = os.path.join(ROOT, 'articles')
if os.path.isdir(articles_dir):
    for f in sorted(os.listdir(articles_dir)):
        if f.endswith('.html'):
            convert(os.path.join(articles_dir, f))
else:
    print('  No articles/ directory')
