DROP DATABASE IF EXISTS attendance_db;
CREATE DATABASE attendance_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE attendance_db;
SET NAMES utf8mb4;

CREATE TABLE departments (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  department_name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE employees (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  phone VARCHAR(20) NULL,
  email VARCHAR(100) NULL UNIQUE,
  department_id INT(11) NOT NULL,
  position VARCHAR(100) NULL,
  hire_date DATE NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  CONSTRAINT fk_employees_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_employees_name (full_name),
  INDEX idx_employees_status (status)
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  employee_id INT(11) NULL UNIQUE,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','employee','manager','accountant') NOT NULL DEFAULT 'employee',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE work_schedules (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  employee_id INT(11) NOT NULL,
  day_of_week TINYINT(4) NOT NULL,
  start_time TIME NOT NULL DEFAULT '09:00:00',
  end_time TIME NOT NULL DEFAULT '18:00:00',
  break_minutes INT(11) NOT NULL DEFAULT 60,
  is_working TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_schedules_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE time_logs (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  employee_id INT(11) NOT NULL,
  work_date DATE NOT NULL,
  check_in DATETIME NULL,
  check_out DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_timelogs_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_timelogs_date (work_date)
) ENGINE=InnoDB;

CREATE TABLE absences (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  employee_id INT(11) NOT NULL,
  absence_type ENUM('leave','sick','business_trip') NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  reason TEXT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  CONSTRAINT chk_absences_dates CHECK (end_date >= start_date),
  CONSTRAINT fk_absences_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE leave_requests (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  employee_id INT(11) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  reason TEXT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  approved_by INT(11) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_leaves_dates CHECK (end_date >= start_date),
  CONSTRAINT fk_leaves_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_leaves_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE holidays (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  holiday_name VARCHAR(150) NOT NULL,
  holiday_date DATE NOT NULL UNIQUE,
  description TEXT NULL
) ENGINE=InnoDB;
