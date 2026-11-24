<?php
$action = $id ? '/work-logs/' . $id . '/edit' : '/work-logs/create';
$val = fn (string $field): string => $this->e((string) ($log[$field] ?? ''));
$invalid = fn (string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
$error = function (string $field) use ($errors): string {
    return isset($errors[$field])
        ? '<div class="invalid-feedback">' . $this->e($errors[$field]) . '</div>'
        : '';
};
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?= $this->e($title) ?></h1>
    <a class="btn btn-outline-secondary" href="<?= $this->url('/work-logs') ?>">Бекор кардан</a>
</div>

<form class="card card-body" method="post" action="<?= $this->url($action) ?>">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="employee_id">Корманд *</label>
            <select class="form-select<?= $invalid('employee_id') ?>" id="employee_id" name="employee_id" required>
                <option value="">— Интихоб кунед —</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= (int) $emp['id'] ?>" <?= ((string) ($log['employee_id'] ?? '') === (string) $emp['id']) ? 'selected' : '' ?>>
                        <?= $this->e($emp['full_name']) ?> (<?= $this->e($emp['department_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <?= $error('employee_id') ?>
        </div>

        <div class="col-md-3">
            <label class="form-label" for="work_date">Санаи корӣ *</label>
            <input class="form-control<?= $invalid('work_date') ?>" type="date" id="work_date" name="work_date" required value="<?= $val('work_date') ?>">
            <?= $error('work_date') ?>
        </div>

        <div class="col-md-3">
            <label class="form-label" for="day_type">Намуди рӯз *</label>
            <select class="form-select<?= $invalid('day_type') ?>" id="day_type" name="day_type" required>
                <?php
                $types = ['work' => 'Рӯзи корӣ', 'overtime' => 'Иловакорӣ', 'remote' => 'Фосилавӣ', 'sick' => 'Беморӣ', 'absent' => 'Ғоиб'];
                foreach ($types as $k => $lbl):
                ?>
                    <option value="<?= $k ?>" <?= (($log['day_type'] ?? 'work') === $k) ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
            <?= $error('day_type') ?>
        </div>

        <div class="col-md-4">
            <label class="form-label" for="clock_in">Вақти омадан *</label>
            <input class="form-control<?= $invalid('clock_in') ?>" type="time" id="clock_in" name="clock_in" required value="<?= $val('clock_in') ?>">
            <?= $error('clock_in') ?>
        </div>

        <div class="col-md-4">
            <label class="form-label" for="clock_out">Вақти рафтан *</label>
            <input class="form-control<?= $invalid('clock_out') ?>" type="time" id="clock_out" name="clock_out" required value="<?= $val('clock_out') ?>">
            <?= $error('clock_out') ?>
        </div>

        <div class="col-md-4">
            <label class="form-label" for="hours_worked">Соатҳои корӣ *</label>
            <input class="form-control<?= $invalid('hours_worked') ?>" type="number" step="0.25" min="0" max="24" id="hours_worked" name="hours_worked" required value="<?= $val('hours_worked') ?>">
            <?= $error('hours_worked') ?>
        </div>

        <div class="col-12">
            <label class="form-label" for="note">Эзоҳ (то 255 аломат)</label>
            <input class="form-control<?= $invalid('note') ?>" type="text" id="note" name="note" maxlength="255" value="<?= $val('note') ?>">
            <?= $error('note') ?>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary" type="submit">Захира кардан</button>
    </div>
</form>
