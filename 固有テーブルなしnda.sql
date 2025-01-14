drop database if exists nda;
create database nda default character set utf8 collate utf8_general_ci;
drop user if exists 'staff'@'%';
create user 'staff'@'%' identified by 'password';
grant all on nda.* to 'staff'@'%';
FLUSH PRIVILEGES;
use nda;

create table users (
	id int auto_increment primary key, 
	login varchar(100) not null unique, 
	password varchar(100) not null
);
