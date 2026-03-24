
<?php

//loadEnv fájl a GitHub -os repobol
function loadEnv(string $path): void
{


    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$name, $value] = explode('=', $line, 2);

            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Használat:
loadEnv(__DIR__ . '/.env');

class Adatbazis
{
    private $szerver;
    private $felhasznalo;
    private $jelszo;
    private $adatbazis;
    public $kapcsolat;

    public function __construct()
    {
        $this->szerver = isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'localhost';
        $this->felhasznalo = isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'root';
        $this->jelszo = isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : '';
        $this->adatbazis = isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'autoszerviz';
    }

    public function csatlakozas()
    {
        $this->kapcsolat = null;
        try {
            $this->kapcsolat = new PDO("mysql:host=" . $this->szerver . ";dbname=" . $this->adatbazis . ";charset=utf8mb4", $this->felhasznalo, $this->jelszo);
            $this->kapcsolat->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $kivetel) {
            echo "Kapcsolodasi hiba: " . $kivetel->getMessage();
        }
        return $this->kapcsolat;
    }
}
