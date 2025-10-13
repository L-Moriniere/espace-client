-- Crée la base principale et la base de test
CREATE DATABASE IF NOT EXISTS db_mgp;
CREATE DATABASE IF NOT EXISTS db_mgp_test;

-- Donne les droits à l'utilisateur existant sur les deux bases
GRANT ALL PRIVILEGES ON db_mgp.* TO 'user'@'%';
GRANT ALL PRIVILEGES ON db_mgp_test.* TO 'user'@'%';
FLUSH PRIVILEGES;
