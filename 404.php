<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница не найдена (404) | ОБОРУДОВАНИЕ КУБАНИ</title>
    <meta name="description" content="Страница не найдена. Перейдите на главную или воспользуйтесь навигацией.">
    <meta name="robots" content="noindex, follow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="favicon.png" type="image/png">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Source Sans Pro',sans-serif;background:#f8f9fa;color:#2c3e50;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:40px 20px;}
        .error-code{font-size:8rem;font-weight:800;color:#2b2b39;line-height:1;margin-bottom:10px;letter-spacing:-4px;}
        .error-code span{color:#F77C2A;}
        h1{font-size:1.8rem;font-weight:700;color:#2b2b39;margin-bottom:12px;}
        p{font-size:1.1rem;color:#666;max-width:500px;line-height:1.6;margin-bottom:35px;}
        .links{display:flex;flex-wrap:wrap;gap:12px;justify-content:center;max-width:500px;}
        .links a{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:8px;font-weight:600;font-size:1rem;text-decoration:none;transition:all 0.3s;}
        .links a.primary{background:#F77C2A;color:white;}
        .links a.primary:hover{background:#e55a00;transform:translateY(-2px);box-shadow:0 4px 12px rgba(247,124,42,0.3);}
        .links a.secondary{background:white;color:#2b2b39;border:2px solid #e0e0e0;}
        .links a.secondary:hover{border-color:#F77C2A;color:#F77C2A;transform:translateY(-2px);}
        .links a svg{flex-shrink:0;}
        @media (max-width:480px){
            .error-code{font-size:5rem;}
            h1{font-size:1.4rem;}
            p{font-size:1rem;}
            .links a{width:100%;justify-content:center;padding:14px 20px;}
        }
    </style>
</head>
<body>
<main>
    <div class="error-code">4<span>0</span>4</div>
    <h1>Страница не найдена</h1>
    <p>Страница, которую вы ищете, была удалена или никогда не существовала. Попробуйте начать с главной или перейти в нужный раздел.</p>
    <div class="links">
        <a href="/" class="primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            На главную
        </a>
        <a href="/beer.php" class="secondary">Пивоваренное оборудование</a>
        <a href="/dairy.php" class="secondary">Молочное оборудование</a>
        <a href="/winery.php" class="secondary">Винодельческое оборудование</a>
        <a href="/articles.php" class="secondary">Статьи</a>
    </div>
</main>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/footer.php'; ?>
</body>
</html>
