<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Repositories\DepartmentRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\WorkLogRepository;
use PDOException;

final class WorkLogController
{
    private const PER_PAGE = 10;

    private WorkLogRepository $logs;
    private EmployeeRepository $employees;
    private DepartmentRepository $departments;
    private View $view;

    public function __construct()
    {
        $this->logs = new WorkLogRepository();
        $this->employees = new EmployeeRepository();
        $this->departments = new DepartmentRepository();
        $this->view = new View(BASE_PATH . '/views');
    }

    public function index(): void
    {
        $filters = [
            'q'             => $_GET['q'] ?? '',
            'department_id' => $_GET['department_id'] ?? '',
            'day_type'      => $_GET['day_type'] ?? '',
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $result = $this->logs->paginate($filters, $page, self::PER_PAGE);

        echo $this->view->render('work_logs/index', [
            'title'       => 'Баҳисобгирии вақти корӣ',
            'logs'        => $result['items'],
            'total'       => $result['total'],
            'page'        => $result['page'],
            'pages'       => $result['pages'],
            'filters'     => $filters,
            'departments' => $this->departments->all(),
            'employees'   => $this->employees->all(),
        ]);
    }

    public function show(int $id): void
    {
        $log = $this->findOrFail($id);
        echo $this->view->render('work_logs/show', [
            'title' => 'Варақаи баҳисобгирии вақт #' . $id,
            'log'   => $log,
        ]);
    }

    public function create(): void
    {
        $this->renderForm([
            'work_date'    => date('Y-m-d'),
            'clock_in'     => '08:00',
            'clock_out'    => '17:00',
            'hours_worked' => '8.00',
            'day_type'     => 'work',
        ], [], null);
    }

    public function store(): void
    {
        [$data, $errors] = $this->validate($_POST, null);
        if ($errors) {
            $this->renderForm($_POST, $errors, null, 422);
            return;
        }
        $id = $this->logs->create($data);
        $this->redirect('/work-logs/' . $id . '?msg=created');
    }

    public function edit(int $id): void
    {
        $log = $this->findOrFail($id);
        $log['clock_in'] = substr((string) $log['clock_in'], 0, 5);
        $log['clock_out'] = substr((string) $log['clock_out'], 0, 5);
        $this->renderForm($log, [], $id);
    }

    public function update(int $id): void
    {
        $this->findOrFail($id);
        [$data, $errors] = $this->validate($_POST, $id);
        if ($errors) {
            $this->renderForm($_POST, $errors, $id, 422);
            return;
        }
        $this->logs->update($id, $data);
        $this->redirect('/work-logs/' . $id . '?msg=updated');
    }

    public function delete(int $id): void
    {
        $this->findOrFail($id);
        $this->logs->delete($id);
        $this->redirect('/work-logs?msg=deleted');
    }

    /** Санҷиши нест кардани корманд бо истинодҳои FK (1451) */
    public function deleteEmployee(int $id): void
    {
        try {
            $this->employees->delete($id);
            $this->redirect('/work-logs?msg=emp_deleted');
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1451) {
                $this->redirect('/work-logs?msg=fk_blocked');
                return;
            }
            throw $e;
        }
    }

    private function validate(array $input, ?int $id): array
    {
        $v = new Validator($input);
        $v->required('employee_id', 'Корманд')->in('employee_id', $this->employees->ids(), 'Корманд')
          ->required('work_date', 'Санаи корӣ')->date('work_date', 'Санаи корӣ')
          ->required('clock_in', 'Вақти омадан')->time('clock_in', 'Вақти омадан')
          ->required('clock_out', 'Вақти рафтан')->time('clock_out', 'Вақти рафтан')
          ->required('hours_worked', 'Соатҳои корӣ')->numericRange('hours_worked', 0, 24, 'Соатҳои корӣ')
          ->required('day_type', 'Намуди рӯз')->in('day_type', ['work', 'overtime', 'remote', 'sick', 'absent'], 'Намуди рӯз')
          ->maxLength('note', 255, 'Эзоҳ');

        $data = [
            'employee_id'  => (int) ($input['employee_id'] ?? 0),
            'work_date'    => trim((string) ($input['work_date'] ?? '')),
            'clock_in'     => trim((string) ($input['clock_in'] ?? '')),
            'clock_out'    => trim((string) ($input['clock_out'] ?? '')),
            'hours_worked' => (float) ($input['hours_worked'] ?? 0),
            'day_type'     => trim((string) ($input['day_type'] ?? 'work')),
            'note'         => trim((string) ($input['note'] ?? '')) ?: null,
        ];

        $errors = $v->errors();

        if (!isset($errors['work_date']) && $data['work_date'] > date('Y-m-d')) {
            $v->addError('work_date', 'Санаи корӣ наметавонад дар оянда бошад');
        }

        if (!isset($errors['clock_in']) && !isset($errors['clock_out']) && !in_array($data['day_type'], ['sick', 'absent'], true)) {
            if ($data['clock_out'] <= $data['clock_in']) {
                $v->addError('clock_out', 'Вақти рафтан бояд аз вақти омадан дертар бошад');
            }
        }

        // Қоидаи беназирӣ бо муроҷиат ба ПД
        if (!isset($errors['employee_id']) && !isset($errors['work_date'])) {
            if ($this->logs->logExists($data['employee_id'], $data['work_date'], $id)) {
                $v->addError('work_date', 'Барои ин корманд дар ин сана аллакай сабти вақти корӣ мавҷуд аст');
            }
        }

        return [$data, $v->errors()];
    }

    private function renderForm(array $log, array $errors, ?int $id, int $status = 200): void
    {
        http_response_code($status);
        echo $this->view->render('work_logs/form', [
            'title'     => $id ? 'Таҳрири сабти вақти корӣ #' . $id : 'Сабти нави вақти корӣ',
            'log'       => $log,
            'errors'    => $errors,
            'id'        => $id,
            'employees' => $this->employees->all(),
        ]);
    }

    private function findOrFail(int $id): array
    {
        $log = $this->logs->find($id);
        if (!$log) {
            http_response_code(404);
            echo $this->view->render('errors/error', [
                'title'   => 'Сабт ёфт нашуд',
                'code'    => 404,
                'message' => "Сабти вақти корӣ бо ID={$id} мавҷуд нест.",
            ]);
            exit;
        }
        return $log;
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $this->view->url($path), true, 302);
        exit;
    }
}
