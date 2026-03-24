<?php

class UgyfelVezerlo {
    private $ugyfel;

    public function __construct($adatbazis) {
        $this->ugyfel = new Ugyfel($adatbazis);
    }

    public function feldolgoz($keres_mod) {
        $azonosito = isset($_GET['id']) ? $_GET['id'] : null;

        switch($keres_mod) {
            case 'GET':
                if($azonosito) {
                    $this->ugyfel->id = $azonosito;
                    $this->ugyfel->olvasas_egy();
                    if($this->ugyfel->nev != null) {
                        $ugyfel_tomb = array(
                            "id" =>  $this->ugyfel->id,
                            "nev" => $this->ugyfel->nev,
                            "telefon" => $this->ugyfel->telefon,
                            "email" => $this->ugyfel->email
                        );
                        http_response_code(200);
                        echo json_encode($ugyfel_tomb);
                    } else {
                        http_response_code(404);
                        echo json_encode(array("uzenet" => "Hiba: Az ugyfel nem talalhato."));
                    }
                } else {
                    $eredmeny = $this->ugyfel->olvasas();
                    $szam = $eredmeny->rowCount();
                    if($szam > 0) {
                        $ugyfelek_tomb = array();
                        while ($sor = $eredmeny->fetch(PDO::FETCH_ASSOC)) {
                            extract($sor);
                            $ugyfel_elem = array(
                                "id" => $id,
                                "nev" => $nev,
                                "telefon" => $telefon,
                                "email" => $email
                            );
                            array_push($ugyfelek_tomb, $ugyfel_elem);
                        }
                        http_response_code(200);
                        echo json_encode($ugyfelek_tomb);
                    } else {
                        http_response_code(200);
                        echo json_encode(array());
                    }
                }
                break;
            case 'POST':
                $adatok = json_decode(file_get_contents("php://input"));
                if(!empty($adatok->nev) && !empty($adatok->telefon) && !empty($adatok->email)) {
                    $this->ugyfel->nev = $adatok->nev;
                    $this->ugyfel->telefon = $adatok->telefon;
                    $this->ugyfel->email = $adatok->email;
                    
                    if($this->ugyfel->letrehozas()) {
                        http_response_code(201);
                        echo json_encode(array("uzenet" => "Ugyfel sikeresen letrehozva."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("uzenet" => "Hiba: Nem sikerult letrehozni az ugyfelet."));
                    }
                } else {
                    if (isset($_POST['nev']) && isset($_POST['telefon']) && isset($_POST['email'])) {
                        $this->ugyfel->nev = $_POST['nev'];
                        $this->ugyfel->telefon = $_POST['telefon'];
                        $this->ugyfel->email = $_POST['email'];
                        
                        if($this->ugyfel->letrehozas()) {
                            http_response_code(201);
                            echo json_encode(array("uzenet" => "Ugyfel sikeresen letrehozva."));
                        } else {
                            http_response_code(503);
                            echo json_encode(array("uzenet" => "Hiba: Nem sikerult letrehozni az ugyfelet."));
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(array("uzenet" => "Hianyzo adatok az ugyfel letrehozasahoz."));
                    }
                }
                break;
            case 'PUT':
                $adatok = json_decode(file_get_contents("php://input"));
                if(!empty($adatok->id) && !empty($adatok->nev) && !empty($adatok->telefon) && !empty($adatok->email)) {
                    $this->ugyfel->id = $adatok->id;
                    $this->ugyfel->nev = $adatok->nev;
                    $this->ugyfel->telefon = $adatok->telefon;
                    $this->ugyfel->email = $adatok->email;
                    
                    if($this->ugyfel->frissites()) {
                        http_response_code(200);
                        echo json_encode(array("uzenet" => "Ugyfel sikeresen frissitve."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("uzenet" => "Hiba: Nem sikerult frissiteni az ugyfelet."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("uzenet" => "Hianyzo adatok az ugyfel frissitesehez."));
                }
                break;
            case 'DELETE':
                $adatok = json_decode(file_get_contents("php://input"));
                if(!empty($adatok->id)) {
                    $this->ugyfel->id = $adatok->id;
                    if($this->ugyfel->torles()) {
                        http_response_code(200);
                        echo json_encode(array("uzenet" => "Ugyfel sikeresen torolve."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("uzenet" => "Hiba: Nem sikerult torolni az ugyfelet."));
                    }
                } else {
                    if($azonosito) {
                        $this->ugyfel->id = $azonosito;
                        if($this->ugyfel->torles()) {
                            http_response_code(200);
                            echo json_encode(array("uzenet" => "Ugyfel sikeresen torolve."));
                        } else {
                            http_response_code(503);
                            echo json_encode(array("uzenet" => "Hiba: Nem sikerult torolni az ugyfelet."));
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(array("uzenet" => "Hianyzo adatok a torleshez."));
                    }
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(array("uzenet" => "Hiba: Nem engedelyezett HTTP metodus."));
                break;
        }
    }
}
?>
