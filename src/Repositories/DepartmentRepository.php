<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class DepartmentRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM departments ORDER BY name')->fetchAll();
    }

    public function hoursReport(): array
    {
        $sql = "
            SELECT d.id, d.name AS department_name,
                   COUNT(DISTINCT e.id) AS employee_count,
                   COUNT(w.id) AS total_logs,
                   COALESCE(SUM(w.hours_worked), 0) AS total_hours,
                   COALESCE(SUM(w.day_type = 'overtime'), 0) AS overtime_days,
                   COALESCE(SUM(w.day_type = 'absent'), 0) AS absent_days
            FROM departments d
            LEFT JOIN employees e ON e.department_id = d.id
            LEFT JOIN work_logs w ON w.employee_id = e.id
            GROUP BY d.id, d.name
            ORDER BY d.name";
        return $this->db->query($sql)->fetchAll();
    }
}
