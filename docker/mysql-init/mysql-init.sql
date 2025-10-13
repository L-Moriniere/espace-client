-- Crée la base de test si elle n'existe pas
CREATE DATABASE IF NOT EXISTS db_mgp_test;

-- Crée ou recrée l'utilisateur avec les droits sur la base de test
DROP USER IF EXISTS 'user'@'%';
CREATE USER 'user'@'%' IDENTIFIED BY 'mgp_p4ssW0rd!';
GRANT ALL PRIVILEGES ON db_mgp_test.* TO 'user'@'%';
FLUSH PRIVILEGES;
