<?php
$typeLabels = [
    'work'     => ['Рӯзи корӣ', 'bg-success'],
    'overtime' => ['Иловакорӣ', 'bg-primary'],
    'remote'   => ['Фосилавӣ', 'bg-info text-dark'],
    'sick'     => ['Беморӣ', 'bg-warning text-dark'],
    'absent'   => ['Ғоиб', 'bg-danger'],
];
$buildPageUrl = function (int $p) use ($filters): string {
    $params = array_filter([
        'q'             => $filters['q'] ?? '',
        'department_id' => $filters['department_id'] ?? '',
        'day_type'      => $filters['day_type'] ?? '',
        'page'          => $p,
    ], fn ($v) => $v !== '');
    return $this->url('/work-logs?' . http_build_query($params));
};
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Баҳисобгирии вақти кории кормандон</h1>
    <a class="btn btn-primary" href="<?= $this->url('/work-logs/create') ?>">+ Илова кардан</a>
</div>

<form class="card card-body mb-3" method="get" action="<?= $this->url('/work-logs') ?>">
    <div class="row g-2">
        <div class="col-md-4">
            <input class="form-control" name="q" placeholder="Ҷустуҷӯ аз рӯи ном ё эзоҳ..." value="<?= $this->e($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select class="form-select" name="department_id">
                <option value="">— Ҳамаи шӯъбаҳо —</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= (int) $d['id'] ?>" <?= ((string) ($filters['department_id'] ?? '') === (string) $d['id']) ? 'selected' : '' ?>>
                        <?= $this->e($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" name="day_type">
                <option value="">— Ҳамаи намудҳои рӯз —</option>
                <?php foreach ($typeLabels as $code => [$label, $_]): ?>
                    <option value="<?= $code ?>" <?= (($filters['day_type'] ?? '') === $code) ? 'selected' : '' ?>>
                        <?= $this->e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button class="btn btn-secondary w-100" type="submit">Ёфтан</button>
            <a class="btn btn-outline-secondary" href="<?= $this->url('/work-logs') ?>">Тоза</a>
        </div>
    </div>
</form>

<p class="text-muted">Ёфт шуд: <strong><?= (int) $total ?></strong> сабт (Саҳифаи <?= (int) $page ?> аз <?= (int) $pages ?>)</p>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Сана</th>
                    <th>Корманд</th>
                    <th>Шӯъба</th>
                    <th>Омад / Рафт</th>
                    <th>Соат</th>
                    <th>Намуди рӯз</th>
                    <th>Эзоҳ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$logs): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Сабтҳо ёфт нашуданд</td></tr>
            <?php endif; ?>
            <?php foreach ($logs as $row): [$tLabel, $tClass] = $typeLabels[$row['day_type']] ?? [$row['day_type'], 'bg-secondary']; ?>
                <tr>
                    <td><?= $this->e($row['work_date']) ?></td>
                    <td>
                        <a href="<?= $this->url('/work-logs/' . $row['id']) ?>" class="fw-semibold text-decoration-none">
                            <?= $this->e($row['employee_name']) ?>
                        </a>
                        <div class="small text-muted"><?= $this->e($row['employee_tab']) ?> · <?= $this->e($row['employee_position']) ?></div>
                    </td>
                    <td><?= $this->e($row['department_name']) ?></td>
                    <td><?= $this->e(substr($row['clock_in'], 0, 5)) ?> – <?= $this->e(substr($row['clock_out'], 0, 5)) ?></td>
                    <td><strong><?= number_format((float) $row['hours_worked'], 2) ?></strong></td>
                    <td><span class="badge <?= $tClass ?>"><?= $this->e($tLabel) ?></span></td>
                    <td class="small"><?= $this->e($row['note'] ?? '—') ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="<?= $this->url('/work-logs/' . $row['id'] . '/edit') ?>">Таҳрир</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pages > 1): ?>
<nav class="mt-3">
    <ul class="pagination">
        <?php for ($p = 1; $p <= $pages; $p++): ?>
            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                <a class="page-link" href="<?= $this->e($buildPageUrl($p)) ?>"><?= $p ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<div class="card card-body mt-4 bg-white">
    <h2 class="h6 text-muted">Санҷиши маҳдудияти калиди хориҷӣ (FK RESTRICT)</h2>
    <form method="post" action="<?= $this->url('/employees/1/delete') ?>" class="d-flex align-items-center gap-2">
        <span class="small">Кӯшиши нест кардани корманд «Раҳимов Ф.К.» (ID=1, сабтҳои вақти корӣ дорад):</span>
        <button class="btn btn-sm btn-outline-danger" type="submit">Нест кардани корманд #1</button>
    </form>
</div>
