<?php

class FoglalasVezerlo {
    private $foglalas;

    public function __construct($adatbazis) {
        $this->foglalas = new Foglalas($adatbazis);
    }

    public function feldolgoz($keres_mod) {
        $azonosito = isset($_GET['id']) ? $_GET['id'] : null;

        switch($keres_mod) {
            case 'GET':
                if($azonosito) {
                    $this->foglalas->id = $azonosito;
                    $this->foglalas->olvasas_egy();
                    if($this->foglalas->ugyfel_id != null) {
                        $foglalas_tomb = array(
                            "id" =>  $this->foglalas->id,
                            "ugyfel_id" => $this->foglalas->ugyfel_id,
                            "szolgaltatas_id" => $this->foglalas->szolgaltatas_id,
                            "idopont" => $this->foglalas->idopont,
                            "allapot" => $this->foglalas->allapot,
                            "ugyfel_nev" => $this->foglalas->ugyfel_nev,
                            "szolgaltatas_megnevezes" => $this->foglalas->szolgaltatas_megnevezes
                        );
                        http_response_code(200);
                        echo json_encode($foglalas_tomb);
                    } else {
                        http_response_code(404);
                        echo json_encode(array("uzenet" => "Hiba: A foglalas nem talalhato."));
                    }
                } else {
                    $eredmeny = $this->foglalas->olvasas();
                    $szam = $eredmeny->rowCount();
                    if($szam > 0) {
                        $foglalasok_tomb = array();
                        while ($sor = $eredmeny->fetch(PDO::FETCH_ASSOC)) {
                            extract($sor);
                            $foglalas_elem = array(
                                "id" => $id,
                                "ugyfel_id" => $ugyfel_id,
                                "szolgaltatas_id" => $szolgaltatas_id,
                                "idopont" => $idopont,
                                "allapot" => $allapot,
                                "ugyfel_nev" => $ugyfel_nev,
                                "szolgaltatas_megnevezes" => $szolgaltatas_megnevezes
                            );
                            array_push($foglalasok_tomb, $foglalas_elem);
                        }
                        http_response_code(200);
                        echo json_encode($foglalasok_tomb);
                    } else {
                        http_response_code(200);
                        echo json_encode(array());
                    }
                }
                break;
            case 'POST':
                $adatok = json_decode(file_get_contents("php://input"));
                if(!empty($adatok->ugyfel_id) && !empty($adatok->szolgaltatas_id) && !empty($adatok->idopont) && !empty($adatok->allapot)) {
                    $this->foglalas->ugyfel_id = $adatok->ugyfel_id;
                    $this->foglalas->szolgaltatas_id = $adatok->szolgaltatas_id;
                    $this->foglalas->idopont = $adatok->idopont;
                    $this->foglalas->allapot = $adatok->allapot;
                    
                    if($this->foglalas->letrehozas()) {
                        http_response_code(201);
                        echo json_encode(array("uzenet" => "Foglalas letrehozva."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("uzenet" => "Hiba: Nem sikerult letrehozni a foglalast."));
                    }
                } else {
                    if (isset($_POST['ugyfel_id']) && isset($_POST['szolgaltatas_id']) && isset($_POST['idopont']) && isset($_POST['allapot'])) {
                        $this->foglalas->ugyfel_id = $_POST['ugyfel_id'];
                        $this->foglalas->szolgaltatas_id = $_POST['szolgaltatas_id'];
                        $this->foglalas->idopont = $_POST['idopont'];
                        $this->foglalas->allapot = $_POST['allapot'];
                        
                        if($this->foglalas->letrehozas()) {
                            http_response_code(201);
                            echo json_encode(array("uzenet" => "Foglalas letrehozva."));
                        } else {
                            http_response_code(503);
                            echo json_encode(array("uzenet" => "Hiba: Nem sikerult letrehozni a foglalast."));
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(array("uzenet" => "Hianyzo adatok a foglalas letrehozasahoz."));
                    }
                }
                break;
            case 'PUT':
                $adatok = json_decode(file_get_contents("php://input"));
                if(!empty($adatok->id) && !empty($adatok->ugyfel_id) && !empty($adatok->szolgaltatas_id) && !empty($adatok->idopont) && !empty($adatok->allapot)) {
                    $this->foglalas->id = $adatok->id;
                    $this->foglalas->ugyfel_id = $adatok->ugyfel_id;
                    $this->foglalas->szolgaltatas_id = $adatok->szolgaltatas_id;
                    $this->foglalas->idopont = $adatok->idopont;
                    $this->foglalas->allapot = $adatok->allapot;
                    
                    if($this->foglalas->frissites()) {
                        http_response_code(200);
                        echo json_encode(array("uzenet" => "Foglalas frissitve."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("uzenet" => "Hiba: Nem sikerult frissiteni a foglalast."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("uzenet" => "Hianyzo adatok a foglalas frissitesehez."));
                }
                break;
            case 'DELETE':
                $adatok = json_decode(file_get_contents("php://input"));
                if(!empty($adatok->id)) {
                    $this->foglalas->id = $adatok->id;
                    if($this->foglalas->torles()) {
                        http_response_code(200);
                        echo json_encode(array("uzenet" => "Foglalas torolve."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("uzenet" => "Nem sikerult torolni a foglalast."));
                    }
                } else {
                    if($azonosito) {
                        $this->foglalas->id = $azonosito;
                        if($this->foglalas->torles()) {
                            http_response_code(200);
                            echo json_encode(array("uzenet" => "Foglalas torolve."));
                        } else {
                            http_response_code(503);
                            echo json_encode(array("uzenet" => "Nem sikerult torolni a foglalast."));
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(array("uzenet" => "Hianyzo adatok a foglalas torlesehez."));
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
