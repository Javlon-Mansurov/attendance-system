<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class WorkLogRepository
{
    public const FIELDS = [
        'employee_id', 'work_date', 'clock_in', 'clock_out',
        'hours_worked', 'day_type', 'note'
    ];

    private const SELECT = '
        SELECT w.*,
               e.full_name AS employee_name,
               e.tab_number AS employee_tab,
               e.position AS employee_position,
               d.id AS department_id,
               d.name AS department_name
        FROM work_logs w
        JOIN employees e ON e.id = w.employee_id
        JOIN departments d ON d.id = e.department_id';

    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    private function buildWhere(array $filters): array
    {
        $conditions = [];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $conditions[] = "(e.full_name LIKE :q_name OR w.note LIKE :q_note)";
            $escaped = '%' . addcslashes($q, '%_\\') . '%';
            $params[':q_name'] = $escaped;
            $params[':q_note'] = $escaped;
        }

        if (!empty($filters['department_id'])) {
            $conditions[] = 'e.department_id = :department_id';
            $params[':department_id'] = (int) $filters['department_id'];
        }

        if (!empty($filters['day_type'])) {
            $conditions[] = 'w.day_type = :day_type';
            $params[':day_type'] = (string) $filters['day_type'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }

    public function paginate(array $filters, int $page, int $perPage = 10): array
    {
        [$where, $params] = $this->buildWhere($filters);

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM work_logs w
            JOIN employees e ON e.id = w.employee_id
            {$where}
        ");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);

        $sql = self::SELECT . " {$where} ORDER BY w.work_date DESC, e.full_name ASC, w.id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page'  => $page,
            'pages' => $pages,
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(self::SELECT . ' WHERE w.id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function logExists(int $employeeId, string $workDate, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM work_logs WHERE employee_id = :emp_id AND work_date = :w_date';
        $params = [':emp_id' => $employeeId, ':w_date' => $workDate];
        if ($excludeId !== null) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $columns = implode(', ', self::FIELDS);
        $placeholders = ':' . implode(', :', self::FIELDS);
        $stmt = $this->db->prepare("INSERT INTO work_logs ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($this->onlyFields($data));
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $set = implode(', ', array_map(fn ($f) => "{$f} = :{$f}", self::FIELDS));
        $stmt = $this->db->prepare("UPDATE work_logs SET {$set} WHERE id = :id");
        $stmt->execute($this->onlyFields($data) + ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM work_logs WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    private function onlyFields(array $data): array
    {
        $clean = [];
        foreach (self::FIELDS as $field) {
            $clean[$field] = $data[$field] ?? null;
        }
        return $clean;
    }
}
