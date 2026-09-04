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

INSERT INTO users (id, firstname, lastname, email, username) VALUES
    (1, 'Lloyd Jedrick', 'Abdon', 'lloyd.abdon@example.com', 'lloydjedrick'),
    (2, 'Robert', 'Downey Jr.', 'robert.downey@gmail.com', 'robertdowneyjr'),
    (3, 'Chris', 'Evans', 'chris.evans@gmail.com', 'chrisevans'),
    (4, 'Chris', 'Hemsworth', 'chris.hemsworth@gmail.com', 'chrishemsworth'),
    (5, 'Scarlett', 'Johansson', 'scarlett.johansson@gmail.com', 'scarlettjohansson')
ON DUPLICATE KEY UPDATE
    firstname=VALUES(firstname),
    lastname=VALUES(lastname),
    email=VALUES(email),
    username=VALUES(username);

SELECT id, firstname, lastname, email, username FROM users ORDER BY id;
