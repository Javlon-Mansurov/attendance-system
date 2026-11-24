<h1 class="h3 mb-3"><?= $this->e($title) ?></h1>

<div class="card">
    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Шӯъба</th>
                    <th class="text-end">Кормандон</th>
                    <th class="text-end">Ҳамагӣ сабтҳо</th>
                    <th class="text-end">Ҳамагӣ соатҳо</th>
                    <th class="text-end">Рӯзҳои иловакорӣ</th>
                    <th class="text-end">Рӯзҳои ғоиб</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sumEmp = $sumLogs = $sumOver = $sumAbs = 0;
            $sumHours = 0.0;
            foreach ($rows as $r):
                $sumEmp += (int) $r['employee_count'];
                $sumLogs += (int) $r['total_logs'];
                $sumHours += (float) $r['total_hours'];
                $sumOver += (int) $r['overtime_days'];
                $sumAbs += (int) $r['absent_days'];
            ?>
                <tr>
                    <td><?= $this->e($r['department_name']) ?></td>
                    <td class="text-end"><?= (int) $r['employee_count'] ?></td>
                    <td class="text-end"><?= (int) $r['total_logs'] ?></td>
                    <td class="text-end"><strong><?= number_format((float) $r['total_hours'], 2) ?></strong></td>
                    <td class="text-end"><?= (int) $r['overtime_days'] ?></td>
                    <td class="text-end"><?= (int) $r['absent_days'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td>ҲАМАГӢ</td>
                    <td class="text-end"><?= $sumEmp ?></td>
                    <td class="text-end"><?= $sumLogs ?></td>
                    <td class="text-end"><?= number_format($sumHours, 2) ?></td>
                    <td class="text-end"><?= $sumOver ?></td>
                    <td class="text-end"><?= $sumAbs ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
