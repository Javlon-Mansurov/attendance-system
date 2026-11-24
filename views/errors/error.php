<!doctype html>
<html lang="tg">
<head>
    <meta charset="utf-8">
    <title>Хатои <?= (int) $code ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
<div class="container text-center">
    <h1 class="display-4 text-danger"><?= (int) $code ?></h1>
    <h2 class="h4 mb-3"><?= $this->e($title) ?></h2>
    <p class="text-muted"><?= $this->e($message) ?></p>
</div>
</body>
</html>
