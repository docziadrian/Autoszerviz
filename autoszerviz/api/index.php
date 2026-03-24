<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once 'config/Adatbazis.php';
include_once 'models/Ugyfel.php';
include_once 'models/Szolgaltatas.php';
include_once 'models/Foglalas.php';
include_once 'controllers/UgyfelVezerlo.php';
include_once 'controllers/SzolgaltatasVezerlo.php';
include_once 'controllers/FoglalasVezerlo.php';

$adatbazis = new Adatbazis();
$kapcsolat = $adatbazis->csatlakozas();

$vegpont = isset($_GET['vegpont']) ? $_GET['vegpont'] : '';
$keres_mod = $_SERVER['REQUEST_METHOD'];

switch($vegpont) {
    case 'ugyfelek':
        $vezerlo = new UgyfelVezerlo($kapcsolat);
        $vezerlo->feldolgoz($keres_mod);
        break;
    case 'szolgaltatasok':
        $vezerlo = new SzolgaltatasVezerlo($kapcsolat);
        $vezerlo->feldolgoz($keres_mod);
        break;
    case 'foglalasok':
        $vezerlo = new FoglalasVezerlo($kapcsolat);
        $vezerlo->feldolgoz($keres_mod);
        break;
    default:
        http_response_code(404);
        echo json_encode(array("uzenet" => "A kert API vegpont nem letezik."));
        break;
}
?>
