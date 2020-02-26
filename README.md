# Web Application Project - Finance Tracker

### The database used in this project comes with the XAMPP open-source cross-platform web server solution stack package.

Please ensure the following information is the same
```
servername = "localhost"
username = "root"
password = ""
dbname = "project_wa"
```
Can change the connection to database via xampp\htdocs\project\common\db_conn.php

1. CREATE DATABASE project_wa;

2. CREATE TABLE user (
	id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	username VARCHAR( 50 ) NOT NULL ,
	password VARCHAR( 50 ) NOT NULL ,
	email VARCHAR( 50 ) NOT NULL ,
	phone_num VARCHAR( 50 ) NOT NULL
);

2. CREATE TABLE account (
	id int NOT NULL AUTO_INCREMENT,
	name varchar(50) NOT NULL,
	user_id int NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY (user_id) 
	REFERENCES user (id)
);

3. CREATE TABLE transaksi ( 
	id int not null auto_increment, 
	amount int not null, 
	category int not null, 
	acc_id int, 
	type varchar(15) not null, 
	transdate date not null,
 	user_id int, 
	PRIMARY KEY (id),
 	CONSTRAINT fk_acc_id FOREIGN KEY (acc_id) REFERENCEs account(id) ON DELETE CASCADE,
 	CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCEs user(id) ON DELETE CASCADE
 );
