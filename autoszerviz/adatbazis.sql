CREATE DATABASE IF NOT EXISTS autoszerviz CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci;
USE autoszerviz;

CREATE TABLE IF NOT EXISTS ugyfelek (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nev VARCHAR(100) NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS szolgaltatasok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    megnevezes VARCHAR(100) NOT NULL,
    ar INT NOT NULL,
    idotartam_perc INT NOT NULL
);

CREATE TABLE IF NOT EXISTS foglalasok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ugyfel_id INT NOT NULL,
    szolgaltatas_id INT NOT NULL,
    idopont DATETIME NOT NULL,
    allapot VARCHAR(50) NOT NULL DEFAULT 'foglalt',
    FOREIGN KEY (ugyfel_id) REFERENCES ugyfelek(id) ON DELETE CASCADE,
    FOREIGN KEY (szolgaltatas_id) REFERENCES szolgaltatasok(id) ON DELETE CASCADE
);
