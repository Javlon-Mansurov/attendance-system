<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\DepartmentRepository;

final class ReportController
{
    public function hours(): void
    {
        $repo = new DepartmentRepository();
        $view = new View(BASE_PATH . '/views');
        echo $view->render('reports/hours', [
            'title' => 'Ҳисоботи вақти корӣ аз рӯи шӯъбаҳо',
            'rows'  => $repo->hoursReport(),
        ]);
    }
}
