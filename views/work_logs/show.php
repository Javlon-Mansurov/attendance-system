<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?= $this->e($title) ?></h1>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="<?= $this->url('/work-logs') ?>">← Ба рӯйхат</a>
        <a class="btn btn-primary" href="<?= $this->url('/work-logs/' . $log['id'] . '/edit') ?>">Таҳрир</a>
        <form method="post" action="<?= $this->url('/work-logs/' . $log['id'] . '/delete') ?>" onsubmit="return confirm('Ин сабти вақти корӣ нест карда шавад?');">
            <button class="btn btn-danger" type="submit">Нест кардан</button>
        </form>
    </div>
</div>

<div class="card card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Корманд</dt>
        <dd class="col-sm-9"><?= $this->e($log['employee_name']) ?> (<?= $this->e($log['employee_tab']) ?>)</dd>

        <dt class="col-sm-3">Вазифа ва шӯъба</dt>
        <dd class="col-sm-9"><?= $this->e($log['employee_position']) ?> — <strong><?= $this->e($log['department_name']) ?></strong></dd>

        <dt class="col-sm-3">Санаи корӣ</dt>
        <dd class="col-sm-9"><?= $this->e($log['work_date']) ?></dd>

        <dt class="col-sm-3">Вақти омадан ва рафтан</dt>
        <dd class="col-sm-9"><?= $this->e(substr($log['clock_in'], 0, 5)) ?> – <?= $this->e(substr($log['clock_out'], 0, 5)) ?></dd>

        <dt class="col-sm-3">Соатҳои корӣ</dt>
        <dd class="col-sm-9"><strong><?= number_format((float) $log['hours_worked'], 2) ?> соат</strong></dd>

        <dt class="col-sm-3">Намуди рӯз</dt>
        <dd class="col-sm-9"><?= $this->e($log['day_type']) ?></dd>

        <dt class="col-sm-3">Эзоҳ</dt>
        <dd class="col-sm-9"><?= $this->e($log['note'] ?? '—') ?></dd>
    </dl>
</div>
