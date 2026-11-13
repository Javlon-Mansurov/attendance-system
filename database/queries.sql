USE attendance_db;
SET NAMES utf8mb4;

-- 1. Рӯйхати кормандон бо номи шӯъба ва нақши корбарӣ (JOIN 3 таблиц) (ФТ-07)
SELECT e.id, e.full_name, d.department_name, e.position, u.username, u.role
FROM employees e
JOIN departments d ON d.id = e.department_id
LEFT JOIN users u ON u.employee_id = e.id
ORDER BY e.full_name;

-- 2. Ҷустуҷӯи корманд аз рӯи насаб бо ҳарфҳои кириллӣ (LIKE) (ФТ-09)
SELECT id, full_name, position, phone
FROM employees
WHERE full_name LIKE 'Мансуров%';

-- 3. Шумораи кормандон дар ҳар як шӯъба (GROUP BY + COUNT + LEFT JOIN) (ФТ-08)
SELECT d.department_name, COUNT(e.id) AS employee_count
FROM departments d
LEFT JOIN employees e ON e.department_id = d.id AND e.status = 'active'
GROUP BY d.id, d.department_name;

-- 4. Аризаҳои рухсатӣ, ки тасдиқи менеҷерро интизоранд (ФТ-04)
SELECT lr.id, e.full_name, lr.start_date, lr.end_date, lr.reason, lr.status
FROM leave_requests lr
JOIN employees e ON e.id = lr.employee_id
WHERE lr.status = 'pending';

-- 5. Ҳисоби соатҳои кории кормандон аз рӯи табел (ФТ-08)
SELECT e.full_name, t.work_date, t.check_in, t.check_out,
       TIMESTAMPDIFF(HOUR, t.check_in, t.check_out) AS hours_worked
FROM time_logs t
JOIN employees e ON e.id = t.employee_id
ORDER BY t.work_date DESC;

-- 6. Саҳифабандии рӯйхати кормандон (LIMIT / OFFSET) (ФТ-07)
SELECT id, full_name, position, hire_date
FROM employees
ORDER BY id
LIMIT 3 OFFSET 0;
