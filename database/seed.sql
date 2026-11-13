USE attendance_db;
SET NAMES utf8mb4;

INSERT INTO departments (id, department_name, description) VALUES
(1, 'Шӯъбаи барномарезӣ ва ТИ', 'Таҳия ва дастгирии низомҳои иттилоотӣ'),
(2, 'Шӯъбаи муҳосибот', 'Ҳисоббаробаркунӣ ва музди меҳнат'),
(3, 'Шӯъбаи кадрҳо ва маъмурият', 'Идоракунии кормандон ва ҳуҷҷатгузорӣ');

INSERT INTO employees (id, full_name, phone, email, department_id, position, hire_date, status) VALUES
(1, 'Мансуров Ҷавлонҷон', '+992927001122', 'javlon@company.tj', 1, 'Барномасози пешбар', '2024-02-01', 'active'),
(2, 'Раҳимова Нигина Собировна', '+992927001123', 'nigina@company.tj', 2, 'Сармуҳосиб', '2022-05-15', 'active'),
(3, 'Каримов Фирдавс Алиевич', '+992927001124', 'firdavs@company.tj', 3, 'Мудири шӯъбаи кадрҳо', '2021-09-01', 'active'),
(4, 'Шарипова Мадина Рустамовна', '+992927001125', 'madina@company.tj', 1, 'Муҳандиси сифат (QA)', '2025-01-10', 'active');

-- Рамзи ҳамаи корбарон: password
INSERT INTO users (id, employee_id, username, password, role) VALUES
(1, NULL, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 3, 'manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
(3, 2, 'accountant', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'accountant'),
(4, 1, 'javlon', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee');

INSERT INTO work_schedules (employee_id, day_of_week, start_time, end_time, break_minutes, is_working) VALUES
(1, 1, '09:00:00', '18:00:00', 60, 1),
(1, 2, '09:00:00', '18:00:00', 60, 1),
(1, 6, '00:00:00', '00:00:00', 0, 0);

INSERT INTO time_logs (employee_id, work_date, check_in, check_out) VALUES
(1, '2026-11-11', '2026-11-11 08:55:00', '2026-11-11 18:02:00'),
(1, '2026-11-12', '2026-11-12 09:00:00', '2026-11-12 18:05:00'),
(4, '2026-11-12', '2026-11-12 09:10:00', '2026-11-12 18:00:00');

INSERT INTO absences (employee_id, absence_type, start_date, end_date, reason, status) VALUES
(4, 'sick', '2026-11-05', '2026-11-07', 'Варақаи беморӣ №142', 'approved');

INSERT INTO leave_requests (employee_id, start_date, end_date, reason, status, approved_by) VALUES
(1, '2026-12-10', '2026-12-20', 'Рухсатии меҳнатии солона', 'approved', 2),
(4, '2026-12-25', '2026-12-30', 'Сабабҳои оилавӣ', 'pending', NULL);

INSERT INTO holidays (holiday_name, holiday_date, description) VALUES
('Рӯзи Конститутсияи ҶТ', '2026-11-06', 'Рӯзи иди расмӣ'),
('Ҷашни Наврӯз', '2026-03-21', 'Иди байналмилалии Наврӯз'),
('Рӯзи Истиқлолияти давлатӣ', '2026-09-09', 'Иди миллии Ҷумҳурии Тоҷикистон');
