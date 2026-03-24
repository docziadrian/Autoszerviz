<?php



session_start();

$alap_url = "http://" . $_SERVER['HTTP_HOST'] . "/DAM/autoszerviz/api/index.php?vegpont=";

function api_kuld($url, $metodus = 'GET', $adatok = null)
{
    $curl = curl_init();
    $opciok = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $metodus,
        CURLOPT_HTTPHEADER => array('Content-Type: application/json')
    );
    if ($adatok) {
        $opciok[CURLOPT_POSTFIELDS] = json_encode($adatok);
    }
    curl_setopt_array($curl, $opciok);
    $valasz = curl_exec($curl);
    curl_close($curl); //TODO ?
    return json_decode($valasz, true);
}

$oldal = isset($_GET['oldal']) ? $_GET['oldal'] : 'foglalasok';
$muvelet = isset($_GET['muvelet']) ? $_GET['muvelet'] : 'lista';
$id = isset($_GET['id']) ? $_GET['id'] : null;

$tartalom_fajl = "views/" . $oldal . ".php";

if (!file_exists($tartalom_fajl)) {
    $tartalom_fajl = "views/foglalasok.php";
    $oldal = 'foglalasok';
}

require_once 'views/sablon.php';
