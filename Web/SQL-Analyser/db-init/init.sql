CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100),
  password VARCHAR(100)
);

INSERT INTO users (username, password) VALUES
('super_admin', 'S7{1nj3ct10n_SQL_by_Qu3ry_L0g1c}'),
('Boby', 'Pa$$word123!');

