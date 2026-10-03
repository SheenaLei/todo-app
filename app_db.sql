CREATE DATABASE IF NOT EXISTS todo_db;
USE todo_db;

CREATE TABLE IF NOT EXISTS tasks (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    task VARCHAR(255) NOT NULL,
    description TEXT,
    deadline_date DATE,
    deadline_time TIME,
    status VARCHAR(50) DEFAULT 'Pending'
);