# Логическая модель данных: `attendance_db`

## 1. Исходная таблица (не нормализованная)
| ФИО сотрудника | Отдел | Должность | График (дни и часы) | Дата работы | Приход / Уход | Отпуска и статусы |
|---|---|---|---|---|---|---|
| Мансуров Ҷавлонҷон | Отдел разработки | Программист | Пн–Пт 09:00–18:00 | 2026-11-12 | 08:58 / 18:05 | 01.07–14.07 (approved) |

## 2. Нормализация
### 1НФ
Повторяющиеся группы («График», «Приход / Уход», «Отпуска») вынесены в отдельные таблицы `work_schedules`, `time_logs` и `leave_requests`, чтобы в каждой ячейке хранилось одно атомарное значение.
### 2НФ
Все таблицы имеют простой суррогатный первичный ключ `id`, частичные зависимости отсутствуют.
### 3НФ
Устранена транзитивная зависимость `сотрудник → отдел → описание отдела`: отделы вынесены в справочник `departments` (`department_id`).

## 3. Сущности (8 таблиц)
### `departments` — отделы
| Атрибут | Тип | Ключ | NULL | Описание |
|---|---|---|---|---|
| id | INT(11) | PK | нет | Уникальный идентификатор |
| department_name | VARCHAR(100) | UQ | нет | Название отдела |
| description | TEXT | | да | Описание отдела |
| created_at | TIMESTAMP | | нет | Дата создания |

### `employees` — сотрудники
| Атрибут | Тип | Ключ | NULL | Описание |
|---|---|---|---|---|
| id | INT(11) | PK | нет | Уникальный идентификатор |
| full_name | VARCHAR(150) | | нет | ФИО сотрудника |
| phone | VARCHAR(20) | | да | Телефон |
| email | VARCHAR(100) | UQ | да | Электронная почта |
| department_id | INT(11) | FK | нет | Отдел (`departments.id`) |
| position | VARCHAR(100) | | да | Должность |
| hire_date | DATE | | да | Дата приёма на работу |
| status | ENUM('active','inactive') | | нет | Статус сотрудника |

### `users` — пользователи системы
| Атрибут | Тип | Ключ | NULL | Описание |
|---|---|---|---|---|
| id | INT(11) | PK | нет | Уникальный идентификатор |
| employee_id | INT(11) | FK, UQ | да | Связанный сотрудник |
| username | VARCHAR(50) | UQ | нет | Логин для входа |
| password | VARCHAR(255) | | нет | Хеш пароля |
| role | ENUM('admin','employee','manager','accountant') | | нет | Роль доступа |
| created_at | TIMESTAMP | | нет | Дата регистрации |

### `work_schedules` — графики работы
| Атрибут | Тип | Ключ | NULL | Описание |
|---|---|---|---|---|
| id | INT(11) | PK | нет | Уникальный идентификатор |
| employee_id | INT(11) | FK | нет | Сотрудник |
| day_of_week | TINYINT(4) | | нет | День недели (1–7) |
| start_time | TIME | | нет | Начало смены |
| end_time | TIME | | нет | Окончание смены |
| break_minutes | INT(11) | | нет | Обеденный перерыв (мин) |
| is_working | TINYINT(1) | | нет | 1 — рабочий, 0 — выходной |

### `time_logs` — журнал учёта рабочего времени
| Атрибут | Тип | Ключ | NULL | Описание |
|---|---|---|---|---|
| id | INT(11) | PK | нет | Уникальный идентификатор |
| employee_id | INT(11) | FK | нет | Сотрудник |
| work_date | DATE | | нет | Рабочая дата |
| check_in | DATETIME | | да | Время прихода |
| check_out | DATETIME | | да | Время ухода |
| created_at | TIMESTAMP | | нет | Метка создания записи |

### `absences` — отсутствия (больничные, командировки)
| Атрибут | Тип | Ключ | NULL | Описание |
|---|---|---|---|---|
| id | INT(11) | PK | нет | Уникальный идентификатор |
| employee_id | INT(11) | FK | нет | Сотрудник |
| absence_type | ENUM('leave','sick','business_trip') | | нет | Тип отсутствия |
| start_date | DATE | | нет | Дата начала |
| end_date | DATE | | нет | Дата окончания |
| reason | TEXT | | да | Причина / номер документа |
| status | ENUM('pending','approved','rejected') | | нет | Статус |

### `leave_requests` — заявки на отпуск
| Атрибут | Тип | Ключ | NULL | Описание |
|---|---|---|---|---|
| id | INT(11) | PK | нет | Уникальный идентификатор |
| employee_id | INT(11) | FK | нет | Сотрудник |
| start_date | DATE | | нет | Дата начала отпуска |
| end_date | DATE | | нет | Дата окончания |
| reason | TEXT | | да | Причина |
| status | ENUM('pending','approved','rejected') | | нет | Статус заявки |
| approved_by | INT(11) | FK | да | Кто утвердил (`users.id`) |
| created_at | TIMESTAMP | | нет | Дата подачи |

### `holidays` — праздничные дни
| Атрибут | Тип | Ключ | NULL | Описание |
|---|---|---|---|---|
| id | INT(11) | PK | нет | Уникальный идентификатор |
| holiday_name | VARCHAR(150) | | нет | Название праздника |
| holiday_date | DATE | UQ | нет | Дата праздника |
| description | TEXT | | да | Примечание |

## 4. Связи
- `departments` 1:N `employees` (`department_id`)
- `employees` 1:1 `users` (`employee_id`)
- `employees` 1:N `work_schedules` (`employee_id`)
- `employees` 1:N `time_logs` (`employee_id`)
- `employees` 1:N `absences` (`employee_id`)
- `employees` 1:N `leave_requests` (`employee_id`)
- `users` 1:N `leave_requests` (`approved_by`)

## 5. Ограничения целостности
- `UNIQUE (users.username)`, `UNIQUE (employees.email)`, `UNIQUE (holidays.holiday_date)`
- `CHECK (end_date >= start_date)` в таблицах `leave_requests` и `absences`
- `ON DELETE RESTRICT` для `employees.department_id` и `leave_requests.approved_by`; `ON DELETE CASCADE` для `time_logs`, `work_schedules`, `absences`, `leave_requests` при удалении карточки сотрудника; `ON DELETE SET NULL` для `users.employee_id`.
