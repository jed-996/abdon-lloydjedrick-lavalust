CREATE DATABASE IF NOT EXISTS mydb
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mydb;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    username VARCHAR(100) NOT NULL,
    UNIQUE KEY users_email_unique (email),
    UNIQUE KEY users_username_unique (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (firstname, lastname, email, username) VALUES
    ('Lloyd Jedrick', 'Abdon', 'lloyd.abdon@example.com', 'lloydjedrick'),
    ('Maya', 'Santos', 'maya.santos@example.com', 'mayasantos'),
    ('Paolo', 'Ramirez', 'paolo.ramirez@example.com', 'paoloramirez'),
    ('Jhon Joseph', 'Evora', 'jhon.evora@example.com', 'jhonevora'),
    ('Arielle', 'Reyes', 'arielle.reyes@example.com', 'ariellereyes')
ON DUPLICATE KEY UPDATE firstname=VALUES(firstname), lastname=VALUES(lastname);

SELECT id, firstname, lastname, email, username FROM users ORDER BY id;
