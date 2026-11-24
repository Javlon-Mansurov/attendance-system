<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class EmployeeRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function all(): array
    {
        $sql = 'SELECT e.*, d.name AS department_name
                FROM employees e
                JOIN departments d ON d.id = e.department_id
                ORDER BY e.full_name';
        return $this->db->query($sql)->fetchAll();
    }

    public function ids(): array
    {
        return array_map('intval', $this->db->query('SELECT id FROM employees')->fetchAll(PDO::FETCH_COLUMN));
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM employees WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
