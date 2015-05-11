# LinkedIn_Client

Create a table in MYSQL.

CREATE TABLE user (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
first_name VARCHAR(30) NOT NULL,
last_name VARCHAR(30) NOT NULL,
company_name VARCHAR(30),
title VARCHAR(30),
skills VARCHAR(30),
college_name VARCHAR(30),
college_degree VARCHAR(30),
courses text,
images text
);
