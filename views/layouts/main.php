<!doctype html>
<html lang="tg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->e($title ?? 'Баҳисобгирии вақти корӣ') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= $this->url('/work-logs') ?>">⏱ Вақти корӣ</a>
        <div class="navbar-nav">
            <a class="nav-link" href="<?= $this->url('/work-logs') ?>">Журнали вақти корӣ</a>
            <a class="nav-link" href="<?= $this->url('/reports/hours') ?>">Ҳисоботи шӯъбаҳо</a>
        </div>
    </div>
</nav>
<main class="container mb-5">
    <?php require BASE_PATH . '/views/partials/notice.php'; ?>
    <?= $content ?>
</main>
</body>
</html>
