# UI-kit: Учёт рабочего времени сотрудников (timetracking)

Figma: <вставьте ссылку на файл> · прототип: <вставьте ссылку Present>
(доступ «Anyone with the link — can view»)

## Сетка
Desktop 1440: 12 колонок × 88 px, gutter 24 px, контент 1320 px (.container Bootstrap 5.3).
Mobile 390: 4 колонки, margin 16, gutter 16.
Шаг отступов: 4 / 8 / 16 / 24 / 32 px.

Раскладка на Bootstrap: фильтры — `col-3`, основной контент — `col-9`.

## Цвета
Брендовый цвет совпадает с Bootstrap, переопределять `--bs-primary` в день 10 не нужно.

| Стиль Figma | HEX | Bootstrap | Где используется |
|---|---|---|---|
| color/primary | #0D6EFD | --bs-primary | основные кнопки, ссылки, «Отметить приход» |
| color/success | #198754 | --bs-success | статус «На работе», approved |
| color/warning | #FFC107 | --bs-warning | статус «Опоздал», pending (текст тёмный) |
| color/danger | #DC3545 | --bs-danger | «Отсутствует», ошибки, rejected, «Удалить» |
| color/text | #212529 | --bs-body-color | основной текст |
| color/muted | #6C757D | --bs-secondary | подписи |
| color/border | #DEE2E6 | --bs-border-color | рамки |
| color/bg | #F8F9FA | --bs-light | фон страницы |

## Типографика
Noto Sans (полная кириллица): h1 32/40 SemiBold, h2 24/32 SemiBold, body 16/24 Regular, small 14/20 Regular.

## Компоненты
- **Button**: Primary / Secondary / Danger × Default / Hover / Disabled → `.btn .btn-primary`, `.btn-outline-secondary`, `.btn-danger`. Высота 44 px.
- **Input**: Default / Focus / Error / Disabled → `.form-control`, `.is-invalid`, подпись `<label>`. Высота 44 px.
- **EmployeeCard** (Auto Layout, горизонтальный): фото 64×64, ФИО, должность, отдел, бейдж статуса → `.card` + `.d-flex`.
- **TimeEntryRow** (строка журнала): дата, приход, уход, часов, статус.

## Бейджи статуса (слово + цвет, не только цвет)
| Статус | Фон | Текст |
|---|---|---|
| На работе | color/success | белый |
| Опоздал | color/warning | color/text (тёмный) |
| Отсутствует | color/danger | белый |
| В отпуске | color/primary | белый |

## Доступность
| Пара | Результат |
|---|---|
| белый на color/primary (#0D6EFD) | ≥ 4.5:1 — проходит |
| color/muted (#6C757D) на белом | ≈ 4.7:1 — проходит |
| белый на color/warning (#FFC107) | не проходит → текст тёмный (color/text) |

Кнопки и поля ≥ 44 px по высоте; у всех полей есть подписи; основной шрифт 16 px.

## Экраны
| Экран | Маршрут | Файл |
|---|---|---|
| Вход | /login | screens/login.png |
| Главная (дашборд) | / | screens/dashboard.png |
| Журнал учёта времени | /attendance | screens/attendance.png |
| Табель | /timesheet | screens/timesheet.png |
| Список сотрудников | /employees | screens/employees.png |
| Карточка сотрудника | /employees/{id} | screens/employee.png |
| Заявка на отпуск | /leaves/create | screens/leave-create.png |
| Мобильная версия (390) | /attendance | screens/attendance-mobile.png |

![Журнал учёта времени](screens/attendance.png)
